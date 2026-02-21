<?php
require_once '../core/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// obtener todos los productos activos
$query = "SELECT id_producto, nombre_producto, sku_codigo_referencia, id_categoria, descripcion, precio_venta, precio_costo, stock_inicial, alerta_stock_minimo, imagen_producto, estado, fecha_creacion FROM productos WHERE estado='activo' ORDER BY fecha_creacion DESC";

$result = $conexion->query($query);
$productos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

echo json_encode($productos, JSON_UNESCAPED_UNICODE);
?>