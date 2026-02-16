<?php
/**
 * API para eliminar cuenta del usuario
 * Borra el usuario y todos sus datos relacionados en cascada
 */

require_once '../core/sesiones.php';

header('Content-Type: application/json');

// Verificar si el usuario está autenticado
if (!usuarioAutenticado()) {
    http_response_code(401);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'No autorizado'
    ]);
    exit();
}

// Solo procesar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido'
    ]);
    exit();
}

global $conexion;

$id_usuario = $_SESSION['id_usuario'];

// Iniciar transacción
$conexion->begin_transaction();

try {
    // 1. Eliminar pedidos del usuario
    $query = "DELETE FROM pedidos WHERE id_cliente IN (SELECT id_cliente FROM clientes WHERE id_usuario = ?)";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();
    
    // 2. Eliminar lista de deseos del usuario
    $query = "DELETE FROM lista_deseos WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();
    
    // 3. Eliminar datos del cliente
    $query = "DELETE FROM clientes WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();
    
    // 4. Eliminar usuario
    $query = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();
    
    // Confirmar transacción
    $conexion->commit();
    
    // Destruir sesión
    session_destroy();
    
    echo json_encode([
        'exito' => true,
        'mensaje' => 'Tu cuenta ha sido eliminada correctamente',
        'redirect' => '../index1.php'
    ]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();
    
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al eliminar la cuenta: ' . $e->getMessage()
    ]);
}

?>
