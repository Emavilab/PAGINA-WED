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
          COALESCE(c.tasa_impuesto, cp.tasa_impuesto, 0) AS tasa_impuesto,
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

// Construir URLs absolutas para las imágenes
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
// Obtener el path del directorio sin el nombre del archivo
$script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = rtrim($protocol . $host . $script_path, '/') . '/../';

foreach ($productos as &$prod) {
    if (!empty($prod['imagen_principal'])) {
        $prod['imagen_principal'] = $baseUrl . str_replace(' ', '%20', $prod['imagen_principal']);
    }
    if (!empty($prod['imagenes'])) {
        foreach ($prod['imagenes'] as &$img) {
            $img = $baseUrl . str_replace(' ', '%20', $img);
        }
    }
}

echo json_encode($productos, JSON_UNESCAPED_UNICODE);
?>