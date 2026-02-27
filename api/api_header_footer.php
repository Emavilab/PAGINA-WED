<?php
/**
 * API para gestionar Header y Footer
 * Rutas: 
 * - GET: Obtener header/footer
 * - POST: Guardar/actualizar header/footer
 * - DELETE: Eliminar header/footer
 */

header('Content-Type: application/json; charset=utf-8');

require_once '../core/sesiones.php';
require_once '../core/conexion.php';

// Validar que sea admin
if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'Acceso denegado']);
    exit();
}

$accion = $_REQUEST['accion'] ?? '';
$tipo = $_REQUEST['tipo'] ?? ''; // 'header' o 'footer'

if (empty($accion)) {
    http_response_code(400);
    echo json_encode(['exito' => false, 'mensaje' => 'Acción no especificada']);
    exit();
}

// ==================== OBTENER ====================
if ($accion === 'obtener' && !empty($tipo)) {
    $tipo = htmlspecialchars($tipo);
    
    if ($tipo !== 'header' && $tipo !== 'footer') {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'Tipo inválido']);
        exit();
    }

    $query = "SELECT * FROM header_footer WHERE tipo = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('s', $tipo);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $datos = $resultado->fetch_assoc();
        echo json_encode([
            'exito' => true,
            'datos' => $datos
        ]);
    } else {
        echo json_encode([
            'exito' => false,
            'mensaje' => ucfirst($tipo) . ' no encontrado'
        ]);
    }
    exit();
}

// ==================== GUARDAR/ACTUALIZAR ====================
if ($accion === 'guardar' && !empty($tipo)) {
    $tipo = htmlspecialchars($tipo);
    $titulo = $_POST['titulo'] ?? '';
    $contenido = $_POST['contenido'] ?? '';
    $estado = $_POST['estado'] ?? 'activo';

    if ($tipo !== 'header' && $tipo !== 'footer') {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'Tipo inválido']);
        exit();
    }

    if (empty($contenido)) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'El contenido no puede estar vacío']);
        exit();
    }

    if ($estado !== 'activo' && $estado !== 'inactivo') {
        $estado = 'activo';
    }

    // Verificar si ya existe
    $check = "SELECT id FROM header_footer WHERE tipo = ?";
    $stmt_check = $conexion->prepare($check);
    $stmt_check->bind_param('s', $tipo);
    $stmt_check->execute();
    $existe = $stmt_check->get_result()->num_rows > 0;

    if ($existe) {
        // Actualizar
        $query = "UPDATE header_footer SET titulo = ?, contenido = ?, estado = ?, fecha_actualizacion = NOW() WHERE tipo = ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('ssss', $titulo, $contenido, $estado, $tipo);
        
        if ($stmt->execute()) {
            echo json_encode([
                'exito' => true,
                'mensaje' => ucfirst($tipo) . ' actualizado exitosamente'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al actualizar: ' . $stmt->error
            ]);
        }
    } else {
        // Insertar
        $query = "INSERT INTO header_footer (tipo, titulo, contenido, estado) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('ssss', $tipo, $titulo, $contenido, $estado);
        
        if ($stmt->execute()) {
            echo json_encode([
                'exito' => true,
                'mensaje' => ucfirst($tipo) . ' creado exitosamente'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al crear: ' . $stmt->error
            ]);
        }
    }
    exit();
}

// ==================== ELIMINAR CONTENIDO ====================
if ($accion === 'eliminar' && !empty($tipo)) {
    $tipo = htmlspecialchars($tipo);
    
    if ($tipo !== 'header' && $tipo !== 'footer') {
        http_response_code(400);
        echo json_encode(['exito' => false, 'mensaje' => 'Tipo inválido']);
        exit();
    }

    $query = "UPDATE header_footer SET contenido = '', estado = 'inactivo' WHERE tipo = ?";
    $stmt = $conexion->prepare($query);
    $stmt->bind_param('s', $tipo);
    
    if ($stmt->execute()) {
        echo json_encode([
            'exito' => true,
            'mensaje' => ucfirst($tipo) . ' eliminado exitosamente'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Error al eliminar: ' . $stmt->error
        ]);
    }
    exit();
}

// Si llegamos aquí, la acción no fue reconocida
http_response_code(400);
echo json_encode(['exito' => false, 'mensaje' => 'Acción no reconocida']);
?>
