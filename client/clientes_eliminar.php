<?php

require_once '../core/sesiones.php';
require_once '../core/eliminacion_usuario.php';
require_once '../core/csrf.php';

header('Content-Type: application/json; charset=utf-8');

validarCSRFMiddleware();

if (!usuarioAutenticado()) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no autenticado'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
    exit();
}

$payload = json_decode(file_get_contents('php://input'), true);
$idRecibido = (int)($payload['id'] ?? 0);
$idSesion = (int)($_SESSION['id_usuario'] ?? $_SESSION['id'] ?? 0);
$esAdmin = (int)($_SESSION['id_rol'] ?? 0) === 1;

if ($idSesion <= 0) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Sesión inválida'
    ]);
    exit();
}

$idUsuarioObjetivo = $idSesion;

if ($esAdmin && $idRecibido > 0) {
    $idPorCliente = obtenerUsuarioObjetivoPorCliente($conexion, $idRecibido);

    if ($idPorCliente !== null) {
        $idUsuarioObjetivo = $idPorCliente;
    } else {
        $idUsuarioObjetivo = $idRecibido;
    }
}

if (!$esAdmin && $idUsuarioObjetivo !== $idSesion) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'No tienes permisos para eliminar esta cuenta'
    ]);
    exit();
}

try {
    eliminarUsuarioEnCascada($conexion, $idUsuarioObjetivo);

    $eliminaPropiaSesion = $idUsuarioObjetivo === $idSesion;
    if ($eliminaPropiaSesion) {
        session_destroy();
    }

    echo json_encode([
        'success' => true,
        'message' => 'Cliente eliminado correctamente',
        'redirect' => $eliminaPropiaSesion ? '/index.php' : null
    ]);
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error al eliminar: ' . $e->getMessage()
    ]);
}
