<?php
/*
==============================================================
API PARA OBTENER LISTA DE DEPARTAMENTOS DE ENVÍO
==============================================================

DESCRIPCIÓN:
Este script consulta en la base de datos todos los departamentos
disponibles para realizar envíos dentro del sistema y devuelve
la información en formato JSON.

FUNCIONALIDAD:
1. Define que la respuesta del servidor será en formato JSON.
2. Conecta con la base de datos mediante el archivo conexion.php.
3. Realiza una consulta para obtener los departamentos registrados.
4. Ordena los resultados alfabéticamente por nombre del departamento.
5. Guarda los registros en un arreglo.
6. Devuelve los departamentos al cliente en formato JSON.

TABLA UTILIZADA:
- departamentos_envio

CAMPOS CONSULTADOS:
- id_departamento
- nombre_departamento
- costo_envio

RESPUESTA JSON:
{
  "success": true,
  "departamentos": [
      {
        "id_departamento": "...",
        "nombre_departamento": "...",
        "costo_envio": "..."
      }
  ]
}

Este endpoint normalmente se utiliza durante el proceso
de compra para que el usuario seleccione su departamento
y se pueda calcular el costo de envío correspondiente.

==============================================================
*/

header('Content-Type: application/json; charset=utf-8');

// Incluir archivo de conexión a la base de datos
require_once '../core/conexion.php';

/*
--------------------------------------------------------------
CONSULTA SQL
--------------------------------------------------------------
Se obtienen todos los departamentos registrados junto con
su costo de envío. Los resultados se ordenan alfabéticamente.
*/
$query = "SELECT id_departamento, nombre_departamento, costo_envio 
          FROM departamentos_envio 
          ORDER BY nombre_departamento ASC";

// Ejecutar consulta
$result = $conexion->query($query);

// Arreglo donde se almacenarán los departamentos
$departamentos = [];

/*
--------------------------------------------------------------
RECORRER RESULTADOS
--------------------------------------------------------------
Se recorren los registros obtenidos de la base de datos
y se guardan en el arreglo $departamentos.
*/
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $departamentos[] = $row;
    }
}

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
Se devuelve la lista de departamentos disponibles junto
con un indicador de éxito.
*/
echo json_encode([
    'success' => true,
    'departamentos' => $departamentos
]); 