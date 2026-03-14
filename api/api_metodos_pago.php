<?php
/*
==============================================================
API PARA OBTENER MÉTODOS DE PAGO DISPONIBLES
==============================================================

DESCRIPCIÓN:
Este script consulta en la base de datos los métodos de pago
que se encuentran activos dentro del sistema y devuelve la
información en formato JSON.

FUNCIONALIDAD:
1. Define el tipo de respuesta como JSON.
2. Conecta con la base de datos.
3. Consulta los métodos de pago activos.
4. Ordena los resultados por el identificador del método de pago.
5. Almacena los resultados en un arreglo.
6. Devuelve la lista de métodos de pago disponibles.

TABLA UTILIZADA:
- metodos_pago

CAMPOS CONSULTADOS:
- id_metodo_pago
- nombre
- descripcion

RESPUESTA JSON:
{
  "exito": true,
  "metodos": [
      {
        "id_metodo_pago": "...",
        "nombre": "...",
        "descripcion": "..."
      }
  ]
}

Este endpoint se utiliza generalmente durante el proceso
de compra para mostrar al usuario las opciones de pago
disponibles en el sistema.

==============================================================
*/

header('Content-Type: application/json; charset=utf-8');

// Incluir archivo de conexión a la base de datos
require_once '../core/conexion.php';

/*
--------------------------------------------------------------
CONSULTA SQL
--------------------------------------------------------------
Se seleccionan los métodos de pago que estén activos en la
base de datos y se ordenan por su identificador.
*/
$sql = "SELECT id_metodo_pago, nombre, descripcion 
        FROM metodos_pago 
        WHERE estado = 'activo'
        ORDER BY id_metodo_pago ASC";

// Ejecutar la consulta
$result = $conexion->query($sql);

// Arreglo donde se almacenarán los métodos de pago
$metodos = [];

/*
--------------------------------------------------------------
RECORRER RESULTADOS
--------------------------------------------------------------
Se recorren los registros obtenidos y se guardan en el
arreglo $metodos.
*/
while ($row = $result->fetch_assoc()) {
    $metodos[] = $row;
}

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
Se devuelve la lista de métodos de pago disponibles junto
con un indicador de éxito.
*/
echo json_encode([
    "exito" => true,
    "metodos" => $metodos
]); 