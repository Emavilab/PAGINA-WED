<?php
require_once __DIR__ . '/../core/conexion.php';
header('Content-Type: application/json; charset=utf-8');

$res = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");

if ($res && mysqli_num_rows($res) > 0) {
    $config = mysqli_fetch_assoc($res);
    
    // Decodificar redes sociales
    $config['redes'] = !empty($config['redes_sociales']) ? json_decode($config['redes_sociales'], true) : [];
    
    echo json_encode([
        'success' => true,
        'data' => $config
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se encontró configuración'
    ]);
}
