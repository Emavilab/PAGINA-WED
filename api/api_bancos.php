<?php
/*
========================================================
MODULO: OBTENER LISTA DE BANCOS (API)
========================================================
Este archivo funciona como un servicio API que devuelve
la lista de bancos registrados en el sistema.

FUNCIONALIDAD:
✔ Conectarse a la base de datos
✔ Obtener todos los bancos registrados
✔ Incluir el tipo de cuenta de cada banco
✔ Ordenar los resultados por nombre del banco
✔ Retornar los datos en formato JSON

TABLAS UTILIZADAS:
- bancos
- tipos_cuenta_banco

RESPUESTA DEL SERVIDOR:
{
  "exito": true,
  "bancos": [ ... ]
}

En caso de error:
{
  "exito": false,
  "error": "Mensaje de error"
}

USO:
Este archivo normalmente es llamado mediante
peticiones AJAX o fetch desde JavaScript.

AUTOR: Sistema de Tienda Online
========================================================
*/

require_once '../core/conexion.php'; // Conexión a la base de datos

/*
========================================================
CONFIGURAR TIPO DE RESPUESTA
========================================================
Se establece que la respuesta del servidor será en
formato JSON para que pueda ser interpretada por
JavaScript o cualquier cliente que consuma la API.
*/
header('Content-Type: application/json');

try {

    /*
    ====================================================
    CONSULTA PARA OBTENER BANCOS
    ====================================================
    Se obtienen los siguientes datos:
    - ID del banco
    - Nombre del banco
    - Número de cuenta
    - Logo del banco
    - Tipo de cuenta (ahorro, corriente, etc.)
    
    Se utiliza LEFT JOIN para obtener el tipo de cuenta
    asociado al banco.
    */
    $sql = "
    SELECT 
    b.id_banco,
    b.nombre,
    b.numero_cuenta,
    b.logo,
    t.nombre AS tipo_cuenta
    FROM bancos b
    LEFT JOIN tipos_cuenta_banco t 
    ON t.id_tipo_cuenta = b.id_tipo_cuenta
    ORDER BY b.nombre ASC
    ";

    // Ejecutar consulta en la base de datos
    $res = mysqli_query($conexion, $sql);

    /*
    ====================================================
    ALMACENAR RESULTADOS
    ====================================================
    Se recorren los resultados obtenidos de la consulta
    y se guardan en un arreglo para luego enviarlos
    como respuesta JSON.
    */
    $bancos = [];

    while($row = mysqli_fetch_assoc($res)){
        $bancos[] = $row;
    }

    /*
    ====================================================
    RESPUESTA EXITOSA
    ====================================================
    Se devuelve un JSON indicando que la operación fue
    exitosa junto con la lista de bancos obtenida.
    */
    echo json_encode([
        "exito" => true,
        "bancos" => $bancos
    ]);

} catch (Exception $e) {

    /*
    ====================================================
    MANEJO DE ERRORES
    ====================================================
    Si ocurre algún problema durante la ejecución
    se devuelve una respuesta JSON indicando error.
    */
    echo json_encode([
        "exito" => false,
        "error" => "Error al obtener bancos"
    ]);

}