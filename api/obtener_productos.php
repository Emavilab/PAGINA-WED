<?php
require_once '../core/conexion.php';

header('Content-Type: application/json; charset=utf-8');

// obtener todos los productos disponibles
$query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, p.estado, p.fecha_creacion,
          p.id_categoria, p.id_marca, p.precio_descuento, p.en_oferta,
          c.nombre AS categoria_nombre, c.id_padre AS categoria_id_padre,
          COALESCE(cp.nombre, c.nombre) AS categoria_padre_nombre,
          COALESCE(cp.id_categoria, c.id_categoria) AS categoria_padre_id,
          m.nombre AS marca_nombre,
          (SELECT ri.ruta_imagen FROM producto_imagenes ri WHERE ri.id_producto = p.id_producto ORDER BY ri.orden ASC LIMIT 1) AS imagen_principal
          FROM productos p
          LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
          LEFT JOIN categorias cp ON c.id_padre = cp.id_categoria
          LEFT JOIN marcas m ON p.id_marca = m.id_marca
          WHERE p.estado='disponible'
          ORDER BY p.fecha_creacion DESC";

$result = $conexion->query($query);
$productos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Obtener todas las imágenes del producto
        $stmtImg = $conexion->prepare("SELECT ruta_imagen FROM producto_imagenes WHERE id_producto = ? ORDER BY orden ASC");
        $stmtImg->bind_param("i", $row['id_producto']);
        $stmtImg->execute();
        $resImg = $stmtImg->get_result();
        $imagenes = [];
        while ($img = $resImg->fetch_assoc()) {
            $imagenes[] = $img['ruta_imagen'];
        }
        $row['imagenes'] = $imagenes;
        $productos[] = $row;
    }
}

echo json_encode($productos, JSON_UNESCAPED_UNICODE);
?>