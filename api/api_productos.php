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

    // --- Listar productos ---
    if ($accion === 'listar') {
        $busqueda = $_GET['busqueda'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $categoria = $_GET['categoria'] ?? '';

        $sql = "SELECT p.*, c.nombre AS categoria_nombre, m.nombre AS marca_nombre,
                (SELECT ri.ruta_imagen FROM producto_imagenes ri WHERE ri.id_producto = p.id_producto ORDER BY ri.orden ASC LIMIT 1) AS imagen_principal
                FROM productos p
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                LEFT JOIN marcas m ON p.id_marca = m.id_marca
                WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($estado) && $estado !== 'todos') {
            $sql .= " AND p.estado = ?";
            $params[] = $estado;
            $types .= 's';
        }

        if (!empty($categoria)) {
            $sql .= " AND (p.id_categoria = ? OR p.id_categoria IN (SELECT id_categoria FROM categorias WHERE id_padre = ?))";
            $params[] = (int)$categoria;
            $params[] = (int)$categoria;
            $types .= 'ii';
        }

        if (!empty($busqueda)) {
            $sql .= " AND (p.codigo LIKE ? OR p.nombre LIKE ? OR p.descripcion LIKE ? OR c.nombre LIKE ? OR m.nombre LIKE ?)";
            $b = "%$busqueda%";
            $params = array_merge($params, [$b, $b, $b, $b, $b]);
            $types .= 'sssss';
        }

        $sql .= " ORDER BY p.fecha_creacion DESC";

        $stmt = $conexion->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();

        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }

        // Conteos
        $conteos = ['total' => 0, 'disponible' => 0, 'agotado' => 0, 'oferta' => 0];
        $rc = $conexion->query("SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN estado = 'disponible' THEN 1 ELSE 0 END) AS disponible,
            SUM(CASE WHEN estado = 'agotado' THEN 1 ELSE 0 END) AS agotado,
            SUM(CASE WHEN en_oferta = 1 THEN 1 ELSE 0 END) AS oferta
            FROM productos");
        if ($f = $rc->fetch_assoc()) {
            $conteos = [
                'total' => (int)$f['total'],
                'disponible' => (int)$f['disponible'],
                'agotado' => (int)$f['agotado'],
                'oferta' => (int)$f['oferta']
            ];
        }

        echo json_encode(['exito' => true, 'productos' => $productos, 'conteos' => $conteos]);
        exit();
    }

    // --- Obtener un producto ---
    if ($accion === 'obtener') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        $stmt = $conexion->prepare("SELECT p.*, c.nombre AS categoria_nombre, m.nombre AS marca_nombre
            FROM productos p
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            LEFT JOIN marcas m ON p.id_marca = m.id_marca
            WHERE p.id_producto = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $producto = $stmt->get_result()->fetch_assoc();

        if (!$producto) {
            echo json_encode(['exito' => false, 'error' => 'Producto no encontrado']);
            exit();
        }

        // Imágenes
        $stmtImg = $conexion->prepare("SELECT * FROM producto_imagenes WHERE id_producto = ? ORDER BY orden ASC");
        $stmtImg->bind_param("i", $id);
        $stmtImg->execute();
        $resImg = $stmtImg->get_result();
        $imagenes = [];
        while ($img = $resImg->fetch_assoc()) {
            $imagenes[] = $img;
        }
        $producto['imagenes'] = $imagenes;

        echo json_encode(['exito' => true, 'producto' => $producto]);
        exit();
    }

    // --- Listar marcas activas ---
    if ($accion === 'listar_marcas') {
        $resultado = $conexion->query("SELECT id_marca, nombre FROM marcas WHERE estado='activo' ORDER BY nombre ASC");
        $marcas = [];
        while ($f = $resultado->fetch_assoc()) {
            $marcas[] = $f;
        }
        echo json_encode(['exito' => true, 'marcas' => $marcas]);
        exit();
    }

    // --- Listar categorías para select (árbol) ---
    if ($accion === 'listar_categorias') {
        $resultado = $conexion->query(
            "SELECT c.id_categoria, c.nombre, c.id_padre, p.nombre AS nombre_padre
             FROM categorias c
             LEFT JOIN categorias p ON c.id_padre = p.id_categoria
             WHERE c.estado='activo'
             ORDER BY COALESCE(c.id_padre, c.id_categoria), c.id_padre IS NOT NULL, c.nombre ASC"
        );
        $categorias = [];
        while ($f = $resultado->fetch_assoc()) {
            $categorias[] = $f;
        }
        echo json_encode(['exito' => true, 'categorias' => $categorias]);
        exit();
    }

    echo json_encode(['exito' => false, 'error' => 'Acción GET no válida']);
    exit();
}

