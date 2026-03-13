<?php
/*
========================================================
MODULO: BUSCADOR GLOBAL DE PRODUCTOS
========================================================
Este archivo funciona como una API de búsqueda que
permite encontrar información dentro del sistema
de tienda en línea.

FUNCIONALIDADES:
✔ Buscar productos por nombre o descripción
✔ Buscar marcas por nombre
✔ Buscar categorías o departamentos por nombre
✔ Retornar los resultados en formato JSON
✔ Limitar resultados para mejorar rendimiento

RESPUESTA DEL SERVIDOR:
{
  "productos": [],
  "marcas": [],
  "categorias": []
}

En caso de término muy corto:
{
  "productos": [],
  "marcas": [],
  "categorias": [],
  "error": "Término de búsqueda muy corto"
}

TABLAS UTILIZADAS:
- productos
- producto_imagenes
- marcas
- categorias

USO:
Este archivo es consumido mediante solicitudes AJAX
(fetch o XMLHttpRequest) desde el frontend para
mostrar resultados de búsqueda en tiempo real.

AUTOR: Sistema de Tienda Online
========================================================
*/

require_once '../core/conexion.php'; // Conexión a la base de datos

/*
========================================================
CONFIGURAR TIPO DE RESPUESTA
========================================================
Se establece que la respuesta del servidor será
en formato JSON y con codificación UTF-8 para
soportar caracteres especiales.
*/
header('Content-Type: application/json; charset=utf-8');


/*
========================================================
OBTENER TÉRMINO DE BÚSQUEDA
========================================================
Se obtiene el parámetro "q" enviado por GET desde
la barra de búsqueda del frontend.
*/
$termino = isset($_GET['q']) ? trim($_GET['q']) : '';


/*
========================================================
VALIDACIÓN DEL TÉRMINO DE BÚSQUEDA
========================================================
Se valida que el término no esté vacío y tenga
al menos 2 caracteres para evitar consultas
innecesarias a la base de datos.
*/
if (empty($termino) || strlen($termino) < 2) {

    echo json_encode([
        'productos' => [],
        'marcas' => [],
        'categorias' => [],
        'error' => 'Término de búsqueda muy corto'
    ]);

    exit;
}


/*
========================================================
PREPARAR TÉRMINO PARA CONSULTA SQL
========================================================
Se agrega el operador LIKE (%) para permitir
búsquedas parciales dentro de la base de datos.
Además se escapa el valor para mayor seguridad.
*/
$termino_sql = '%' . $conexion->real_escape_string($termino) . '%';


/*
========================================================
ESTRUCTURA DE RESULTADOS
========================================================
Se inicializa un arreglo donde se almacenarán
los resultados encontrados en cada sección.
*/
$resultado = [
    'productos' => [],
    'marcas' => [],
    'categorias' => []
];


/*
========================================================
1. BÚSQUEDA DE PRODUCTOS
========================================================
Se buscan productos disponibles cuyo nombre o
descripción coincida con el término ingresado.

También se obtiene:
- nombre de la marca
- imagen principal del producto
*/
$query_productos = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.stock, p.estado, 
                    m.nombre AS marca_nombre,
                    (SELECT ruta_imagen FROM producto_imagenes 
                     WHERE id_producto = p.id_producto 
                     ORDER BY orden ASC 
                     LIMIT 1) AS imagen_principal
                    FROM productos p
                    LEFT JOIN marcas m ON p.id_marca = m.id_marca
                    WHERE p.estado='disponible' AND (p.nombre LIKE ? OR p.descripcion LIKE ?)
                    LIMIT 20";

$stmt = $conexion->prepare($query_productos);

if ($stmt) {

    // Asignar parámetros a la consulta preparada
    $stmt->bind_param('ss', $termino_sql, $termino_sql);

    // Ejecutar consulta
    $stmt->execute();

    $result = $stmt->get_result();

    // Recorrer resultados y agregarlos al arreglo
    while ($row = $result->fetch_assoc()) {
        $resultado['productos'][] = $row;
    }

    $stmt->close();
}


/*
========================================================
2. BÚSQUEDA DE MARCAS
========================================================
Se buscan marcas activas cuyo nombre coincida
con el término de búsqueda.
*/
$query_marcas = "SELECT id_marca, nombre, logo, estado
                FROM marcas
                WHERE estado='activo' AND nombre LIKE ?
                LIMIT 10";

$stmt = $conexion->prepare($query_marcas);

if ($stmt) {

    // Asignar parámetro a la consulta
    $stmt->bind_param('s', $termino_sql);

    // Ejecutar consulta
    $stmt->execute();

    $result = $stmt->get_result();

    // Guardar resultados
    while ($row = $result->fetch_assoc()) {
        $resultado['marcas'][] = $row;
    }

    $stmt->close();
}


/*
========================================================
3. BÚSQUEDA DE CATEGORÍAS
========================================================
Se buscan categorías o departamentos activos
cuyo nombre coincida con el término de búsqueda.
*/
$query_categorias = "SELECT id_categoria, nombre, descripcion, estado
                    FROM categorias
                    WHERE estado='activo' AND nombre LIKE ?
                    LIMIT 10";

$stmt = $conexion->prepare($query_categorias);

if ($stmt) {

    // Asignar parámetro de búsqueda
    $stmt->bind_param('s', $termino_sql);

    // Ejecutar consulta
    $stmt->execute();

    $result = $stmt->get_result();

    // Guardar resultados
    while ($row = $result->fetch_assoc()) {
        $resultado['categorias'][] = $row;
    }

    $stmt->close();
}


/*
========================================================
RESPUESTA FINAL
========================================================
Se envían los resultados encontrados en formato
JSON para que puedan ser utilizados por el
frontend del sistema.
*/
echo json_encode($resultado, JSON_UNESCAPED_UNICODE);

?> 