<?php
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!usuarioAutenticado()) {
    echo json_encode(["exito" => false, "error" => "No autorizado"]);
    exit;
}

$usuario = obtenerDatosUsuario();
$id_cliente = $usuario['id_cliente'];

/* 🔥 IMPORTANTE
   Ahora usamos $_POST en vez de JSON
*/

if (
    !isset($_POST['id_direccion']) ||
    !isset($_POST['id_metodo_pago'])
) {
    echo json_encode(["exito" => false, "error" => "Datos incompletos"]);
    exit;
}

$id_direccion = intval($_POST['id_direccion']);
$id_envio = !empty($_POST['id_envio']) ? intval($_POST['id_envio']) : null;
$id_metodo_pago = intval($_POST['id_metodo_pago']);

$nombreComprobante = null;

/* ================================
   SUBIR COMPROBANTE
================================ */

if (isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === 0) {

    $archivo = $_FILES['comprobante'];

    if ($archivo['size'] > 3 * 1024 * 1024) {
        echo json_encode(["exito" => false, "error" => "El comprobante supera los 3MB"]);
        exit;
    }

    $tiposPermitidos = ['image/jpeg','image/png','image/webp'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipoReal = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);

    if (!in_array($tipoReal, $tiposPermitidos)) {
        echo json_encode(["exito" => false, "error" => "Formato no permitido"]);
        exit;
    }

    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombreComprobante = uniqid("comp_") . "." . $extension;

    $rutaDestino = "../img/comprobantes/" . $nombreComprobante;

    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        echo json_encode(["exito" => false, "error" => "Error al guardar comprobante"]);
        exit;
    }
}

try {

    $conexion->begin_transaction();

    /* ================================
       OBTENER CARRITO
    ================================ */

    $stmt = $conexion->prepare("
        SELECT id_carrito
        FROM carritos
        WHERE id_cliente = ? AND estado = 'activo'
        LIMIT 1
    ");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $carrito = $resultado->fetch_assoc();

    if (!$carrito) {
        throw new Exception("Carrito vacío");
    }

    $id_carrito = $carrito['id_carrito'];

    /* ================================
       OBTENER DETALLES
    ================================ */

    $stmt = $conexion->prepare("
        SELECT cd.*, p.stock
        FROM carrito_detalle cd
        JOIN productos p ON p.id_producto = cd.id_producto
        WHERE cd.id_carrito = ?
    ");
    $stmt->bind_param("i", $id_carrito);
    $stmt->execute();
    $detalles = $stmt->get_result();

    if ($detalles->num_rows == 0) {
        throw new Exception("Carrito sin productos");
    }

    $subtotal = 0;
    $impuesto_total = 0;
    $items = [];

    while ($item = $detalles->fetch_assoc()) {

        if ($item['stock'] < $item['cantidad']) {
            throw new Exception("Stock insuficiente");
        }

        $subtotal += $item['subtotal'];
        $impuesto_total += ($item['subtotal'] * 0.15);

        $items[] = $item;
    }

    /* ================================
   OBTENER ENVIO DEPARTAMENTO
================================ */

$stmtEnvio = $conexion->prepare("
    SELECT de.costo_envio
    FROM direcciones_cliente dc
    JOIN departamentos_envio de 
    ON dc.id_departamento = de.id_departamento
    WHERE dc.id_direccion = ?
");

$stmtEnvio->bind_param("i", $id_direccion);
$stmtEnvio->execute();
$resEnvio = $stmtEnvio->get_result();
$rowEnvio = $resEnvio->fetch_assoc();

$envio_departamento = $rowEnvio ? $rowEnvio['costo_envio'] : 0;
/* ================================
   OBTENER ENVIO METODO
================================ */

$envio_metodo = 0;
if ($id_envio) {
    $stmtMetodo = $conexion->prepare("
        SELECT costo
        FROM metodos_envio
        WHERE id_envio = ?
    ");

    $stmtMetodo->bind_param("i", $id_envio);
    $stmtMetodo->execute();
    $resMetodo = $stmtMetodo->get_result();
    $rowMetodo = $resMetodo->fetch_assoc();
    $envio_metodo = $rowMetodo ? $rowMetodo['costo'] : 0;
}

/* ================================
   TOTAL
================================ */

$total = $subtotal + $impuesto_total + $envio_departamento + $envio_metodo;

    /* ================================
       INSERTAR PEDIDO
    ================================ */

    $stmt = $conexion->prepare("
        INSERT INTO pedidos 
        (subtotal, envio_departamento, impuesto_total, total, id_cliente, id_direccion, id_envio, id_metodo_pago, comprobante_pago, fecha_pedido)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?,?, NOW())
    ");

    $stmt->bind_param(
        "ddddiiiis",
        $subtotal,
        $envio_departamento,
        $impuesto_total,
        $total,
        $id_cliente,
        $id_direccion,
        $id_envio,
        $id_metodo_pago,
        $nombreComprobante
    );

    $stmt->execute();
    $id_pedido = $conexion->insert_id;

    /* ================================
       INSERTAR DETALLE
    ================================ */

    foreach ($items as $item) {

        $monto_impuesto = $item['subtotal'] * 0.15;

        $stmt = $conexion->prepare("
            INSERT INTO detalle_pedido
            (cantidad, precio_unitario, subtotal, tasa_impuesto, monto_impuesto, id_pedido, id_producto)
            VALUES (?, ?, ?, 15, ?, ?, ?)
        ");

        $stmt->bind_param(
            "idddii",
            $item['cantidad'],
            $item['precio_unitario'],
            $item['subtotal'],
            $monto_impuesto,
            $id_pedido,
            $item['id_producto']
        );

        $stmt->execute();

        $stmtStock = $conexion->prepare("
            UPDATE productos
            SET stock = stock - ?
            WHERE id_producto = ?
        ");

        $stmtStock->bind_param("ii", $item['cantidad'], $item['id_producto']);
        $stmtStock->execute();
    }

    $conexion->query("DELETE FROM carrito_detalle WHERE id_carrito = $id_carrito");
    $conexion->query("UPDATE carritos SET estado = 'comprado' WHERE id_carrito = $id_carrito");

    unset($_SESSION['carrito']);

    $conexion->commit();

    echo json_encode(["exito" => true, "id_pedido" => $id_pedido]);
    exit;

} catch (Exception $e) {

    $conexion->rollback();

    echo json_encode([
        "exito" => false,
        "error" => $e->getMessage()
    ]);
    exit;
}