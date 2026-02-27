<?php
/**
 * Script para crear la tabla header_footer
 * Accede a: http://localhost/PAGINA-WED/setup_header_footer.php
 */

require_once 'core/conexion.php';

$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `header_footer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` enum('header','footer') NOT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `contenido` longtext NOT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `tipo` (`tipo`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `header_footer` (`tipo`, `contenido`, `estado`) 
VALUES 
('header', '<nav class="bg-white shadow-md"><div class="container mx-auto px-4 py-4"><div class="flex justify-between items-center"><h1 class="text-2xl font-bold">Mi Tienda</h1><ul class="hidden md:flex gap-6"><li><a href="/" class="hover:text-cyan-600">Inicio</a></li><li><a href="/categoria" class="hover:text-cyan-600">Productos</a></li><li><a href="/contacto" class="hover:text-cyan-600">Contacto</a></li></ul></div></div></nav>', 'activo'),
('footer', '<footer class="bg-gray-800 text-white py-8 mt-12"><div class="container mx-auto px-4"><div class="grid grid-cols-1 md:grid-cols-3 gap-8"><div><h3 class="font-bold mb-4">Mi Tienda</h3><p class="text-gray-400">Somos tu mejor opción en línea.</p></div><div><h3 class="font-bold mb-4">Enlaces</h3><ul class="text-gray-400"><li><a href="/">Inicio</a></li><li><a href="/productos">Productos</a></li><li><a href="/contacto">Contacto</a></li></ul></div><div><h3 class="font-bold mb-4">Contacto</h3><p class="text-gray-400">Email: info@mitienda.com</p><p class="text-gray-400">Teléfono: +123 4567890</p></div></div><div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400"><p>&copy; 2024 Mi Tienda. Todos los derechos reservados.</p></div></div></footer>', 'activo')
ON DUPLICATE KEY UPDATE tipo=tipo;
SQL;

// Dividir en múltiples querys si es necesario
$queries = array_filter(array_map('trim', explode(';', $sql)));
$success = true;
$messages = [];

foreach ($queries as $query) {
    if (!empty($query)) {
        if ($conexion->query($query)) {
            $messages[] = "✓ Query ejecutada correctamente";
        } else {
            $success = false;
            $messages[] = "✗ Error: " . $conexion->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Header/Footer</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto py-12 px-4">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 rounded-full flex items-center justify-center" style="background-color: <?php echo $success ? '#10b981' : '#ef4444'; ?>">
                    <i class="fas fa-<?php echo $success ? 'check' : 'times'; ?> text-2xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <?php echo $success ? 'Tabla Creada Exitosamente' : 'Error al Crear Tabla'; ?>
                </h1>
            </div>

            <div class="bg-gray-50 rounded-lg p-4 mb-6 max-h-80 overflow-y-auto">
                <?php foreach ($messages as $msg): ?>
                    <p class="text-gray-700 mb-2 font-mono text-sm"><?php echo $msg; ?></p>
                <?php endforeach; ?>
            </div>

            <div class="space-y-3 text-gray-700">
                <h2 class="font-bold text-lg">Próximos pasos:</h2>
                <ol class="list-decimal list-inside space-y-2">
                    <li>Accede al panel de Configuración</li>
                    <li>Haz clic en el tab "Header y Footer"</li>
                    <li>Edita el contenido HTML según necesites</li>
                    <li>Guarda los cambios</li>
                    <li>El header y footer se mostrarán automáticamente en tu sitio</li>
                </ol>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-200">
                <a href="admin/configuracion.php" class="inline-block bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg font-bold transition">
                    <i class="fas fa-cog mr-2"></i> Ir a Configuración
                </a>
                <a href="javascript:history.back()" class="inline-block ml-3 bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-bold transition">
                    <i class="fas fa-arrow-left mr-2"></i> Atrás
                </a>
            </div>
        </div>
    </div>
</body>
</html>
