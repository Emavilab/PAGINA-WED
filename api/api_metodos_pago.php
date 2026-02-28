<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../core/conexion.php';

$sql = "SELECT id_metodo_pago, nombre, descripcion 
        FROM metodos_pago 
        WHERE estado = 'activo'
        ORDER BY id_metodo_pago ASC";

$result = $conexion->query($sql);

$metodos = [];

while ($row = $result->fetch_assoc()) {
    $metodos[] = $row;
}

echo json_encode([
    "exito" => true,
    "metodos" => $metodos
]);