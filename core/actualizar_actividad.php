<?php
/**
 * Endpoint para Actualizar Actividad de Sesión
 * Se llama desde JavaScript cuando el usuario interactúa con la página
 */

require_once 'sesiones.php';
require_once 'conexion.php';

// Verificar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    exit(json_encode(['error' => 'Método no permitido']));
}

// Verificar que el usuario esté autenticado
if (!usuarioAutenticado()) {
    http_response_code(401);
    exit(json_encode(['error' => 'No autenticado']));
}

// Obtener datos JSON
$json = json_decode(file_get_contents('php://input'), true);

// Actualizar la última actividad
$_SESSION['ultima_actividad'] = time();

// Responder con JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Actividad actualizada'
]);
exit();
?>
