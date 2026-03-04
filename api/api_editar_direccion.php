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
        "message" => "Debes iniciar sesión para editar una dirección"
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
$id_departamento = isset($_POST['id_departamento']) ? (int) $_POST['id_departamento'] : 0;

if (!$id_direccion || !$direccion || !$ciudad) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit();
}
if ($id_departamento <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "El departamento es obligatorio"
    ]);
    exit();
}

// Validar que id_departamento exista en departamentos_envio
$stmtDep = $conexion->prepare("SELECT id_departamento FROM departamentos_envio WHERE id_departamento = ?");
$stmtDep->bind_param("i", $id_departamento);
$stmtDep->execute();
$resDep = $stmtDep->get_result();
if ($resDep->num_rows === 0) {
    $stmtDep->close();
    echo json_encode([
        "success" => false,
        "message" => "Departamento no válido"
    ]);
    exit();
}
$stmtDep->close();

try {

    $stmt = $conexion->prepare("
        UPDATE direcciones_cliente
        SET direccion = ?, ciudad = ?, codigo_postal = ?, telefono = ?, referencia = ?, id_departamento = ?
        WHERE id_direccion = ? AND id_cliente = ?
    ");

    $stmt->bind_param(
        "sssssiii",
        $direccion,
        $ciudad,
        $codigo_postal,
        $telefono,
        $referencia,
        $id_departamento,
        $id_direccion,
        $id_cliente
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