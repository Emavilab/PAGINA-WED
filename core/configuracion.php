<?php
require_once 'sesiones.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    header("Location: ../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Configuraciones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            </div>


             <!-- MARCAS -->
<div id="tab-marcas" class="tab-content">

<?php
require_once '../core/conexion.php';
// Ordenamos por ID 
$resultado = mysqli_query($conexion, "SELECT * FROM marcas ORDER BY id_marca DESC");
?>

<div class="mb-6">
    <button onclick="prepararNuevaMarca()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-bold shadow-md transition-all flex items-center gap-2">
        <i class="fas fa-plus"></i> Nueva Marca
    </button>
</div>

<div id="formulario-marca" class="hidden bg-white p-8 mb-8 rounded-xl shadow-lg border border-slate-200">
    <h3 id="titulo-form" class="text-xl font-bold mb-6 text-slate-800">Datos de la Marca</h3>
    
    <form action="../core/procesar_configuracion.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <input type="hidden" name="accion" value="guardar_marca">
        <input type="hidden" name="id_marca" id="id_marca">

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Nombre de la Marca</label>
            <input type="text" name="nombre" id="nombre_input" required class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-cyan-500 outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">Estado</label>
            <select name="estado" id="estado_input" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-cyan-500 outline-none">
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
                Guardar Marca
            </button>
            <button type="button" onclick="document.getElementById('formulario-marca').classList.add('hidden')" class="bg-slate-400 text-white px-8 py-3 rounded-lg font-bold">
                Cancelar
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
                <?php while($reg = mysqli_fetch_assoc($resultado)): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-6 py-4 text-slate-600 font-medium">#<?php echo $reg['id_marca']; ?></td>
                    <td class="px-6 py-4 flex justify-center">
                        <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center border border-slate-200 shadow-sm overflow-hidden">
                            <?php if(!empty($reg['logo'])): ?>
                                <img src="../assets/img/marcas/<?php echo $reg['logo']; ?>" class="w-10 h-10 object-contain">
                            <?php else: ?>
                                <i class="fas fa-image text-slate-300 text-xl"></i>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-bold text-slate-700"><?php echo $reg['nombre']; ?></td>
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
                            <button onclick="eliminarMarca(<?php echo $reg['id_marca']; ?>)" class="w-9 h-9 flex items-center justify-center text-red-500 border border-red-500 rounded-lg hover:bg-red-50 transition-all">
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

<script>
// Función para limpiar y mostrar el formulario (NUEVA MARCA)
function prepararNuevaMarca() {
    document.getElementById('formulario-marca').classList.remove('hidden');
    document.getElementById('id_marca').value = ""; // Limpia el ID
    document.getElementsById('nombre_input')[0].value = ""; // Limpia el nombre
    document.getElementsById('estado_imput')[0].value = "activo";
    document.getElementById('titulo-from').innerText ="Crear Nueva Marca";
}

