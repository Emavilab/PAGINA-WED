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

$query = "SELECT d.*, dep.nombre_departamento, dep.costo_envio 
          FROM direcciones_cliente d 
          LEFT JOIN departamentos_envio dep ON d.id_departamento = dep.id_departamento 
          WHERE d.id_cliente = ? 
          ORDER BY d.id_direccion DESC";

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