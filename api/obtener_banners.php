<?php
/*
====================================================================
API PARA OBTENER BANNERS ACTIVOS
====================================================================

DESCRIPCIÓN:
Este script consulta en la base de datos todos los banners que
se encuentran activos y los devuelve en formato JSON.

Los banners generalmente se utilizan en el sitio web para
mostrar promociones, anuncios o imágenes destacadas en el
inicio de la página.

FUNCIONALIDAD:
1. Incluye el archivo de conexión a la base de datos.
2. Define que la respuesta será en formato JSON.
3. Consulta los banners activos en la base de datos.
4. Ordena los banners por el campo "orden" y luego por el
   identificador del banner.
5. Guarda los resultados en un arreglo.
6. Devuelve los datos en formato JSON.

TABLA UTILIZADA:
- banners

CAMPOS IMPORTANTES:
- id_banner
- titulo
- imagen
- enlace
- orden
- estado

RESPUESTA JSON:
{
  "success": true,
  "data": [ ... ]
}

OPCIONES JSON UTILIZADAS:
- JSON_UNESCAPED_UNICODE → evita que caracteres especiales se escapen
- JSON_UNESCAPED_SLASHES → evita escapar las barras en URLs

Este endpoint normalmente es utilizado por el frontend para
mostrar los banners dinámicos en la página principal del sitio.

====================================================================
*/

// Incluir archivo de conexión a la base de datos
require_once __DIR__ . '/../core/conexion.php';

// Definir que la respuesta del servidor será JSON
header('Content-Type: application/json; charset=utf-8');

/*
--------------------------------------------------------------
CONSULTA A LA BASE DE DATOS
--------------------------------------------------------------
Se obtienen todos los banners que tengan estado "activo".
Los resultados se ordenan primero por el campo "orden"
y luego por el ID del banner en orden descendente.
*/
$res = mysqli_query($conexion, "SELECT * FROM banners WHERE estado = 'activo' ORDER BY orden ASC, id_banner DESC");

// Arreglo donde se almacenarán los banners
$banners = [];

/*
--------------------------------------------------------------
RECORRER RESULTADOS
--------------------------------------------------------------
Se recorren los registros obtenidos y se agregan al arreglo
$banners para posteriormente devolverlos en formato JSON.
*/
while($row = mysqli_fetch_assoc($res)) {
    $banners[] = $row;
}

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
Se devuelven los banners activos encontrados en la base
de datos junto con un indicador de éxito.
*/
echo json_encode(
    ['success' => true, 'data' => $banners],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
); 
