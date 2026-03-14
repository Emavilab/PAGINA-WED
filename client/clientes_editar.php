<?php

/*
=========================================================
API: ACTUALIZAR DATOS DE CLIENTE
=========================================================

DESCRIPCIÓN:
Este script permite actualizar la información básica de un
cliente dentro del sistema.

Los datos actualizados son:
✔ Nombre del cliente
✔ Correo electrónico

Las actualizaciones se realizan en dos tablas relacionadas:
1. tabla clientes
2. tabla usuarios

El proceso se ejecuta dentro de una TRANSACCIÓN para asegurar
la integridad de los datos.

FUNCIONAMIENTO GENERAL:
1. Se reciben los datos mediante POST
2. Se validan los datos recibidos
3. Se inicia una transacción en la base de datos
4. Se actualiza la tabla clientes
5. Se actualiza la tabla usuarios
6. Si todo es correcto se confirma la transacción (commit)
7. Si ocurre un error se revierte la operación (rollback)

RESPUESTA:
El script devuelve una respuesta en formato JSON.

Ejemplo de respuesta exitosa:
{
  "success": true
}

Ejemplo de error:
{
  "success": false,
  "message": "Datos inválidos"
}

REQUISITOS:
- Archivo de conexión: ../core/conexion.php
- Método de envío: POST
- Campos requeridos:
  - id
  - nombre
  - correo

AUTOR: Sistema de Gestión
=========================================================
*/

require_once '../core/conexion.php';

/*
---------------------------------------------------------
CONFIGURACIÓN DE RESPUESTA JSON
---------------------------------------------------------
Se establece que la respuesta del servidor será en
formato JSON.
*/
header('Content-Type: application/json');

/*
---------------------------------------------------------
CONFIGURACIÓN DE ERRORES
---------------------------------------------------------
Se desactiva la visualización de errores para evitar
mostrar información sensible al usuario.
*/
ini_set('display_errors', 0);
error_reporting(0);

/*
---------------------------------------------------------
RESPUESTA INICIAL
---------------------------------------------------------
Se define una estructura de respuesta por defecto.
*/
$response = ["success" => false];

try {

    /*
    -----------------------------------------------------
    VALIDACIÓN DE DATOS RECIBIDOS
    -----------------------------------------------------
    Verifica que los campos requeridos existan en la
    petición POST.
    */
    if (!isset($_POST['id'], $_POST['nombre'], $_POST['correo'])) {
        throw new Exception("Datos incompletos");
    }

    /*
    -----------------------------------------------------
    LIMPIEZA Y FORMATO DE DATOS
    -----------------------------------------------------
    Se convierten los valores a los tipos correctos.
    */
    $id     = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);

    /*
    -----------------------------------------------------
    VALIDACIÓN DE CONTENIDO
    -----------------------------------------------------
    Verifica que los datos no estén vacíos o inválidos.
    */
    if ($id <= 0 || empty($nombre) || empty($correo)) {
        throw new Exception("Datos inválidos");
    }

    /*
    -----------------------------------------------------
    ACTIVAR MODO ESTRICTO DE MYSQLI
    -----------------------------------------------------
    Permite que MySQL lance excepciones si ocurre
    algún error en las consultas.
    */
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    /*
    -----------------------------------------------------
    INICIO DE TRANSACCIÓN
    -----------------------------------------------------
    Se inicia una transacción para asegurar que ambas
    tablas se actualicen correctamente.
    */
    $conexion->begin_transaction();

    /*
    =====================================================
    1️⃣ ACTUALIZAR TABLA CLIENTES
    =====================================================
    Se actualiza el nombre del cliente asociado
    al usuario.
    */
    $stmt1 = $conexion->prepare("
        UPDATE clientes 
        SET nombre = ?
        WHERE id_usuario = ?
    ");
    $stmt1->bind_param("si", $nombre, $id);
    $stmt1->execute();

    /*
    =====================================================
    2️⃣ ACTUALIZAR TABLA USUARIOS
    =====================================================
    Se actualiza el nombre y el correo electrónico
    del usuario en la tabla usuarios.
    */
    $stmt2 = $conexion->prepare("
        UPDATE usuarios 
        SET nombre = ?, correo = ?
        WHERE id_usuario = ?
    ");
    $stmt2->bind_param("ssi", $nombre, $correo, $id);
    $stmt2->execute();

    /*
    -----------------------------------------------------
    CONFIRMAR TRANSACCIÓN
    -----------------------------------------------------
    Si ambas consultas se ejecutan correctamente,
    se guardan los cambios en la base de datos.
    */
    $conexion->commit();

    /*
    -----------------------------------------------------
    RESPUESTA EXITOSA
    -----------------------------------------------------
    */
    $response["success"] = true;

} catch (Exception $e) {

    /*
    -----------------------------------------------------
    MANEJO DE ERRORES
    -----------------------------------------------------
    Si ocurre un error durante la transacción se
    revierte la operación para mantener consistencia.
    */
    if ($conexion->errno) {
        $conexion->rollback();
    }

    /*
    -----------------------------------------------------
    RESPUESTA DE ERROR
    -----------------------------------------------------
    */
    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

/*
---------------------------------------------------------
SALIDA FINAL EN FORMATO JSON
---------------------------------------------------------
*/
echo json_encode($response);
exit; 