<?php
/**
 * API del Carrito de Compras
 * Maneja agregar, listar, actualizar cantidad y eliminar items del carrito
 * Funciona con sesión (cliente autenticado) usando las tablas carritos + carrito_detalle
 */
header('Content-Type: application/json; charset=utf-8');
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

// Verificar autenticación
if (!usuarioAutenticado()) {
    echo json_encode(['exito' => false, 'error' => 'Debes iniciar sesión para usar el carrito']);
    exit();
}

$datosUsuario = obtenerDatosUsuario();
// Obtener id_cliente a partir del usuario autenticado
$stmtCli = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ? AND estado = 'activo'");
$stmtCli->bind_param("i", $_SESSION['id_usuario']);
$stmtCli->execute();
$resCli = $stmtCli->get_result()->fetch_assoc();

if (!$resCli) {
    echo json_encode(['exito' => false, 'error' => 'Cliente no encontrado']);
    exit();
}
$id_cliente = (int)$resCli['id_cliente'];

$metodo = $_SERVER['REQUEST_METHOD'];
$accion = $_REQUEST['accion'] ?? '';

// ============ Función auxiliar: obtener o crear carrito activo ============
function obtenerCarritoActivo($conexion, $id_cliente) {
    $stmt = $conexion->prepare("SELECT id_carrito FROM carritos WHERE id_cliente = ? AND estado = 'activo' LIMIT 1");
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res) return (int)$res['id_carrito'];

    // Crear nuevo carrito
    $stmt2 = $conexion->prepare("INSERT INTO carritos (id_cliente, estado) VALUES (?, 'activo')");
    $stmt2->bind_param("i", $id_cliente);
    $stmt2->execute();
    return (int)$stmt2->insert_id;
}

// ============ Función auxiliar: obtener items del carrito con info completa ============
function obtenerItemsCarrito($conexion, $id_carrito) {
    $sql = "SELECT cd.id_carrito_detalle, cd.id_producto, cd.cantidad, cd.precio_unitario, cd.subtotal,
                   p.nombre, p.precio, p.precio_descuento, p.en_oferta, p.stock,
                   COALESCE(c.tasa_impuesto, cp.tasa_impuesto, 0) AS tasa_impuesto,
                   (SELECT ri.ruta_imagen FROM producto_imagenes ri WHERE ri.id_producto = p.id_producto ORDER BY ri.orden ASC LIMIT 1) AS imagen
            FROM carrito_detalle cd
            INNER JOIN productos p ON cd.id_producto = p.id_producto
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            LEFT JOIN categorias cp ON c.id_padre = cp.id_categoria
            WHERE cd.id_carrito = ?
            ORDER BY cd.id_carrito_detalle ASC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id_carrito);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $items = [];
    $subtotal = 0;
    $impuesto_total = 0;

    while ($fila = $resultado->fetch_assoc()) {
        $precio = ($fila['en_oferta'] && $fila['precio_descuento']) ? (float)$fila['precio_descuento'] : (float)$fila['precio'];
        $sub = $precio * (int)$fila['cantidad'];
        $tasa = (float)$fila['tasa_impuesto'];
        $impuesto = $sub * ($tasa / 100);

        $items[] = [
            'id_carrito_detalle' => (int)$fila['id_carrito_detalle'],
            'id_producto' => (int)$fila['id_producto'],
            'nombre' => $fila['nombre'],
            'cantidad' => (int)$fila['cantidad'],
            'precio_unitario' => $precio,
            'precio_original' => (float)$fila['precio'],
            'precio_descuento' => $fila['precio_descuento'] ? (float)$fila['precio_descuento'] : null,
            'en_oferta' => (int)$fila['en_oferta'],
            'stock' => (int)$fila['stock'],
            'tasa_impuesto' => $tasa,
            'impuesto' => round($impuesto, 2),
            'subtotal' => round($sub, 2),
            'imagen' => $fila['imagen']
        ];

        $subtotal += $sub;
        $impuesto_total += $impuesto;
    }

    return [
        'items' => $items,
        'total_items' => count($items),
        'total_cantidad' => array_sum(array_column($items, 'cantidad')),
        'subtotal' => round($subtotal, 2),
        'impuesto_total' => round($impuesto_total, 2),
        'total' => round($subtotal + $impuesto_total, 2)
    ];
}

