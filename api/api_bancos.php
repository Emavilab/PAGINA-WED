<?php
require_once '../core/conexion.php';

header('Content-Type: application/json');

try {

$sql = "
SELECT 
b.id_banco,
b.nombre,
b.numero_cuenta,
b.logo,
t.nombre AS tipo_cuenta
FROM bancos b
LEFT JOIN tipos_cuenta_banco t 
ON t.id_tipo_cuenta = b.id_tipo_cuenta
ORDER BY b.nombre ASC
";

$res = mysqli_query($conexion, $sql);

$bancos = [];

while($row = mysqli_fetch_assoc($res)){
    $bancos[] = $row;
}

echo json_encode([
    "exito" => true,
    "bancos" => $bancos
]);

} catch (Exception $e) {

echo json_encode([
    "exito" => false,
    "error" => "Error al obtener bancos"
]);

}