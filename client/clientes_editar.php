<?php

require_once '../core/conexion.php';

header('Content-Type: application/json');

ini_set('display_errors', 0);
error_reporting(0);

$response = ["success" => false];

try {

    if (!isset($_POST['id'], $_POST['nombre'], $_POST['correo'])) {
        throw new Exception("Datos incompletos");
    }

    $id     = intval($_POST['id']);
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);

    if ($id <= 0 || empty($nombre) || empty($correo)) {
        throw new Exception("Datos inválidos");
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $conexion->begin_transaction();

    // 1️⃣ Actualizar tabla clientes
    $stmt1 = $conexion->prepare("
        UPDATE clientes 
        SET nombre = ?
        WHERE id_usuario = ?
    ");
    $stmt1->bind_param("si", $nombre, $id);
    $stmt1->execute();

    // 2️⃣ Actualizar tabla usuarios
    $stmt2 = $conexion->prepare("
        UPDATE usuarios 
        SET nombre = ?, correo = ?
        WHERE id_usuario = ?
    ");
    $stmt2->bind_param("ssi", $nombre, $correo, $id);
    $stmt2->execute();

    $conexion->commit();

    $response["success"] = true;

} catch (Exception $e) {

    if ($conexion->errno) {
        $conexion->rollback();
    }

    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
exit;