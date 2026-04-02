<?php

require_once '../core/sesiones.php';
require_once '../core/eliminacion_usuario.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();

header('Content-Type: application/json; charset=utf-8');

if (!usuarioAutenticado()) {
    http_response_code(401);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'No autorizado'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido'
    ]);
    exit();
}

$idUsuarioSesion = (int)($_SESSION['id_usuario'] ?? $_SESSION['id'] ?? 0);
if ($idUsuarioSesion <= 0) {
    http_response_code(401);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Sesión inválida'
    ]);
    exit();
}

try {
    eliminarUsuarioEnCascada($conexion, $idUsuarioSesion);

    session_destroy();

    echo json_encode([
        'exito' => true,
        'mensaje' => 'Tu cuenta ha sido eliminada correctamente',
        'redirect' => '/index.php'
    ]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}
