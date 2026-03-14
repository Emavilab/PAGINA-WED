<?php
/*
==============================================================
API PARA OBTENER MÉTODOS DE ENVÍO DISPONIBLES
==============================================================

DESCRIPCIÓN:
Este script consulta los métodos de envío activos registrados
en la base de datos y devuelve la información en formato JSON.

FUNCIONALIDAD:
1. Establece el tipo de respuesta como JSON.
2. Conecta con la base de datos.
3. Consulta los métodos de envío activos.
4. Guarda los resultados en un arreglo.
5. Devuelve la lista de métodos de envío al cliente.

TABLA UTILIZADA:
- metodos_envio

CAMPOS CONSULTADOS:
- id_envio
- nombre
- descripcion
- costo
- reduccion_dias

RESPUESTA JSON:
{
  "success": true,
  "metodos": [ ... ]
}

Este endpoint suele utilizarse en el proceso de compra
para mostrar las opciones de envío disponibles al usuario.

==============================================================
*/

header('Content-Type: application/json; charset=utf-8');

// Incluir archivo de conexión a la base de datos
require_once '../core/conexion.php';

/*
--------------------------------------------------------------
CONSULTA SQL
--------------------------------------------------------------
Se seleccionan los métodos de envío que estén activos
para mostrarlos al usuario durante el proceso de compra.
*/
$sql = "SELECT id_envio, nombre, descripcion, costo, reduccion_dias 
        FROM metodos_envio 
        WHERE estado = 'activo'";

// Ejecutar consulta
$result = $conexion->query($sql);

// Arreglo donde se almacenarán los métodos de envío
$metodos = [];

/*
--------------------------------------------------------------
RECORRER RESULTADOS
--------------------------------------------------------------
Se recorren los registros obtenidos de la base de datos
y se almacenan dentro del arreglo $metodos.
*/
while ($row = $result->fetch_assoc()) {
    $metodos[] = $row;
}

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
Se devuelve la lista de métodos de envío disponibles
junto con un indicador de éxito.
*/
echo json_encode([
    "success" => true,
    "metodos" => $metodos
]); 