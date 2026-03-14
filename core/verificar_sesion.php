<?php
/**
 * =====================================================
 * ENDPOINT: Verificar Estado de Sesión
 * =====================================================
 *
 * Este archivo verifica si el usuario sigue autenticado
 * y devuelve un JSON indicando si la sesión está activa.
 *
 * FUNCIONALIDAD:
 * - Comprueba si la sesión del usuario está activa
 * - Devuelve un objeto JSON con:
 *     - 'activa': true/false según estado de sesión
 *     - 'tiempo': timestamp actual del servidor
 */

// =====================================================
// INCLUIR FUNCIONES DE SESIÓN
// =====================================================
require_once 'sesiones.php'; // Contiene usuarioAutenticado() y manejo de sesión

// =====================================================
// DEFINIR CABECERA DE RESPUESTA
// =====================================================
header('Content-Type: application/json'); // Indicar que la respuesta es JSON

// =====================================================
// PREPARAR RESPUESTA
// =====================================================
$respuesta = [
    'activa' => usuarioAutenticado(), // true si el usuario tiene sesión activa
    'tiempo' => time() // timestamp actual del servidor
];

// =====================================================
// ENVIAR RESPUESTA JSON
// =====================================================
echo json_encode($respuesta); // Convertir arreglo a JSON y enviarlo
exit(); // Terminar la ejecución del script
?> 