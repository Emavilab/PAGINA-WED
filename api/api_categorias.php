<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    echo json_encode(['exito' => false, 'error' => 'No autorizado']);
    exit();
}

if (!isset($conexion)) {
    echo json_encode(['exito' => false, 'error' => 'Conexión a BD no disponible']);
    exit();
}

$metodo = $_SERVER['REQUEST_METHOD'];

// ===================== GET: Obtener categorías =====================
if ($metodo === 'GET') {
    $accion = $_GET['accion'] ?? 'listar';

    if ($accion === 'listar') {
        $busqueda = $_GET['busqueda'] ?? '';
        $estado = $_GET['estado'] ?? '';

        $sql = "SELECT * FROM categorias WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($estado) && $estado !== 'todos') {
            $sql .= " AND estado = ?";
            $params[] = $estado;
            $types .= 's';
        }

        if (!empty($busqueda)) {
            $sql .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
            $busquedaLike = "%$busqueda%";
            $params[] = $busquedaLike;
            $params[] = $busquedaLike;
            $types .= 'ss';
        }

        $sql .= " ORDER BY id_categoria DESC";

        $stmt = $conexion->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();

        $categorias = [];
        while ($fila = $resultado->fetch_assoc()) {
            $categorias[] = $fila;
        }

        // Conteos
        $conteos = ['total' => 0, 'activo' => 0, 'inactivo' => 0];
        $resConteo = $conexion->query("SELECT estado, COUNT(*) as total FROM categorias GROUP BY estado");
        while ($fila = $resConteo->fetch_assoc()) {
            $conteos[$fila['estado']] = (int)$fila['total'];
        }
        $conteos['total'] = $conteos['activo'] + $conteos['inactivo'];

        echo json_encode([
            'exito' => true,
            'categorias' => $categorias,
            'conteos' => $conteos
        ]);
        exit();
    }

    if ($accion === 'obtener') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        $stmt = $conexion->prepare("SELECT * FROM categorias WHERE id_categoria = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $categoria = $resultado->fetch_assoc();

        if ($categoria) {
            echo json_encode(['exito' => true, 'categoria' => $categoria]);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Categoría no encontrada']);
        }
        exit();
    }
}

// ===================== POST: Crear, Editar, Eliminar =====================
if ($metodo === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // --- Crear categoría ---
    if ($accion === 'crear') {
        $nombre = trim($_POST['nombre'] ?? '');
        $icono = trim($_POST['icono'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'activo';

        if (empty($nombre)) {
            echo json_encode(['exito' => false, 'error' => 'El nombre es obligatorio']);
            exit();
        }

        // Verificar nombre duplicado
        $stmtCheck = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ?");
        $stmtCheck->bind_param("s", $nombre);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            echo json_encode(['exito' => false, 'error' => 'Ya existe una categoría con ese nombre']);
            exit();
        }

        $stmt = $conexion->prepare("INSERT INTO categorias (nombre, icono, descripcion, estado) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nombre, $icono, $descripcion, $estado);

        if ($stmt->execute()) {
            echo json_encode(['exito' => true, 'mensaje' => 'Categoría creada correctamente', 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Error al crear la categoría: ' . $stmt->error]);
        }
        exit();
    }

    // --- Editar categoría ---
    if ($accion === 'editar') {
        $id = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $icono = trim($_POST['icono'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'activo';

        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }
        if (empty($nombre)) {
            echo json_encode(['exito' => false, 'error' => 'El nombre es obligatorio']);
            exit();
        }

        // Verificar nombre duplicado (excluyendo la categoría actual)
        $stmtCheck = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ? AND id_categoria != ?");
        $stmtCheck->bind_param("si", $nombre, $id);
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            echo json_encode(['exito' => false, 'error' => 'Ya existe otra categoría con ese nombre']);
            exit();
        }

        $stmt = $conexion->prepare("UPDATE categorias SET nombre = ?, icono = ?, descripcion = ?, estado = ? WHERE id_categoria = ?");
        $stmt->bind_param("ssssi", $nombre, $icono, $descripcion, $estado, $id);

        if ($stmt->execute()) {
            echo json_encode(['exito' => true, 'mensaje' => 'Categoría actualizada correctamente']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Error al actualizar: ' . $stmt->error]);
        }
        exit();
    }

    // --- Eliminar categoría ---
    if ($accion === 'eliminar') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        // Verificar si tiene productos asociados
        $stmtCheck = $conexion->prepare("SELECT COUNT(*) as total FROM productos WHERE id_categoria = ?");
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();
        $resultado = $stmtCheck->get_result()->fetch_assoc();
        if ($resultado['total'] > 0) {
            echo json_encode(['exito' => false, 'error' => 'No se puede eliminar: hay ' . $resultado['total'] . ' producto(s) asociados a esta categoría']);
            exit();
        }

        $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['exito' => true, 'mensaje' => 'Categoría eliminada correctamente']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'No se pudo eliminar la categoría']);
        }
        exit();
    }

    echo json_encode(['exito' => false, 'error' => 'Acción no reconocida']);
    exit();
}

echo json_encode(['exito' => false, 'error' => 'Método no permitido']);
?>
