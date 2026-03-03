<?php
require_once '../core/conexion.php';
require_once '../core/sesiones.php';

header('Content-Type: application/json');

// Verificar autenticación
if (!usuarioAutenticado()) {
    echo json_encode(["success" => false, "message" => "Usuario no autenticado"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$id_usuario = intval($data['id'] ?? 0);

if ($id_usuario <= 0) {
    echo json_encode(["success" => false, "message" => "ID inválido"]);
    exit();
}

// El usuario solo puede eliminar su propia cuenta
if ($id_usuario !== $_SESSION['id_usuario']) {
    echo json_encode(["success" => false, "message" => "No tienes permisos para eliminar esta cuenta"]);
    exit();
}

try {
    $conexion->begin_transaction();
    
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
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $stmt->close();
        }
        
        // Eliminar historial de pedidos (si existe)
        $tableExists = $conexion->query("SHOW TABLES LIKE 'historial_pedido'");
        if ($tableExists && $tableExists->num_rows > 0) {
            $stmt = $conexion->prepare("DELETE FROM historial_pedido WHERE id_usuario = ?");
            $stmt->bind_param("i", $id_usuario);
            $stmt->execute();
            $stmt->close();
        }
        
        // Eliminar cliente
        $stmt = $conexion->prepare("DELETE FROM clientes WHERE id_cliente = ?");
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $stmt->close();
    }
    
    // Eliminar usuario
    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $stmt->close();
    
    // Reactivar foreign key checks
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");
    
    // Confirmar transacción
    $conexion->commit();
    
    // Destruir sesión
    session_destroy();
    
    echo json_encode(["success" => true, "message" => "Cliente eliminado correctamente", "redirect" => "/index.php"]);
    
} catch (Exception $e) {
    // Reactivar foreign key checks en caso de error
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");
    
    $conexion->rollback();
    
    echo json_encode([
        "success" => false,
        "message" => "Error al eliminar: " . $e->getMessage()
    ]);
}
?>