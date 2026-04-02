<?php
/*
=====================================================================
API PARA GESTIONAR LA LISTA DE DESEOS
=====================================================================

DESCRIPCIÓN:
Este script permite gestionar la lista de deseos de los clientes
dentro del sistema de tienda en línea.

FUNCIONALIDADES PRINCIPALES:
1. Verificar autenticación del usuario.
2. Crear la tabla lista_deseos si no existe.
3. Obtener el cliente asociado al usuario.
4. Permitir las siguientes acciones:
   - Agregar productos a la lista de deseos.
   - Eliminar productos de la lista de deseos.
   - Listar productos guardados en la lista de deseos.
   - Endpoint de depuración para verificar sesión y datos.

SEGURIDAD:
- Solo usuarios autenticados pueden utilizar la lista de deseos.
- Se utilizan consultas preparadas para evitar SQL Injection.
- Se valida que los productos existan antes de agregarlos.

RESPUESTAS:
Las respuestas se devuelven en formato JSON.

ACCIONES DISPONIBLES:
- accion=agregar  (POST)
- accion=eliminar (POST)
- accion=listar   (GET)
- accion=debug    (GET)

TABLAS UTILIZADAS:
- clientes
- productos
- producto_imagenes
- lista_deseos

=====================================================================
*/


// Asegurar que la API devuelva únicamente JSON (evitar warnings/HTML en la salida)
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Definir que la respuesta será en formato JSON
header('Content-Type: application/json; charset=utf-8');

// Iniciar buffer de salida para evitar contenido extra
ob_start();

// Cargar archivos necesarios
require_once '../core/sesiones.php';
require_once '../core/conexion.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();

// Si hay alguna salida previa limpiarla
if (ob_get_length() > 0) ob_clean();

/*
---------------------------------------------------------------------
VERIFICAR AUTENTICACIÓN DEL USUARIO
---------------------------------------------------------------------
Si el usuario no ha iniciado sesión se devuelve un error.
*/
if (!usuarioAutenticado()) {
    echo json_encode(['exito' => false, 'error' => 'Debes iniciar sesión para usar la lista de deseos']);
    exit();
}

/*
---------------------------------------------------------------------
CREAR TABLA LISTA_DESEOS SI NO EXISTE
---------------------------------------------------------------------
Esto evita errores del sistema si la tabla aún no ha sido creada
en la base de datos.
*/
$createTbl = "CREATE TABLE IF NOT EXISTS lista_deseos (
    id_lista INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT NOT NULL,
    id_producto INT NOT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_cliente_prod (id_cliente, id_producto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conexion->query($createTbl);


/*
---------------------------------------------------------------------
OBTENER ID_CLIENTE ASOCIADO AL USUARIO
---------------------------------------------------------------------
Buscar el id_cliente desde la tabla clientes.
*/
$id_cliente = null;
$stmt_cliente = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
$stmt_cliente->bind_param("i", $_SESSION['id_usuario']);
$stmt_cliente->execute();
$resultado = $stmt_cliente->get_result();

if ($resultado->num_rows > 0) {
    $row = $resultado->fetch_assoc();
    $id_cliente = $row['id_cliente'];
} else {
    echo json_encode(['exito' => false, 'error' => 'Cliente no encontrado']);
    exit();
}
$stmt_cliente->close();

/*
---------------------------------------------------------------------
OBTENER ACCIÓN Y MÉTODO DE LA PETICIÓN
---------------------------------------------------------------------
accion: determina qué operación se realizará
metodo: POST o GET
*/
$accion = $_REQUEST['accion'] ?? '';
$metodo = $_SERVER['REQUEST_METHOD'];


/*
---------------------------------------------------------------------
ENDPOINT DE DEPURACIÓN (DEBUG)
---------------------------------------------------------------------
Permite verificar datos de sesión, cookies y headers para pruebas.
*/
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


/*
=====================================================================
AGREGAR PRODUCTO A LA LISTA DE DESEOS
=====================================================================
*/
if ($accion === 'agregar' && $metodo === 'POST') {

    // Obtener ID del producto
    $id_producto = (int)($_POST['id_producto'] ?? 0);

    if ($id_producto <= 0) {
        echo json_encode(['exito' => false, 'error' => 'Producto inválido']);
        exit();
    }

    /*
    -------------------------------------------------------------
    VERIFICAR QUE EL PRODUCTO EXISTA Y ESTÉ DISPONIBLE
    -------------------------------------------------------------
    */
    $stmtP = $conexion->prepare("SELECT id_producto, nombre FROM productos WHERE id_producto = ? AND estado = 'disponible'");
    $stmtP->bind_param("i", $id_producto);
    $stmtP->execute();
    $prod = $stmtP->get_result()->fetch_assoc();

    if (!$prod) {
        echo json_encode(['exito' => false, 'error' => 'Producto no disponible']);
        exit();
    }

    /*
    -------------------------------------------------------------
    VERIFICAR SI EL PRODUCTO YA ESTÁ EN LA LISTA
    -------------------------------------------------------------
    */
    $stmtChk = $conexion->prepare("SELECT id_lista FROM lista_deseos WHERE id_cliente = ? AND id_producto = ? LIMIT 1");
    $stmtChk->bind_param("ii", $id_cliente, $id_producto);
    $stmtChk->execute();
    $exists = $stmtChk->get_result()->fetch_assoc();

    if ($exists) {
        echo json_encode(['exito' => true, 'mensaje' => 'Producto ya en la lista']);
        exit();
    }

    /*
    -------------------------------------------------------------
    VERIFICAR QUE LA COLUMNA fecha_registro EXISTA
    -------------------------------------------------------------
    */
    try {
        $colCheck = $conexion->query("SHOW COLUMNS FROM lista_deseos LIKE 'fecha_registro'");
        if ($colCheck && $colCheck->num_rows === 0) {
            $conexion->query("ALTER TABLE lista_deseos ADD COLUMN fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        }
    } catch (Exception $e) {
        // Ignorar error y continuar
    }

    /*
    -------------------------------------------------------------
    INSERTAR PRODUCTO EN LA LISTA DE DESEOS
    -------------------------------------------------------------
    */
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


/*
=====================================================================
ELIMINAR PRODUCTO DE LA LISTA DE DESEOS
=====================================================================
*/
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


/*
=====================================================================
LISTAR PRODUCTOS DE LA LISTA DE DESEOS
=====================================================================
*/
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


/*
---------------------------------------------------------------------
ACCIÓN NO RECONOCIDA
---------------------------------------------------------------------
Si no se envía una acción válida se devuelve un error.
*/
echo json_encode(['exito' => false, 'error' => 'Acción no reconocida']);
