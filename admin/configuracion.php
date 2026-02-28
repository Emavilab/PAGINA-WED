<?php
require_once '../core/sesiones.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    header("Location: ../index.php");
    exit();
}

require_once '../core/conexion.php';
$resultado_marcas = mysqli_query($conexion, "SELECT * FROM marcas ORDER BY id_marca DESC");
$res_envio = mysqli_query($conexion, "SELECT * FROM metodos_envio ORDER BY id_envio DESC");
$res_pagos = mysqli_query($conexion, "SELECT * FROM metodos_pago ORDER BY id_metodo_pago DESC");

// Cargar configuración general
$res_config = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");
$config = ($res_config && mysqli_num_rows($res_config) > 0) ? mysqli_fetch_assoc($res_config) : [];
$redes = !empty($config['redes_sociales']) ? json_decode($config['redes_sociales'], true) : [];

// Menú de navegación del header (JSON) y columnas del footer (JSON)
$header_menu = [];
if (!empty($config['header_menu'])) {
    $tmp = json_decode($config['header_menu'], true);
    if (is_array($tmp)) {
        $header_menu = $tmp;
    }
}

$footer_columns = [];
if (!empty($config['footer_columns'])) {
    $tmpFooter = json_decode($config['footer_columns'], true);
    if (is_array($tmpFooter)) {
        $footer_columns = $tmpFooter;
    }
}

// Colores del tema (con valores por defecto y validación básica)
function normalizar_color($valor, $defecto) {
    if (!is_string($valor)) return $defecto;
    $valor = trim($valor);
    if ($valor === '') return $defecto;
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) return $defecto;
    return strtoupper($valor);
}

