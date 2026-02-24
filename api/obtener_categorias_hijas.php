<?php
require_once '../core/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$idPadre = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idPadre <= 0) {
    echo json_encode([]);
    exit();
}

$stmt = $conexion->prepare("SELECT id_categoria FROM categorias WHERE id_padre = ? AND estado = 'activo'");
$stmt->bind_param("i", $idPadre);
$stmt->execute();
$result = $stmt->get_result();

$ids = [];
while ($row = $result->fetch_assoc()) {
    $ids[] = (int)$row['id_categoria'];
}

echo json_encode($ids);
?>
