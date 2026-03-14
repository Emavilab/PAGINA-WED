<?php

/*
============================================================
API PARA OBTENER INFORMACIÓN DE ENVÍO POR DEPARTAMENTO
============================================================

DESCRIPCIÓN:
Este script permite consultar la información de envío
de un departamento específico dentro del sistema.

FUNCIONAMIENTO:
1. Recibe el nombre del departamento mediante GET.
2. Consulta la tabla departamentos_envio.
3. Obtiene el costo de envío y los días estimados de entrega.
4. Devuelve la información en formato JSON.

RESPUESTA DEL SISTEMA:

Si la consulta es exitosa:
{
 "success": true,
 "envio": {
    "nombre_departamento": "...",
    "costo_envio": "...",
    "dias_entrega": "..."
 }
}

Si ocurre un error o no existe el departamento:
{
 "success": false
}

TABLA UTILIZADA:
- departamentos_envio

SEGURIDAD:
Se utiliza consulta preparada para evitar SQL Injection.
============================================================
*/

require_once '../core/conexion.php'; // Archivo que establece la conexión con la base de datos

// Definir que la respuesta será en formato JSON
header('Content-Type: application/json');

// Obtener el nombre del departamento enviado por GET
$departamento = $_GET['departamento'] ?? '';

/*
------------------------------------------------------------
VALIDACIÓN DEL PARÁMETRO
------------------------------------------------------------
Si no se recibe el nombre del departamento,
se devuelve una respuesta indicando fallo.
*/
if(!$departamento){
    echo json_encode([
        "success"=>false
    ]);
    exit;
}

/*
------------------------------------------------------------
CONSULTA A LA BASE DE DATOS
------------------------------------------------------------
Se busca el departamento en la tabla departamentos_envio
para obtener su costo de envío y días estimados de entrega.
*/
$stmt = $conexion->prepare("
SELECT nombre_departamento,costo_envio,dias_entrega
FROM departamentos_envio
WHERE nombre_departamento = ?
LIMIT 1
");

// Vincular el parámetro recibido a la consulta preparada
$stmt->bind_param("s",$departamento);

// Ejecutar la consulta
$stmt->execute();

// Obtener el resultado
$res = $stmt->get_result();

/*
------------------------------------------------------------
VERIFICAR SI EL DEPARTAMENTO EXISTE
------------------------------------------------------------
Si se encuentra un registro, se devuelve la información
de envío correspondiente.
*/
if($row = $res->fetch_assoc()){

echo json_encode([
"success"=>true,
"envio"=>$row
]);

}else{

/*
Si no se encuentra el departamento,
se devuelve una respuesta negativa
*/
echo json_encode([
"success"=>false
]);

} 