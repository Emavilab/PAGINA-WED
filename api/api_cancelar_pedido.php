<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('America/Tegucigalpa');

require_once '../core/conexion.php';
require_once '../core/sesiones.php';

if (!usuarioAutenticado()) {
    echo json_encode(['exito' => false, 'error' => 'No autorizado']);
    exit;
}

$usuario = obtenerDatosUsuario();
$id_cliente = $usuario['id_cliente'] ?? null;

if (!$id_cliente) {
    echo json_encode(['exito' => false, 'error' => 'Debes iniciar sesión']);
    exit;
}

$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

if ($id_pedido <= 0) {
    echo json_encode(['exito' => false, 'error' => 'Pedido inválido']);
    exit;
}

// Obtener pedido
$stmt = $conexion->prepare("SELECT id_pedido, estado, fecha_pedido FROM pedidos WHERE id_pedido = ? AND id_cliente = ? LIMIT 1");
$stmt->bind_param("ii", $id_pedido, $id_cliente);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    $stmt->close();
    echo json_encode(['exito' => false, 'error' => 'Pedido no encontrado']);
    exit;
}

$pedido = $res->fetch_assoc();
$stmt->close();

// Validar estado
if ($pedido['estado'] !== 'pendiente') {
    echo json_encode(['exito' => false, 'error' => 'Solo se pueden cancelar pedidos en estado pendiente']);
    exit;
}

/*
================================
CALCULO DE LAS 3 HORAS
================================
*/

$fechaPedido = new DateTime($pedido['fecha_pedido']);

$fechaLimite = clone $fechaPedido;
$fechaLimite->modify('+3 hours');

$ahora = new DateTime();

if ($ahora > $fechaLimite) {
    echo json_encode([
        'exito' => false,
        'error' => 'Solo puedes cancelar dentro de las primeras 3 horas desde que realizaste el pedido'
    ]);
    exit;
}

// Cancelar pedido
$stmtUp = $conexion->prepare("UPDATE pedidos SET estado = 'cancelado' WHERE id_pedido = ? AND id_cliente = ?");
$stmtUp->bind_param("ii", $id_pedido, $id_cliente);

if ($stmtUp->execute() && $stmtUp->affected_rows > 0) {
    $stmtUp->close();

    // Devolver productos al stock solo si el estado anterior NO era "cancelado" (evitar doble devolución)
    if ($pedido['estado'] !== 'cancelado') {
        $stmtDetalle = $conexion->prepare("SELECT id_producto, cantidad FROM detalle_pedido WHERE id_pedido = ?");
        $stmtDetalle->bind_param("i", $id_pedido);
        $stmtDetalle->execute();
        $resDetalle = $stmtDetalle->get_result();
        while ($fila = $resDetalle->fetch_assoc()) {
            $stmtStock = $conexion->prepare("UPDATE productos SET stock = stock + ? WHERE id_producto = ?");
            $stmtStock->bind_param("ii", $fila['cantidad'], $fila['id_producto']);
            $stmtStock->execute();
            $stmtStock->close();
        }
        $stmtDetalle->close();
    }

    echo json_encode([
        'exito' => true,
        'mensaje' => 'Pedido cancelado correctamente'
    ]);
} else {
    $stmtUp->close();
    echo json_encode([
        'exito' => false,
        'error' => 'No se pudo cancelar el pedido'
    ]);
}