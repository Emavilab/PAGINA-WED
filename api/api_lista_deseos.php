<?php
// Asegurar que la API devuelva únicamente JSON (evitar warnings/HTML en la salida)
ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');
ob_start();
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

// Si hay alguna salida previa limpiarla
if (ob_get_length() > 0) ob_clean();

if (!usuarioAutenticado()) {
    echo json_encode(['exito' => false, 'error' => 'Debes iniciar sesión para usar la lista de deseos']);
    exit();
}

// Asegurar que la tabla exista (evita errores 500 cuando no está creada)
$createTbl = "CREATE TABLE IF NOT EXISTS lista_deseos (
    id_lista INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_producto INT NOT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cliente_prod (id_cliente, id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conexion->query($createTbl);

// Obtener id_cliente asociado al usuario
$stmtCli = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ? AND estado = 'activo' LIMIT 1");
$stmtCli->bind_param("i", $_SESSION['id_usuario']);
$stmtCli->execute();
$resCli = $stmtCli->get_result()->fetch_assoc();
if (!$resCli) {
    echo json_encode(['exito' => false, 'error' => 'Debes iniciar sesión para usar la lista de deseos']);
    exit();
}
$id_cliente = (int)$resCli['id_cliente'];

$accion = $_REQUEST['accion'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];

// Endpoint de depuración temporal
if ($accion === 'debug') {
    $headers = function_exists('getallheaders') ? getallheaders() : [];
    echo json_encode([
        'exito' => true,
        'debug' => true,
        'usuarioAutenticado' => usuarioAutenticado(),
        'session' => isset($_SESSION) ? $_SESSION : new stdClass(),
        'cookies' => $_COOKIE,
        'headers' => $headers
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// Agregar a lista de deseos
if ($accion === 'agregar' && $metodo === 'POST') {
    $id_producto = (int)($_POST['id_producto'] ?? 0);
    if ($id_producto <= 0) {
        echo json_encode(['exito' => false, 'error' => 'Producto inválido']);
        exit();
    }

    // Verificar producto existe
    $stmtP = $conexion->prepare("SELECT id_producto, nombre FROM productos WHERE id_producto = ? AND estado = 'disponible'");
    $stmtP->bind_param("i", $id_producto);
    $stmtP->execute();
    $prod = $stmtP->get_result()->fetch_assoc();
    if (!$prod) {
        echo json_encode(['exito' => false, 'error' => 'Producto no disponible']);
        exit();
    }

    // Verificar si ya está en la lista
    $stmtChk = $conexion->prepare("SELECT id_lista FROM lista_deseos WHERE id_cliente = ? AND id_producto = ? LIMIT 1");
    $stmtChk->bind_param("ii", $id_cliente, $id_producto);
    $stmtChk->execute();
    $exists = $stmtChk->get_result()->fetch_assoc();
    if ($exists) {
        echo json_encode(['exito' => true, 'mensaje' => 'Producto ya en la lista']);
        exit();
    }

    // Asegurar que la columna fecha_registro exista (si la tabla fue creada anteriormente sin ella)
    try {
        $colCheck = $conexion->query("SHOW COLUMNS FROM lista_deseos LIKE 'fecha_registro'");
        if ($colCheck && $colCheck->num_rows === 0) {
            $conexion->query("ALTER TABLE lista_deseos ADD COLUMN fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        }
    } catch (Exception $e) {
        // Ignorar y continuar; el INSERT a continuación intentará usar valores por defecto
    }

    // Insertar (no especificamos fecha_registro para compatibilidad)
    try {
        $stmtIns = $conexion->prepare("INSERT INTO lista_deseos (id_cliente, id_producto) VALUES (?, ?)");
        $stmtIns->bind_param("ii", $id_cliente, $id_producto);
        if ($stmtIns->execute()) {
            echo json_encode(['exito' => true, 'mensaje' => 'Producto añadido a la lista de deseos']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Error al guardar en la lista']);
        }
    } catch (Exception $ex) {
        echo json_encode(['exito' => false, 'error' => 'Error al guardar en la lista: ' . $ex->getMessage()]);
    }
    exit();
}

// Eliminar de lista de deseos
if ($accion === 'eliminar' && $metodo === 'POST') {
    $id_producto = (int)($_POST['id_producto'] ?? 0);
    if ($id_producto <= 0) {
        echo json_encode(['exito' => false, 'error' => 'Producto inválido']);
        exit();
    }

    $stmtDel = $conexion->prepare("DELETE FROM lista_deseos WHERE id_cliente = ? AND id_producto = ?");
    $stmtDel->bind_param("ii", $id_cliente, $id_producto);
    $stmtDel->execute();
    if ($stmtDel->affected_rows > 0) {
        echo json_encode(['exito' => true, 'mensaje' => 'Producto eliminado de la lista']);
    } else {
        echo json_encode(['exito' => false, 'error' => 'Producto no encontrado en tu lista']);
    }
    exit();
}

// Listar
if ($accion === 'listar') {
    $sql = "SELECT ld.id_lista, p.id_producto, p.nombre, p.descripcion, p.precio, p.precio_descuento, p.en_oferta,
               (SELECT ruta_imagen FROM producto_imagenes ri WHERE ri.id_producto = p.id_producto ORDER BY ri.orden ASC LIMIT 1) AS imagen
            FROM lista_deseos ld
            INNER JOIN productos p ON ld.id_producto = p.id_producto
            WHERE ld.id_cliente = ?
            ORDER BY ld.fecha_registro DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $res = $stmt->get_result();
    $items = [];
    while ($row = $res->fetch_assoc()) {
        $items[] = $row;
    }
    echo json_encode(['exito' => true, 'items' => $items]);
    exit();
}

echo json_encode(['exito' => false, 'error' => 'Acción no reconocida']);