// ===================== POST =====================
if ($metodo === 'POST') {
    $accion = $_POST['accion'] ?? '';

    // --- Crear producto ---
    if ($accion === 'crear') {
        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $id_categoria = intval($_POST['id_categoria'] ?? 0);
        $id_marca = intval($_POST['id_marca'] ?? 0);
        $estado = $_POST['estado'] ?? 'disponible';
        $precio_descuento = !empty($_POST['precio_descuento']) ? floatval($_POST['precio_descuento']) : null;
        $en_oferta = isset($_POST['en_oferta']) ? intval($_POST['en_oferta']) : 0;
        $fecha_inicio_oferta = !empty($_POST['fecha_inicio_oferta']) ? $_POST['fecha_inicio_oferta'] : null;
        $fecha_fin_oferta = !empty($_POST['fecha_fin_oferta']) ? $_POST['fecha_fin_oferta'] : null;

        // Validaciones
        if (empty($codigo)) {
            echo json_encode(['exito' => false, 'error' => 'El código / SKU es obligatorio']);
            exit();
        }
        // Verificar código único
        $stmtChk = $conexion->prepare("SELECT id_producto FROM productos WHERE codigo = ?");
        $stmtChk->bind_param("s", $codigo);
        $stmtChk->execute();
        if ($stmtChk->get_result()->num_rows > 0) {
            echo json_encode(['exito' => false, 'error' => 'El código "' . $codigo . '" ya está en uso por otro producto']);
            exit();
        }
        if (empty($nombre)) {
            echo json_encode(['exito' => false, 'error' => 'El nombre es obligatorio']);
            exit();
        }
        if ($precio <= 0) {
            echo json_encode(['exito' => false, 'error' => 'El precio debe ser mayor a 0']);
            exit();
        }
        if ($id_categoria <= 0) {
            echo json_encode(['exito' => false, 'error' => 'Debe seleccionar una categoría']);
            exit();
        }
        if ($id_marca <= 0) {
            echo json_encode(['exito' => false, 'error' => 'Debe seleccionar una marca']);
            exit();
        }
        if ($en_oferta && (empty($precio_descuento) || $precio_descuento <= 0)) {
            echo json_encode(['exito' => false, 'error' => 'Debe ingresar un precio de descuento válido para marcar el producto en oferta']);
            exit();
        }
        if ($en_oferta && (empty($fecha_inicio_oferta) || empty($fecha_fin_oferta))) {
            echo json_encode(['exito' => false, 'error' => 'Debe indicar las fechas de inicio y fin de oferta']);
            exit();
        }

        $stmt = $conexion->prepare(
            "INSERT INTO productos (codigo, nombre, descripcion, precio, stock, id_categoria, id_marca, estado, precio_descuento, en_oferta, fecha_inicio_oferta, fecha_fin_oferta, fecha_creacion)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param("sssdiiisdiss",
            $codigo, $nombre, $descripcion, $precio, $stock,
            $id_categoria, $id_marca, $estado, $precio_descuento, $en_oferta,
            $fecha_inicio_oferta, $fecha_fin_oferta
        );

        if ($stmt->execute()) {
            $nuevoId = $conexion->insert_id;

            // Subir imágenes si se enviaron
            if (isset($_FILES['imagenes'])) {
                subirMultiplesImagenes($conexion, $nuevoId, $_FILES['imagenes']);
            }

            echo json_encode(['exito' => true, 'mensaje' => 'Producto creado exitosamente', 'id' => $nuevoId]);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Error al crear: ' . $conexion->error]);
        }
        exit();
    }

    // --- Editar producto ---
    if ($accion === 'editar') {
        $id = intval($_POST['id'] ?? 0);
        $codigo = trim($_POST['codigo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $stock = intval($_POST['stock'] ?? 0);
        $id_categoria = intval($_POST['id_categoria'] ?? 0);
        $id_marca = intval($_POST['id_marca'] ?? 0);
        $estado = $_POST['estado'] ?? 'disponible';
        $precio_descuento = !empty($_POST['precio_descuento']) ? floatval($_POST['precio_descuento']) : null;
        $en_oferta = isset($_POST['en_oferta']) ? intval($_POST['en_oferta']) : 0;
        $fecha_inicio_oferta = !empty($_POST['fecha_inicio_oferta']) ? $_POST['fecha_inicio_oferta'] : null;
        $fecha_fin_oferta = !empty($_POST['fecha_fin_oferta']) ? $_POST['fecha_fin_oferta'] : null;

        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }
        if (empty($codigo)) {
            echo json_encode(['exito' => false, 'error' => 'El código / SKU es obligatorio']);
            exit();
        }
        // Verificar código único (excluyendo el producto actual)
        $stmtChk = $conexion->prepare("SELECT id_producto FROM productos WHERE codigo = ? AND id_producto != ?");
        $stmtChk->bind_param("si", $codigo, $id);
        $stmtChk->execute();
        if ($stmtChk->get_result()->num_rows > 0) {
            echo json_encode(['exito' => false, 'error' => 'El código "' . $codigo . '" ya está en uso por otro producto']);
            exit();
        }
        if (empty($nombre)) {
            echo json_encode(['exito' => false, 'error' => 'El nombre es obligatorio']);
            exit();
        }
        if ($id_categoria <= 0) {
            echo json_encode(['exito' => false, 'error' => 'Debe seleccionar una categoría']);
            exit();
        }
        if ($id_marca <= 0) {
            echo json_encode(['exito' => false, 'error' => 'Debe seleccionar una marca']);
            exit();
        }
        if ($en_oferta && (empty($precio_descuento) || $precio_descuento <= 0)) {
            echo json_encode(['exito' => false, 'error' => 'Debe ingresar un precio de descuento válido para marcar el producto en oferta']);
            exit();
        }
        if ($en_oferta && (empty($fecha_inicio_oferta) || empty($fecha_fin_oferta))) {
            echo json_encode(['exito' => false, 'error' => 'Debe indicar las fechas de inicio y fin de oferta']);
            exit();
        }

        $stmt = $conexion->prepare(
            "UPDATE productos SET codigo=?, nombre=?, descripcion=?, precio=?, stock=?, id_categoria=?, id_marca=?, estado=?, precio_descuento=?, en_oferta=?, fecha_inicio_oferta=?, fecha_fin_oferta=? WHERE id_producto=?"
        );
        $stmt->bind_param("sssdiiisdissi",
            $codigo, $nombre, $descripcion, $precio, $stock,
            $id_categoria, $id_marca, $estado, $precio_descuento, $en_oferta,
            $fecha_inicio_oferta, $fecha_fin_oferta, $id
        );

        if ($stmt->execute()) {
            // Subir nuevas imágenes si se enviaron
            if (isset($_FILES['imagenes'])) {
                subirMultiplesImagenes($conexion, $id, $_FILES['imagenes']);
            }

            echo json_encode(['exito' => true, 'mensaje' => 'Producto actualizado exitosamente']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Error al actualizar: ' . $conexion->error]);
        }
        exit();
    }

    // --- Eliminar producto ---
    if ($accion === 'eliminar') {
        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID inválido']);
            exit();
        }

        // Eliminar imágenes del servidor
        $stmtImg = $conexion->prepare("SELECT ruta_imagen FROM producto_imagenes WHERE id_producto = ?");
        $stmtImg->bind_param("i", $id);
        $stmtImg->execute();
        $resImg = $stmtImg->get_result();
        while ($img = $resImg->fetch_assoc()) {
            $rutaCompleta = __DIR__ . '/../' . $img['ruta_imagen'];
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }
        }

        // Eliminar registros de imágenes
        $conexion->prepare("DELETE FROM producto_imagenes WHERE id_producto = ?")->bind_param("i", $id);
        $stmtDelImg = $conexion->prepare("DELETE FROM producto_imagenes WHERE id_producto = ?");
        $stmtDelImg->bind_param("i", $id);
        $stmtDelImg->execute();

        // Eliminar producto
        $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['exito' => true, 'mensaje' => 'Producto eliminado exitosamente']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Error al eliminar: ' . $conexion->error]);
        }
        exit();
    }

    // --- Eliminar imagen ---
    if ($accion === 'eliminar_imagen') {
        $id_imagen = intval($_POST['id_imagen'] ?? 0);
        if ($id_imagen <= 0) {
            echo json_encode(['exito' => false, 'error' => 'ID de imagen inválido']);
            exit();
        }

        $stmt = $conexion->prepare("SELECT ruta_imagen FROM producto_imagenes WHERE id_imagen = ?");
        $stmt->bind_param("i", $id_imagen);
        $stmt->execute();
        $img = $stmt->get_result()->fetch_assoc();

        if ($img) {
            $rutaCompleta = __DIR__ . '/../' . $img['ruta_imagen'];
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }
            $stmtDel = $conexion->prepare("DELETE FROM producto_imagenes WHERE id_imagen = ?");
            $stmtDel->bind_param("i", $id_imagen);
            $stmtDel->execute();
            echo json_encode(['exito' => true, 'mensaje' => 'Imagen eliminada']);
        } else {
            echo json_encode(['exito' => false, 'error' => 'Imagen no encontrada']);
        }
        exit();
    }

    echo json_encode(['exito' => false, 'error' => 'Acción POST no válida']);
    exit();
}

