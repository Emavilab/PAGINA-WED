<?php
/**
 * =====================================================
 * SISTEMA DE PROTECCIÓN CSRF (CROSS-SITE REQUEST FORGERY)
 * =====================================================
 *
 * Este archivo implementa protección contra ataques CSRF
 * usando tokens únicos por sesión.
 *
 * FUNCIONALIDADES:
 * - Generar tokens CSRF únicos
 * - Validar tokens CSRF
 * - Regenerar tokens periódicamente
 * - Compatibilidad con formularios y AJAX
 */

// Asegurar que la sesión esté iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Inicializar o obtener el token CSRF de la sesión
 *
 * @return string Token CSRF de la sesión única
 */
function obtenerTokenCSRF() {
    // Si aún no existe token en la sesión, generarlo
    if (!isset($_SESSION['csrf_token'])) {
        generarTokenCSRF();
    }

    return $_SESSION['csrf_token'];
}

/**
 * Generar un nuevo token CSRF y almacenarlo en la sesión
 *
 * @return string Token CSRF generado
 */
function generarTokenCSRF() {
    // Generar token aleatorio de 32 bytes (64 caracteres hex)
    $token = bin2hex(random_bytes(32));

    // Guardar en sesión
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();

    return $token;
}

/**
 * Validar un token CSRF recibido del cliente
 *
 * Compara el token enviado con el almacenado en la sesión.
 * También puede validar expiracion del token.
 *
 * @param string $token Token a validar
 * @param int $maxAge Edad máxima del token en segundos (0 = sin límite)
 * @return bool True si es válido, false si no
 */
function validarTokenCSRF($token, $maxAge = 0) {
    // Verificar que exista token en sesión
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }

    // Comparar tokens usando hash_equals() para evitar timing attacks
    $esValido = hash_equals($_SESSION['csrf_token'], $token ?? '');

    if (!$esValido) {
        error_log("Token CSRF inválido: token recibido no coincide con sesión");
        return false;
    }

    // Si se especifica edad máxima, validar
    if ($maxAge > 0 && isset($_SESSION['csrf_token_time'])) {
        $edad = time() - $_SESSION['csrf_token_time'];
        if ($edad > $maxAge) {
            error_log("Token CSRF expirado: edad=$edad segundos, máximo=$maxAge");
            // Regenerar token expirado para siguiente solicitud
            generarTokenCSRF();
            return false;
        }
    }

    return true;
}

/**
 * Validar token CSRF desde $_POST o $_REQUEST
 *
 * Atajo para validar directamente desde la solicitud HTTP
 *
 * @param string $fieldName Nombre del campo donde se envía el token (default: csrf_token)
 * @return bool True si es válido
 */
function validarCSRFRequest($fieldName = 'csrf_token') {
    $token = $_POST[$fieldName] ?? $_REQUEST[$fieldName] ?? '';
    return validarTokenCSRF($token);
}

/**
 * Generar campo input HTML con token CSRF
 *
 * Útil para incluir en formularios HTML
 *
 * @param string $fieldName Nombre del campo (default: csrf_token)
 * @return string HTML del input hidden
 */
function campoTokenCSRF($fieldName = 'csrf_token') {
    $token = obtenerTokenCSRF();
    return '<input type="hidden" name="' . htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Incluir token CSRF en respuesta JSON
 *
 * Útil para AJAX - devolver el token en la respuesta para renovarlo
 *
 * @return array Array con el token CSRF actualizado
 */
function tokenCSRFJSON() {
    return [
        'csrf_token' => obtenerTokenCSRF()
    ];
}

/**
 * Middleware para validar CSRF en solicitudes POST (para APIs)
 *
 * Llamar al inicio de APIs que aceptan POST
 * Devuelve JSON con error si token no es válido
 *
 * @param string $fieldName Campo donde se envía el token
 * @return void Termina ejecución si token inválido
 */
function validarCSRFMiddleware($fieldName = 'csrf_token') {
    // Solo validar en solicitudes POST/PUT/DELETE
    if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT', 'DELETE'])) {
        return;
    }

    // Obtener token de diferentes fuentes
    $token = $_POST[$fieldName] 
        ?? $_REQUEST[$fieldName] 
        ?? (isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : null)
        ?? null;

    if (!validarTokenCSRF($token)) {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(403);
        echo json_encode([
            'exito' => false,
            'mensaje' => 'Token CSRF inválido. Solicitud rechazada.',
            'csrf_token' => obtenerTokenCSRF() // Enviar nuevo token
        ]);
        exit();
    }
}

/**
 * Regenerar token CSRF después de autenticación
 *
 * IMPORTANTE: Llamar después de login exitoso para evitar
 * session fixation attacks
 *
 * @return string Nuevo token generado
 */
function regenerarTokenCSRFDespuesLogin() {
    // Destruir token antiguo
    unset($_SESSION['csrf_token']);
    unset($_SESSION['csrf_token_time']);

    // Generar nuevo token
    return generarTokenCSRF();
}

?>
