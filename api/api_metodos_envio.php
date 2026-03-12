<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../core/conexion.php';

$sql = "SELECT id_envio, nombre, descripcion, costo, reduccion_dias 
        FROM metodos_envio 
        WHERE estado = 'activo'";

$result = $conexion->query($sql);

$metodos = [];

while ($row = $result->fetch_assoc()) {
    $metodos[] = $row;
}

echo json_encode([
    "success" => true,
    "metodos" => $metodos
]);