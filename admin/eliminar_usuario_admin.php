<?php
/**
 * Eliminar Usuario desde Admin
 * Procesa la eliminación de usuarios
 * (La autenticación está protegida en usuarios.php)
 */

require_once '../core/conexion.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asegurarse de que siempre retornamos JSON
header('Content-Type: application/json; charset=utf-8');

// Solo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido']);
    exit();
}

// Obtener ID del usuario a eliminar
$id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;

if ($id_usuario <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'ID de usuario inválido']);
    exit();
}

// No permitir eliminar al usuario actual (solo si una sesión está activa)
if (isset($_SESSION['id_usuario']) && $id_usuario === $_SESSION['id_usuario']) {
    echo json_encode(['exito' => false, 'mensaje' => 'No puedes eliminar tu propia cuenta']);
    exit();
}

// Verificar que el usuario existe
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

// Usar transacción para eliminar usuario y sus datos relacionados
$conexion->begin_transaction();

try {
    // Eliminar usuario (cascade elimina clientes y pedidos automáticamente)
    $query_delete = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query_delete);
    $stmt->bind_param("i", $id_usuario);

    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar usuario: " . $conexion->error);
    }

    $stmt->close();

    // Confirmar transacción
    $conexion->commit();

    echo json_encode([
        'exito' => true,
        'mensaje' => 'Usuario eliminado exitosamente'
    ]);

} catch (Exception $e) {
    // Revertir cambios en caso de error
    $conexion->rollback();

    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}
