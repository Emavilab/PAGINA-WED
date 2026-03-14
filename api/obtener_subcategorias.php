<?php
/*
====================================================================
API PARA OBTENER SUBCATEGORÍAS DE UNA CATEGORÍA
====================================================================

DESCRIPCIÓN:
Este script consulta en la base de datos todas las subcategorías
activas que pertenecen a una categoría padre específica.

Además de los datos de la subcategoría, también devuelve
la cantidad de productos disponibles que tiene cada una.

FUNCIONALIDAD:
1. Incluye la conexión a la base de datos.
2. Define que la respuesta del servidor será en formato JSON.
3. Obtiene el ID de la categoría padre enviado por GET.
4. Verifica que el ID sea válido.
5. Consulta las subcategorías activas que pertenecen a esa categoría.
6. Cuenta cuántos productos disponibles tiene cada subcategoría.
7. Devuelve los resultados en formato JSON.

TABLAS UTILIZADAS:
- categorias
- productos

CAMPOS UTILIZADOS:
- id_categoria
- nombre
- descripcion
- icono
- id_padre
- estado

RESPUESTA JSON:
[
  {
    "id_categoria": 5,
    "nombre": "Camisas",
    "descripcion": "Camisas para hombre",
    "icono": "fa-shirt",
    "total_productos": 10
  }
]

USO EN EL SISTEMA:
Este endpoint suele utilizarse para:
✔ Mostrar subcategorías en el menú
✔ Filtrar productos por categoría
✔ Construir navegación dinámica en la tienda

====================================================================
*/

// Incluir conexión a la base de datos
require_once '../core/conexion.php';

// Definir que la respuesta será en formato JSON
header('Content-Type: application/json; charset=utf-8');

/*
--------------------------------------------------------------
OBTENER ID DE LA CATEGORÍA PADRE
--------------------------------------------------------------
Se obtiene el parámetro "id" enviado por GET y se convierte
a número entero para evitar problemas de seguridad.
*/
$idPadre = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/*
--------------------------------------------------------------
VALIDAR ID
--------------------------------------------------------------
Si el ID no es válido se devuelve un arreglo vacío.
*/
if ($idPadre <= 0) {
    echo json_encode([]);
    exit();
}

/*
--------------------------------------------------------------
CONSULTA A LA BASE DE DATOS
--------------------------------------------------------------
Se obtienen las subcategorías activas que tengan como
padre el ID recibido.
También se cuenta la cantidad de productos disponibles
en cada subcategoría.
*/
$stmt = $conexion->prepare(
    "SELECT c.id_categoria, c.nombre, c.descripcion, c.icono,
    (SELECT COUNT(*) FROM productos p WHERE p.id_categoria = c.id_categoria AND p.estado='disponible') AS total_productos
    FROM categorias c
    WHERE c.id_padre = ? AND c.estado = 'activo'
    ORDER BY c.nombre ASC"
);

/*
--------------------------------------------------------------
ASIGNAR PARÁMETROS Y EJECUTAR CONSULTA
--------------------------------------------------------------
*/
$stmt->bind_param("i", $idPadre);
$stmt->execute();
$result = $stmt->get_result();

/*
--------------------------------------------------------------
ALMACENAR RESULTADOS
--------------------------------------------------------------
Se guardan las subcategorías encontradas en un arreglo.
*/
$subcategorias = [];

while ($row = $result->fetch_assoc()) {
    $subcategorias[] = $row;
}

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
Se devuelve el listado de subcategorías.
*/
echo json_encode($subcategorias, JSON_UNESCAPED_UNICODE);

?> 