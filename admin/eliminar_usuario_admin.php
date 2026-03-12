<?php
/*
========================================================
MODULO: ELIMINAR USUARIO DESDE PANEL ADMINISTRATIVO
========================================================

Este archivo procesa la eliminación de usuarios dentro
del sistema administrativo.

FUNCIONES PRINCIPALES:
✔ Validar que el usuario esté autenticado
✔ Verificar permisos (solo administrador puede eliminar)
✔ Validar el ID del usuario a eliminar
✔ Evitar que el usuario elimine su propia cuenta
✔ Verificar que el usuario exista en la base de datos
✔ Eliminar todos los datos relacionados con el usuario
✔ Utilizar transacciones para mantener integridad de datos
✔ Retornar respuesta en formato JSON para AJAX

PROCESO DE ELIMINACION:
1. Verificar usuario
2. Buscar cliente asociado
3. Eliminar dependencias:
   - Carritos
   - Detalles de carrito
   - Pedidos
   - Detalles de pedidos
   - Direcciones
   - Lista de deseos (si existe)
   - Mensajes (si existe)
   - Historial de pedidos (si existe)
4. Eliminar cliente
5. Eliminar usuario

SEGURIDAD:
✔ Uso de prepared statements
✔ Uso de transacciones
✔ Validación de permisos
✔ Control de foreign keys

AUTOR: Sistema Web
========================================================
*/


/* ====================================================
   CONEXION A LA BASE DE DATOS
==================================================== */
require_once '../core/conexion.php';


/* ====================================================
   SISTEMA DE SESIONES
   Permite validar autenticación del usuario
==================================================== */
require_once '../core/sesiones.php';


// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* ====================================================
   CONFIGURAR RESPUESTA EN FORMATO JSON
   Todas las respuestas serán JSON
==================================================== */
header('Content-Type: application/json; charset=utf-8');


/* ====================================================
   VALIDAR AUTENTICACION Y PERMISOS
   Solo administrador (rol 1) puede eliminar usuarios
==================================================== */
if (!usuarioAutenticado() || $_SESSION['id_rol'] != 1) {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'No tienes permisos para eliminar usuarios']);
    exit();
}


/* ====================================================
   VALIDAR QUE LA PETICION SEA POST
==================================================== */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}


/* ====================================================
   OBTENER ID DEL USUARIO A ELIMINAR
==================================================== */
$id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;

if ($id_usuario <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'ID de usuario inválido']);
    exit();
}


/* ====================================================
   EVITAR QUE EL USUARIO ELIMINE SU PROPIA CUENTA
==================================================== */
if (isset($_SESSION['id_usuario']) && $id_usuario === $_SESSION['id_usuario']) {
    echo json_encode(['exito' => false, 'mensaje' => 'No puedes eliminar tu propia cuenta']);
    exit();
}


/* ====================================================
   VERIFICAR QUE EL USUARIO EXISTA
==================================================== */
$query_verify = "SELECT id_usuario FROM usuarios WHERE id_usuario = ?";
$stmt = $conexion->prepare($query_verify);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    echo json_encode(['exito' => false, 'mensaje' => 'El usuario no existe']);
    exit();
}
$stmt->close();


/* ====================================================
   INICIAR TRANSACCION
   Permite revertir cambios si ocurre un error
==================================================== */
$conexion->begin_transaction();


