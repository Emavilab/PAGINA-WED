<?php
/**
 * =====================================================
 * ENDPOINT: Actualizar Actividad de Sesión
 * =====================================================
 *
 * Este archivo se utiliza para actualizar la última
 * actividad del usuario en la sesión.
 * Se espera que sea llamado desde JavaScript mediante
 * una solicitud POST cuando el usuario interactúa
 * con la página (por ejemplo, moviendo el mouse o
 * haciendo scroll).
 *
 * FUNCIONALIDAD:
 * - Verifica que la solicitud sea POST
 * - Comprueba que el usuario esté autenticado
 * - Actualiza el timestamp de la última actividad
 * - Devuelve un JSON de confirmación
 */

// =====================================================
// INCLUSIÓN DE ARCHIVOS NECESARIOS
// =====================================================

// Archivo que contiene funciones de sesiones y autenticación
require_once 'sesiones.php';
require_once 'csrf.php';

// Archivo que establece la conexión a la base de datos (si se necesitara)
require_once 'conexion.php';

// =====================================================
// VERIFICAR MÉTODO DE SOLICITUD
// =====================================================

// Solo se permite método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400); // Código HTTP 400: solicitud incorrecta
    exit(json_encode(['error' => 'Método no permitido'])); // Respuesta en JSON
}

// =====================================================
// VERIFICAR AUTENTICACIÓN
// =====================================================

// Si el usuario no está autenticado, no se permite actualizar la actividad
if (!usuarioAutenticado()) {
    http_response_code(401); // Código HTTP 401: no autorizado
    exit(json_encode(['error' => 'No autenticado'])); // Respuesta en JSON
}

// =====================================================
// OBTENER DATOS DE LA SOLICITUD
// =====================================================

// Leer datos JSON enviados desde JavaScript
// Nota: en este endpoint no se usan, pero se decodifica por si se agregan campos en el futuro
$json = json_decode(file_get_contents('php://input'), true);

// =====================================================
// ACTUALIZAR ÚLTIMA ACTIVIDAD EN SESIÓN
// =====================================================

// Guardar el timestamp actual en la sesión del usuario
$_SESSION['ultima_actividad'] = time();

// Asegurar que los cambios se escriben en el servidor de sesiones
session_write_close();

// =====================================================
// RESPUESTA JSON
// =====================================================

// Definir cabecera para indicar que la respuesta es JSON
header('Content-Type: application/json');

// Evitar que la respuesta se almacene en caché
header('Cache-Control: no-cache, must-revalidate');

// Devolver objeto JSON con información de éxito
echo json_encode([
    'success' => true,
    'message' => 'Actividad actualizada correctamente',
    'timestamp' => time() // Timestamp actualizado
]);

// Terminar la ejecución del script
exit();
?>
