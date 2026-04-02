<?php
require 'core/conexion.php';

echo "=== VERIFICAR CONTRASEÑAS EN BD ===\n\n";

$result = $conexion->query("SELECT id_usuario, nombre, correo, contraseña, id_rol FROM usuarios");
while($row = $result->fetch_assoc()) {
    echo "Usuario: " . $row['correo'] . "\n";
    echo "  - Nombre: " . $row['nombre'] . "\n";
    echo "  - id_rol: " . $row['id_rol'] . "\n";
    echo "  - contraseña (primeros 30 chars): " . substr($row['contraseña'], 0, 30) . "...\n";
    
    // Verificar si es válida con password_verify
    $test1 = password_verify('demo123', $row['contraseña']);
    $test2 = password_verify('password123', $row['contraseña']);
    $test3 = ($row['contraseña'] === 'demo123');
    $test4 = ($row['contraseña'] === 'password123');
    
    echo "  - Verifica con demo123: " . ($test1 ? "SÍ" : "NO") . "\n";
    echo "  - Verifica con password123: " . ($test2 ? "SÍ" : "NO") . "\n";
    echo "  - Es texto plano 'demo123': " . ($test3 ? "SÍ" : "NO") . "\n";
    echo "  - Es texto plano 'password123': " . ($test4 ? "SÍ" : "NO") . "\n";
    echo "\n";
}

?>
