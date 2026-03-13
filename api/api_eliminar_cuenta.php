<?php
/**
 * ========================================================
 * API: ELIMINAR CUENTA DE USUARIO
 * ========================================================
 *
 * Este archivo permite eliminar completamente la cuenta
 * de un usuario autenticado dentro del sistema.
 *
 * FUNCIONALIDADES:
 * ✔ Verificar autenticación del usuario
 * ✔ Iniciar una transacción en la base de datos
 * ✔ Desactivar temporalmente las claves foráneas
 * ✔ Eliminar todas las dependencias del usuario
 * ✔ Eliminar el cliente asociado
 * ✔ Eliminar el usuario del sistema
 * ✔ Confirmar la transacción
 * ✔ Destruir la sesión activa
 *
 * TABLAS RELACIONADAS:
 * - usuarios
 * - clientes
 * - carritos
 * - carrito_detalle
 * - pedidos
 * - detalle_pedido
 * - direcciones_cliente
 * - lista_deseos
 * - mensajes (si existe)
 * - historial_pedido (si existe)
 *
 * RESPUESTA JSON:
 *
 * Éxito:
 * {
 *   "exito": true,
 *   "mensaje": "Tu cuenta ha sido eliminada correctamente",
 *   "redirect": "/index.php"
 * }
 *
 * Error:
 * {
 *   "exito": false,
 *   "mensaje": "Error: descripción"
 * }
 *
 * NOTA:
 * Se incluye un arreglo de debug para facilitar
 * el seguimiento del proceso durante el desarrollo.
 * ========================================================
 */

require_once '../core/sesiones.php';

// Definir encabezado de respuesta JSON
header('Content-Type: application/json; charset=utf-8');

// Arreglo para registrar pasos del proceso (debug)
$debug = [];

