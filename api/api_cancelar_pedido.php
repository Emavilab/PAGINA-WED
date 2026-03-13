<?php
/*
========================================================
MODULO: CANCELAR PEDIDO DEL CLIENTE
========================================================
Este archivo funciona como una API que permite a un
cliente cancelar un pedido realizado en la tienda.

CONDICIONES PARA CANCELAR:
✔ El usuario debe estar autenticado
✔ El pedido debe pertenecer al cliente
✔ El estado del pedido debe ser "pendiente"
✔ Solo se puede cancelar dentro de las primeras 3 horas
  después de haber realizado el pedido

FUNCIONALIDADES:
✔ Verificar sesión activa del usuario
✔ Validar que el pedido exista
✔ Validar el estado del pedido
✔ Verificar límite de tiempo de cancelación (3 horas)
✔ Cambiar estado del pedido a "cancelado"
✔ Devolver los productos al stock del inventario
✔ Retornar respuesta en formato JSON

TABLAS UTILIZADAS:
- pedidos
- detalle_pedido
- productos

RESPUESTA DEL SERVIDOR:

Éxito:
{
  "exito": true,
  "mensaje": "Pedido cancelado correctamente"
}

Error:
{
  "exito": false,
  "error": "Descripción del error"
}

AUTOR: Sistema de Tienda Online
========================================================
*/

// Establecer que la respuesta será en formato JSON
header('Content-Type: application/json; charset=utf-8');

// Configurar zona horaria del sistema
date_default_timezone_set('America/Tegucigalpa');

// Incluir conexión a base de datos y sistema de sesiones
require_once '../core/conexion.php';
require_once '../core/sesiones.php';

/*
========================================================
VERIFICAR AUTENTICACIÓN DEL USUARIO
========================================================
Se valida que el usuario tenga sesión activa.
*/
if (!usuarioAutenticado()) {
    echo json_encode(['exito' => false, 'error' => 'No autorizado']);
    exit;
}

/*
========================================================
OBTENER DATOS DEL USUARIO
========================================================
Se obtienen los datos del usuario desde la sesión.
*/
$usuario = obtenerDatosUsuario();
$id_cliente = $usuario['id_cliente'] ?? null;

// Validar que el usuario tenga un cliente asociado
if (!$id_cliente) {
    echo json_encode(['exito' => false, 'error' => 'Debes iniciar sesión']);
    exit;
}

/*
========================================================
OBTENER ID DEL PEDIDO
========================================================
Se obtiene el identificador del pedido enviado
desde el formulario o solicitud AJAX.
*/
$id_pedido = isset($_POST['id_pedido']) ? intval($_POST['id_pedido']) : 0;

if ($id_pedido <= 0) {
    echo json_encode(['exito' => false, 'error' => 'Pedido inválido']);
    exit;
}

/*
========================================================
OBTENER INFORMACIÓN DEL PEDIDO
========================================================
Se consulta el pedido en la base de datos verificando
que pertenezca al cliente autenticado.
*/
$stmt = $conexion->prepare("SELECT id_pedido, estado, fecha_pedido FROM pedidos WHERE id_pedido = ? AND id_cliente = ? LIMIT 1");

$stmt->bind_param("ii", $id_pedido, $id_cliente);

$stmt->execute();

$res = $stmt->get_result();

if ($res->num_rows === 0) {

    $stmt->close();

    echo json_encode(['exito' => false, 'error' => 'Pedido no encontrado']);

    exit;
}

// Guardar información del pedido
$pedido = $res->fetch_assoc();

$stmt->close();

/*
========================================================
VALIDAR ESTADO DEL PEDIDO
========================================================
Solo se permite cancelar pedidos que estén en estado
"pendiente".
*/
if ($pedido['estado'] !== 'pendiente') {

    echo json_encode([
        'exito' => false,
        'error' => 'Solo se pueden cancelar pedidos en estado pendiente'
    ]);

    exit;
}

/*
================================
CALCULO DE LAS 3 HORAS
================================
Se calcula si el pedido aún se encuentra dentro del
tiempo permitido para cancelación.
*/

// Convertir fecha del pedido a objeto DateTime
$fechaPedido = new DateTime($pedido['fecha_pedido']);

// Clonar fecha del pedido para calcular el límite
$fechaLimite = clone $fechaPedido;

// Agregar 3 horas al tiempo del pedido
$fechaLimite->modify('+3 hours');

// Obtener fecha actual
$ahora = new DateTime();

// Verificar si ya pasó el tiempo permitido
if ($ahora > $fechaLimite) {

    echo json_encode([
        'exito' => false,
        'error' => 'Solo puedes cancelar dentro de las primeras 3 horas desde que realizaste el pedido'
    ]);

    exit;
}

/*
========================================================
ACTUALIZAR ESTADO DEL PEDIDO
========================================================
Se cambia el estado del pedido a "cancelado".
*/
$stmtUp = $conexion->prepare("UPDATE pedidos SET estado = 'cancelado' WHERE id_pedido = ? AND id_cliente = ?");

if (!$stmtUp) {

    echo json_encode([
        'exito' => false,
        'error' => 'Error en la consulta: ' . $conexion->error
    ]);

    exit;
}

// Vincular parámetros
if (!$stmtUp->bind_param("ii", $id_pedido, $id_cliente)) {

    echo json_encode([
        'exito' => false,
        'error' => 'Error al vincular parámetros: ' . $stmtUp->error
    ]);

    $stmtUp->close();

    exit;
}

// Ejecutar actualización
if (!$stmtUp->execute()) {

    echo json_encode([
        'exito' => false,
        'error' => 'Error al ejecutar la actualización: ' . $stmtUp->error
    ]);

    $stmtUp->close();

    exit;
}

/*
========================================================
VERIFICAR SI EL PEDIDO FUE ACTUALIZADO
========================================================
Si la actualización fue exitosa se procede a devolver
los productos al stock del inventario.
*/
if ($stmtUp->affected_rows > 0) {

    $stmtUp->close();

    /*
    ====================================================
    DEVOLVER PRODUCTOS AL STOCK
    ====================================================
    Se recorren los productos del pedido y se suma
    nuevamente la cantidad al inventario.
    */

    // Evitar devolver stock dos veces si ya estaba cancelado
    if ($pedido['estado'] !== 'cancelado') {

        $stmtDetalle = $conexion->prepare("SELECT id_producto, cantidad FROM detalle_pedido WHERE id_pedido = ?");

        if ($stmtDetalle) {

            $stmtDetalle->bind_param("i", $id_pedido);

            $stmtDetalle->execute();

            $resDetalle = $stmtDetalle->get_result();

            // Recorrer productos del pedido
            while ($fila = $resDetalle->fetch_assoc()) {

                $stmtStock = $conexion->prepare("UPDATE productos SET stock = stock + ? WHERE id_producto = ?");

                if ($stmtStock) {

                    $stmtStock->bind_param("ii", $fila['cantidad'], $fila['id_producto']);

                    $stmtStock->execute();

                    $stmtStock->close();
                }
            }

            $stmtDetalle->close();
        }
    }

    // Respuesta exitosa
    echo json_encode([
        'exito' => true,
        'mensaje' => 'Pedido cancelado correctamente'
    ]);

} else {

    $stmtUp->close();

    // Si no se pudo cancelar el pedido
    echo json_encode([
        'exito' => false,
        'error' => 'No se pudo cancelar el pedido. Verifica que el pedido exista y su estado sea válido'
    ]);
} 