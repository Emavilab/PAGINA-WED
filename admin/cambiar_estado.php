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

// Evitar que warnings/notices rompan la respuesta JSON
ini_set('display_errors', 0);
ini_set('log_errors', 1);


/* ====================================================
   CONEXION A LA BASE DE DATOS
==================================================== */

require_once '../core/conexion.php';
require_once '../core/smtp_config.php';


/* ====================================================
   SISTEMA DE SESIONES
   Se utiliza para validar que el usuario esté logueado
==================================================== */

require_once '../core/sesiones.php';
require_once '../core/csrf.php';


/* ====================================================
   CONFIGURACION DE CABECERA
   Se define que la respuesta será en formato JSON
==================================================== */

header('Content-Type: application/json; charset=utf-8');
ob_start();


/* ====================================================
   VALIDACION DE AUTENTICACION Y PERMISOS
   Solo usuarios con rol 1 o 2 pueden cambiar
   el estado de un pedido
==================================================== */

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    ob_clean();
    echo json_encode(["exito" => false, "error" => "No autorizado"]);
    ob_end_flush();
    exit();
}



/* ====================================================
   VALIDACION DE DATOS RECIBIDOS
   Se verifica que el formulario haya enviado
   el ID del pedido y el nuevo estado
==================================================== */

if (!isset($_POST['id'], $_POST['estado'])) {
    ob_clean();
    echo json_encode(["exito" => false, "error" => "Datos incompletos"]);
    ob_end_flush();
    exit();
}



/* ====================================================
   OBTENCION DE DATOS
==================================================== */

$id = intval($_POST['id']);
$estado = $_POST['estado'];

if ($id <= 0) {
   ob_clean();
   echo json_encode(["exito" => false, "error" => "ID de pedido inválido"]);
   ob_end_flush();
   exit();
}



/* ====================================================
   LISTA DE ESTADOS VALIDOS
   Se usa para evitar estados incorrectos
==================================================== */

$estadosValidos = ['pendiente','confirmado','enviado','entregado','cancelado'];



/* ====================================================
   VALIDAR QUE EL ESTADO SEA PERMITIDO
==================================================== */

if (!in_array($estado, $estadosValidos)) {
    ob_clean();
    echo json_encode(["exito" => false, "error" => "Estado inválido"]);
    ob_end_flush();
    exit();
}



/* ====================================================
   OBTENER ESTADO ACTUAL Y DATOS DEL CLIENTE (antes del UPDATE)
   Para notificar por correo solo cuando el estado cambie
==================================================== */

$sqlDatos = "SELECT p.estado AS estado_actual, c.nombre AS nombre_cliente, u.correo AS correo_cliente
    FROM pedidos p
    INNER JOIN clientes c ON p.id_cliente = c.id_cliente
    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
    WHERE p.id_pedido = ?";
$stmtDatos = $conexion->prepare($sqlDatos);
$stmtDatos->bind_param("i", $id);
$stmtDatos->execute();
$resDatos = $stmtDatos->get_result();
$datosPedido = $resDatos->fetch_assoc();
$stmtDatos->close();

$estado_anterior = $datosPedido ? $datosPedido['estado_actual'] : null;
$nombre_cliente = $datosPedido ? trim($datosPedido['nombre_cliente']) : '';
$correo_cliente = $datosPedido ? trim($datosPedido['correo_cliente']) : '';

if ($estado_anterior === null) {
   ob_clean();
   echo json_encode(["exito" => false, "error" => "Pedido no encontrado"]);
   ob_end_flush();
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
    ob_clean();
    echo json_encode(["exito" => false, "error" => $conexion->error]);
    ob_end_flush();
    exit();
}



/* ====================================================
   VINCULAR PARAMETROS Y EJECUTAR CONSULTA
   s = string (estado)
   i = integer (id del pedido)
==================================================== */

$stmt->bind_param("si", $estado, $id);
$stmt->execute();

if ($stmt->errno) {
   ob_clean();
   echo json_encode(["exito" => false, "error" => "Error al actualizar estado: " . $stmt->error]);
   ob_end_flush();
   exit();
}



/* ====================================================
   NOTIFICAR AL CLIENTE POR CORREO SI EL ESTADO CAMBIÓ
   Solo para estados: confirmado, enviado, entregado, cancelado
==================================================== */

if ($estado_anterior !== null && $estado_anterior !== $estado) {
    notificarCambioEstadoPedido($id, $estado, $nombre_cliente, $correo_cliente);
}



/* ====================================================
   RESPUESTA FINAL
   Indica que la actualización fue exitosa
==================================================== */

ob_clean();
echo json_encode(["exito" => true]);
ob_end_flush();
exit();