try {

    /*
    ========================================================
    1. VERIFICAR AUTENTICACIÓN DEL USUARIO
    ========================================================
    */
    $debug[] = "1. Verificando autenticación";

    // Verificar si el usuario tiene sesión activa
    if (!usuarioAutenticado()) {

        http_response_code(401);

        echo json_encode([
            'exito' => false,
            'mensaje' => 'No autorizado',
            'debug' => $debug
        ]);

        exit();
    }

    // Registrar ID del usuario autenticado
    $debug[] = "2. Usuario autenticado: " . $_SESSION['id_usuario'];

    /*
    ========================================================
    2. VALIDAR MÉTODO HTTP
    ========================================================
    Solo se permite el método POST para eliminar cuentas
    por seguridad.
    */
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        http_response_code(405);

        echo json_encode([
            'exito' => false,
            'mensaje' => 'Método no permitido',
            'debug' => $debug
        ]);

        exit();
    }

    /*
    ========================================================
    3. INICIAR TRANSACCIÓN
    ========================================================
    Se usa una transacción para asegurar que todas las
    eliminaciones se ejecuten correctamente o se reviertan
    en caso de error.
    */
    $debug[] = "3. Iniciando transacción";

    $id_usuario = $_SESSION['id_usuario'];

    $conexion->begin_transaction();

    /*
    ========================================================
    4. DESACTIVAR FOREIGN KEY CHECKS
    ========================================================
    Esto permite eliminar registros relacionados sin
    conflictos de claves foráneas.
    */
    $conexion->query("SET FOREIGN_KEY_CHECKS=0");

    $debug[] = "4. Foreign keys desactivadas";

    /*
    ========================================================
    5. OBTENER ID DEL CLIENTE ASOCIADO
    ========================================================
    */
    $stmtCli = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
    $stmtCli->bind_param("i", $id_usuario);
    $stmtCli->execute();

    $resCli = $stmtCli->get_result();
    $id_cliente = null;

    if ($filaCli = $resCli->fetch_assoc()) {
        $id_cliente = (int)$filaCli['id_cliente'];
    }

    $stmtCli->close();

    $debug[] = "5. Cliente encontrado: " . ($id_cliente ? $id_cliente : "ninguno");

    /*
    ========================================================
    6. ELIMINAR DEPENDENCIAS DEL CLIENTE
    ========================================================
    Se eliminan primero los registros relacionados
    antes de borrar el cliente.
    */
    if ($id_cliente) {

        // Eliminar detalles del carrito
        $conexion->query("DELETE FROM carrito_detalle WHERE id_carrito IN (SELECT id_carrito FROM carritos WHERE id_cliente = $id_cliente)");
        $debug[] = "6a. Eliminado carrito_detalle";

        // Eliminar carritos
        $conexion->query("DELETE FROM carritos WHERE id_cliente = $id_cliente");
        $debug[] = "6b. Eliminado carritos";

        // Eliminar detalles de pedidos
        $conexion->query("DELETE FROM detalle_pedido WHERE id_pedido IN (SELECT id_pedido FROM pedidos WHERE id_cliente = $id_cliente)");
        $debug[] = "6c. Eliminado detalle_pedido";

        // Eliminar pedidos
        $conexion->query("DELETE FROM pedidos WHERE id_cliente = $id_cliente");
        $debug[] = "6d. Eliminado pedidos";

        // Eliminar direcciones del cliente
        $conexion->query("DELETE FROM direcciones_cliente WHERE id_cliente = $id_cliente");
        $debug[] = "6e. Eliminado direcciones";

        // Eliminar cliente
        $conexion->query("DELETE FROM clientes WHERE id_cliente = $id_cliente");
        $debug[] = "6f. Eliminado cliente";
    }

    /*
    ========================================================
    7. ELIMINAR LISTA DE DESEOS
    ========================================================
    Dependiendo de la estructura se elimina por cliente
    o por usuario.
    */
    if ($id_cliente) {

        $conexion->query("DELETE FROM lista_deseos WHERE id_cliente = $id_cliente");
        $debug[] = "7. Eliminado lista_deseos (por cliente)";

    } else {

        $conexion->query("DELETE FROM lista_deseos WHERE id_usuario = $id_usuario");
        $debug[] = "7. Eliminado lista_deseos (por usuario)";
    }

    /*
    ========================================================
    8. ELIMINAR MENSAJES (SI EXISTE LA TABLA)
    ========================================================
    */
    $tableExists = $conexion->query("SHOW TABLES LIKE 'mensajes'");

    if ($tableExists && $tableExists->num_rows > 0) {

        $conexion->query("DELETE FROM mensajes WHERE id_usuario = $id_usuario OR destinatario_id = $id_usuario");

        $debug[] = "8. Eliminado mensajes";

    } else {

        $debug[] = "8. Tabla mensajes no existe (ignorada)";
    }

    /*
    ========================================================
    9. ELIMINAR HISTORIAL DE PEDIDOS (SI EXISTE)
    ========================================================
    */
    $tableExists = $conexion->query("SHOW TABLES LIKE 'historial_pedido'");

    if ($tableExists && $tableExists->num_rows > 0) {

        $conexion->query("DELETE FROM historial_pedido WHERE id_usuario = $id_usuario");

        $debug[] = "9. Eliminado historial";

    } else {

        $debug[] = "9. Tabla historial_pedido no existe (ignorada)";
    }

    /*
    ========================================================
    10. ELIMINAR USUARIO
    ========================================================
    */
    $conexion->query("DELETE FROM usuarios WHERE id_usuario = $id_usuario");

    $debug[] = "10. Eliminado usuario";

    /*
    ========================================================
    11. REACTIVAR FOREIGN KEYS
    ========================================================
    */
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");

    $debug[] = "11. Foreign keys reactivadas";

    /*
    ========================================================
    12. CONFIRMAR TRANSACCIÓN
    ========================================================
    */
    $conexion->commit();

    $debug[] = "12. Transacción confirmada";

    /*
    ========================================================
    13. DESTRUIR SESIÓN DEL USUARIO
    ========================================================
    */
    session_destroy();

    $debug[] = "13. Sesión destruida";

    /*
    ========================================================
    RESPUESTA FINAL
    ========================================================
    */
    echo json_encode([
        'exito' => true,
        'mensaje' => 'Tu cuenta ha sido eliminada correctamente',
        'redirect' => '/index.php'
    ]);

} catch (Exception $e) {

    /*
    ========================================================
    MANEJO DE ERRORES
    ========================================================
    Si ocurre un error se revierten los cambios y se
    reactiva la validación de claves foráneas.
    */

    $conexion->query("SET FOREIGN_KEY_CHECKS=1");

    $conexion->rollback();

    http_response_code(400);

    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}

?> 