// Función para EDITAR (Llena los campos con los datos de la fila)
function editarMarca(datos) {
    document.getElementById('formulario-marca').classList.remove('hidden');
    ocument.getElementById('id_marca').value = datos.id_marca;
    document.getElementById('nombre_input').value = datos.nombre;
    document.getElementById('estado_input').value = datos.estado;
    document.getElementById('titulo-form').innerText = "Editando Marca: " + datos.nombre;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Función para ELIMINAR
function eliminarMarca(id) {
    if (confirm('¿Seguro que deseas eliminar esta marca?')) {
        window.location.href = `../core/procesar_configuracion.php?eliminar_marca=${id}`;
    }
}

// Función para los Tabs (Mantén la que ya tienes, funciona bien)
window.mostrarTab = function(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
    const selectedTab = document.getElementById('tab-' + tabId);
    if (selectedTab) selectedTab.classList.remove('hidden');
    // ... resto de tu lógica de colores de botones ...
}
</script>
 
  <!-- MÉTODOS DE ENVIO -->
<div id="tab-metodos-envio" class="tab-content hidden">
    <?php
    require_once '../core/conexion.php';
    // Leemos los datos reales de la base de datos
    $res_envio = mysqli_query($conexion, "SELECT * FROM metodos_envio ORDER BY id_envio DESC");
    ?>

    <div class="mb-6">
        <button onclick="prepararNuevoEnvio()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
            <i class="fas fa-plus"></i> Nuevo Método de Envío
        </button>
    </div>

    <div id="formulario-envio" class="hidden bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">
            <i class="fas fa-edit text-cyan-600"></i> <span id="titulo-envio">Crear/Editar Método de Envío</span>
        </h2>
        
        <form action="../core/procesar_configuracion.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                <button type="button" onclick="toggleFormulario('envio')" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
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
                        <td class="px-6 py-4 text-sm text-gray-700 font-semibold"><?php echo $env['nombre']; ?></td>
                        <td class="px-6 py-4 text-sm text-green-600 font-bold">$<?php echo number_format($env['costo'], 2); ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo $env['tiempo_estimado']; ?></td>
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
                                <button onclick="eliminarEnvio(<?php echo $env['id_envio']; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow">
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
</div> <script>
// Tus funciones de Envíos se quedan aquí abajo tal cual
function prepararNuevoEnvio() {
    document.getElementById('formulario-envio').classList.remove('hidden');
    document.getElementById('envio_id').value = "";
    document.getElementById('envio_nombre').value = "";
    document.getElementById('envio_costo').value = "";
    document.getElementById('envio_tiempo').value = "";
    document.getElementById('envio_estado').value = "activo";
    document.getElementById('envio_descripcion').value = "";
    document.getElementById('titulo-envio').innerText = "Crear Nuevo Método de Envío";
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function editarEnvio(datos) {
    document.getElementById('formulario-envio').classList.remove('hidden');
    document.getElementById('envio_id').value = datos.id_envio;
    document.getElementById('envio_nombre').value = datos.nombre;
    document.getElementById('envio_costo').value = datos.costo;
    document.getElementById('envio_tiempo').value = datos.tiempo_estimado;
    document.getElementById('envio_estado').value = datos.estado;
    document.getElementById('envio_descripcion').value = datos.descripcion || "";
    document.getElementById('titulo-envio').innerText = "Editando: " + datos.nombre;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function eliminarEnvio(id) {
    if(confirm('¿Seguro que quieres eliminar este método?')) {
        window.location.href = `../core/procesar_configuracion.php?eliminar_envio=${id}`;
    }
}
</script>

            <!-- MÉTODOS DE PAGO -->

            <div id="tab-metodos-pago" class="tab-content hidden">
    <div class="mb-6">
        <button onclick="prepararNuevoPago()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
            <i class="fas fa-plus"></i> Nuevo Método de Pago
        </button>
    </div>

    <div id="formulario-pago" class="hidden bg-white rounded-lg shadow-lg p-8 mb-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">
            <i class="fas fa-edit text-cyan-600"></i> <span id="titulo-pago">Crear/Editar Método de Pago</span>
        </h2>
        <form action="../core/procesar_configuracion.php" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                <button type="button" onclick="toggleFormulario('pago')" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
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
                <tbody id="tabla-pagos-body">
                    <?php
                    // Usamos la conexión ya existente
                    $sql_pagos = "SELECT * FROM metodos_pago ORDER BY id_metodo_pago DESC";
                    $res_pagos = mysqli_query($conexion, $sql_pagos);

                    if($res_pagos && mysqli_num_rows($res_pagos) > 0):
                        while($row = mysqli_fetch_assoc($res_pagos)): 
                    ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-700">#<?php echo $row['id_metodo_pago']; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-700 font-semibold"><?php echo $row['nombre']; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?php echo $row['descripcion']; ?></td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo ($row['estado'] == 'activo') ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                <?php echo ucfirst($row['estado']); ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick='llenarFormularioPago(<?php echo json_encode($row); ?>)' class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded shadow">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="eliminarPago(<?php echo $row['id_metodo_pago']; ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded shadow">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
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

<script>
// Función para resetear el formulario (NUEVO PAGO)
function prepararNuevoPago() {
    document.getElementById('formulario-pago').classList.remove('hidden');
    document.getElementById('id_pago').value = "";
    document.getElementById('nombre_pago').value = "";
    document.getElementById('descripcion_pago').value = "";
    document.getElementById('estado_pago').value = "activo";
    document.getElementById('titulo-pago').innerText = "Crear Nuevo Método de Pago";
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Función para EDITAR
function llenarFormularioPago(datos) {
    document.getElementById('formulario-pago').classList.remove('hidden');
    document.getElementById('id_pago').value = datos.id_metodo_pago;
    document.getElementById('nombre_pago').value = datos.nombre;
    document.getElementById('estado_pago').value = datos.estado;
    document.getElementById('descripcion_pago').value = datos.descripcion;
    document.getElementById('titulo-pago').innerText = "Editando: " + datos.nombre;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Función para ELIMINAR
function eliminarPago(id) {
    if(confirm('¿Estás seguro de que deseas eliminar este método de pago?')) {
        window.location.href = `../core/procesar_configuracion.php?eliminar_pago=${id}`;
    }
}
</script>



            <!-- CONFIGURACIÓN GENERAL -->
            <div id="tab-general" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">
                        <i class="fas fa-cogs text-cyan-600"></i> Configuración General
                    </h2>
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-store text-cyan-500"></i> Nombre del Negocio
                            </label>
                            <input type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="Mi Negocio">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-envelope text-cyan-500"></i> Correo
                            </label>
                            <input type="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="contacto@minegocio.com">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-phone text-cyan-500"></i> Teléfono
                            </label>
                            <input type="tel" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="123-456-7890">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt text-cyan-500"></i> Dirección
                            </label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="Dirección del negocio">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-cyan-500"></i> Texto de Inicio
                            </label>
                            <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="Bienvenido a nuestro negocio..."></textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-share-alt text-cyan-500"></i> Redes Sociales (JSON)
                            </label>
                            <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder='{"facebook":"url","twitter":"url"}'></textarea>
                        </div>

                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                                <i class="fas fa-save mr-2"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>

    <script>
        // Funciones globales
        window.toggleFormulario = function(type) {
            const formulario = document.getElementById('formulario-' + type);
            if (formulario) {
                formulario.classList.toggle('hidden');
            }
        }

        window.mostrarTab = function(tabId) {
            // Ocultar todos los tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Mostrar el tab seleccionado
            const selectedTab = document.getElementById('tab-' + tabId);
            if (selectedTab) {
                selectedTab.classList.remove('hidden');
            }
            
            // Actualizar estilos de botones
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-cyan-600', 'hover:bg-cyan-700');
                btn.classList.add('bg-gray-400', 'hover:bg-gray-500');
            });
            
            // Buscar el botón que corresponde a este tab y darle estilo activo
            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach((btn, index) => {
                // Determinar qué tab le corresponde a este botón basado en su posición o contenido
                let btnTabId = '';
                if (btn.textContent.includes('Marcas')) {
                    btnTabId = 'marcas';
                } else if (btn.textContent.includes('Envío')) {
                    btnTabId = 'metodos-envio';
                } else if (btn.textContent.includes('Pago')) {
                    btnTabId = 'metodos-pago';
                } else if (btn.textContent.includes('General')) {
                    btnTabId = 'general';
                }
                
                if (btnTabId === tabId) {
                    btn.classList.remove('bg-gray-400', 'hover:bg-gray-500');
                    btn.classList.add('bg-cyan-600', 'hover:bg-cyan-700');
                }
            });
        }

        // Función para inicializar los event listeners
        function initConfiguracionFunctions() {
            // Preview de imagen para marcas
            const marcaImagenInput = document.getElementById('marca-imagen-input');
            if (marcaImagenInput) {
                marcaImagenInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const preview = document.getElementById('preview-marca');
                            const img = document.getElementById('preview-marca-img');
                            if (preview && img) {
                                img.src = event.target.result;
                                preview.classList.remove('hidden');
                            }
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }
        }

        // Ejecutar al cargar el contenido
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initConfiguracionFunctions);
        } else {
            initConfiguracionFunctions();
        }
    </script>