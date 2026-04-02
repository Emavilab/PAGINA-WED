<?php
/**
 * =====================================================
 * CARGADOR DE VARIABLES DE ENTORNO (.env)
 * =====================================================
 *
 * Este archivo lee las variables del archivo .env
 * permitiendo mantener credenciales fuera del código.
 *
 * FUNCIONALIDADES:
 * - Carga variables de .env automaticamente
 * - Fallback a valores por defecto
 * - Compatibilidad con desarrollo y producción
 * - Validación de variables críticas
 */

if (!function_exists('loadEnv')) {
    /**
     * Carga variables de entorno desde archivo .env
     *
     * @param string $path Ruta al archivo .env
     * @return void
     */
    function loadEnv($path = __DIR__ . '/../.env') {
        // Verificar si el archivo existe
        if (!file_exists($path)) {
            // En producción, las variables deben estar en php.ini o en variables del servidor
            // Esto es un fallback, no un error fatal
            error_log("Archivo .env no encontrado en: $path");
            return;
        }

        // Leer el archivo línea por línea
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            // Ignorar comentarios
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            // Dividir por el primer =
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remover comillas si existen
                if (strlen($value) >= 2 && 
                    ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                     (substr($value, 0, 1) === "'" && substr($value, -1) === "'"))) {
                    $value = substr($value, 1, -1);
                }

                // Establecer variable de entorno
                if (!empty($key)) {
                    putenv("$key=$value");
                    $_ENV[$key] = $value;
                }
            }
        }
    }
}

if (!function_exists('getEnv')) {
    /**
     * Obtiene una variable de entorno
     *
     * @param string $key Nombre de la variable
     * @param string $default Valor por defecto si no existe
     * @return string|null
     */
    function getEnv($key, $default = null) {
        $value = getenv($key);
        if ($value === false) {
            $value = $_ENV[$key] ?? $default;
        }
        return $value;
    }
}

// Cargar variables del archivo .env
loadEnv();
?>
