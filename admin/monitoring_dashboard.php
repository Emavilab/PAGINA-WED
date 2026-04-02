<?php
/**
 * DASHBOARD DE MONITOREO Y ALERTAS
 * Página para visualizar todas las alertas de seguridad del sistema
 * 
 * Solo accesible para Administrador (rol 1)
 */

require_once '../core/conexion.php';
require_once '../core/sesiones.php';
require_once '../core/monitoring_alerts.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();

// Verificar autenticación
if (!usuarioAutenticado() || $_SESSION['id_rol'] != 1) {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'Acceso denegado']);
    exit();
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    if ($accion === 'marcar_leida') {
        $alerta_id = intval($_POST['alerta_id'] ?? 0);
        if ($alerta_id > 0) {
            marcarAlertaLeida($alerta_id);
        }
        header('Location: monitoring_dashboard.php');
        exit();
    }
}

// Obtener datos
$alertas = obtenerTodasLasAlertas();
$stats = obtenerEstadisticasAlertas();
$alertas_sin_leer = $stats['sin_leer'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Monitoreo - <?php echo htmlspecialchars($_SESSION['nombre'] ?? 'Admin'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .alerta-critica { border-left: 4px solid #d32f2f; background-color: #ffebee; }
        .alerta-alta { border-left: 4px solid #f57c00; background-color: #fff3e0; }
        .alerta-media { border-left: 4px solid #fbc02d; background-color: #fffde7; }
        .alerta-baja { border-left: 4px solid #388e3c; background-color: #f1f8e9; }
        
        .badge-critica { background-color: #d32f2f; color: white; }
        .badge-alta { background-color: #f57c00; color: white; }
        .badge-media { background-color: #fbc02d; color: #333; }
        .badge-baja { background-color: #388e3c; color: white; }
        
        .stat-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900">🛡️ Centro de Monitoreo</h1>
                    <p class="text-gray-600 mt-2">Sistema de detección de amenazas y alertas automáticas</p>
                </div>
                <a href="Dashboard.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    ← Volver al Panel
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="stat-box rounded-lg p-6 text-white">
                <div class="text-3xl font-bold"><?php echo $stats['total'] ?? 0; ?></div>
                <div class="text-sm mt-2">Total de Alertas</div>
            </div>
            
            <div class="bg-red-100 rounded-lg p-6 border-l-4 border-red-600">
                <div class="text-3xl font-bold text-red-600"><?php echo $stats['criticas'] ?? 0; ?></div>
                <div class="text-sm text-gray-700 mt-2">Alertas Críticas</div>
            </div>
            
            <div class="bg-orange-100 rounded-lg p-6 border-l-4 border-orange-600">
                <div class="text-3xl font-bold text-orange-600"><?php echo $stats['altas'] ?? 0; ?></div>
                <div class="text-sm text-gray-700 mt-2">Alertas Altas</div>
            </div>
            
            <div class="bg-blue-100 rounded-lg p-6 border-l-4 border-blue-600">
                <div class="text-3xl font-bold text-blue-600"><?php echo $alertas_sin_leer; ?></div>
                <div class="text-sm text-gray-700 mt-2">Sin Leer</div>
            </div>
        </div>

        <!-- Alertas -->
        <div class="bg-white rounded-lg shadow-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Registro de Alertas</h2>
            </div>

            <?php if (empty($alertas)): ?>
                <div class="p-8 text-center text-gray-500">
                    <p class="text-lg">✅ No hay alertas de seguridad</p>
                    <p class="text-sm mt-2">El sistema está funcionando correctamente</p>
                </div>
            <?php else: ?>
                <div class="divide-y">
                    <?php foreach ($alertas as $alerta): ?>
                        <div class="alerta-<?php echo strtolower($alerta['severidad']); ?> p-6 hover:bg-opacity-75 transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-4">
                                        <span class="badge-<?php echo strtolower($alerta['severidad']); ?> px-4 py-1 rounded-full text-sm font-bold">
                                            <?php echo $alerta['severidad']; ?>
                                        </span>
                                        <h3 class="text-lg font-bold text-gray-900"><?php echo htmlspecialchars($alerta['titulo']); ?></h3>
                                        <?php if (!$alerta['leida']): ?>
                                            <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs font-bold">NUEVA</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-gray-700 mt-3"><?php echo htmlspecialchars($alerta['mensaje']); ?></p>
                                    <p class="text-gray-500 text-sm mt-2">
                                        <strong>Tipo:</strong> <?php echo htmlspecialchars($alerta['tipo']); ?> | 
                                        <strong>Detectado:</strong> <?php echo date('d/m/Y H:i:s', strtotime($alerta['tiempo'])); ?>
                                    </p>

                                    <?php if (!empty($alerta['detalles'])): ?>
                                        <details class="mt-3">
                                            <summary class="cursor-pointer text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                                📊 Ver detalles técnicos
                                            </summary>
                                            <pre class="bg-gray-100 p-4 rounded mt-2 text-xs overflow-x-auto text-gray-700">
<?php echo htmlspecialchars(json_decode($alerta['detalles'], true) ? 
    json_encode(json_decode($alerta['detalles'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : 
    $alerta['detalles']); ?>
                                            </pre>
                                        </details>
                                    <?php endif; ?>
                                </div>

                                <div class="ml-4">
                                    <?php if (!$alerta['leida']): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="accion" value="marcar_leida">
                                            <input type="hidden" name="alerta_id" value="<?php echo $alerta['id']; ?>">
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                                                ✓ Marcar como leída
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">✓ Leída</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Info adicional -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-600 p-6 rounded">
            <h3 class="text-lg font-bold text-blue-900 mb-3">ℹ️ Información del Sistema</h3>
            <ul class="text-sm text-blue-800 space-y-2">
                <li>✅ <strong>Rate Limiting:</strong> Activo - Protegiendo contra intentos fallidos</li>
                <li>✅ <strong>Audit Logging:</strong> Activo - Registrando todas las acciones</li>
                <li>✅ <strong>Monitoring:</strong> Activo - Analizando patrones de seguridad cada hora</li>
                <li>✅ <strong>Email Alerts:</strong> Activo - Enviando alertas críticas por email</li>
            </ul>
        </div>

        <!-- Recomendaciones -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-yellow-50 border-l-4 border-yellow-600 p-6 rounded">
                <h3 class="font-bold text-yellow-900 mb-2">⚠️ Recomendaciones</h3>
                <ul class="text-sm text-yellow-800 space-y-1">
                    <li>• Revisar alertas críticas inmediatamente</li>
                    <li>• Verificar IPs sospechosas en el firewall</li>
                    <li>• Revisar cambios en usuarios administrativos</li>
                    <li>• Hacer backup ante múltiples DELETEs</li>
                </ul>
            </div>

            <div class="bg-green-50 border-l-4 border-green-600 p-6 rounded">
                <h3 class="font-bold text-green-900 mb-2">✅ Acciones Disponibles</h3>
                <ul class="text-sm text-green-800 space-y-1">
                    <li>• Marcar alertas como leídas</li>
                    <li>• Ver detalles técnicos de cada alerta</li>
                    <li>• Exportar logs de auditoría (admin/audit_panel.php)</li>
                    <li>• Configurar alertas críticas (config .env)</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Auto-refresh cada 60 segundos -->
    <script>
        setTimeout(function() {
            location.reload();
        }, 60000);
    </script>
</body>
</html>