try {

    /* ====================================================
       DESACTIVAR FOREIGN KEY CHECKS
       Permite eliminar registros relacionados manualmente
    ==================================================== */
    $conexion->query("SET FOREIGN_KEY_CHECKS=0");
    

    /* ====================================================
       OBTENER CLIENTE ASOCIADO AL USUARIO
    ==================================================== */
    $stmtCli = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
    $stmtCli->bind_param("i", $id_usuario);
    $stmtCli->execute();
    $resCli = $stmtCli->get_result();

    $id_cliente = null;

    if ($filaCli = $resCli->fetch_assoc()) {
        $id_cliente = (int)$filaCli['id_cliente'];
    }

    $stmtCli->close();


    /* ====================================================
       SI EL USUARIO TIENE CLIENTE ASOCIADO
       SE ELIMINAN TODAS SUS DEPENDENCIAS
    ==================================================== */
    if ($id_cliente) {


        /* ===============================
           ELIMINAR DETALLES DE CARRITO
        =============================== */
        $stmt = $conexion->prepare("DELETE FROM carrito_detalle WHERE id_carrito IN (SELECT id_carrito FROM carritos WHERE id_cliente = ?)");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();


        /* ===============================
           ELIMINAR CARRITOS
        =============================== */
        $stmt = $conexion->prepare("DELETE FROM carritos WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();


        /* ===============================
           ELIMINAR DETALLES DE PEDIDOS
        =============================== */
        $stmt = $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido IN (SELECT id_pedido FROM pedidos WHERE id_cliente = ?)");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();


        /* ===============================
           ELIMINAR PEDIDOS
        =============================== */
        $stmt = $conexion->prepare("DELETE FROM pedidos WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();


        /* ===============================
           ELIMINAR DIRECCIONES
        =============================== */
        $stmt = $conexion->prepare("DELETE FROM direcciones_cliente WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();


        /* ===============================
           ELIMINAR LISTA DE DESEOS
           (SI LA TABLA EXISTE)
        =============================== */
        $tableExists = $conexion->query("SHOW TABLES LIKE 'lista_deseos'");

        if ($tableExists && $tableExists->num_rows > 0) {

            $stmt = $conexion->prepare("DELETE FROM lista_deseos WHERE id_cliente = ?");

            if ($stmt) {
                $stmt->bind_param("i", $id_cliente);
                $stmt->execute();
                $stmt->close();
            }
        }


        /* ===============================
           ELIMINAR MENSAJES
           (SI LA TABLA EXISTE)
        =============================== */
        $tableExists = $conexion->query("SHOW TABLES LIKE 'mensajes'");

        if ($tableExists && $tableExists->num_rows > 0) {

            $stmt = $conexion->prepare("DELETE FROM mensajes WHERE id_usuario = ?");

            if ($stmt) {
                $stmt->bind_param("i", $id_usuario);
                $stmt->execute();
                $stmt->close();
            }
        }


        /* ===============================
           ELIMINAR HISTORIAL DE PEDIDOS
           (SI LA TABLA EXISTE)
        =============================== */
        $tableExists = $conexion->query("SHOW TABLES LIKE 'historial_pedido'");

        if ($tableExists && $tableExists->num_rows > 0) {

            $stmt = $conexion->prepare("DELETE FROM historial_pedido WHERE id_usuario = ?");

            if ($stmt) {
                $stmt->bind_param("i", $id_usuario);
                $stmt->execute();
                $stmt->close();
            }
        }


        /* ===============================
           ELIMINAR CLIENTE
        =============================== */
        $stmt = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();
    }


    /* ====================================================
       ELIMINAR USUARIO
    ==================================================== */
    $query_delete = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query_delete);
    $stmt->bind_param("i", $id_usuario);

    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar usuario: " . $conexion->error);
    }

    $stmt->close();


    /* ====================================================
       REACTIVAR FOREIGN KEYS
    ==================================================== */
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");


    /* ====================================================
       CONFIRMAR TRANSACCION
    ==================================================== */
    $conexion->commit();


    /* ====================================================
       RESPUESTA EXITOSA
    ==================================================== */
    echo json_encode([
        'exito' => true,
        'mensaje' => 'Usuario eliminado exitosamente'
    ]);

} catch (Exception $e) {


    /* ====================================================
       SI OCURRE UN ERROR:
       - Reactivar foreign keys
       - Revertir cambios
    ==================================================== */
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");

    $conexion->rollback();


    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}