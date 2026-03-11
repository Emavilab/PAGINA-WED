<?php

require_once '../core/conexion.php';
header('Content-Type: application/json');

$departamento = $_GET['departamento'] ?? '';

if(!$departamento){
    echo json_encode([
        "success"=>false
    ]);
    exit;
}

$stmt = $conexion->prepare("
SELECT nombre_departamento,costo_envio,dias_entrega
FROM departamentos_envio
WHERE nombre_departamento = ?
LIMIT 1
");

$stmt->bind_param("s",$departamento);
$stmt->execute();
$res = $stmt->get_result();

if($row = $res->fetch_assoc()){

echo json_encode([
"success"=>true,
"envio"=>$row
]);

}else{

echo json_encode([
"success"=>false
]);

}