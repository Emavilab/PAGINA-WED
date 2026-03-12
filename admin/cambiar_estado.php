<?php

/*
========================================================
MODULO: ACTUALIZAR ESTADO DE PEDIDOS
========================================================

Este archivo permite actualizar el estado de un pedido
dentro del sistema administrativo.

FUNCIONES PRINCIPALES:
✔ Validar que el usuario esté autenticado
✔ Verificar permisos de acceso (rol 1 o 2)
✔ Validar datos recibidos desde el formulario
✔ Verificar que el estado del pedido sea válido
✔ Actualizar el estado del pedido en la base de datos
✔ Devolver respuesta en formato JSON para AJAX

ESTADOS PERMITIDOS DEL PEDIDO:
- pendiente
- confirmado
- enviado
- entregado
- cancelado

RESPUESTAS:
El sistema responde en formato JSON indicando si
la operación fue exitosa o si ocurrió algún error.

AUTOR: Sistema Web
========================================================
*/


/* ====================================================
   CONEXION A LA BASE DE DATOS
==================================================== */

require_once '../core/conexion.php';


/* ====================================================
   SISTEMA DE SESIONES
   Se utiliza para validar que el usuario esté logueado
==================================================== */

require_once '../core/sesiones.php';


/* ====================================================
   CONFIGURACION DE CABECERA
   Se define que la respuesta será en formato JSON
==================================================== */

header('Content-Type: application/json; charset=utf-8');



/* ====================================================
   VALIDACION DE AUTENTICACION Y PERMISOS
   Solo usuarios con rol 1 o 2 pueden cambiar
   el estado de un pedido
==================================================== */

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    echo json_encode(["exito" => false, "error" => "No autorizado"]);
    exit();
}



/* ====================================================
   VALIDACION DE DATOS RECIBIDOS
   Se verifica que el formulario haya enviado
   el ID del pedido y el nuevo estado
==================================================== */

if (!isset($_POST['id'], $_POST['estado'])) {
    echo json_encode(["exito" => false, "error" => "Datos incompletos"]);
    exit();
}



/* ====================================================
   OBTENCION DE DATOS
==================================================== */

$id = intval($_POST['id']);
$estado = $_POST['estado'];



/* ====================================================
   LISTA DE ESTADOS VALIDOS
   Se usa para evitar estados incorrectos
==================================================== */

$estadosValidos = ['pendiente','confirmado','enviado','entregado','cancelado'];



/* ====================================================
   VALIDAR QUE EL ESTADO SEA PERMITIDO
==================================================== */

if (!in_array($estado, $estadosValidos)) {
    echo json_encode(["exito" => false, "error" => "Estado inválido"]);
    exit();
}



/* ====================================================
   CONSULTA SQL PARA ACTUALIZAR EL ESTADO DEL PEDIDO
   Se utiliza una consulta preparada para mayor
   seguridad y evitar inyección SQL
==================================================== */

$sql = "UPDATE pedidos SET estado = ? WHERE id_pedido = ?";
$stmt = $conexion->prepare($sql);



/* ====================================================
   VALIDACION DE PREPARACION DE CONSULTA
==================================================== */

if (!$stmt) {
    echo json_encode(["exito" => false, "error" => $conexion->error]);
    exit();
}



/* ====================================================
   VINCULAR PARAMETROS Y EJECUTAR CONSULTA
   s = string (estado)
   i = integer (id del pedido)
==================================================== */

$stmt->bind_param("si", $estado, $id);
$stmt->execute();



/* ====================================================
   RESPUESTA FINAL
   Indica que la actualización fue exitosa
==================================================== */

echo json_encode(["exito" => true]);

exit();