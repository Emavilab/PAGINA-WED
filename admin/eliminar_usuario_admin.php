<?php
/**
 * Eliminar Usuario desde Admin
 * Procesa la eliminación de usuarios
 */

require_once '../core/conexion.php';
require_once '../core/sesiones.php';

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Asegurarse de que siempre retornamos JSON
header('Content-Type: application/json; charset=utf-8');

// Validar autenticación - Solo admin (rol 1) puede eliminar usuarios
if (!usuarioAutenticado() || $_SESSION['id_rol'] != 1) {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'No tienes permisos para eliminar usuarios']);
    exit();
}

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
    // Desactivar foreign key checks para mayor control
    $conexion->query("SET FOREIGN_KEY_CHECKS=0");
    
    // Obtener id_cliente asociado
    $stmtCli = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
    $stmtCli->bind_param("i", $id_usuario);
    $stmtCli->execute();
    $resCli = $stmtCli->get_result();
    $id_cliente = null;
    if ($filaCli = $resCli->fetch_assoc()) {
        $id_cliente = (int)$filaCli['id_cliente'];
    }
    $stmtCli->close();

    // Si el usuario tiene un registro de cliente, eliminar todas sus dependencias
    if ($id_cliente) {
        // Eliminar detalles de carrito
        $stmt = $conexion->prepare("DELETE FROM carrito_detalle WHERE id_carrito IN (SELECT id_carrito FROM carritos WHERE id_cliente = ?)");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();

        // Eliminar carritos
        $stmt = $conexion->prepare("DELETE FROM carritos WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();

        // Eliminar detalles de pedidos
        $stmt = $conexion->prepare("DELETE FROM detalle_pedido WHERE id_pedido IN (SELECT id_pedido FROM pedidos WHERE id_cliente = ?)");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();

        // Eliminar pedidos
        $stmt = $conexion->prepare("DELETE FROM pedidos WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();

        // Eliminar direcciones del cliente
        $stmt = $conexion->prepare("DELETE FROM direcciones_cliente WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();

        // Eliminar lista de deseos (si existe)
        $tableExists = $conexion->query("SHOW TABLES LIKE 'lista_deseos'");
        if ($tableExists && $tableExists->num_rows > 0) {
            $stmt = $conexion->prepare("DELETE FROM lista_deseos WHERE id_cliente = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id_cliente);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Eliminar mensajería (si existe)
        $tableExists = $conexion->query("SHOW TABLES LIKE 'mensajes'");
        if ($tableExists && $tableExists->num_rows > 0) {
            $stmt = $conexion->prepare("DELETE FROM mensajes WHERE id_usuario = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id_usuario);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Eliminar historial de pedidos (si existe)
        $tableExists = $conexion->query("SHOW TABLES LIKE 'historial_pedido'");
        if ($tableExists && $tableExists->num_rows > 0) {
            $stmt = $conexion->prepare("DELETE FROM historial_pedido WHERE id_usuario = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id_usuario);
                $stmt->execute();
                $stmt->close();
            }
        }

        // Eliminar cliente
        $stmt = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();
    }

    // Eliminar usuario
    $query_delete = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conexion->prepare($query_delete);
    $stmt->bind_param("i", $id_usuario);

    if (!$stmt->execute()) {
        throw new Exception("Error al eliminar usuario: " . $conexion->error);
    }

    $stmt->close();

    // Reactivar foreign key checks
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");

    // Confirmar transacción
    $conexion->commit();

    echo json_encode([
        'exito' => true,
        'mensaje' => 'Usuario eliminado exitosamente'
    ]);

} catch (Exception $e) {
    // Reactivar foreign key checks en caso de error
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");
    
    // Revertir cambios en caso de error
    $conexion->rollback();

    echo json_encode([
        'exito' => false,
        'mensaje' => $e->getMessage()
    ]);
}
