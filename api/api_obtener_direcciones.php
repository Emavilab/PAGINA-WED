<?php
header('Content-Type: application/json; charset=utf-8');

require_once '../core/conexion.php';
require_once '../core/sesiones.php';

if (!usuarioAutenticado()) {
    echo json_encode(['success' => false]);
    exit;
}

$usuario = obtenerDatosUsuario();

if (!$usuario || !isset($usuario['id_cliente'])) {
    echo json_encode(['success' => false]);
    exit;
}

$id_cliente = $usuario['id_cliente'];

$query = "SELECT * FROM direcciones_cliente 
          WHERE id_cliente = ? 
          ORDER BY fecha_creacion DESC";

$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();

$result = $stmt->get_result();
$direcciones = [];

while ($row = $result->fetch_assoc()) {
    $direcciones[] = $row;
}

$stmt->close();

echo json_encode([
    'success' => true,
    'direcciones' => $direcciones
]);