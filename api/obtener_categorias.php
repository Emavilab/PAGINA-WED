<?php
require_once '../core/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Obtener categorías principales activas (sin padre)
$query = "SELECT c.id_categoria, c.nombre, c.descripcion, c.icono,
          (SELECT COUNT(*) FROM productos p WHERE (p.id_categoria = c.id_categoria OR p.id_categoria IN (SELECT sc.id_categoria FROM categorias sc WHERE sc.id_padre = c.id_categoria)) AND p.estado='disponible') AS total_productos
          FROM categorias c
          WHERE c.estado = 'activo' AND c.id_padre IS NULL
          ORDER BY c.nombre ASC";

$result = $conexion->query($query);
$categorias = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}

echo json_encode($categorias, JSON_UNESCAPED_UNICODE);
?>
