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

// ===================== GET =====================
if ($metodo === 'GET') {
    $accion = $_GET['accion'] ?? 'listar';

    // --- Listar todas las categorías ---
    if ($accion === 'listar') {
        $busqueda = $_GET['busqueda'] ?? '';
        $estado = $_GET['estado'] ?? '';

        $sql = "SELECT c.*, p.nombre AS nombre_padre,
                (SELECT COUNT(*) FROM categorias h WHERE h.id_padre = c.id_categoria) AS total_hijos,
                (SELECT COUNT(*) FROM productos pr WHERE pr.id_categoria = c.id_categoria) AS total_productos
                FROM categorias c
                LEFT JOIN categorias p ON c.id_padre = p.id_categoria
                WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($estado) && $estado !== 'todos') {
            $sql .= " AND c.estado = ?";
            $params[] = $estado;
            $types .= 's';
        }

        if (!empty($busqueda)) {
            $sql .= " AND (c.nombre LIKE ? OR c.descripcion LIKE ?)";
            $busquedaLike = "%$busqueda%";
            $params[] = $busquedaLike;
            $params[] = $busquedaLike;
            $types .= 'ss';
        }

        $sql .= " ORDER BY COALESCE(c.id_padre, c.id_categoria), c.id_padre IS NOT NULL, c.nombre ASC";

        $stmt = $conexion->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();

        $categorias = [];
        while ($fila = $resultado->fetch_assoc()) {
            $fila['total_hijos'] = (int)$fila['total_hijos'];
            $fila['total_productos'] = (int)$fila['total_productos'];
            $categorias[] = $fila;
        }

        // Construir árbol
        $arbol = construirArbol($categorias);

        // Conteos
        $conteos = ['total' => 0, 'principales' => 0, 'subcategorias' => 0, 'activo' => 0, 'inactivo' => 0];
        $resConteo = $conexion->query("SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN id_padre IS NULL THEN 1 ELSE 0 END) AS principales,
            SUM(CASE WHEN id_padre IS NOT NULL THEN 1 ELSE 0 END) AS subcategorias,
            SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) AS activo,
            SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) AS inactivo
            FROM categorias");
        if ($fila = $resConteo->fetch_assoc()) {
            $conteos = [
                'total' => (int)$fila['total'],
                'principales' => (int)$fila['principales'],
                'subcategorias' => (int)$fila['subcategorias'],
                'activo' => (int)$fila['activo'],
                'inactivo' => (int)$fila['inactivo']
            ];
        }

        echo json_encode([
            'exito' => true,
            'categorias' => $categorias,
            'arbol' => $arbol,
            'conteos' => $conteos
        ]);
        exit();
    }

    // --- Obtener una categoría ---
    if ($accion === 'obtener') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        $stmt = $conexion->prepare("SELECT c.*, p.nombre AS nombre_padre 
            FROM categorias c 
            LEFT JOIN categorias p ON c.id_padre = p.id_categoria 
            WHERE c.id_categoria = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $categoria = $stmt->get_result()->fetch_assoc();

        if ($categoria) {
            echo json_encode(['exito' => true, 'categoria' => $categoria]);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Categoría no encontrada']);
        }
        exit();
    }

    // --- Listar solo categorías principales (para select de padre) ---
    if ($accion === 'listar_padres') {
        $excluir = (int)($_GET['excluir'] ?? 0);
        $sql = "SELECT id_categoria, nombre, icono FROM categorias WHERE id_padre IS NULL AND estado = 'activo'";
        if ($excluir > 0) {
            $sql .= " AND id_categoria != " . $excluir;
        }
        $sql .= " ORDER BY nombre ASC";
        $resultado = $conexion->query($sql);
        $padres = [];
        while ($fila = $resultado->fetch_assoc()) {
            $padres[] = $fila;
        }
        echo json_encode(['exito' => true, 'padres' => $padres]);
        exit();
    }
}

