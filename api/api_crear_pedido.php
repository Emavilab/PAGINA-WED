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

$input = json_decode(file_get_contents("php://input"), true);

if (
    !isset($input['id_direccion']) ||
    !isset($input['id_envio']) ||
    !isset($input['id_metodo_pago'])
) {
    echo json_encode(["exito" => false, "error" => "Datos incompletos"]);
    exit;
}

$id_direccion = $input['id_direccion'];
$id_envio = $input['id_envio'];
$id_metodo_pago = $input['id_metodo_pago'];

try {

    $conexion->begin_transaction();

    // 1️⃣ Obtener carrito activo
    $stmt = $conexion->prepare("
        SELECT c.id_carrito
        FROM carritos c
        WHERE c.id_cliente = ? AND c.estado = 'activo'
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

    // 2️⃣ Obtener detalles del carrito
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
            throw new Exception("Stock insuficiente para un producto");
        }

        $subtotal += $item['subtotal'];
        $impuesto_total += ($item['subtotal'] * 0.15); // 15% ejemplo

        $items[] = $item;
    }

    $total = $subtotal + $impuesto_total;

    // 3️⃣ Insertar pedido
    $stmt = $conexion->prepare("
        INSERT INTO pedidos 
        (subtotal, impuesto_total, total, id_cliente, id_direccion, id_envio, id_metodo_pago)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "dddiiii",
        $subtotal,
        $impuesto_total,
        $total,
        $id_cliente,
        $id_direccion,
        $id_envio,
        $id_metodo_pago
    );

    $stmt->execute();
    $id_pedido = $conexion->insert_id;

    // 4️⃣ Insertar detalle pedido + descontar stock
    foreach ($items as $item) {

        $stmt = $conexion->prepare("
            INSERT INTO detalle_pedido
            (cantidad, precio_unitario, subtotal, tasa_impuesto, monto_impuesto, id_pedido, id_producto)
            VALUES (?, ?, ?, 15, ?, ?, ?)
        ");

        $monto_impuesto = $item['subtotal'] * 0.15;

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

        // Descontar stock
        $stmtStock = $conexion->prepare("
            UPDATE productos
            SET stock = stock - ?
            WHERE id_producto = ?
        ");

        $stmtStock->bind_param("ii", $item['cantidad'], $item['id_producto']);
        $stmtStock->execute();
    }

    // 5️⃣ Insertar historial
    $stmt = $conexion->prepare("
        INSERT INTO historial_pedido
        (id_pedido, estado, comentario, id_usuario)
        VALUES (?, 'pendiente', 'Pedido creado', NULL)
    ");

    $stmt->bind_param("i", $id_pedido);
    $stmt->execute();

    // 6️⃣ Vaciar carrito
    $conexion->query("DELETE FROM carrito_detalle WHERE id_carrito = $id_carrito");
$conexion->query("UPDATE carritos SET estado = 'comprado' WHERE id_carrito = $id_carrito");

    // Limpiar sesión carrito si existe
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