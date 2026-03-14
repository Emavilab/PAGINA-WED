<?php
/*
====================================================================
API PARA OBTENER LOS HERO SLIDES (SLIDER PRINCIPAL DEL SITIO)
====================================================================

DESCRIPCIÓN:
Este script consulta en la base de datos los slides activos
del banner principal (Hero Slider) del sitio web y devuelve
los datos en formato JSON.

Los hero slides suelen utilizarse en la página principal
para mostrar promociones, imágenes destacadas o campañas.

FUNCIONALIDAD:
1. Incluye el archivo de conexión a la base de datos.
2. Define que la respuesta será en formato JSON.
3. Consulta los slides activos en la base de datos.
4. Ordena los slides por prioridad (orden) y luego por ID.
5. Almacena los resultados en un arreglo.
6. Devuelve los datos al cliente en formato JSON.

TABLA UTILIZADA:
- hero_slides

CAMPOS IMPORTANTES:
- id_slide
- estado
- orden

RESPUESTA JSON:
{
  "success": true,
  "data": [
    {
      "id_slide": 1,
      "titulo": "Promoción verano",
      "imagen": "slide1.jpg"
    }
  ]
}

USO EN EL SISTEMA:
Este endpoint suele utilizarse para:
✔ Mostrar el slider principal en la página de inicio
✔ Cargar banners promocionales dinámicos
✔ Administrar campañas visuales desde el panel de administración

====================================================================
*/

// Incluir archivo de conexión a la base de datos
require_once __DIR__ . '/../core/conexion.php';

// Definir que la respuesta será en formato JSON
header('Content-Type: application/json; charset=utf-8');

/*
--------------------------------------------------------------
CONSULTA A LA BASE DE DATOS
--------------------------------------------------------------
Se obtienen todos los slides que estén activos.
Los resultados se ordenan primero por el campo "orden"
y luego por el ID del slide en orden descendente.
*/
$res = mysqli_query($conexion, "SELECT * FROM hero_slides WHERE estado = 'activo' ORDER BY orden ASC, id_slide DESC");

/*
--------------------------------------------------------------
ALMACENAR RESULTADOS
--------------------------------------------------------------
Se recorren los resultados de la consulta y se guardan
en un arreglo llamado $slides.
*/
$slides = [];

while($row = mysqli_fetch_assoc($res)) {
    $slides[] = $row;
}

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
Se devuelve el arreglo de slides en formato JSON.
*/
echo json_encode(['success' => true, 'data' => $slides], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); 