<?php
/*
====================================================================
API PARA OBTENER LISTA DE PRODUCTOS DISPONIBLES
====================================================================

DESCRIPCIÓN:
Este script consulta todos los productos disponibles en la base
de datos y devuelve la información en formato JSON.

Además de los datos del producto, también incluye:
✔ Categoría
✔ Categoría padre
✔ Marca
✔ Tasa de impuesto
✔ Imagen principal
✔ Todas las imágenes del producto

FUNCIONALIDAD:
1. Conecta con la base de datos.
2. Consulta los productos disponibles.
3. Obtiene la categoría y marca asociadas.
4. Obtiene la imagen principal del producto.
5. Obtiene todas las imágenes del producto.
6. Convierte las rutas de imágenes a URLs absolutas.
7. Devuelve los datos en formato JSON.

TABLAS UTILIZADAS:
- productos
- categorias
- marcas
- producto_imagenes

RESPUESTA JSON:
[
  {
    "id_producto": 1,
    "nombre": "Camisa",
    "precio": 25,
    "categoria_nombre": "Ropa",
    "marca_nombre": "Nike",
    "imagen_principal": "http://sitio.com/img/producto.jpg",
    "imagenes": ["http://sitio.com/img/1.jpg"]
  }
]

USO EN EL SISTEMA:
Este endpoint suele utilizarse para:
✔ Mostrar productos en la tienda
✔ Cargar catálogo de productos
✔ API para frontend o aplicación móvil

====================================================================
*/

// Incluir conexión a la base de datos
require_once '../core/conexion.php';

// Definir que la respuesta será en formato JSON
header('Content-Type: application/json; charset=utf-8');

/*
--------------------------------------------------------------
CONSULTA PRINCIPAL DE PRODUCTOS
--------------------------------------------------------------
Obtiene todos los productos disponibles junto con su categoría,
marca, tasa de impuesto e imagen principal.
*/
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

/*
--------------------------------------------------------------
EJECUTAR CONSULTA
--------------------------------------------------------------
*/
$result = $conexion->query($query);

// Arreglo donde se almacenarán los productos
$productos = [];

/*
--------------------------------------------------------------
RECORRER RESULTADOS
--------------------------------------------------------------
Por cada producto encontrado se obtienen también todas
sus imágenes almacenadas en la tabla producto_imagenes.
*/
if ($result) {
    while ($row = $result->fetch_assoc()) {

        /*
        ----------------------------------------------------------
        CONSULTAR IMÁGENES DEL PRODUCTO
        ----------------------------------------------------------
        */
        $stmtImg = $conexion->prepare("SELECT ruta_imagen FROM producto_imagenes WHERE id_producto = ? ORDER BY orden ASC");
        $stmtImg->bind_param("i", $row['id_producto']);
        $stmtImg->execute();
        $resImg = $stmtImg->get_result();

        // Arreglo para almacenar imágenes del producto
        $imagenes = [];

        while ($img = $resImg->fetch_assoc()) {
            $imagenes[] = $img['ruta_imagen'];
        }

        // Agregar imágenes al arreglo del producto
        $row['imagenes'] = $imagenes;

        // Agregar producto al arreglo principal
        $productos[] = $row;
    }
}

/*
--------------------------------------------------------------
CONSTRUIR URL BASE PARA IMÁGENES
--------------------------------------------------------------
Se generan URLs absolutas para que las imágenes puedan
ser accedidas desde el frontend o aplicaciones externas.
*/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Obtener la ruta del directorio del script
$script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Construir URL base
$baseUrl = rtrim($protocol . $host . $script_path, '/') . '/../';

/*
--------------------------------------------------------------
CONVERTIR RUTAS DE IMÁGENES A URLs COMPLETAS
--------------------------------------------------------------
*/
foreach ($productos as &$prod) {

    // Convertir imagen principal
    if (!empty($prod['imagen_principal'])) {
        $prod['imagen_principal'] = $baseUrl . str_replace(' ', '%20', $prod['imagen_principal']);
    }

    // Convertir todas las imágenes
    if (!empty($prod['imagenes'])) {
        foreach ($prod['imagenes'] as &$img) {
            $img = $baseUrl . str_replace(' ', '%20', $img);
        }
    }
}

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
*/
echo json_encode($productos, JSON_UNESCAPED_UNICODE);

?> 