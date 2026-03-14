<?php
/*
====================================================================
API PARA OBTENER SUBCATEGORÍAS DE UNA CATEGORÍA
====================================================================

DESCRIPCIÓN:
Este script consulta en la base de datos las subcategorías
que pertenecen a una categoría padre específica.

Se utiliza normalmente cuando el sistema tiene categorías
jerárquicas (categoría principal → subcategorías).

FUNCIONALIDAD:
1. Incluye el archivo de conexión a la base de datos.
2. Define que la respuesta será en formato JSON.
3. Obtiene el ID de la categoría padre desde la URL.
4. Verifica que el ID sea válido.
5. Consulta las categorías hijas activas.
6. Devuelve un arreglo con los IDs de las subcategorías.

TABLA UTILIZADA:
- categorias

CAMPOS UTILIZADOS:
- id_categoria
- id_padre
- estado

EJEMPLO DE USO:
api_categorias_hijas.php?id=5

RESPUESTA JSON:
[3,4,7,9]

Cada número representa el ID de una subcategoría.

Este endpoint suele utilizarse para:
✔ Filtrar productos por subcategoría
✔ Construir árboles de categorías
✔ Aplicar filtros dinámicos en el frontend

====================================================================
*/

// Incluir archivo de conexión a la base de datos
require_once '../core/conexion.php';

// Definir que la respuesta será en formato JSON
header('Content-Type: application/json; charset=utf-8');

/*
--------------------------------------------------------------
OBTENER ID DE LA CATEGORÍA PADRE
--------------------------------------------------------------
Se obtiene el parámetro "id" enviado por GET y se convierte
a número entero para evitar errores o inyección de datos.
*/
$idPadre = isset($_GET['id']) ? (int)$_GET['id'] : 0;

/*
--------------------------------------------------------------
VALIDAR ID
--------------------------------------------------------------
Si el ID no es válido (menor o igual a 0) se devuelve
un arreglo vacío y se detiene la ejecución.
*/
if ($idPadre <= 0) {
    echo json_encode([]);
    exit();
}

/*
--------------------------------------------------------------
CONSULTA A LA BASE DE DATOS
--------------------------------------------------------------
Se buscan todas las categorías activas cuyo id_padre
sea igual al ID recibido.
*/
$stmt = $conexion->prepare("SELECT id_categoria FROM categorias WHERE id_padre = ? AND estado = 'activo'");
$stmt->bind_param("i", $idPadre);
$stmt->execute();
$result = $stmt->get_result();

/*
--------------------------------------------------------------
ALMACENAR RESULTADOS
--------------------------------------------------------------
Se guardan los IDs de las subcategorías en un arreglo.
*/
$ids = [];

while ($row = $result->fetch_assoc()) {
    $ids[] = (int)$row['id_categoria'];
}

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
Se devuelve el arreglo con los IDs de las subcategorías.
*/
echo json_encode($ids);

?> 