// ===================== POST =====================
if ($metodo === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // --- Crear categoría ---
    if ($accion === 'crear') {
        $nombre = trim($_POST['nombre'] ?? '');
        $icono = trim($_POST['icono'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'activo';
        $id_padre = !empty($_POST['id_padre']) ? (int)$_POST['id_padre'] : null;

        if (empty($nombre)) {
            echo json_encode(['exito' => false, 'error' => 'El nombre es obligatorio']);
            exit();
        }

        // Verificar nombre duplicado en el mismo nivel
        if ($id_padre) {
            $stmtCheck = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ? AND id_padre = ?");
            $stmtCheck->bind_param("si", $nombre, $id_padre);
        } else {
            $stmtCheck = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ? AND id_padre IS NULL");
            $stmtCheck->bind_param("s", $nombre);
        }
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            echo json_encode(['exito' => false, 'error' => 'Ya existe una categoría con ese nombre en este nivel']);
            exit();
        }

        // Validar padre
        if ($id_padre) {
            $stmtPadre = $conexion->prepare("SELECT id_categoria, id_padre FROM categorias WHERE id_categoria = ?");
            $stmtPadre->bind_param("i", $id_padre);
            $stmtPadre->execute();
            $padre = $stmtPadre->get_result()->fetch_assoc();
            if (!$padre) {
                echo json_encode(['exito' => false, 'error' => 'La categoría padre no existe']);
                exit();
            }
            if ($padre['id_padre'] !== null) {
                echo json_encode(['exito' => false, 'error' => 'Solo se permite un nivel de subcategorías']);
                exit();
            }
        }

        $stmt = $conexion->prepare("INSERT INTO categorias (nombre, id_padre, icono, descripcion, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sisss", $nombre, $id_padre, $icono, $descripcion, $estado);

        if ($stmt->execute()) {
            $tipo = $id_padre ? 'Subcategoría' : 'Categoría principal';
            echo json_encode(['exito' => true, 'mensaje' => "$tipo creada correctamente", 'id' => $stmt->insert_id]);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Error al crear: ' . $stmt->error]);
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
        $id_padre = !empty($_POST['id_padre']) ? (int)$_POST['id_padre'] : null;

        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }
        if (empty($nombre)) {
            echo json_encode(['exito' => false, 'error' => 'El nombre es obligatorio']);
            exit();
        }
        if ($id_padre === $id) {
            echo json_encode(['exito' => false, 'error' => 'Una categoría no puede ser subcategoría de sí misma']);
            exit();
        }

        // Si se cambia a subcategoría, verificar que no tenga hijos
        if ($id_padre) {
            $stmtHijos = $conexion->prepare("SELECT COUNT(*) AS total FROM categorias WHERE id_padre = ?");
            $stmtHijos->bind_param("i", $id);
            $stmtHijos->execute();
            $hijos = $stmtHijos->get_result()->fetch_assoc();
            if ($hijos['total'] > 0) {
                echo json_encode(['exito' => false, 'error' => 'No se puede convertir en subcategoría porque tiene ' . $hijos['total'] . ' subcategoría(s)']);
                exit();
            }

            $stmtPadre = $conexion->prepare("SELECT id_padre FROM categorias WHERE id_categoria = ?");
            $stmtPadre->bind_param("i", $id_padre);
            $stmtPadre->execute();
            $padre = $stmtPadre->get_result()->fetch_assoc();
            if ($padre && $padre['id_padre'] !== null) {
                echo json_encode(['exito' => false, 'error' => 'Solo se permite un nivel de subcategorías']);
                exit();
            }
        }

        // Verificar duplicado en mismo nivel
        if ($id_padre) {
            $stmtCheck = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ? AND id_padre = ? AND id_categoria != ?");
            $stmtCheck->bind_param("sii", $nombre, $id_padre, $id);
        } else {
            $stmtCheck = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ? AND id_padre IS NULL AND id_categoria != ?");
            $stmtCheck->bind_param("si", $nombre, $id);
        }
        $stmtCheck->execute();
        if ($stmtCheck->get_result()->num_rows > 0) {
            echo json_encode(['exito' => false, 'error' => 'Ya existe otra categoría con ese nombre en este nivel']);
            exit();
        }

        $stmt = $conexion->prepare("UPDATE categorias SET nombre = ?, id_padre = ?, icono = ?, descripcion = ?, estado = ? WHERE id_categoria = ?");
        $stmt->bind_param("sisssi", $nombre, $id_padre, $icono, $descripcion, $estado, $id);

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

        // Productos directos
        $stmtProd = $conexion->prepare("SELECT COUNT(*) as total FROM productos WHERE id_categoria = ?");
        $stmtProd->bind_param("i", $id);
        $stmtProd->execute();
        $resProd = $stmtProd->get_result()->fetch_assoc();
        if ($resProd['total'] > 0) {
            echo json_encode(['exito' => false, 'error' => 'No se puede eliminar: tiene ' . $resProd['total'] . ' producto(s) asociados']);
            exit();
        }

        // Productos en subcategorías
        $stmtSubProd = $conexion->prepare("SELECT COUNT(*) as total FROM productos WHERE id_categoria IN (SELECT id_categoria FROM categorias WHERE id_padre = ?)");
        $stmtSubProd->bind_param("i", $id);
        $stmtSubProd->execute();
        $resSubProd = $stmtSubProd->get_result()->fetch_assoc();
        if ($resSubProd['total'] > 0) {
            echo json_encode(['exito' => false, 'error' => 'No se puede eliminar: sus subcategorías tienen ' . $resSubProd['total'] . ' producto(s)']);
            exit();
        }

        // CASCADE eliminará subcategorías
        $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {
            echo json_encode(['exito' => true, 'mensaje' => 'Categoría eliminada correctamente']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'No se pudo eliminar']);
        }
        exit();
    }

    echo json_encode(['exito' => false, 'error' => 'Acción no reconocida']);
    exit();
}

// ===================== Construir árbol =====================
function construirArbol($categorias) {
    $arbol = [];
    $hijos = [];
    foreach ($categorias as $cat) {
        if ($cat['id_padre'] === null) {
            $cat['subcategorias'] = [];
            $arbol[$cat['id_categoria']] = $cat;
        } else {
            $hijos[] = $cat;
        }
    }
    foreach ($hijos as $hijo) {
        if (isset($arbol[$hijo['id_padre']])) {
            $arbol[$hijo['id_padre']]['subcategorias'][] = $hijo;
        }
    }
    return array_values($arbol);
}
