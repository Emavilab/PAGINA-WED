<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Incluir conexión
        include(__DIR__ . "/../core/conexion.php");
        
        // Validar que la conexión existe
        if (!isset($conexion)) {
            echo json_encode(['exito' => false, 'error' => 'Conexión a BD no disponible']);
            exit();
        }

        // Obtener datos del formulario
        $nombre   = $_POST['nombre'] ?? '';
        $correo   = $_POST['correo'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $asunto   = $_POST['asunto'] ?? '';
        $mensaje  = $_POST['mensaje'] ?? '';

        // Validaciones básicas
        if (empty($nombre) || empty($correo) || empty($asunto) || empty($mensaje)) {
            echo json_encode(['exito' => false, 'error' => 'Faltan campos requeridos']);
            exit();
        }

        // Preparar statement
        $stmt = $conexion->prepare("INSERT INTO mensajes_contacto 
            (nombre, correo, telefono, asunto, mensaje, estado) 
            VALUES (?, ?, ?, ?, ?, 'nuevo')");

        if (!$stmt) {
            echo json_encode(['exito' => false, 'error' => 'Error en prepare: ' . $conexion->error]);
            exit();
        }

        // Bind parameters
        $stmt->bind_param("sssss", $nombre, $correo, $telefono, $asunto, $mensaje);

        // Ejecutar
        if ($stmt->execute()) {
            echo "ok";
        } else {
            echo json_encode(['exito' => false, 'error' => 'Error en execute: ' . $stmt->error]);
        }

        $stmt->close();
        
    } catch (Exception $e) {
        echo json_encode(['exito' => false, 'error' => 'Exception: ' . $e->getMessage()]);
    }
}
?>
