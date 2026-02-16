<?php
include(__DIR__ . "/../core/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre   = $_POST['nombre'] ?? '';
    $correo   = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $asunto   = $_POST['asunto'] ?? '';
    $mensaje  = $_POST['mensaje'] ?? '';

    $stmt = $conexion->prepare("INSERT INTO mensajes_contacto 
        (nombre, correo, telefono, asunto, mensaje, estado) 
        VALUES (?, ?, ?, ?, ?, 'nuevo')");

    if (!$stmt) {
        echo "error";
        exit;
    }

    $stmt->bind_param("sssss", $nombre, $correo, $telefono, $asunto, $mensaje);

    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "error";
    }

    $stmt->close();
    $conexion->close();
}
?>
