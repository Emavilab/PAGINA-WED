<?php
/**
 * Endpoint para Verificar Estado de Sesión
 * Responde si la sesión sigue activa
 */

require_once 'sesiones.php';

header('Content-Type: application/json');

$respuesta = [
    'activa' => usuarioAutenticado(),
    'tiempo' => time()
];

echo json_encode($respuesta);
exit();
?>
