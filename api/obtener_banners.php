<?php
require_once __DIR__ . '/../core/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$res = mysqli_query($conexion, "SELECT * FROM banners WHERE estado = 'activo' ORDER BY orden ASC, id_banner DESC");
$banners = [];
while($row = mysqli_fetch_assoc($res)) {
    $banners[] = $row;
}

echo json_encode(['success' => true, 'data' => $banners], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
