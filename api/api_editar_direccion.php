<?php
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

header('Content-Type: application/json; charset=utf-8');

if (!usuarioAutenticado()) {
    echo json_encode([
        "success" => false,
        "message" => "Usuario no autenticado"
    ]);
    exit();
}

$id_usuario = obtenerIdUsuario();

/* 🔥 OBTENER id_cliente REAL */
$stmtCliente = $conexion->prepare("SELECT id_cliente FROM clientes WHERE id_usuario = ?");
$stmtCliente->bind_param("i", $id_usuario);
$stmtCliente->execute();
$resultCliente = $stmtCliente->get_result();
$cliente = $resultCliente->fetch_assoc();
$stmtCliente->close();

if (!$cliente) {
    echo json_encode([
        "success" => false,
        "message" => "Cliente no encontrado"
    ]);
    exit();
}

$id_cliente = $cliente['id_cliente'];

/* =============================== */

$id_direccion = $_POST['id_direccion'] ?? null;
$direccion = trim($_POST['direccion'] ?? '');
$ciudad = trim($_POST['ciudad'] ?? '');
$codigo_postal = trim($_POST['codigo_postal'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$referencia = trim($_POST['referencia'] ?? '');

if (!$id_direccion || !$direccion || !$ciudad) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit();
}

try {

    $stmt = $conexion->prepare("
        UPDATE direcciones_cliente
        SET direccion = ?, ciudad = ?, codigo_postal = ?, telefono = ?, referencia = ?
        WHERE id_direccion = ? AND id_cliente = ?
    ");

    $stmt->bind_param(
        "sssssii",
        $direccion,
        $ciudad,
        $codigo_postal,
        $telefono,
        $referencia,
        $id_direccion,
        $id_cliente   // 🔥 AHORA SÍ CORRECTO
    );

    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "No se pudo actualizar la dirección"
        ]);
        exit();
    }

    echo json_encode([
        "success" => true,
        "message" => "Dirección actualizada correctamente"
    ]);

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Error al actualizar"
    ]);
}