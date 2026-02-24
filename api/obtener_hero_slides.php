<?php
require_once __DIR__ . '/../core/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$res = mysqli_query($conexion, "SELECT * FROM hero_slides WHERE estado = 'activo' ORDER BY orden ASC, id_slide DESC");
$slides = [];
while($row = mysqli_fetch_assoc($res)) {
    $slides[] = $row;
}

echo json_encode(['success' => true, 'data' => $slides], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
