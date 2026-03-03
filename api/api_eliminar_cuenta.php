<?php
/**
 * API para eliminar cuenta del usuario
 * Borra el usuario y todos sus datos relacionados en cascada
 */

require_once '../core/sesiones.php';

header('Content-Type: application/json; charset=utf-8');

// Log para debugging
$debug = [];

try {
    $debug[] = "1. Verificando autenticación";
    
    // Verificar si el usuario está autenticado
    if (!usuarioAutenticado()) {
        http_response_code(401);
        echo json_encode([
            'exito' => false,
            'mensaje' => 'No autorizado',
            'debug' => $debug
        ]);
        exit();
    }
    
    $debug[] = "2. Usuario autenticado: " . $_SESSION['id_usuario'];
    
    // Solo procesar POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Método no permitido',
            'debug' => $debug
        ]);
        exit();
    }
    
    $debug[] = "3. Iniciando transacción";
    $id_usuario = $_SESSION['id_usuario'];
    
    $conexion->begin_transaction();
    
    // Desactivar foreign key checks
    $conexion->query("SET FOREIGN_KEY_CHECKS=0");
    $debug[] = "4. Foreign keys desactivadas";
    
    // Obtener id_cliente
    $stmtCli = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
    $stmtCli->bind_param("i", $id_usuario);
    $stmtCli->execute();
    $resCli = $stmtCli->get_result();
    $id_cliente = null;
    
    if ($filaCli = $resCli->fetch_assoc()) {
        $id_cliente = (int)$filaCli['id_cliente'];
    }
    $stmtCli->close();
    
    $debug[] = "5. Cliente encontrado: " . ($id_cliente ? $id_cliente : "ninguno");
    
    // Eliminar dependencias en orden
    if ($id_cliente) {
        // Carrito detalle
        $conexion->query("DELETE FROM carrito_detalle WHERE id_carrito IN (SELECT id_carrito FROM carritos WHERE id_cliente = $id_cliente)");
        $debug[] = "6a. Eliminado carrito_detalle";
        
        // Carritos
        $conexion->query("DELETE FROM carritos WHERE id_cliente = $id_cliente");
        $debug[] = "6b. Eliminado carritos";
        
        // Detalle pedido
        $conexion->query("DELETE FROM detalle_pedido WHERE id_pedido IN (SELECT id_pedido FROM pedidos WHERE id_cliente = $id_cliente)");
        $debug[] = "6c. Eliminado detalle_pedido";
        
        // Pedidos
        $conexion->query("DELETE FROM pedidos WHERE id_cliente = $id_cliente");
        $debug[] = "6d. Eliminado pedidos";
        
        // Direcciones
        $conexion->query("DELETE FROM direcciones_cliente WHERE id_cliente = $id_cliente");
        $debug[] = "6e. Eliminado direcciones";
        
        // Cliente
        $conexion->query("DELETE FROM clientes WHERE id_cliente = $id_cliente");
        $debug[] = "6f. Eliminado cliente";
    }
    
    // Lista de deseos (intentar con id_cliente primero)
    if ($id_cliente) {
        $conexion->query("DELETE FROM lista_deseos WHERE id_cliente = $id_cliente");
        $debug[] = "7. Eliminado lista_deseos (por cliente)";
    } else {
        $conexion->query("DELETE FROM lista_deseos WHERE id_usuario = $id_usuario");
        $debug[] = "7. Eliminado lista_deseos (por usuario)";
    }
    
    // Mensajes (si la tabla existe)
    $tableExists = $conexion->query("SHOW TABLES LIKE 'mensajes'");
    if ($tableExists && $tableExists->num_rows > 0) {
        $conexion->query("DELETE FROM mensajes WHERE id_usuario = $id_usuario OR destinatario_id = $id_usuario");
        $debug[] = "8. Eliminado mensajes";
    } else {
        $debug[] = "8. Tabla mensajes no existe (ignorada)";
    }
    
    // Historial (si la tabla existe)
    $tableExists = $conexion->query("SHOW TABLES LIKE 'historial_pedido'");
    if ($tableExists && $tableExists->num_rows > 0) {
        $conexion->query("DELETE FROM historial_pedido WHERE id_usuario = $id_usuario");
        $debug[] = "9. Eliminado historial";
    } else {
        $debug[] = "9. Tabla historial_pedido no existe (ignorada)";
    }
    
    // Usuario
    $conexion->query("DELETE FROM usuarios WHERE id_usuario = $id_usuario");
    $debug[] = "10. Eliminado usuario";
    
    // Reactivar foreign keys
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");
    $debug[] = "11. Foreign keys reactivadas";
    
    // Commit
    $conexion->commit();
    $debug[] = "12. Transacción confirmada";
    
    // Destruir sesión
    session_destroy();
    $debug[] = "13. Sesión destruida";
    
    echo json_encode([
        'exito' => true,
        'mensaje' => 'Tu cuenta ha sido eliminada correctamente',
        'redirect' => '/index.php'
    ]);
    
} catch (Exception $e) {
    $conexion->query("SET FOREIGN_KEY_CHECKS=1");
    $conexion->rollback();
    
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}

?>