$color_primary = normalizar_color($config['color_primary'] ?? '#137fec', '#137FEC');
$color_primary_dark = normalizar_color($config['color_primary_dark'] ?? '#0d66c2', '#0D66C2');
$color_bg_light = normalizar_color($config['color_background_light'] ?? '#f6f7f8', '#F6F7F8');
$color_bg_dark = normalizar_color($config['color_background_dark'] ?? '#101922', '#101922');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Configuraciones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Material Symbols para vista previa de iconos del header -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        .material-symbols-outlined {
            font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 24;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">

        <main class="max-w-7xl mx-auto px-4 py-8">
            <!-- Tabs -->
            <div class="flex gap-4 mb-6 flex-wrap">
                <button onclick="mostrarTab('marcas')" class="tab-btn bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-trademark mr-2"></i> Marcas
                </button>
                <button onclick="mostrarTab('metodos-envio')" class="tab-btn bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-truck mr-2"></i> Métodos de Envío
                </button>
                <button onclick="mostrarTab('metodos-pago')" class="tab-btn bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-credit-card mr-2"></i> Métodos de Pago
                </button>
                <button onclick="mostrarTab('general')" class="tab-btn bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-sliders-h mr-2"></i> Configuración General
                </button>
                <button onclick="mostrarTab('banners')" class="tab-btn bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-images mr-2"></i> Banners Promocionales
                </button>
                <button onclick="mostrarTab('hero-slides')" class="tab-btn bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-play-circle mr-2"></i> Hero Carrusel
                </button>
              
            </div>

            <!-- ==================== MARCAS ==================== -->
            <div id="tab-marcas" class="tab-content">
                <div class="mb-6">
                    <button onclick="prepararNuevaMarca()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nueva Marca
                    </button>
                </div>

                <div id="formulario-marca" class="hidden bg-white p-8 mb-8 rounded-xl shadow-lg border border-slate-200">
                    <h3 id="titulo-form-marca" class="text-xl font-bold mb-6 text-slate-800">Crear Nueva Marca</h3>
                    <form id="formMarca" class="grid grid-cols-1 md:grid-cols-2 gap-6" onsubmit="return submitMarca(event)">
                        <input type="hidden" name="accion" value="guardar_marca">
                        <input type="hidden" name="id_marca" id="id_marca">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Marca</label>
                            <input type="text" name="nombre" id="nombre_marca" required class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-cyan-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Estado</label>
                            <select name="estado" id="estado_marca" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-cyan-500 outline-none">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Logo</label>
                            <input type="file" name="logo" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                        </div>
                        <div class="md:col-span-2 flex gap-3">
                            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg transition-all">
                                <i class="fas fa-save mr-2"></i> Guardar Marca
                            </button>
                            <button type="button" onclick="document.getElementById('formulario-marca').classList.add('hidden')" class="bg-slate-400 text-white px-8 py-3 rounded-lg font-bold">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-md border border-slate-200 overflow-hidden">
                    <div class="bg-cyan-700 p-4 flex items-center gap-3">
                        <i class="fas fa-list text-white"></i>
                        <h4 class="text-white font-bold text-lg">Lista de Marcas</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">ID</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Logo</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Nombre</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Estado</th>
                                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php while($reg = mysqli_fetch_assoc($resultado_marcas)): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-4 text-slate-600 font-medium">#<?php echo $reg['id_marca']; ?></td>
                                    <td class="px-6 py-4 flex justify-center">
                                        <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200 shadow-sm overflow-hidden">
                                            <?php if(!empty($reg['logo'])): ?>
                                                <img src="../img/marcas/<?php echo $reg['logo']; ?>" class="w-10 h-10 object-contain">
                                            <?php else: ?>
                                                <i class="fas fa-image text-slate-300 text-xl"></i>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-bold text-slate-700"><?php echo htmlspecialchars($reg['nombre']); ?></td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo ($reg['estado'] == 'activo') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                            <?php echo ucfirst($reg['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <button onclick='editarMarca(<?php echo json_encode($reg); ?>)' class="w-9 h-9 flex items-center justify-center text-blue-500 border border-blue-500 rounded-lg hover:bg-blue-50 transition-all">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="confirmarEliminar('marca', <?php echo $reg['id_marca']; ?>, '<?php echo htmlspecialchars(addslashes($reg['nombre'])); ?>')" class="w-9 h-9 flex items-center justify-center text-red-500 border border-red-500 rounded-lg hover:bg-red-50 transition-all">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==================== MÉTODOS DE ENVÍO ==================== -->
            <div id="tab-metodos-envio" class="tab-content hidden">
                <div class="mb-6">
                    <button onclick="prepararNuevoEnvio()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nuevo Método de Envío
                    </button>
                </div>

                <div id="formulario-envio" class="hidden bg-white rounded-lg shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">
                        <i class="fas fa-edit text-cyan-600"></i> <span id="titulo-envio">Crear Nuevo Método de Envío</span>
                    </h2>
                    <form id="formEnvio" class="grid grid-cols-1 md:grid-cols-2 gap-6" onsubmit="return submitEnvio(event)">
                        <input type="hidden" name="accion" value="guardar_envio">
                        <input type="hidden" name="id_envio" id="envio_id">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre</label>
                            <input type="text" name="nombre" id="envio_nombre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none" placeholder="Ej: Envío Express">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Costo</label>
                            <input type="number" name="costo" id="envio_costo" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tiempo Estimado</label>
                            <input type="text" name="tiempo" id="envio_tiempo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none" placeholder="Ej: 1-2 días">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Estado</label>
                            <select name="estado" id="envio_estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Descripción</label>
                            <textarea name="descripcion" id="envio_descripcion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none"></textarea>
                        </div>
                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                                <i class="fas fa-save mr-2"></i> Guardar
                            </button>
                            <button type="button" onclick="document.getElementById('formulario-envio').classList.add('hidden')" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-12">
                    <div class="bg-gradient-to-r from-cyan-600 to-cyan-800 text-white p-4">
                        <h2 class="text-xl font-bold"><i class="fas fa-list mr-2"></i> Métodos de Envío</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-100 border-b-2 border-gray-300">
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Costo</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tiempo</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($env = mysqli_fetch_assoc($res_envio)): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">#<?php echo $env['id_envio']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold"><?php echo htmlspecialchars($env['nombre']); ?></td>
                                    <td class="px-6 py-4 text-sm text-green-600 font-bold">$<?php echo number_format($env['costo'], 2); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($env['tiempo_estimado']); ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo ($env['estado'] == 'activo') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                            <?php echo ucfirst($env['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <div class="flex justify-center gap-2">
                                            <button onclick='editarEnvio(<?php echo json_encode($env); ?>)' class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded shadow">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="confirmarEliminar('envio', <?php echo $env['id_envio']; ?>, '<?php echo htmlspecialchars(addslashes($env['nombre'])); ?>')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==================== MÉTODOS DE PAGO ==================== -->
            <div id="tab-metodos-pago" class="tab-content hidden">
                <div class="mb-6">
                    <button onclick="prepararNuevoPago()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nuevo Método de Pago
                    </button>
                </div>

                <div id="formulario-pago" class="hidden bg-white rounded-lg shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">
                        <i class="fas fa-edit text-cyan-600"></i> <span id="titulo-pago">Crear Nuevo Método de Pago</span>
                    </h2>
                    <form id="formPago" class="grid grid-cols-1 md:grid-cols-2 gap-6" onsubmit="return submitPago(event)">
                        <input type="hidden" name="accion" value="guardar_pago">
                        <input type="hidden" name="id_pago" id="id_pago">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heading text-cyan-500"></i> Nombre
                            </label>
                            <input type="text" name="nombre" id="nombre_pago" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ej: Tarjeta de Crédito">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-green-500"></i> Estado
                            </label>
                            <select name="estado" id="estado_pago" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-cyan-500"></i> Descripción
                            </label>
                            <textarea name="descripcion" id="descripcion_pago" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Descripción del método de pago..."></textarea>
                        </div>
                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                                <i class="fas fa-save mr-2"></i> Guardar
                            </button>
                            <button type="button" onclick="document.getElementById('formulario-pago').classList.add('hidden')" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-12">
                    <div class="bg-gradient-to-r from-cyan-600 to-cyan-800 text-white p-4">
                        <h2 class="text-xl font-bold"><i class="fas fa-list mr-2"></i> Métodos de Pago Registrados</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-100 border-b-2 border-gray-300">
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Descripción</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($res_pagos && mysqli_num_rows($res_pagos) > 0):
                                    while($row = mysqli_fetch_assoc($res_pagos)): ?>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">#<?php echo $row['id_metodo_pago']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold"><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo ($row['estado'] == 'activo') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                            <?php echo ucfirst($row['estado']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <div class="flex justify-center gap-2">
                                            <button onclick='editarPago(<?php echo json_encode($row); ?>)' class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded shadow">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="confirmarEliminar('pago', <?php echo $row['id_metodo_pago']; ?>, '<?php echo htmlspecialchars(addslashes($row['nombre'])); ?>')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        <i class="fas fa-info-circle mr-2"></i> No hay métodos de pago registrados.
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==================== CONFIGURACIÓN GENERAL ==================== -->
            <div id="tab-general" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-600 to-cyan-800 p-5 flex items-center gap-3">
                        <i class="fas fa-cogs text-white text-xl"></i>
                        <h2 class="text-xl font-bold text-white">Configuración General del Negocio</h2>
                    </div>

                    <form id="formConfigGeneral" class="p-8" onsubmit="return submitConfigGeneral(event)" enctype="multipart/form-data">
                        <input type="hidden" name="accion" value="guardar_config_general">

                        <!-- Sección: Información Básica -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-slate-700 mb-4 pb-2 border-b border-slate-200">
                                <i class="fas fa-store text-cyan-500 mr-2"></i>Información del Negocio
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-building text-cyan-500 mr-1"></i> Nombre del Negocio
                                    </label>
                                    <input type="text" name="nombre_negocio" id="cfg_nombre_negocio" value="<?php echo htmlspecialchars($config['nombre_negocio'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Mi Negocio">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-quote-left text-cyan-500 mr-1"></i> Slogan
                                    </label>
                                    <input type="text" name="slogan" id="cfg_slogan" value="<?php echo htmlspecialchars($config['slogan'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Tu mejor tienda online">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-envelope text-cyan-500 mr-1"></i> Correo Electrónico
                                    </label>
                                    <input type="email" name="correo" id="cfg_correo" value="<?php echo htmlspecialchars($config['correo'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="contacto@minegocio.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-phone text-cyan-500 mr-1"></i> Teléfono
                                    </label>
                                    <input type="tel" name="telefono" id="cfg_telefono" value="<?php echo htmlspecialchars($config['telefono'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="9999-9999">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-map-marker-alt text-cyan-500 mr-1"></i> Dirección
                                    </label>
                                    <input type="text" name="direccion" id="cfg_direccion" value="<?php echo htmlspecialchars($config['direccion'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Dirección del negocio">
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Imágenes -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-slate-700 mb-4 pb-2 border-b border-slate-200">
                                <i class="fas fa-image text-cyan-500 mr-2"></i>Imágenes
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-image text-cyan-500 mr-1"></i> Logo del Negocio
                                    </label>
                                    <?php if(!empty($config['logo'])): ?>
                                        <div class="mb-2 flex items-center gap-3">
                                            <img src="../img/<?php echo htmlspecialchars($config['logo']); ?>" class="w-16 h-16 object-contain border rounded-lg p-1">
                                            <span class="text-xs text-gray-500"><?php echo $config['logo']; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="logo" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-icons text-cyan-500 mr-1"></i> Favicon
                                    </label>
                                    <?php if(!empty($config['favicon'])): ?>
                                        <div class="mb-2 flex items-center gap-3">
                                            <img src="../img/<?php echo htmlspecialchars($config['favicon']); ?>" class="w-8 h-8 object-contain border rounded-lg p-1">
                                            <span class="text-xs text-gray-500"><?php echo $config['favicon']; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="favicon" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Comercio -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-slate-700 mb-4 pb-2 border-b border-slate-200">
                                <i class="fas fa-coins text-cyan-500 mr-2"></i>Configuración Comercial
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-dollar-sign text-cyan-500 mr-1"></i> Moneda
                                    </label>
                                    <select name="moneda" id="cfg_moneda" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition">
                                        <?php
                                        $monedas = ['USD' => 'USD - Dólar', 'EUR' => 'EUR - Euro', 'MXN' => 'MXN - Peso Mexicano', 'COP' => 'COP - Peso Colombiano', 'ARS' => 'ARS - Peso Argentino', 'GTQ' => 'GTQ - Quetzal', 'HNL' => 'HNL - Lempira', 'CRC' => 'CRC - Colón'];
                                        $monedaActual = $config['moneda'] ?? 'USD';
                                        foreach($monedas as $cod => $nom):
                                        ?>
                                        <option value="<?php echo $cod; ?>" <?php echo ($monedaActual == $cod) ? 'selected' : ''; ?>><?php echo $nom; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-percent text-cyan-500 mr-1"></i> Impuesto (%)
                                    </label>
                                    <input type="number" name="impuesto" id="cfg_impuesto" step="0.01" min="0" max="100" value="<?php echo htmlspecialchars($config['impuesto'] ?? '0'); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="0.00">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-clock text-cyan-500 mr-1"></i> Horario de Atención
                                    </label>
                                    <input type="text" name="horario_atencion" id="cfg_horario" value="<?php echo htmlspecialchars($config['horario_atencion'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Lun-Vie 8:00am - 5:00pm">
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Colores del Sitio -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-slate-700 mb-4 pb-2 border-b border-slate-200 flex items-center gap-2">
                                <i class="fas fa-palette text-cyan-500"></i>
                                Colores del Sitio
                            </h3>
                            <p class="text-xs text-gray-500 mb-4">
                                Personaliza la paleta principal que se usa en botones, enlaces y fondos. Usa colores con buen contraste para que el texto siempre sea legible.
                            </p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col items-center gap-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Color Primario</label>
                                        <input type="color" name="color_primary" value="<?php echo htmlspecialchars($color_primary); ?>" class="w-14 h-10 rounded border border-gray-300 cursor-pointer">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 mb-1">Se usa en botones, enlaces destacados y acentos principales.</p>
                                        <input type="text" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-xs font-mono text-gray-600" value="<?php echo htmlspecialchars($color_primary); ?>">
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col items-center gap-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Color Primario Oscuro</label>
                                        <input type="color" name="color_primary_dark" value="<?php echo htmlspecialchars($color_primary_dark); ?>" class="w-14 h-10 rounded border border-gray-300 cursor-pointer">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 mb-1">Se usa cuando pasas el mouse sobre botones y enlaces.</p>
                                        <input type="text" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-xs font-mono text-gray-600" value="<?php echo htmlspecialchars($color_primary_dark); ?>">
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col items-center gap-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Fondo Claro</label>
                                        <input type="color" name="color_background_light" value="<?php echo htmlspecialchars($color_bg_light); ?>" class="w-14 h-10 rounded border border-gray-300 cursor-pointer">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 mb-1">Color de fondo principal en modo claro.</p>
                                        <input type="text" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-xs font-mono text-gray-600" value="<?php echo htmlspecialchars($color_bg_light); ?>">
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col items-center gap-2">
                                        <label class="block text-xs font-semibold text-gray-700 mb-1 uppercase tracking-wide">Fondo Oscuro</label>
                                        <input type="color" name="color_background_dark" value="<?php echo htmlspecialchars($color_bg_dark); ?>" class="w-14 h-10 rounded border border-gray-300 cursor-pointer">
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs text-gray-500 mb-1">Color de fondo en modo oscuro.</p>
                                        <input type="text" readonly class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-xs font-mono text-gray-600" value="<?php echo htmlspecialchars($color_bg_dark); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Textos -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-slate-700 mb-4 pb-2 border-b border-slate-200">
                                <i class="fas fa-align-left text-cyan-500 mr-2"></i>Textos de la Página
                            </h3>
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-bullhorn text-cyan-500 mr-1"></i> Texto del Banner Superior
                                    </label>
                                    <input type="text" name="texto_banner_superior" id="cfg_banner_superior" value="<?php echo htmlspecialchars($config['texto_banner_superior'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="🚚 ¡Envío gratis en pedidos mayores a $50!">
                                    <p class="text-xs text-gray-400 mt-1">Se muestra en la barra azul superior. Déjalo vacío para ocultarlo.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-home text-cyan-500 mr-1"></i> Texto de Inicio / Bienvenida
                                    </label>
                                    <textarea name="texto_inicio" id="cfg_texto_inicio" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Bienvenido a nuestro negocio..."><?php echo htmlspecialchars($config['texto_inicio'] ?? ''); ?></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-shoe-prints text-cyan-500 mr-1"></i> Texto del Pie de Página (Footer)
                                    </label>
                                    <textarea name="pie_pagina" id="cfg_pie_pagina" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="© 2024 Mi Negocio. Todos los derechos reservados."><?php echo htmlspecialchars($config['pie_pagina'] ?? ''); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Menú de Navegación (Header) -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-slate-700 pb-1 border-b border-slate-200 flex items-center gap-2">
                                    <i class="fas fa-bars text-cyan-500"></i>
                                    Menú de Navegación (Header)
                                </h3>
                                <button type="button" onclick="agregarItemHeader()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold shadow-md">
                                    <i class="fas fa-plus"></i>
                                    Añadir ítem
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mb-2">
                                Define las opciones del menú principal del header. Cada elemento puede apuntar a una ruta interna (por ejemplo <code>/categorias</code>) o a una URL externa.
                            </p>
                            <p class="text-[11px] text-gray-400 mb-4">
                                Opcionalmente puedes definir el nombre del icono de <strong>Material Symbols</strong>, por ejemplo <code>grid_view</code>, <code>sell</code>, <code>shopping_cart</code>. Si lo dejas vacío se usará un icono por defecto según la ruta.
                            </p>
                            <div id="header-menu-items" class="space-y-3">
                                <?php
                                $header_menu_render = $header_menu;
                                if (empty($header_menu_render)) {
                                    $header_menu_render = [
                                        ['label' => 'Categorías', 'path' => '/categorias'],
                                        ['label' => 'Ofertas', 'path' => '/ofertas'],
                                        ['label' => 'Contáctanos', 'path' => '/contacto'],
                                    ];
                                }
                                foreach ($header_menu_render as $item):
                                    $lbl = htmlspecialchars($item['label'] ?? '');
                                    $pth = htmlspecialchars($item['path'] ?? '');
                                    $ico = htmlspecialchars($item['icon'] ?? '');
                                ?>
                                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-slate-50 border border-slate-200 rounded-lg p-4" data-header-item="1">
                                    <div class="md:col-span-4">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Texto visible</label>
                                        <input type="text" class="header-label w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="Ej: Categorías" value="<?php echo $lbl; ?>">
                                    </div>
                                    <div class="md:col-span-5">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Ruta / URL</label>
                                        <input type="text" class="header-path w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="/categorias o https://tusitio.com" value="<?php echo $pth; ?>">
                                        <p class="text-[11px] text-gray-400 mt-1">Si usas rutas como <code>/categorias</code>, se mapearán automáticamente a las secciones internas cuando sea posible.</p>
                                    </div>
                                    <div class="md:col-span-3 flex flex-col gap-2 mt-3 md:mt-0">
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Icono (Material Symbols)</label>
                                            <div class="flex gap-2">
                                                <input type="text" class="header-icon flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="Ej: grid_view" value="<?php echo $ico; ?>">
                                                <button type="button" onclick="abrirSelectorIconos(this)" class="px-3 py-2 rounded-lg border border-cyan-200 text-cyan-600 text-xs font-semibold hover:bg-cyan-50 flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-sm">apps</span>
                                                    <span>Ver iconos</span>
                                                </button>
                                            </div>
                                            <p class="text-[10px] text-gray-400 mt-1">Ejemplos: <code>grid_view</code>, <code>sell</code>, <code>favorite</code>, <code>shopping_cart</code>.</p>
                                        </div>
                                        <div class="flex md:justify-end">
                                            <button type="button" onclick="eliminarItemHeader(this)" class="px-3 py-2 rounded-lg border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 flex items-center gap-1">
                                                <i class="fas fa-trash"></i>
                                                Quitar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="header_menu_json" id="header_menu_json" value="<?php echo htmlspecialchars($config['header_menu'] ?? ''); ?>">
                        </div>

                        <!-- Sección: Columnas del Pie de Página (Footer) -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-slate-700 pb-1 border-b border-slate-200 flex items-center gap-2">
                                    <i class="fas fa-columns text-cyan-500"></i>
                                    Columnas del Pie de Página (Footer)
                                </h3>
                                <button type="button" onclick="agregarColumnaFooter()" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold shadow-md">
                                    <i class="fas fa-plus"></i>
                                    Añadir Columna
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mb-4">
                                Administra las columnas de enlaces que aparecen en el footer. Puedes crear tantas columnas como necesites y dentro de cada una añadir, editar o quitar enlaces.
                            </p>
                            <div id="footer-columns" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <?php
                                $footer_cols_render = $footer_columns;
                                if (empty($footer_cols_render)) {
                                    $footer_cols_render = [
                                        [
                                            'title' => 'Sobre Nosotros',
                                            'links' => [
                                                ['label' => 'Nuestra Historia', 'path' => '/nosotros'],
                                                ['label' => 'Bolsa de Trabajo', 'path' => '/empleos'],
                                                ['label' => 'Sostenibilidad', 'path' => '/sustentabilidad'],
                                            ],
                                        ],
                                        [
                                            'title' => 'Servicio al Cliente',
                                            'links' => [
                                                ['label' => 'Centro de Ayuda', 'path' => '/ayuda'],
                                                ['label' => 'Políticas de Envío', 'path' => '/envios'],
                                                ['label' => 'Devoluciones', 'path' => '/devoluciones'],
                                            ],
                                        ],
                                    ];
                                }
                                foreach ($footer_cols_render as $col):
                                    $colTitle = htmlspecialchars($col['title'] ?? '');
                                    $links = is_array($col['links'] ?? null) ? $col['links'] : [];
                                ?>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex flex-col gap-3" data-footer-column="1">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <div class="flex-1">
                                            <label class="block text-xs font-semibold text-gray-600 mb-1">Título de la Columna</label>
                                            <input type="text" class="footer-title w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="Ej: Sobre Nosotros" value="<?php echo $colTitle; ?>">
                                        </div>
                                        <button type="button" onclick="eliminarColumnaFooter(this)" class="mt-5 px-3 py-2 rounded-lg border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 flex items-center gap-1">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="space-y-2" data-footer-links>
                                        <?php if(empty($links)): ?>
                                            <div class="grid grid-cols-12 gap-2 items-end" data-footer-link>
                                                <div class="col-span-6">
                                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Texto</label>
                                                    <input type="text" class="footer-link-label w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="Ej: Nuestra Historia">
                                                </div>
                                                <div class="col-span-5">
                                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Ruta / URL</label>
                                                    <input type="text" class="footer-link-path w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="/nosotros">
                                                </div>
                                                <div class="col-span-1 flex justify-end">
                                                    <button type="button" onclick="eliminarEnlaceFooter(this)" class="mb-1 px-2 py-2 rounded-lg border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <?php foreach ($links as $lnk):
                                                $lnkLabel = htmlspecialchars($lnk['label'] ?? '');
                                                $lnkPath = htmlspecialchars($lnk['path'] ?? '');
                                            ?>
                                            <div class="grid grid-cols-12 gap-2 items-end" data-footer-link>
                                                <div class="col-span-6">
                                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Texto</label>
                                                    <input type="text" class="footer-link-label w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" value="<?php echo $lnkLabel; ?>" placeholder="Ej: Nuestra Historia">
                                                </div>
                                                <div class="col-span-5">
                                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Ruta / URL</label>
                                                    <input type="text" class="footer-link-path w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" value="<?php echo $lnkPath; ?>" placeholder="/nosotros">
                                                </div>
                                                <div class="col-span-1 flex justify-end">
                                                    <button type="button" onclick="eliminarEnlaceFooter(this)" class="mb-1 px-2 py-2 rounded-lg border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" onclick="agregarEnlaceFooter(this)" class="mt-1 inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-dashed border-cyan-400 text-cyan-600 text-xs font-semibold hover:bg-cyan-50">
                                        <i class="fas fa-plus"></i>
                                        Añadir Enlace
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="footer_columns_json" id="footer_columns_json" value="<?php echo htmlspecialchars($config['footer_columns'] ?? ''); ?>">
                        </div>

                        <!-- Sección: Hero Principal -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-slate-700 mb-4 pb-2 border-b border-slate-200">
                                <i class="fas fa-desktop text-cyan-500 mr-2"></i>Sección Hero (Página de Inicio)
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-tag text-cyan-500 mr-1"></i> Etiqueta
                                    </label>
                                    <input type="text" name="hero_etiqueta" id="cfg_hero_etiqueta" value="<?php echo htmlspecialchars($config['hero_etiqueta'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ofertas Exclusivas Online">
                                    <p class="text-xs text-gray-400 mt-1">Texto pequeño encima del título.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-image text-cyan-500 mr-1"></i> Imagen Hero
                                    </label>
                                    <?php if(!empty($config['hero_imagen'])): ?>
                                        <div class="mb-2 flex items-center gap-3">
                                            <img src="../img/<?php echo htmlspecialchars($config['hero_imagen']); ?>" class="w-20 h-12 object-cover border rounded-lg">
                                            <span class="text-xs text-gray-500"><?php echo $config['hero_imagen']; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="hero_imagen" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-heading text-cyan-500 mr-1"></i> Título Principal
                                    </label>
                                    <input type="text" name="hero_titulo" id="cfg_hero_titulo" value="<?php echo htmlspecialchars($config['hero_titulo'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Bienvenido a Nuestra Tienda">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-font text-cyan-500 mr-1"></i> Subtítulo (color primario)
                                    </label>
                                    <input type="text" name="hero_subtitulo" id="cfg_hero_subtitulo" value="<?php echo htmlspecialchars($config['hero_subtitulo'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Los Mejores Productos">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-paragraph text-cyan-500 mr-1"></i> Descripción
                                    </label>
                                    <textarea name="hero_descripcion" id="cfg_hero_descripcion" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Descripción breve de tu negocio..."><?php echo htmlspecialchars($config['hero_descripcion'] ?? ''); ?></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-mouse-pointer text-cyan-500 mr-1"></i> Botón Primario
                                    </label>
                                    <input type="text" name="hero_btn_primario" id="cfg_hero_btn1" value="<?php echo htmlspecialchars($config['hero_btn_primario'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Comprar Ahora">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-mouse-pointer text-cyan-500 mr-1"></i> Botón Secundario
                                    </label>
                                    <input type="text" name="hero_btn_secundario" id="cfg_hero_btn2" value="<?php echo htmlspecialchars($config['hero_btn_secundario'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ver Catálogo">
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Redes Sociales -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-slate-700 mb-4 pb-2 border-b border-slate-200">
                                <i class="fas fa-share-alt text-cyan-500 mr-2"></i>Redes Sociales
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fab fa-facebook text-blue-600 mr-1"></i> Facebook
                                    </label>
                                    <input type="url" name="red_facebook" id="cfg_facebook" value="<?php echo htmlspecialchars($redes['facebook'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="https://facebook.com/tu-pagina">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fab fa-instagram text-pink-500 mr-1"></i> Instagram
                                    </label>
                                    <input type="url" name="red_instagram" id="cfg_instagram" value="<?php echo htmlspecialchars($redes['instagram'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="https://instagram.com/tu-pagina">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fab fa-whatsapp text-green-500 mr-1"></i> WhatsApp
                                    </label>
                                    <input type="text" name="red_whatsapp" id="cfg_whatsapp" value="<?php echo htmlspecialchars($redes['whatsapp'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="+504 9999-9999">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fab fa-tiktok text-gray-800 mr-1"></i> TikTok
                                    </label>
                                    <input type="url" name="red_tiktok" id="cfg_tiktok" value="<?php echo htmlspecialchars($redes['tiktok'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="https://tiktok.com/@tu-pagina">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fab fa-twitter text-sky-500 mr-1"></i> Twitter / X
                                    </label>
                                    <input type="url" name="red_twitter" id="cfg_twitter" value="<?php echo htmlspecialchars($redes['twitter'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="https://x.com/tu-pagina">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fab fa-youtube text-red-600 mr-1"></i> YouTube
                                    </label>
                                    <input type="url" name="red_youtube" id="cfg_youtube" value="<?php echo htmlspecialchars($redes['youtube'] ?? ''); ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="https://youtube.com/c/tu-canal">
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex gap-4 pt-4 border-t border-slate-200">
                            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg transition-all flex items-center gap-2">
                                <i class="fas fa-save"></i> Guardar Configuración
                            </button>
                            <button type="button" onclick="resetConfigGeneral()" class="bg-gray-400 hover:bg-gray-500 text-white px-8 py-3 rounded-lg font-bold transition-all flex items-center gap-2">
                                <i class="fas fa-undo"></i> Restaurar Valores
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- ==================== BANNERS PROMOCIONALES ==================== -->
            <div id="tab-banners" class="tab-content hidden">
                <div class="mb-6">
                    <button onclick="prepararNuevoBanner()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nuevo Banner
                    </button>
                </div>

                <div id="formulario-banner" class="hidden bg-white p-8 mb-8 rounded-xl shadow-lg border border-slate-200">
                    <h3 id="titulo-form-banner" class="text-xl font-bold mb-6 text-slate-800">Crear Nuevo Banner</h3>
                    <form id="formBanner" class="grid grid-cols-1 md:grid-cols-2 gap-6" onsubmit="return submitBanner(event)" enctype="multipart/form-data">
                        <input type="hidden" name="accion" value="guardar_banner">
                        <input type="hidden" name="id_banner" id="id_banner">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heading text-cyan-500 mr-1"></i> Título *
                            </label>
                            <input type="text" name="titulo" id="banner_titulo" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ej: Productos Nuevos">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-mouse-pointer text-cyan-500 mr-1"></i> Texto del Botón
                            </label>
                            <input type="text" name="texto_boton" id="banner_texto_boton" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ej: Ver Ahora">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-cyan-500 mr-1"></i> Descripción
                            </label>
                            <input type="text" name="descripcion" id="banner_descripcion" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ej: Hasta 30% de descuento">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-link text-cyan-500 mr-1"></i> Enlace (URL)
                            </label>
                            <input type="text" name="enlace" id="banner_enlace" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ej: #productos o URL externa">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sort-numeric-up text-cyan-500 mr-1"></i> Orden
                            </label>
                            <input type="number" name="orden" id="banner_orden" min="0" value="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-image text-cyan-500 mr-1"></i> Imagen del Banner *
                            </label>
                            <div id="banner_img_preview" class="hidden mb-2 flex items-center gap-3">
                                <img id="banner_img_thumb" src="" class="w-20 h-12 object-cover border rounded-lg">
                                <span id="banner_img_name" class="text-xs text-gray-500"></span>
                            </div>
                            <input type="file" name="imagen_banner" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-cyan-500 mr-1"></i> Estado
                            </label>
                            <select name="estado" id="banner_estado" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex gap-3 pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2.5 rounded-lg font-bold transition-all flex items-center gap-2">
                                <i class="fas fa-save"></i> Guardar Banner
                            </button>
                            <button type="button" onclick="document.getElementById('formulario-banner').classList.add('hidden')" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2.5 rounded-lg font-bold transition-all">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-600 to-cyan-800 p-5 flex items-center gap-3">
                        <i class="fas fa-images text-white text-xl"></i>
                        <h2 class="text-xl font-bold text-white">Banners Registrados</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Imagen</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Descripción</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Orden</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-banners" class="divide-y divide-gray-200">
                                <?php
                                $res_banners = mysqli_query($conexion, "SELECT * FROM banners ORDER BY orden ASC, id_banner DESC");
                                if ($res_banners && mysqli_num_rows($res_banners) > 0):
                                    while ($ban = mysqli_fetch_assoc($res_banners)):
                                ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <?php if(!empty($ban['imagen'])): ?>
                                            <img src="../img/banners/<?php echo htmlspecialchars($ban['imagen']); ?>" class="w-24 h-14 object-cover rounded-lg border">
                                        <?php else: ?>
                                            <div class="w-24 h-14 bg-gray-200 rounded-lg flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-800"><?php echo htmlspecialchars($ban['titulo']); ?></td>
                                    <td class="px-6 py-4 text-gray-600 text-sm"><?php echo htmlspecialchars($ban['descripcion'] ?? ''); ?></td>
                                    <td class="px-6 py-4 text-center font-bold"><?php echo $ban['orden']; ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if($ban['estado'] == 1): ?>
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Activo</span>
                                        <?php else: ?>
                                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex gap-2 justify-center">
                                            <button onclick='editarBanner(<?php echo json_encode($ban); ?>)' class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-sm font-bold transition">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="confirmarEliminacion('banner', <?php echo $ban['id_banner']; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm font-bold transition">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400"><i class="fas fa-images text-4xl mb-2 block"></i>No hay banners registrados</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ==================== HERO CARRUSEL ==================== -->
            <div id="tab-hero-slides" class="tab-content hidden">
                <div class="mb-6">
                    <button onclick="prepararNuevoSlide()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nuevo Slide
                    </button>
                </div>

                <div id="formulario-slide" class="hidden bg-white p-8 mb-8 rounded-xl shadow-lg border border-slate-200">
                    <h3 id="titulo-form-slide" class="text-xl font-bold mb-6 text-slate-800">Crear Nuevo Slide</h3>
                    <form id="formSlide" class="grid grid-cols-1 md:grid-cols-2 gap-6" onsubmit="return submitSlide(event)" enctype="multipart/form-data">
                        <input type="hidden" name="accion" value="guardar_hero_slide">
                        <input type="hidden" name="id_slide" id="slide_id">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heading text-cyan-500 mr-1"></i> Título *
                            </label>
                            <input type="text" name="titulo" id="slide_titulo" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ej: Temporada de Verano">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-font text-cyan-500 mr-1"></i> Subtítulo
                            </label>
                            <input type="text" name="subtitulo" id="slide_subtitulo" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ej: Hasta 50% de descuento">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-mouse-pointer text-cyan-500 mr-1"></i> Texto del Botón
                            </label>
                            <input type="text" name="texto_boton" id="slide_texto_boton" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ej: Comprar Ahora">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-link text-cyan-500 mr-1"></i> Enlace
                            </label>
                            <input type="text" name="enlace" id="slide_enlace" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition" placeholder="Ej: #productos">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-image text-cyan-500 mr-1"></i> Imagen del Slide *
                            </label>
                            <div id="slide_img_preview" class="hidden mb-2 flex items-center gap-3">
                                <img id="slide_img_thumb" src="" class="w-20 h-12 object-cover border rounded-lg">
                                <span id="slide_img_name" class="text-xs text-gray-500"></span>
                            </div>
                            <input type="file" name="imagen_slide" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-sort-numeric-up text-cyan-500 mr-1"></i> Orden
                            </label>
                            <input type="number" name="orden" id="slide_orden" min="0" value="0" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-cyan-500 mr-1"></i> Estado
                            </label>
                            <select name="estado" id="slide_estado" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>
                        <div class="md:col-span-2 flex gap-3 pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2.5 rounded-lg font-bold transition-all flex items-center gap-2">
                                <i class="fas fa-save"></i> Guardar Slide
                            </button>
                            <button type="button" onclick="document.getElementById('formulario-slide').classList.add('hidden')" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2.5 rounded-lg font-bold transition-all">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-xl shadow-lg border border-slate-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 p-5 flex items-center gap-3">
                        <i class="fas fa-play-circle text-white text-xl"></i>
                        <h2 class="text-xl font-bold text-white">Slides del Hero Carrusel</h2>
                    </div>
                    <p class="px-6 pt-4 text-sm text-gray-500"><i class="fas fa-info-circle mr-1"></i> Estos slides rotan automáticamente en la sección principal (hero) de la página de inicio. El primer slide siempre es el configurado en "Configuración General".</p>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Imagen</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase">Subtítulo</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Orden</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php
                                $res_slides = mysqli_query($conexion, "SELECT * FROM hero_slides ORDER BY orden ASC, id_slide DESC");
                                if ($res_slides && mysqli_num_rows($res_slides) > 0):
                                    while ($sl = mysqli_fetch_assoc($res_slides)):
                                ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <?php if(!empty($sl['imagen'])): ?>
                                            <img src="../img/slides/<?php echo htmlspecialchars($sl['imagen']); ?>" class="w-24 h-14 object-cover rounded-lg border">
                                        <?php else: ?>
                                            <div class="w-24 h-14 bg-gray-200 rounded-lg flex items-center justify-center"><i class="fas fa-image text-gray-400"></i></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-gray-800"><?php echo htmlspecialchars($sl['titulo']); ?></td>
                                    <td class="px-6 py-4 text-gray-600 text-sm"><?php echo htmlspecialchars($sl['subtitulo'] ?? ''); ?></td>
                                    <td class="px-6 py-4 text-center font-bold"><?php echo $sl['orden']; ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if($sl['estado'] == 'activo'): ?>
                                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">Activo</span>
                                        <?php else: ?>
                                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold">Inactivo</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex gap-2 justify-center">
                                            <button onclick='editarSlide(<?php echo json_encode($sl); ?>)' class="bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg text-sm font-bold transition">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="confirmarEliminacion('hero_slide', <?php echo $sl['id_slide']; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm font-bold transition">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400"><i class="fas fa-play-circle text-4xl mb-2 block"></i>No hay slides registrados</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

           

    <!-- ==================== SELECTOR DE ICONOS HEADER ==================== -->
    <div id="iconSelectorModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-40">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[80vh] overflow-hidden flex flex-col border border-slate-200">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">apps</span>
                    <h3 class="text-lg font-bold text-slate-800">Seleccionar icono</h3>
                </div>
                <button type="button" onclick="cerrarSelectorIconos()" class="p-2 rounded-full hover:bg-slate-100 text-slate-500">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="px-6 pt-3 pb-2 border-b border-slate-200">
                <p class="text-xs text-slate-500">
                    Estos son iconos de <strong>Material Symbols</strong>. Haz clic en uno para usarlo en el menú.
                </p>
            </div>
            <div class="p-4 overflow-y-auto">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 text-sm">
                    <?php
                    $iconos_disponibles = [
                        'home', 'grid_view', 'sell', 'shopping_cart',
                        'favorite', 'package_2', 'contact_support', 'inventory_2',
                        'local_shipping', 'support_agent', 'verified_user', 'workspace_premium',
                        'storefront', 'category', 'info', 'help',
                        'person', 'settings', 'receipt_long', 'payments'
                    ];
                    foreach ($iconos_disponibles as $ic):
                    ?>
                    <button type="button"
                            class="flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 hover:border-primary hover:bg-primary/5 text-slate-700 hover:text-primary transition text-left"
                            onclick="seleccionarIconoHeader('<?php echo $ic; ?>')">
                        <span class="material-symbols-outlined text-xl"><?php echo $ic; ?></span>
                        <span class="text-xs font-mono"><?php echo $ic; ?></span>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="px-6 py-3 border-t border-slate-200 flex justify-end">
                <button type="button" onclick="cerrarSelectorIconos()" class="px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL CONFIRMACIÓN ==================== -->
    <div id="modalConfirmar" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 w-full max-w-sm text-center shadow-2xl">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <h2 class="text-xl font-bold mb-2 text-gray-800">¿Estás seguro?</h2>
            <p id="modalConfirmarTexto" class="text-gray-500 mb-6">Esta acción no se puede deshacer.</p>
            <input type="hidden" id="eliminar_tipo">
            <input type="hidden" id="eliminar_id">
            <div class="flex gap-3">
                <button onclick="ejecutarEliminar()" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3 rounded-lg font-bold transition">
                    <i class="fas fa-trash mr-2"></i> Eliminar
                </button>
                <button onclick="cerrarModalConfirmar()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 py-3 rounded-lg font-bold transition">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    <!-- ==================== MODAL ÉXITO ==================== -->
    <div id="modalExito" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 w-full max-w-sm text-center shadow-2xl">
            <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-500 text-3xl"></i>
            </div>
            <h2 class="text-xl font-bold mb-2 text-green-700">Operación Exitosa</h2>
            <p id="modalExitoTexto" class="text-gray-500 mb-6"></p>
            <button onclick="cerrarModalExito()" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-bold transition">
                Aceptar
            </button>
        </div>
    </div>

    <!-- ==================== MODAL ERROR ==================== -->
    <div id="modalError" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 w-full max-w-sm text-center shadow-2xl">
            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                <i class="fas fa-times-circle text-red-500 text-3xl"></i>
            </div>
            <h2 class="text-xl font-bold mb-2 text-red-700">Error</h2>
            <p id="modalErrorTexto" class="text-gray-500 mb-6"></p>
            <button onclick="cerrarModalError()" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3 rounded-lg font-bold transition">
                Aceptar
            </button>
        </div>
    </div>

<script>
// ==================== TABS ====================
window.mostrarTab = function(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
    const selectedTab = document.getElementById('tab-' + tabId);
    if (selectedTab) selectedTab.classList.remove('hidden');

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-cyan-600', 'hover:bg-cyan-700');
        btn.classList.add('bg-gray-400', 'hover:bg-gray-500');
    });
    document.querySelectorAll('.tab-btn').forEach(btn => {
        let btnTabId = '';
        if (btn.textContent.includes('Marcas')) btnTabId = 'marcas';
        else if (btn.textContent.includes('Envío')) btnTabId = 'metodos-envio';
        else if (btn.textContent.includes('Pago')) btnTabId = 'metodos-pago';
        else if (btn.textContent.includes('General')) btnTabId = 'general';
        else if (btn.textContent.includes('Banners')) btnTabId = 'banners';
        else if (btn.textContent.includes('Hero')) btnTabId = 'hero-slides';
        if (btnTabId === tabId) {
            btn.classList.remove('bg-gray-400', 'hover:bg-gray-500');
            btn.classList.add('bg-cyan-600', 'hover:bg-cyan-700');
        }
    });
}

// ==================== MODALES ====================
function mostrarModalExito(mensaje) {
    document.getElementById('modalExitoTexto').textContent = mensaje;
    const modal = document.getElementById('modalExito');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function cerrarModalExito() {
    const modal = document.getElementById('modalExito');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
function mostrarModalError(mensaje) {
    document.getElementById('modalErrorTexto').textContent = mensaje;
    const modal = document.getElementById('modalError');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function cerrarModalError() {
    const modal = document.getElementById('modalError');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// ==================== CONFIRMAR ELIMINAR ====================
function confirmarEliminar(tipo, id, nombre) {
    document.getElementById('eliminar_tipo').value = tipo;
    document.getElementById('eliminar_id').value = id;
    document.getElementById('modalConfirmarTexto').textContent = '¿Deseas eliminar "' + nombre + '"? Esta acción no se puede deshacer.';
    const modal = document.getElementById('modalConfirmar');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function cerrarModalConfirmar() {
    const modal = document.getElementById('modalConfirmar');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function ejecutarEliminar() {
    const tipo = document.getElementById('eliminar_tipo').value;
    const id = document.getElementById('eliminar_id').value;
    cerrarModalConfirmar();

    fetch('../core/procesar_configuracion.php?eliminar_' + tipo + '=' + id)
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarModalExito(data.message);
            setTimeout(() => recargarModulo(), 1500);
        } else {
            mostrarModalError(data.message || 'Error al eliminar');
        }
    })
    .catch(() => mostrarModalError('Error de conexión con el servidor'));
}

// ==================== RECARGAR MÓDULO (se queda en el mismo tab) ====================
function recargarModulo() {
    // Detectar qué tab está activo
    let tabActivo = 'marcas';
    document.querySelectorAll('.tab-content').forEach(tab => {
        if (!tab.classList.contains('hidden')) {
            tabActivo = tab.id.replace('tab-', '');
        }
    });

    // Guardar tab activo en sessionStorage para restaurarlo después de recargar
    sessionStorage.setItem('configTabActivo', tabActivo);

    // Si estamos dentro del Dashboard (SPA), usar loadPage
    if (typeof loadPage === 'function') {
        loadPage('configuracion.php');
    } else {
        location.reload();
    }
}

// ==================== ENVIAR FORMULARIO VÍA AJAX ====================
var _enviando = false;
function enviarFormulario(formId) {
    if (_enviando) return;
    _enviando = true;

    const form = document.getElementById(formId);
    if (!form) { _enviando = false; return; }

    const formData = new FormData(form);

    fetch('../core/procesar_configuracion.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        _enviando = false;
        if (data.success) {
            mostrarModalExito(data.message);
            setTimeout(() => recargarModulo(), 1500);
        } else {
            mostrarModalError(data.message || 'Error al guardar');
        }
    })
    .catch(() => { _enviando = false; mostrarModalError('Error de conexión con el servidor'); });
}

// ==================== MARCAS ====================
function prepararNuevaMarca() {
    document.getElementById('formulario-marca').classList.remove('hidden');
    document.getElementById('id_marca').value = '';
    document.getElementById('nombre_marca').value = '';
    document.getElementById('estado_marca').value = 'activo';
    document.getElementById('titulo-form-marca').innerText = 'Crear Nueva Marca';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function editarMarca(datos) {
    document.getElementById('formulario-marca').classList.remove('hidden');
    document.getElementById('id_marca').value = datos.id_marca;
    document.getElementById('nombre_marca').value = datos.nombre;
    document.getElementById('estado_marca').value = datos.estado;
    document.getElementById('titulo-form-marca').innerText = 'Editando Marca: ' + datos.nombre;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ==================== ENVÍO ====================
function prepararNuevoEnvio() {
    document.getElementById('formulario-envio').classList.remove('hidden');
    document.getElementById('envio_id').value = '';
    document.getElementById('envio_nombre').value = '';
    document.getElementById('envio_costo').value = '';
    document.getElementById('envio_tiempo').value = '';
    document.getElementById('envio_estado').value = 'activo';
    document.getElementById('envio_descripcion').value = '';
    document.getElementById('titulo-envio').innerText = 'Crear Nuevo Método de Envío';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function editarEnvio(datos) {
    document.getElementById('formulario-envio').classList.remove('hidden');
    document.getElementById('envio_id').value = datos.id_envio;
    document.getElementById('envio_nombre').value = datos.nombre;
    document.getElementById('envio_costo').value = datos.costo;
    document.getElementById('envio_tiempo').value = datos.tiempo_estimado;
    document.getElementById('envio_estado').value = datos.estado;
    document.getElementById('envio_descripcion').value = datos.descripcion || '';
    document.getElementById('titulo-envio').innerText = 'Editando: ' + datos.nombre;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ==================== PAGO ====================
function prepararNuevoPago() {
    document.getElementById('formulario-pago').classList.remove('hidden');
    document.getElementById('id_pago').value = '';
    document.getElementById('nombre_pago').value = '';
    document.getElementById('descripcion_pago').value = '';
    document.getElementById('estado_pago').value = 'activo';
    document.getElementById('titulo-pago').innerText = 'Crear Nuevo Método de Pago';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function editarPago(datos) {
    document.getElementById('formulario-pago').classList.remove('hidden');
    document.getElementById('id_pago').value = datos.id_metodo_pago;
    document.getElementById('nombre_pago').value = datos.nombre;
    document.getElementById('estado_pago').value = datos.estado;
    document.getElementById('descripcion_pago').value = datos.descripcion || '';
    document.getElementById('titulo-pago').innerText = 'Editando: ' + datos.nombre;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ==================== RESTAURAR TAB AL CARGAR ====================
(function() {
    const tabGuardado = sessionStorage.getItem('configTabActivo');
    if (tabGuardado) {
        sessionStorage.removeItem('configTabActivo');
        setTimeout(() => mostrarTab(tabGuardado), 150);
    }
})();

// ==================== SUBMIT HANDLERS (inline onsubmit) ====================
function submitMarca(e) {
    e.preventDefault();
    const nombre = document.getElementById('nombre_marca').value.trim();
    if (!nombre) { mostrarModalError('El nombre de la marca es requerido'); return false; }
    enviarFormulario('formMarca');
    return false;
}

function submitEnvio(e) {
    e.preventDefault();
    const nombre = document.getElementById('envio_nombre').value.trim();
    const costo = document.getElementById('envio_costo').value;
    if (!nombre) { mostrarModalError('El nombre del método de envío es requerido'); return false; }
    if (!costo || costo < 0) { mostrarModalError('El costo debe ser un valor válido'); return false; }
    enviarFormulario('formEnvio');
    return false;
}

function submitPago(e) {
    e.preventDefault();
    const nombre = document.getElementById('nombre_pago').value.trim();
    if (!nombre) { mostrarModalError('El nombre del método de pago es requerido'); return false; }
    enviarFormulario('formPago');
    return false;
}

// ==================== CONFIGURACIÓN GENERAL ====================
function submitConfigGeneral(e) {
    e.preventDefault();
    if (_enviando) return false;
    _enviando = true;

    // Construir los JSON de Header y Footer antes de enviar
    try {
        construirHeaderFooterJSON();
    } catch (err) {
        console.error(err);
    }

    const form = document.getElementById('formConfigGeneral');
    const formData = new FormData(form);

    fetch('../core/procesar_configuracion.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        _enviando = false;
        if (data.success) {
            mostrarModalExito(data.message);
            setTimeout(() => recargarModulo(), 1500);
        } else {
            mostrarModalError(data.message || 'Error al guardar la configuración');
        }
    })
    .catch(() => { _enviando = false; mostrarModalError('Error de conexión con el servidor'); });
    return false;
}

// Construye los JSON de menú de header y columnas de footer y los coloca en los campos ocultos
function construirHeaderFooterJSON() {
    // Header Menu
    var headerItems = [];
    document.querySelectorAll('[data-header-item]').forEach(function(row) {
        var labelInput = row.querySelector('.header-label');
        var pathInput = row.querySelector('.header-path');
        var iconInput = row.querySelector('.header-icon');
        if (!labelInput || !pathInput) return;
        var label = labelInput.value.trim();
        var path = pathInput.value.trim();
        var icon = iconInput ? iconInput.value.trim() : '';
        if (label && path) {
            var item = { label: label, path: path };
            if (icon) {
                item.icon = icon;
            }
            headerItems.push(item);
        }
    });
    var headerField = document.getElementById('header_menu_json');
    if (headerField) {
        headerField.value = JSON.stringify(headerItems);
    }

    // Footer Columns
    var columns = [];
    document.querySelectorAll('[data-footer-column]').forEach(function(colEl) {
        var titleInput = colEl.querySelector('.footer-title');
        if (!titleInput) return;
        var title = titleInput.value.trim();
        if (!title) return;

        var links = [];
        colEl.querySelectorAll('[data-footer-link]').forEach(function(linkEl) {
            var lblInput = linkEl.querySelector('.footer-link-label');
            var pathInput = linkEl.querySelector('.footer-link-path');
            if (!lblInput || !pathInput) return;
            var l = lblInput.value.trim();
            var p = pathInput.value.trim();
            if (l && p) {
                links.push({ label: l, path: p });
            }
        });

        columns.push({ title: title, links: links });
    });
    var footerField = document.getElementById('footer_columns_json');
    if (footerField) {
        footerField.value = JSON.stringify(columns);
    }
}

// Helpers UI Header Menu
function agregarItemHeader() {
    var cont = document.getElementById('header-menu-items');
    if (!cont) return;
    var row = document.createElement('div');
    row.className = 'grid grid-cols-1 md:grid-cols-12 gap-3 items-end bg-slate-50 border border-slate-200 rounded-lg p-4';
    row.setAttribute('data-header-item', '1');
    row.innerHTML = '' +
        '<div class="md:col-span-4">' +
            '<label class="block text-xs font-semibold text-gray-600 mb-1">Texto visible</label>' +
            '<input type="text" class="header-label w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="Ej: Nueva opción">' +
        '</div>' +
        '<div class="md:col-span-5">' +
            '<label class="block text-xs font-semibold text-gray-600 mb-1">Ruta / URL</label>' +
            '<input type="text" class="header-path w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="/ruta-o-url">' +
            '<p class="text-[11px] text-gray-400 mt-1">Ej: /categorias o https://tusitio.com/pagina</p>' +
        '</div>' +
        '<div class="md:col-span-3 flex flex-col gap-2 mt-3 md:mt-0">' +
            '<div>' +
                '<label class="block text-xs font-semibold text-gray-600 mb-1">Icono (Material Symbols)</label>' +
                '<div class="flex gap-2">' +
                    '<input type="text" class="header-icon flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="Ej: grid_view">' +
                    '<button type="button" onclick="abrirSelectorIconos(this)" class="px-3 py-2 rounded-lg border border-cyan-200 text-cyan-600 text-xs font-semibold hover:bg-cyan-50 flex items-center gap-1">' +
                        '<span class="material-symbols-outlined text-sm">apps</span>' +
                        '<span>Ver iconos</span>' +
                    '</button>' +
                '</div>' +
                '<p class="text-[10px] text-gray-400 mt-1">Ejemplos: <code>grid_view</code>, <code>sell</code>, <code>favorite</code>, <code>shopping_cart</code>.</p>' +
            '</div>' +
            '<div class="flex md:justify-end">' +
                '<button type="button" onclick="eliminarItemHeader(this)" class="px-3 py-2 rounded-lg border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 flex items-center gap-1">' +
                    '<i class="fas fa-trash"></i>' +
                    'Quitar' +
                '</button>' +
            '</div>' +
        '</div>';
    cont.appendChild(row);
}

function eliminarItemHeader(btn) {
    var row = btn.closest('[data-header-item]');
    if (row) row.remove();
}

// Selector visual de iconos para el header
var _headerIconTargetInput = null;

function abrirSelectorIconos(btn) {
    var row = btn.closest('[data-header-item]');
    if (!row) return;
    _headerIconTargetInput = row.querySelector('.header-icon');
    if (!_headerIconTargetInput) return;
    var modal = document.getElementById('iconSelectorModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarSelectorIconos() {
    var modal = document.getElementById('iconSelectorModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    _headerIconTargetInput = null;
}

function seleccionarIconoHeader(nombre) {
    if (_headerIconTargetInput) {
        _headerIconTargetInput.value = nombre;
        _headerIconTargetInput.focus();
    }
    cerrarSelectorIconos();
}

// Helpers UI Footer Columns
function agregarColumnaFooter() {
    var cont = document.getElementById('footer-columns');
    if (!cont) return;
    var col = document.createElement('div');
    col.className = 'bg-slate-50 border border-slate-200 rounded-xl p-4 flex flex-col gap-3';
    col.setAttribute('data-footer-column', '1');
    col.innerHTML = '' +
        '<div class="flex items-center justify-between gap-2 mb-1">' +
            '<div class="flex-1">' +
                '<label class="block text-xs font-semibold text-gray-600 mb-1">Título de la Columna</label>' +
                '<input type="text" class="footer-title w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="Ej: Nueva Columna">' +
            '</div>' +
            '<button type="button" onclick="eliminarColumnaFooter(this)" class="mt-5 px-3 py-2 rounded-lg border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50 flex items-center gap-1">' +
                '<i class="fas fa-trash"></i>' +
            '</button>' +
        '</div>' +
        '<div class="space-y-2" data-footer-links></div>' +
        '<button type="button" onclick="agregarEnlaceFooter(this)" class="mt-1 inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-dashed border-cyan-400 text-cyan-600 text-xs font-semibold hover:bg-cyan-50">' +
            '<i class="fas fa-plus"></i>' +
            'Añadir Enlace' +
        '</button>';
    cont.appendChild(col);

    // Crear al menos un enlace vacío para esa columna
    var addBtn = col.querySelector('button[onclick^="agregarEnlaceFooter"]');
    if (addBtn) {
        agregarEnlaceFooter(addBtn);
    }
}

function eliminarColumnaFooter(btn) {
    var col = btn.closest('[data-footer-column]');
    if (col) col.remove();
}

function agregarEnlaceFooter(btn) {
    var col = btn.closest('[data-footer-column]');
    if (!col) return;
    var cont = col.querySelector('[data-footer-links]');
    if (!cont) return;
    var row = document.createElement('div');
    row.className = 'grid grid-cols-12 gap-2 items-end';
    row.setAttribute('data-footer-link', '1');
    row.innerHTML = '' +
        '<div class="col-span-6">' +
            '<label class="block text-[11px] font-semibold text-gray-600 mb-1">Texto</label>' +
            '<input type="text" class="footer-link-label w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="Ej: Nueva página">' +
        '</div>' +
        '<div class="col-span-5">' +
            '<label class="block text-[11px] font-semibold text-gray-600 mb-1">Ruta / URL</label>' +
            '<input type="text" class="footer-link-path w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none text-sm" placeholder="/ruta-o-url">' +
        '</div>' +
        '<div class="col-span-1 flex justify-end">' +
            '<button type="button" onclick="eliminarEnlaceFooter(this)" class="mb-1 px-2 py-2 rounded-lg border border-red-200 text-red-600 text-xs font-semibold hover:bg-red-50">' +
                '<i class="fas fa-times"></i>' +
            '</button>' +
        '</div>';
    cont.appendChild(row);
}

function eliminarEnlaceFooter(btn) {
    var row = btn.closest('[data-footer-link]');
    if (row) row.remove();
}

function resetConfigGeneral() {
    if (confirm('¿Restaurar los valores guardados? Se perderán los cambios no guardados.')) {
        recargarModulo();
    }
}

// ==================== BANNERS PROMOCIONALES ====================
function prepararNuevoBanner() {
    document.getElementById('formulario-banner').classList.remove('hidden');
    document.getElementById('id_banner').value = '';
    document.getElementById('banner_titulo').value = '';
    document.getElementById('banner_descripcion').value = '';
    document.getElementById('banner_texto_boton').value = '';
    document.getElementById('banner_enlace').value = '';
    document.getElementById('banner_orden').value = '0';
    document.getElementById('banner_estado').value = '1';
    document.getElementById('banner_img_preview').classList.add('hidden');
    document.getElementById('titulo-form-banner').innerText = 'Crear Nuevo Banner';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function editarBanner(datos) {
    document.getElementById('formulario-banner').classList.remove('hidden');
    document.getElementById('id_banner').value = datos.id_banner;
    document.getElementById('banner_titulo').value = datos.titulo || '';
    document.getElementById('banner_descripcion').value = datos.descripcion || '';
    document.getElementById('banner_texto_boton').value = datos.texto_boton || '';
    document.getElementById('banner_enlace').value = datos.enlace || '';
    document.getElementById('banner_orden').value = datos.orden || 0;
    document.getElementById('banner_estado').value = datos.estado;
    if (datos.imagen) {
        document.getElementById('banner_img_preview').classList.remove('hidden');
        document.getElementById('banner_img_thumb').src = '../img/banners/' + datos.imagen;
        document.getElementById('banner_img_name').textContent = datos.imagen;
    } else {
        document.getElementById('banner_img_preview').classList.add('hidden');
    }
    document.getElementById('titulo-form-banner').innerText = 'Editando Banner: ' + datos.titulo;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function confirmarEliminacion(tipo, id) {
    confirmarEliminar(tipo, id, tipo + ' #' + id);
}

function submitBanner(e) {
    e.preventDefault();
    const titulo = document.getElementById('banner_titulo').value.trim();
    if (!titulo) { mostrarModalError('El título del banner es requerido'); return false; }
    enviarFormulario('formBanner');
    return false;
}

// ==================== HERO SLIDES ====================
function prepararNuevoSlide() {
    document.getElementById('formulario-slide').classList.remove('hidden');
    document.getElementById('slide_id').value = '';
    document.getElementById('slide_titulo').value = '';
    document.getElementById('slide_subtitulo').value = '';
    document.getElementById('slide_texto_boton').value = '';
    document.getElementById('slide_enlace').value = '';
    document.getElementById('slide_orden').value = '0';
    document.getElementById('slide_estado').value = 'activo';
    document.getElementById('slide_img_preview').classList.add('hidden');
    document.getElementById('titulo-form-slide').innerText = 'Crear Nuevo Slide';
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function editarSlide(datos) {
    document.getElementById('formulario-slide').classList.remove('hidden');
    document.getElementById('slide_id').value = datos.id_slide;
    document.getElementById('slide_titulo').value = datos.titulo || '';
    document.getElementById('slide_subtitulo').value = datos.subtitulo || '';
    document.getElementById('slide_texto_boton').value = datos.texto_boton || '';
    document.getElementById('slide_enlace').value = datos.enlace || '';
    document.getElementById('slide_orden').value = datos.orden || 0;
    document.getElementById('slide_estado').value = datos.estado;
    if (datos.imagen) {
        document.getElementById('slide_img_preview').classList.remove('hidden');
        document.getElementById('slide_img_thumb').src = '../img/slides/' + datos.imagen;
        document.getElementById('slide_img_name').textContent = datos.imagen;
    } else {
        document.getElementById('slide_img_preview').classList.add('hidden');
    }
    document.getElementById('titulo-form-slide').innerText = 'Editando Slide: ' + datos.titulo;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function submitSlide(e) {
    e.preventDefault();
    const titulo = document.getElementById('slide_titulo').value.trim();
    if (!titulo) { mostrarModalError('El título del slide es requerido'); return false; }
    enviarFormulario('formSlide');
    return false;
}
</script>
</body>
</html>