echo json_encode(['exito' => false, 'error' => 'Método no permitido']);

// ===================== FUNCIONES =====================
function subirMultiplesImagenes($conexion, $id_producto, $archivos) {
    $dirDestino = __DIR__ . '/../img/productos/';
    if (!is_dir($dirDestino)) {
        mkdir($dirDestino, 0777, true);
    }

    // Obtener el orden máximo actual
    $stmtOrden = $conexion->prepare("SELECT COALESCE(MAX(orden), 0) AS max_orden FROM producto_imagenes WHERE id_producto = ?");
    $stmtOrden->bind_param("i", $id_producto);
    $stmtOrden->execute();
    $orden = $stmtOrden->get_result()->fetch_assoc()['max_orden'];

    $totalArchivos = is_array($archivos['name']) ? count($archivos['name']) : 1;

    for ($i = 0; $i < $totalArchivos; $i++) {
        $error = is_array($archivos['error']) ? $archivos['error'][$i] : $archivos['error'];
        if ($error !== UPLOAD_ERR_OK) continue;

        $tmpName = is_array($archivos['tmp_name']) ? $archivos['tmp_name'][$i] : $archivos['tmp_name'];
        $fileName = is_array($archivos['name']) ? $archivos['name'][$i] : $archivos['name'];

        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $permitidas)) continue;

        $orden++;
        $nombreArchivo = 'prod_' . $id_producto . '_' . time() . '_' . $i . '.' . $extension;
        $rutaDestino = $dirDestino . $nombreArchivo;
        $rutaRelativa = 'img/productos/' . $nombreArchivo;

        if (move_uploaded_file($tmpName, $rutaDestino)) {
            $stmt = $conexion->prepare("INSERT INTO producto_imagenes (id_producto, ruta_imagen, orden) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $id_producto, $rutaRelativa, $orden);
            $stmt->execute();
        }
    }
}
