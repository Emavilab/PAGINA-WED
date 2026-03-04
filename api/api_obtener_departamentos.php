<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../core/conexion.php';

$query = "SELECT id_departamento, nombre_departamento, costo_envio 
          FROM departamentos_envio 
          ORDER BY nombre_departamento ASC";

$result = $conexion->query($query);

$departamentos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departamentos[] = $row;
    }
}

echo json_encode([
    'success' => true,
    'departamentos' => $departamentos
]);
