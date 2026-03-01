<?php

require_once '../core/conexion.php';
require_once '../core/sesiones.php';

header('Content-Type: application/json; charset=utf-8');

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    echo json_encode(["exito" => false, "error" => "No autorizado"]);
    exit();
}

if (!isset($_POST['id'], $_POST['estado'])) {
    echo json_encode(["exito" => false, "error" => "Datos incompletos"]);
    exit();
}

$id = intval($_POST['id']);
$estado = $_POST['estado'];

$estadosValidos = ['pendiente','confirmado','enviado','entregado'];

if (!in_array($estado, $estadosValidos)) {
    echo json_encode(["exito" => false, "error" => "Estado inválido"]);
    exit();
}

$sql = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    echo json_encode(["exito" => false, "error" => $conexion->error]);
    exit();
}

$stmt->bind_param("si", $estado, $id);
$stmt->execute();

echo json_encode(["exito" => true]);
exit();