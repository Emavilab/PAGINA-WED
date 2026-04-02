<?php
/*
====================================================================
API DE GESTIÓN DE MENSAJES DE CONTACTO
====================================================================

DESCRIPCIÓN:
Este script funciona como una API para gestionar los mensajes
recibidos desde el formulario de contacto del sistema.

FUNCIONALIDADES PRINCIPALES:
- Listar mensajes de contacto
- Filtrar mensajes por estado o búsqueda
- Obtener un mensaje específico
- Marcar mensajes como leídos
- Responder mensajes y enviar correo al cliente
- Cerrar tickets de mensajes
- Eliminar mensajes

MÉTODOS SOPORTADOS:
GET  -> Consultar mensajes
POST -> Ejecutar acciones sobre mensajes

TABLA UTILIZADA:
- mensajes_contacto

ARCHIVOS REQUERIDOS:
- conexion.php     -> Conexión a la base de datos
- smtp_config.php  -> Configuración y funciones para envío de correo

RESPUESTAS:
Todas las respuestas se devuelven en formato JSON.

====================================================================
*/

header('Content-Type: application/json; charset=utf-8');

// Incluir conexión a la base de datos
include(__DIR__ . "/../core/conexion.php");

// Incluir configuración SMTP para envío de correos
include(__DIR__ . "/../core/smtp_config.php");
include(__DIR__ . "/../core/csrf.php");

validarCSRFMiddleware();

// Verificar que la conexión exista
if (!isset($conexion)) {
    echo json_encode(['exito' => false, 'error' => 'Conexión a BD no disponible']);
    exit();
}

// Obtener el método de la petición HTTP
$metodo = $_SERVER['REQUEST_METHOD'];


// ===================== GET: Obtener mensajes =====================
if ($metodo === 'GET') {

    // Acción solicitada (listar por defecto)
    $accion = $_GET['accion'] ?? 'listar';

    /*
    ---------------------------------------------------------------
    LISTAR MENSAJES
    ---------------------------------------------------------------
    Permite listar todos los mensajes con filtros opcionales
    por estado o por texto de búsqueda.
    */
    if ($accion === 'listar') {

        // Filtros opcionales
        $estado = $_GET['estado'] ?? '';
        $busqueda = $_GET['busqueda'] ?? '';
        $estadosPermitidos = ['', 'todos', 'nuevo', 'leido', 'respondido', 'cerrado'];

        if (!in_array($estado, $estadosPermitidos, true)) {
            echo json_encode([
                'exito' => false,
                'error' => 'Estado de filtro inválido'
            ]);
            exit();
        }

        // Consulta base
        $sql = "SELECT * FROM mensajes_contacto WHERE 1=1";
        $params = [];
        $types = '';

        // Filtro por estado
        if (!empty($estado) && $estado !== 'todos') {
            $sql .= " AND estado = ?";
            $params[] = $estado;
            $types .= 's';
        }

        // Filtro por búsqueda en múltiples campos
        if (!empty($busqueda)) {
            $sql .= " AND (nombre LIKE ? OR correo LIKE ? OR asunto LIKE ? OR mensaje LIKE ?)";
            $busquedaLike = "%$busqueda%";
            $params[] = $busquedaLike;
            $params[] = $busquedaLike;
            $params[] = $busquedaLike;
            $params[] = $busquedaLike;
            $types .= 'ssss';
        }

        // Ordenar por fecha
        $sql .= " ORDER BY fecha_mensaje DESC";

        $stmt = $conexion->prepare($sql);

        // Asignar parámetros si existen
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        // Guardar mensajes en arreglo
        $mensajes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $mensajes[] = $fila;
        }

        /*
        -----------------------------------------------------------
        OBTENER CONTEO DE MENSAJES POR ESTADO
        -----------------------------------------------------------
        */
        $conteos = ['todos' => 0, 'nuevo' => 0, 'leido' => 0, 'respondido' => 0, 'cerrado' => 0];

        $sqlConteo = "SELECT estado, COUNT(*) as total FROM mensajes_contacto GROUP BY estado";
        $resConteo = $conexion->query($sqlConteo);

        while ($fila = $resConteo->fetch_assoc()) {
            if (array_key_exists($fila['estado'], $conteos)) {
                $conteos[$fila['estado']] = (int)$fila['total'];
            }
        }

        $conteos['todos'] = array_sum([
            $conteos['nuevo'],
            $conteos['leido'],
            $conteos['respondido'],
            $conteos['cerrado']
        ]);

        // Respuesta JSON
        echo json_encode([
            'exito' => true,
            'mensajes' => $mensajes,
            'conteos' => $conteos
        ]);
        exit();
    }

    /*
    ---------------------------------------------------------------
    OBTENER UN MENSAJE ESPECÍFICO
    ---------------------------------------------------------------
    */
    if ($accion === 'obtener') {

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        $stmt = $conexion->prepare("SELECT * FROM mensajes_contacto WHERE id_mensaje = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $resultado = $stmt->get_result();
        $mensaje = $resultado->fetch_assoc();

        if ($mensaje) {
            echo json_encode(['exito' => true, 'mensaje' => $mensaje]);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Mensaje no encontrado']);
        }

        exit();
    }
}


