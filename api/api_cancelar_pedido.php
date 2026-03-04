<?php
header('Content-Type: application/json; charset=utf-8');

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

// Obtener pedido y validar que sea del cliente, estado pendiente y menos de 3 horas
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

if ($pedido['estado'] !== 'pendiente') {
    echo json_encode(['exito' => false, 'error' => 'Solo se pueden cancelar pedidos en estado pendiente']);
    exit;
}

$segundosDesdePedido = time() - strtotime($pedido['fecha_pedido']);
if ($segundosDesdePedido > 10800) { // 3 horas = 10800 segundos
    echo json_encode(['exito' => false, 'error' => 'Solo puedes cancelar dentro de las primeras 3 horas desde que realizaste el pedido']);
    exit;
}

// Actualizar a cancelado
$stmtUp = $conexion->prepare("UPDATE pedidos SET estado = 'cancelado' WHERE id_pedido = ? AND id_cliente = ?");
$stmtUp->bind_param("ii", $id_pedido, $id_cliente);

if ($stmtUp->execute() && $stmtUp->affected_rows > 0) {
    $stmtUp->close();
    echo json_encode(['exito' => true, 'mensaje' => 'Pedido cancelado correctamente']);
} else {
    $stmtUp->close();
    echo json_encode(['exito' => false, 'error' => 'No se pudo cancelar el pedido']);
}
