<?php
header('Content-Type: application/json; charset=utf-8');
include(__DIR__ . "/../core/conexion.php");
include(__DIR__ . "/../core/smtp_config.php");

if (!isset($conexion)) {
    echo json_encode(['exito' => false, 'error' => 'Conexión a BD no disponible']);
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];

// ===================== GET: Obtener mensajes =====================
if ($metodo === 'GET') {
    $accion = $_GET['accion'] ?? 'listar';

    if ($accion === 'listar') {
        // Filtro por estado (opcional)
        $estado = $_GET['estado'] ?? '';
        $busqueda = $_GET['busqueda'] ?? '';

        $sql = "SELECT * FROM mensajes_contacto WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($estado) && $estado !== 'todos') {
            $sql .= " AND estado = ?";
            $params[] = $estado;
            $types .= 's';
        }

        if (!empty($busqueda)) {
            $sql .= " AND (nombre LIKE ? OR correo LIKE ? OR asunto LIKE ? OR mensaje LIKE ?)";
            $busquedaLike = "%$busqueda%";
            $params[] = $busquedaLike;
            $params[] = $busquedaLike;
            $params[] = $busquedaLike;
            $params[] = $busquedaLike;
            $types .= 'ssss';
        }

        $sql .= " ORDER BY fecha_mensaje DESC";

        $stmt = $conexion->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();

        $mensajes = [];
        while ($fila = $resultado->fetch_assoc()) {
            $mensajes[] = $fila;
        }

        // Conteos por estado
        $conteos = ['todos' => 0, 'nuevo' => 0, 'leido' => 0, 'respondido' => 0, 'cerrado' => 0];
        $sqlConteo = "SELECT estado, COUNT(*) as total FROM mensajes_contacto GROUP BY estado";
        $resConteo = $conexion->query($sqlConteo);
        while ($fila = $resConteo->fetch_assoc()) {
            $conteos[$fila['estado']] = (int)$fila['total'];
        }
        $conteos['todos'] = array_sum([$conteos['nuevo'], $conteos['leido'], $conteos['respondido'], $conteos['cerrado']]);

        echo json_encode([
            'exito' => true,
            'mensajes' => $mensajes,
            'conteos' => $conteos
        ]);
        exit();
    }

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

    // --- Marcar como leído ---
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

    // --- Responder mensaje ---
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

        // Obtener datos del mensaje original para el correo
        $stmtMsg = $conexion->prepare("SELECT nombre, correo, asunto, mensaje FROM mensajes_contacto WHERE id_mensaje = ?");
        $stmtMsg->bind_param("i", $id);
        $stmtMsg->execute();
        $msgOriginal = $stmtMsg->get_result()->fetch_assoc();
        $stmtMsg->close();

        if (!$msgOriginal) {
            echo json_encode(['exito' => false, 'error' => 'Mensaje no encontrado']);
            exit();
        }

        // Guardar respuesta en BD
        $stmt = $conexion->prepare("UPDATE mensajes_contacto SET estado = 'respondido', respuesta = ?, fecha_respuesta = NOW() WHERE id_mensaje = ?");
        $stmt->bind_param("si", $respuesta, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Enviar correo al cliente
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

    // --- Cerrar ticket ---
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

    // --- Eliminar mensaje ---
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

    echo json_encode(['exito' => false, 'error' => 'Acción no reconocida']);
    exit();
}

echo json_encode(['exito' => false, 'error' => 'Método no permitido']);
?>