// ===================== LISTAR CARRITO =====================
if ($accion === 'listar') {
    $id_carrito = obtenerCarritoActivo($conexion, $id_cliente);
    $carrito = obtenerItemsCarrito($conexion, $id_carrito);
    echo json_encode(['exito' => true, 'carrito' => $carrito]);
    exit();
}

// ===================== AGREGAR PRODUCTO =====================
if ($accion === 'agregar' && $metodo === 'POST') {
    $id_producto = (int)($_POST['id_producto'] ?? 0);
    $cantidad = (int)($_POST['cantidad'] ?? 1);

    if ($id_producto <= 0) {
        echo json_encode(['exito' => false, 'error' => 'Producto inválido']);
        exit();
    }
    if ($cantidad < 1) $cantidad = 1;

    // Verificar producto existe y está disponible
    $stmtProd = $conexion->prepare("SELECT id_producto, precio, precio_descuento, en_oferta, stock FROM productos WHERE id_producto = ? AND estado = 'disponible'");
    $stmtProd->bind_param("i", $id_producto);
    $stmtProd->execute();
    $producto = $stmtProd->get_result()->fetch_assoc();

    if (!$producto) {
        echo json_encode(['exito' => false, 'error' => 'Producto no disponible']);
        exit();
    }

    $precio = ($producto['en_oferta'] && $producto['precio_descuento']) ? (float)$producto['precio_descuento'] : (float)$producto['precio'];
    $id_carrito = obtenerCarritoActivo($conexion, $id_cliente);

    // Ver si ya está en el carrito
    $stmtExiste = $conexion->prepare("SELECT id_carrito_detalle, cantidad FROM carrito_detalle WHERE id_carrito = ? AND id_producto = ?");
    $stmtExiste->bind_param("ii", $id_carrito, $id_producto);
    $stmtExiste->execute();
    $existente = $stmtExiste->get_result()->fetch_assoc();

    if ($existente) {
        // Actualizar cantidad
        $nuevaCantidad = (int)$existente['cantidad'] + $cantidad;
        if ($nuevaCantidad > (int)$producto['stock']) {
            $nuevaCantidad = (int)$producto['stock'];
        }
        $nuevoSubtotal = $precio * $nuevaCantidad;

        $stmtUpd = $conexion->prepare("UPDATE carrito_detalle SET cantidad = ?, precio_unitario = ?, subtotal = ? WHERE id_carrito_detalle = ?");
        $stmtUpd->bind_param("iddi", $nuevaCantidad, $precio, $nuevoSubtotal, $existente['id_carrito_detalle']);
        $stmtUpd->execute();
    } else {
        // Insertar nuevo item
        if ($cantidad > (int)$producto['stock']) {
            $cantidad = (int)$producto['stock'];
        }
        $subtotal = $precio * $cantidad;

        $stmtIns = $conexion->prepare("INSERT INTO carrito_detalle (id_carrito, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        $stmtIns->bind_param("iiidd", $id_carrito, $id_producto, $cantidad, $precio, $subtotal);
        $stmtIns->execute();
    }

    $carrito = obtenerItemsCarrito($conexion, $id_carrito);
    echo json_encode(['exito' => true, 'mensaje' => 'Producto agregado al carrito', 'carrito' => $carrito]);
    exit();
}

// ===================== ACTUALIZAR CANTIDAD =====================
if ($accion === 'actualizar' && $metodo === 'POST') {
    $id_carrito_detalle = (int)($_POST['id_carrito_detalle'] ?? 0);
    $cantidad = (int)($_POST['cantidad'] ?? 1);

    if ($id_carrito_detalle <= 0) {
        echo json_encode(['exito' => false, 'error' => 'Item inválido']);
        exit();
    }

    $id_carrito = obtenerCarritoActivo($conexion, $id_cliente);

    // Verificar que el item pertenece al carrito del cliente
    $stmtCheck = $conexion->prepare("SELECT cd.id_carrito_detalle, cd.id_producto, p.precio, p.precio_descuento, p.en_oferta, p.stock
        FROM carrito_detalle cd
        INNER JOIN productos p ON cd.id_producto = p.id_producto
        WHERE cd.id_carrito_detalle = ? AND cd.id_carrito = ?");
    $stmtCheck->bind_param("ii", $id_carrito_detalle, $id_carrito);
    $stmtCheck->execute();
    $item = $stmtCheck->get_result()->fetch_assoc();

    if (!$item) {
        echo json_encode(['exito' => false, 'error' => 'Item no encontrado en tu carrito']);
        exit();
    }

    if ($cantidad < 1) {
        // Eliminar si cantidad es 0 o menos
        $stmtDel = $conexion->prepare("DELETE FROM carrito_detalle WHERE id_carrito_detalle = ?");
        $stmtDel->bind_param("i", $id_carrito_detalle);
        $stmtDel->execute();
    } else {
        if ($cantidad > (int)$item['stock']) {
            $cantidad = (int)$item['stock'];
        }
        $precio = ($item['en_oferta'] && $item['precio_descuento']) ? (float)$item['precio_descuento'] : (float)$item['precio'];
        $subtotal = $precio * $cantidad;

        $stmtUpd = $conexion->prepare("UPDATE carrito_detalle SET cantidad = ?, precio_unitario = ?, subtotal = ? WHERE id_carrito_detalle = ?");
        $stmtUpd->bind_param("iddi", $cantidad, $precio, $subtotal, $id_carrito_detalle);
        $stmtUpd->execute();
    }

    $carrito = obtenerItemsCarrito($conexion, $id_carrito);
    echo json_encode(['exito' => true, 'mensaje' => 'Carrito actualizado', 'carrito' => $carrito]);
    exit();
}

// ===================== ELIMINAR ITEM =====================
if ($accion === 'eliminar' && $metodo === 'POST') {
    $id_carrito_detalle = (int)($_POST['id_carrito_detalle'] ?? 0);

    if ($id_carrito_detalle <= 0) {
        echo json_encode(['exito' => false, 'error' => 'Item inválido']);
        exit();
    }

    $id_carrito = obtenerCarritoActivo($conexion, $id_cliente);

    $stmtDel = $conexion->prepare("DELETE FROM carrito_detalle WHERE id_carrito_detalle = ? AND id_carrito = ?");
    $stmtDel->bind_param("ii", $id_carrito_detalle, $id_carrito);
    $stmtDel->execute();

    if ($stmtDel->affected_rows > 0) {
        $carrito = obtenerItemsCarrito($conexion, $id_carrito);
        echo json_encode(['exito' => true, 'mensaje' => 'Producto eliminado del carrito', 'carrito' => $carrito]);
    } else {
        echo json_encode(['exito' => false, 'error' => 'No se pudo eliminar']);
    }
    exit();
}

// ===================== VACIAR CARRITO =====================
if ($accion === 'vaciar' && $metodo === 'POST') {
    $id_carrito = obtenerCarritoActivo($conexion, $id_cliente);
    $stmtVaciar = $conexion->prepare("DELETE FROM carrito_detalle WHERE id_carrito = ?");
    $stmtVaciar->bind_param("i", $id_carrito);
    $stmtVaciar->execute();

    echo json_encode(['exito' => true, 'mensaje' => 'Carrito vaciado', 'carrito' => obtenerItemsCarrito($conexion, $id_carrito)]);
    exit();
}

// ===================== CONTAR ITEMS (rápido) =====================
if ($accion === 'contar') {
    $id_carrito = obtenerCarritoActivo($conexion, $id_cliente);
    $stmt = $conexion->prepare("SELECT COALESCE(SUM(cantidad), 0) AS total FROM carrito_detalle WHERE id_carrito = ?");
    $stmt->bind_param("i", $id_carrito);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    echo json_encode(['exito' => true, 'total' => (int)$res['total']]);
    exit();
}

echo json_encode(['exito' => false, 'error' => 'Acción no reconocida']);
