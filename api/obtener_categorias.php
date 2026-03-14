<?php
/*
====================================================================
API PARA OBTENER LISTA DE CATEGORÍAS
====================================================================

DESCRIPCIÓN:
Este script consulta las categorías activas registradas en la
base de datos y devuelve la información en formato JSON.

Permite dos tipos de consulta:
1. Obtener todas las categorías (principales y subcategorías).
2. Obtener únicamente las categorías principales.

FUNCIONALIDAD:
- Recibe un parámetro opcional por GET llamado "todas".
- Si todas=1 → devuelve todas las categorías activas.
- Si no se envía el parámetro → devuelve solo categorías principales.
- También calcula la cantidad de productos disponibles en cada
  categoría.

TABLAS UTILIZADAS:
- categorias
- productos

CAMPOS CONSULTADOS:
- id_categoria
- nombre
- descripcion
- icono
- total_productos (calculado mediante subconsulta)

RESPUESTA JSON:
[
  {
    "id_categoria": 1,
    "nombre": "Ropa",
    "descripcion": "Categoría de ropa",
    "icono": "fa-shirt",
    "total_productos": 12
  }
]

USO EN EL SISTEMA:
Este endpoint suele utilizarse para:
✔ Mostrar categorías en el menú de la tienda
✔ Mostrar categorías en filtros de productos
✔ Construir menús dinámicos de navegación

====================================================================
*/

// Incluir archivo de conexión a la base de datos
require_once '../core/conexion.php';

// Definir que la respuesta del servidor será en formato JSON
header('Content-Type: application/json; charset=utf-8');

/*
--------------------------------------------------------------
PARÁMETRO DE CONTROL
--------------------------------------------------------------
Se verifica si el parámetro "todas" fue enviado por GET.

Si todas=1 → se obtendrán todas las categorías activas.
Si no → solo se obtendrán las categorías principales.
*/
$todas = isset($_GET['todas']) && $_GET['todas'] == '1' ? true : false;

/*
--------------------------------------------------------------
CONSULTA SQL
--------------------------------------------------------------
Dependiendo del valor de "todas" se ejecuta una consulta distinta.
*/
if ($todas) {

    /*
    ----------------------------------------------------------
    OBTENER TODAS LAS CATEGORÍAS
    ----------------------------------------------------------
    Incluye tanto categorías principales como subcategorías.
    También calcula cuántos productos disponibles tiene cada
    categoría.
    */
    $query = "SELECT c.id_categoria, c.nombre, c.descripcion, c.icono,
              (SELECT COUNT(*) FROM productos p WHERE p.id_categoria = c.id_categoria AND p.estado='disponible') AS total_productos
              FROM categorias c
              WHERE c.estado = 'activo'
              ORDER BY c.nombre ASC";

} else {

    /*
    ----------------------------------------------------------
    OBTENER SOLO CATEGORÍAS PRINCIPALES
    ----------------------------------------------------------
    Se seleccionan únicamente las categorías que no tienen
    categoría padre (id_padre IS NULL).

    También se cuentan los productos disponibles que pertenezcan
    a esa categoría o a cualquiera de sus subcategorías.
    */
    $query = "SELECT c.id_categoria, c.nombre, c.descripcion, c.icono,
              (SELECT COUNT(*) FROM productos p WHERE (p.id_categoria = c.id_categoria OR p.id_categoria IN (SELECT sc.id_categoria FROM categorias sc WHERE sc.id_padre = c.id_categoria)) AND p.estado='disponible') AS total_productos
              FROM categorias c
              WHERE c.estado = 'activo' AND c.id_padre IS NULL
              ORDER BY c.nombre ASC";
}

/*
--------------------------------------------------------------
EJECUTAR CONSULTA
--------------------------------------------------------------
*/
$result = $conexion->query($query);

// Arreglo donde se almacenarán las categorías
$categorias = [];

/*
--------------------------------------------------------------
RECORRER RESULTADOS
--------------------------------------------------------------
Se guardan los registros obtenidos en el arreglo $categorias.
*/
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $categorias[] = $row;
    }
}

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
Se devuelve la lista de categorías en formato JSON.
*/
echo json_encode($categorias, JSON_UNESCAPED_UNICODE);

?>  