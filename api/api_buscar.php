<?php
require_once '../core/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// Obtener término de búsqueda
$termino = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($termino) || strlen($termino) < 2) {
    echo json_encode([
        'productos' => [],
        'marcas' => [],
        'categorias' => [],
        'error' => 'Término de búsqueda muy corto'
    ]);
    exit;
}

// Escapar búsqueda para SQL
$termino_sql = '%' . $conexion->real_escape_string($termino) . '%';

$resultado = [
    'productos' => [],
    'marcas' => [],
    'categorias' => []
];

// 1. Buscar PRODUCTOS por nombre o descripción
$query_productos = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, p.estado, 
                    m.nombre AS marca_nombre,
                    (SELECT ruta_imagen FROM producto_imagenes WHERE id_producto = p.id_producto ORDER BY orden ASC LIMIT 1) AS imagen_principal
                    FROM productos p
                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
                    WHERE p.estado='disponible' AND (p.nombre LIKE ? OR p.descripcion LIKE ?)
                    LIMIT 20";

$stmt = $conexion->prepare($query_productos);
if ($stmt) {
    $stmt->bind_param('ss', $termino_sql, $termino_sql);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $resultado['productos'][] = $row;
    }
    $stmt->close();
}

// 2. Buscar MARCAS por nombre
$query_marcas = "SELECT id_marca, nombre, logo, estado
                FROM marcas
                WHERE estado='activo' AND nombre LIKE ?
                LIMIT 10";

$stmt = $conexion->prepare($query_marcas);
if ($stmt) {
    $stmt->bind_param('s', $termino_sql);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $resultado['marcas'][] = $row;
    }
    $stmt->close();
}

// 3. Buscar CATEGORÍAS/DEPARTAMENTOS por nombre
$query_categorias = "SELECT id_categoria, nombre, descripcion, estado
                    FROM categorias
                    WHERE estado='activo' AND nombre LIKE ?
                    LIMIT 10";

$stmt = $conexion->prepare($query_categorias);
if ($stmt) {
    $stmt->bind_param('s', $termino_sql);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $resultado['categorias'][] = $row;
    }
    $stmt->close();
}

echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
?>
