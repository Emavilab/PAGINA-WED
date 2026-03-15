<?php
/*
============================================================
API PARA ELIMINAR DIRECCIONES DE CLIENTE
============================================================

DESCRIPCIÓN:
Este script permite eliminar una dirección registrada por
un cliente dentro del sistema. Solo puede eliminarse si el
usuario está autenticado y la dirección pertenece al cliente.

FUNCIONAMIENTO GENERAL:
1. Verifica si el usuario está autenticado.
2. Obtiene el id_usuario desde la sesión activa.
3. Busca el id_cliente asociado a ese usuario.
4. Recibe el id_direccion mediante POST.
5. Elimina la dirección si pertenece al cliente.
6. Devuelve una respuesta en formato JSON.

SEGURIDAD:
- Solo usuarios autenticados pueden ejecutar la acción.
- Se utilizan consultas preparadas para evitar SQL Injection.
- Se valida que la dirección pertenezca al cliente.

RESPUESTAS JSON:
success = true  -> Dirección eliminada correctamente
success = false -> Error o datos inválidos

TABLAS UTILIZADAS:
- clientes
- direcciones_cliente

AUTOR: Sistema de gestión web
============================================================
*/

require_once '../core/sesiones.php';   // Archivo encargado de manejar las sesiones y autenticación
require_once '../core/conexion.php';   // Archivo que establece la conexión con la base de datos

// Establecer el tipo de respuesta como JSON
header('Content-Type: application/json; charset=utf-8');

/*
------------------------------------------------------------
VERIFICAR SI EL USUARIO ESTÁ AUTENTICADO
------------------------------------------------------------
Si el usuario no ha iniciado sesión, se detiene el proceso
y se devuelve un mensaje de error.
*/
if (!usuarioAutenticado()) {
    echo json_encode([
        "success" => false,
        "message" => "Usuario no autenticado"
    ]);
    exit();
}

/*
------------------------------------------------------------
OBTENER EL ID DEL USUARIO ACTUAL
------------------------------------------------------------
Se obtiene el identificador del usuario desde la sesión.
*/
$id_usuario = obtenerIdUsuario();

/* 🔥 OBTENER id_cliente REAL */

/*
------------------------------------------------------------
BUSCAR EL CLIENTE ASOCIADO AL USUARIO
------------------------------------------------------------
Cada usuario tiene asociado un cliente dentro del sistema.
Aquí se obtiene el id_cliente correspondiente.
*/
$stmtCliente = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
$stmtCliente->bind_param("i", $id_usuario);
$stmtCliente->execute();
$resultCliente = $stmtCliente->get_result();
$cliente = $resultCliente->fetch_assoc();
$stmtCliente->close();

/*
------------------------------------------------------------
VALIDAR QUE EL CLIENTE EXISTA
------------------------------------------------------------
Si no existe un cliente asociado al usuario,
no se puede eliminar una dirección.
*/
if (!$cliente) {
    echo json_encode([
        "success" => false,
        "message" => "Debes iniciar sesión para eliminar una dirección"
    ]);
    exit();
}

/*
Guardar el ID del cliente obtenido
*/
$id_cliente = $cliente['id_cliente'];

/* =============================== */

/*
------------------------------------------------------------
OBTENER EL ID DE LA DIRECCIÓN
------------------------------------------------------------
Se recibe el identificador de la dirección a eliminar
mediante el método POST.
*/
$id_direccion = $_POST['id_direccion'] ?? null;

/*
Validar que el ID de la dirección exista
*/
if (!$id_direccion) {
    echo json_encode([
        "success" => false,
        "message" => "ID inválido"
    ]);
    exit();
}

/*
------------------------------------------------------------
PROCESO PARA ELIMINAR LA DIRECCIÓN (SOFT DELETE)
------------------------------------------------------------
Se marca la dirección como inactiva en lugar de eliminarla
físicamente. Así se conserva el historial para pedidos ya
realizados. Solo se actualiza si pertenece al cliente.
*/
try {

    $stmt = $conexion->prepare("
        UPDATE direcciones_cliente
        SET activo = 0,
            fecha_eliminacion = NOW()
        WHERE id_direccion = ?
        AND id_cliente = ?
    ");

    /*
    Asociar los parámetros a la consulta preparada
    */
    $stmt->bind_param("ii", $id_direccion, $id_cliente);
    $stmt->execute();

    /*
    Verificar si la actualización afectó alguna fila
    */
    if ($stmt->affected_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "No se pudo eliminar la dirección"
        ]);
        exit();
    }

    /*
    Respuesta cuando la eliminación (soft delete) fue exitosa
    */
    echo json_encode([
        "success" => true,
        "message" => "Dirección eliminada correctamente"
    ]);

} catch (Exception $e) {

    /*
    Manejo de errores en caso de fallo en la base de datos
    */
    echo json_encode([
        "success" => false,
        "message" => "Error al eliminar"
    ]);
} 