// ===================== POST: Acciones sobre mensajes =====================
if ($metodo === 'POST') {

    $accion = $_POST['accion'] ?? '';

    /*
    ---------------------------------------------------------------
    MARCAR MENSAJE COMO LEÍDO
    ---------------------------------------------------------------
    */
    if ($accion === 'marcar_leido') {

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        $stmt = $conexion->prepare("UPDATE mensajes_contacto SET estado = 'leido' WHERE id_mensaje = ? AND estado = 'nuevo'");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows >= 0) {
            echo json_encode(['exito' => true, 'mensaje' => 'Mensaje marcado como leído']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'No se pudo actualizar el mensaje']);
        }

        exit();
    }


    /*
    ---------------------------------------------------------------
    RESPONDER MENSAJE Y ENVIAR CORREO
    ---------------------------------------------------------------
    */
    if ($accion === 'responder') {

        $id = (int)($_POST['id'] ?? 0);
        $respuesta = trim($_POST['respuesta'] ?? '');

        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        if (empty($respuesta)) {
            echo json_encode(['exito' => false, 'error' => 'La respuesta no puede estar vacía']);
            exit();
        }

        // Obtener mensaje original
        $stmtMsg = $conexion->prepare("SELECT nombre, correo, asunto, mensaje FROM mensajes_contacto WHERE id_mensaje = ?");
        $stmtMsg->bind_param("i", $id);
        $stmtMsg->execute();
        $msgOriginal = $stmtMsg->get_result()->fetch_assoc();
        $stmtMsg->close();

        if (!$msgOriginal) {
            echo json_encode(['exito' => false, 'error' => 'Mensaje no encontrado']);
            exit();
        }

        // Guardar respuesta en la base de datos
        $stmt = $conexion->prepare("UPDATE mensajes_contacto SET estado = 'respondido', respuesta = ?, fecha_respuesta = NOW() WHERE id_mensaje = ?");
        $stmt->bind_param("si", $respuesta, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {

            /*
            -------------------------------------------------------
            ENVÍO DE CORREO AL CLIENTE
            -------------------------------------------------------
            */
            $correoEnviado = false;
            $mensajeCorreo = '';

            try {

                $asuntoCorreo = 'Re: ' . $msgOriginal['asunto'] . ' - ControlPlus';

                $cuerpoHtml = plantillaRespuestaContacto(
                    $msgOriginal['nombre'],
                    $msgOriginal['asunto'],
                    $msgOriginal['mensaje'],
                    $respuesta
                );

                $resultadoCorreo = enviarCorreo($msgOriginal['correo'], $asuntoCorreo, $cuerpoHtml);

                $correoEnviado = $resultadoCorreo['exito'];
                $mensajeCorreo = $resultadoCorreo['mensaje'];

            } catch (Exception $e) {
                $mensajeCorreo = 'Error al enviar correo: ' . $e->getMessage();
            }

            if ($correoEnviado) {

                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Respuesta guardada y correo enviado al cliente (' . $msgOriginal['correo'] . ')'
                ]);

            } else {

                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Respuesta guardada correctamente. Nota: No se pudo enviar el correo - ' . $mensajeCorreo
                ]);
            }

        } else {
            echo json_encode(['exito' => false, 'error' => 'No se pudo guardar la respuesta']);
        }

        exit();
    }


    /*
    ---------------------------------------------------------------
    CERRAR TICKET
    ---------------------------------------------------------------
    */
    if ($accion === 'cerrar') {

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        $stmt = $conexion->prepare("UPDATE mensajes_contacto SET estado = 'cerrado' WHERE id_mensaje = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['exito' => true, 'mensaje' => 'Ticket cerrado correctamente']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'No se pudo cerrar el ticket']);
        }

        exit();
    }


    /*
    ---------------------------------------------------------------
    ELIMINAR MENSAJE
    ---------------------------------------------------------------
    */
    if ($accion === 'eliminar') {

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        $stmt = $conexion->prepare("DELETE FROM mensajes_contacto WHERE id_mensaje = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['exito' => true, 'mensaje' => 'Mensaje eliminado correctamente']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'No se pudo eliminar el mensaje']);
        }

        exit();
    }

    // Acción no válida
    echo json_encode(['exito' => false, 'error' => 'Acción no reconocida']);
    exit();
}

// Método HTTP no permitido
echo json_encode(['exito' => false, 'error' => 'Método no permitido']);
?> 
