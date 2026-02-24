<?php
require_once '../core/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$idPadre = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idPadre <= 0) {
    echo json_encode([]);
    exit();
}

$stmt = $conexion->prepare(
    "SELECT c.id_categoria, c.nombre, c.descripcion, c.icono,
    (SELECT COUNT(*) FROM productos p WHERE p.id_categoria = c.id_categoria AND p.estado='disponible') AS total_productos
    FROM categorias c
    WHERE c.id_padre = ? AND c.estado = 'activo'
    ORDER BY c.nombre ASC"
);
$stmt->bind_param("i", $idPadre);
$stmt->execute();
$result = $stmt->get_result();

$subcategorias = [];
while ($row = $result->fetch_assoc()) {
    $subcategorias[] = $row;
}

echo json_encode($subcategorias, JSON_UNESCAPED_UNICODE);
?>
