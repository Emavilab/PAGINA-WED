<?php
require_once '../core/conexion.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];

try {

    $conexion->begin_transaction();

    $stmt1 = $conexion->prepare("DELETE FROM clientes WHERE id_usuario = ?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();

    $stmt2 = $conexion->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    $conexion->commit();

    echo json_encode(["success" => true]);

} catch (Exception $e) {

    $conexion->rollback();

    echo json_encode([
        "success" => false,
        "message" => "Error al eliminar"
    ]);
}