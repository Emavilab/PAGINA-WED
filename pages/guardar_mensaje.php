<?php
/**
 * =====================================================
 * ENDPOINT: Enviar Mensaje de Contacto
 * =====================================================
 *
 * Este archivo procesa los envíos del formulario de contacto
 * y los guarda en la base de datos.
 *
 * FUNCIONALIDAD:
 * - Recibe datos vía POST: nombre, correo, teléfono, asunto, mensaje
 * - Valida campos obligatorios
 * - Inserta el mensaje en la tabla mensajes_contacto con estado 'nuevo'
 * - Devuelve JSON con resultado de la operación
 */

header('Content-Type: application/json; charset=utf-8'); // Indicar que la respuesta será JSON

// =====================================================
// VERIFICAR MÉTODO DE SOLICITUD
// =====================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // =====================================================
        // INCLUIR CONEXIÓN A BASE DE DATOS
        // =====================================================
        include(__DIR__ . "/../core/conexion.php");

        // Validar que la conexión se estableció correctamente
        if (!isset($conexion)) {
            echo json_encode(['exito' => false, 'error' => 'Conexión a BD no disponible']);
            exit();
        }

        // =====================================================
        // OBTENER DATOS DEL FORMULARIO
        // =====================================================
        $nombre   = $_POST['nombre'] ?? '';
        $correo   = $_POST['correo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $asunto   = $_POST['asunto'] ?? '';
        $mensaje  = $_POST['mensaje'] ?? '';

        // =====================================================
        // VALIDACIONES BÁSICAS
        // =====================================================
        if (empty($nombre) || empty($correo) || empty($asunto) || empty($mensaje)) {
            echo json_encode(['exito' => false, 'error' => 'Faltan campos requeridos']);
            exit();
        }

        // =====================================================
        // PREPARAR SENTENCIA SQL PARA INSERTAR MENSAJE
        // =====================================================
        $stmt = $conexion->prepare("INSERT INTO mensajes_contacto 
            (nombre, correo, telefono, asunto, mensaje, estado) 
            VALUES (?, ?, ?, ?, ?, 'nuevo')");

        if (!$stmt) {
            echo json_encode(['exito' => false, 'error' => 'Error en prepare: ' . $conexion->error]);
            exit();
        }

        // Asociar parámetros con la sentencia preparada
        $stmt->bind_param("sssss", $nombre, $correo, $telefono, $asunto, $mensaje);

        // =====================================================
        // EJECUTAR SENTENCIA
        // =====================================================
        if ($stmt->execute()) {
            // Responder con "ok" si todo salió bien
            echo "ok";
        } else {
            // Enviar error en JSON si falla execute
            echo json_encode(['exito' => false, 'error' => 'Error en execute: ' . $stmt->error]);
        }

        // Cerrar la sentencia
        $stmt->close();

    } catch (Exception $e) {
        // Capturar cualquier excepción y enviarla en JSON
        echo json_encode(['exito' => false, 'error' => 'Exception: ' . $e->getMessage()]);
    }
}
?> 