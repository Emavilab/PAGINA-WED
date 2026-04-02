<?php
/**
 * CRON JOB - MONITOREO AUTOMÁTICO
 * Script para ejecutar cada hora vía scheduler del servidor
 * 
 * Configurar en crontab (Linux/Unix):
 * 0 * * * * php /ruta/a/admin/cron_monitoring.php
 * 
 * O en Windows Scheduler:
 * Ejecutar cada hora: php C:\xampp\htdocs\PAGINA WED\admin\cron_monitoring.php
 * 
 * Funcionalidades:
 * - Ejecuta todas las reglas de monitoreo cada hora
 * - Genera y registra alertas automáticamente
 * - Envía emails para alertas críticas
 * - Limpia alertas antiguas (>30 días)
 * - Registra ejecución en logs
 */

// Suprimir output HTML (ejecución silenciosa)
header('Content-Type: text/plain');

// No se ejecuta en navegador
if (php_sapi_name() !== 'cli' && !isset($_GET['token'])) {
    // Permitir ejecución desde URL con token seguro
    $token_esperado = md5(getEnv('CRON_TOKEN_SECRET', 'token_por_defecto'));
    $token_recibido = $_GET['token'] ?? '';
    
    if ($token_recibido !== $token_esperado) {
        http_response_code(403);
        die("Acceso denegado\n");
    }
}

// Cargar archivos necesarios
require_once dirname(__FILE__) . '/../core/conexion.php';
require_once dirname(__FILE__) . '/../core/monitoring_alerts.php';
require_once dirname(__FILE__) . '/../core/env_loader.php';

// Log file
$log_file = dirname(__FILE__) . '/../logs/cron_monitoring.log';
$log_dir = dirname($log_file);

// Crear directorio de logs si no existe
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}

// Función para escribir logs
function escribirLog($mensaje) {
    global $log_file;
    $fecha = date('Y-m-d H:i:s');
    $log = "[$fecha] $mensaje\n";
    file_put_contents($log_file, $log, FILE_APPEND);
    echo $log;
}

try {
    escribirLog("=== INICIO MONITOREO AUTOMÁTICO ===");
    
    // 1. Ejecutar todas las reglas de monitoreo
    escribirLog("Ejecutando reglas de monitoreo...");
    $alertas = ejecutarMonitoreo();
    
    if (empty($alertas)) {
        escribirLog("No se detectaron anomalías.");
    } else {
        escribirLog("Se generaron " . count($alertas) . " alerta(s):");
        foreach ($alertas as $alerta) {
            escribirLog("  - " . $alerta['tipo'] . " (" . $alerta['severidad'] . "): " . $alerta['titulo']);
        }
    }
    
    // 2. Limpiar alertas antiguas
    escribirLog("Limpiando alertas antiguas (>30 días)...");
    limpiarAlertasAntiguas();
    escribirLog("Alertas antiguas eliminadas.");
    
    // 3. Limpiar logs de auditoría antiguos
    escribirLog("Limpiando logs de auditoría antiguos...");
    limpiarLogsAntiguos();
    escribirLog("Logs de auditoría antiguos eliminados.");
    
    escribirLog("=== FIN MONITOREO EXITOSO ===");
    
} catch (Exception $e) {
    escribirLog("ERROR: " . $e->getMessage());
    escribirLog("Stack trace: " . $e->getTraceAsString());
}

// Retornar estado
http_response_code(200);
echo "Cron ejecutado exitosamente\n";
?>
