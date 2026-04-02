<?php
/*
=====================================================
GUARDAR COMPRA EN LA BASE DE DATOS
=====================================================
Recibe datos por AJAX desde admin_compras.php
1. Crea registro en tabla "compras"
2. Inserta detalle en "detalle_compra"
3. Actualiza stock del producto
4. Retorna respuesta JSON
=====================================================
*/

require_once '../core/conexion.php';
require_once '../core/csrf.php';

header('Content-Type: application/json; charset=utf-8');
validarCSRFMiddleware();

// Validar datos recibidos
if (!isset($_POST['producto_id'], $_POST['cantidad'], $_POST['proveedor'], $_POST['precio'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'Datos incompletos']);
    exit;
}

$producto_id = intval($_POST['producto_id']);
$cantidad    = intval($_POST['cantidad']);
$proveedor   = trim($_POST['proveedor']);
$precio      = floatval($_POST['precio']);

if ($producto_id <= 0 || $cantidad <= 0 || $proveedor === '') {
    echo json_encode(['exito' => false, 'mensaje' => 'Datos inválidos']);
    exit;
}

// Iniciar transacción para garantizar integridad
$conexion->begin_transaction();

try {
    // 1. Crear registro en tabla compras
    $stmt = $conexion->prepare("INSERT INTO compras (proveedor, fecha) VALUES (?, NOW())");
    $stmt->bind_param("s", $proveedor);
    $stmt->execute();
    $compra_id = $conexion->insert_id;
    $stmt->close();

    // 2. Insertar detalle de la compra
    $stmt = $conexion->prepare("INSERT INTO detalle_compra (compra_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $compra_id, $producto_id, $cantidad, $precio);
    $stmt->execute();
    $stmt->close();

    // 3. Actualizar stock y precio_costo del producto
    $stmt = $conexion->prepare("UPDATE productos SET stock = stock + ?, precio_costo = ? WHERE id_producto = ?");
    $stmt->bind_param("idi", $cantidad, $precio, $producto_id);
    $stmt->execute();
    $stmt->close();

    $conexion->commit();

    // Obtener nombre del producto para la respuesta
    $stmt = $conexion->prepare("SELECT nombre, stock FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();
    $stmt->close();

    echo json_encode([
        'exito' => true,
        'mensaje' => 'Compra registrada correctamente',
        'datos' => [
            'compra_id'   => $compra_id,
            'producto'    => $producto['nombre'],
            'cantidad'    => $cantidad,
            'precio'      => $precio,
            'proveedor'   => $proveedor,
            'nuevo_stock' => $producto['stock']
        ]
    ]);

} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar la compra']);
}
?>