<?php
/**
 * EJEMPLO: Cómo integrar Header y Footer
 * 
 * Este es un archivo de ejemplo que muestra cómo implementar
 * el header y footer dinámico en tus páginas.
 * 
 * Puedes copiar esta estructura a tus archivos principales:
 * - index.php
 * - client/categoria.php
 * - client/productos.php
 * - pages/contactanos.php
 * etc.
 */

// 1. Requerir los archivos necesarios
require_once 'core/sesiones.php';
require_once 'core/conexion.php';
require_once 'core/header_footer_helper.php';

// 2. Tu lógica de negocio aquí (obtener productos, categorías, etc.)
$resultado_categorias = mysqli_query($conexion, "SELECT * FROM categorias WHERE estado = 'activo' LIMIT 5");

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Tienda - Inicio</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- 3. MOSTRAR HEADER DINÁMICO -->
    <?php mostrar_header_cache(); ?>
    
    <!-- Contenido Principal -->
    <main class="min-h-screen bg-gray-50">
        <div class="container mx-auto px-4 py-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-8">Bienvenido a Mi Tienda</h1>
            
            <!-- Sección de Categorías -->
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Categorías Destacadas</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php 
                    if ($resultado_categorias && mysqli_num_rows($resultado_categorias) > 0):
                        while ($categoria = mysqli_fetch_assoc($resultado_categorias)):
                    ?>
                    <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-6 cursor-pointer">
                        <div class="w-16 h-16 rounded-full bg-cyan-100 flex items-center justify-center mb-4">
                            <i class="fas fa-<?php echo $categoria['icono'] ?? 'cube'; ?> text-cyan-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($categoria['nombre']); ?></h3>
                        <p class="text-gray-600 text-sm mt-2"><?php echo htmlspecialchars($categoria['descripcion'] ?? ''); ?></p>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <div class="col-span-3 text-center text-gray-500 py-8">
                        <p>No hay categorías disponibles</p>
                    </div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Sección Informativa -->
            <section class="bg-white rounded-lg shadow-md p-8 mb-12">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">¿Por qué elegirnos?</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <i class="fas fa-truck text-4xl text-cyan-600 mb-4"></i>
                        <h3 class="font-bold text-lg mb-2">Envío Rápido</h3>
                        <p class="text-gray-600">Entrega en 24-48 horas</p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-lock text-4xl text-cyan-600 mb-4"></i>
                        <h3 class="font-bold text-lg mb-2">Pago Seguro</h3>
                        <p class="text-gray-600">Transacciones encriptadas</p>
                    </div>
                    <div class="text-center">
                        <i class="fas fa-headset text-4xl text-cyan-600 mb-4"></i>
                        <h3 class="font-bold text-lg mb-2">Soporte 24/7</h3>
                        <p class="text-gray-600">Atención al cliente siempre</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <!-- 4. MOSTRAR FOOTER DINÁMICO -->
    <?php mostrar_footer_cache(); ?>

    <!-- Scripts adicionales -->
    <script>
        // Tu código JavaScript aquí
    </script>
</body>
</html>
