<?php
require_once '../core/conexion.php';

header('Content-Type: application/json');

$id = $_GET['id'];

$stmt = $conexion->prepare("
    SELECT 
        clientes.id_usuario,
        clientes.nombre,
        clientes.estado,
        usuarios.correo
    FROM clientes
    INNER JOIN usuarios 
        ON clientes.id_usuario = usuarios.id_usuario
    WHERE clientes.id_usuario = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();

echo json_encode($resultado->fetch_assoc());