<?php
require_once 'sesiones.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    header("Location: index1.php");
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
                <!-- Botón Crear -->
                <div class="mb-6">
                    <button onclick="toggleFormulario('marca')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nueva Marca
                    </button>
                </div>

                <!-- Formulario -->
                <div id="formulario-marca" class="hidden bg-white rounded-lg shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">
                        <i class="fas fa-edit text-cyan-600"></i> Crear/Editar Marca
                    </h2>
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heading text-cyan-500"></i> Nombre de la Marca
                            </label>
                            <input type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="Ej: Sony">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-green-500"></i> Estado
                            </label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-image text-cyan-500"></i> Logo/Imagen de la Marca
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-cyan-500 transition cursor-pointer" onclick="document.getElementById('marca-imagen-input').click()">
                                <input type="file" id="marca-imagen-input" class="hidden" accept="image/*">
                                <div id="preview-marca" class="hidden mb-4">
                                    <img id="preview-marca-img" src="" alt="Preview" class="h-32 mx-auto rounded-lg shadow">
                                </div>
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-gray-600">Arrastra la imagen o haz clic para seleccionar</p>
                                <p class="text-xs text-gray-500 mt-2">PNG, JPG, GIF (máx. 2MB)</p>
                            </div>
                        </div>

                        <div class="md:col-span-2 flex gap-4">
                            <button type="submit" class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                                <i class="fas fa-save mr-2"></i> Guardar Marca
                            </button>
                            <button type="button" onclick="toggleFormulario('marca')" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Tabla -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-600 to-cyan-800 text-white p-4">
                        <h2 class="text-xl font-bold"><i class="fas fa-list mr-2"></i> Lista de Marcas</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-100 border-b-2 border-gray-300">
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Logo</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                    <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">1</td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold">Sony</td>
                                    <td class="px-6 py-4 text-sm"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span></td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition"><i class="fas fa-edit"></i></button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">2</td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center overflow-hidden font-bold text-white text-lg">
                                            D
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold">Dell</td>
                                    <td class="px-6 py-4 text-sm"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span></td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition"><i class="fas fa-edit"></i></button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">3</td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="w-12 h-12 bg-white border-2 border-gray-400 rounded-lg flex items-center justify-center overflow-hidden font-bold text-gray-700">
                                            🍎
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold">Apple</td>
                                    <td class="px-6 py-4 text-sm"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span></td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition"><i class="fas fa-edit"></i></button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">4</td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-700 rounded-lg flex items-center justify-center overflow-hidden font-bold text-white">
                                            H
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold">HP</td>
                                    <td class="px-6 py-4 text-sm"><span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">Inactivo</span></td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition"><i class="fas fa-edit"></i></button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MÉTODOS DE ENVÍO -->
            <div id="tab-metodos-envio" class="tab-content hidden">
                <div class="mb-6">
                    <button onclick="toggleFormulario('envio')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nuevo Método de Envío
                    </button>
                </div>

                <div id="formulario-envio" class="hidden bg-white rounded-lg shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">
                        <i class="fas fa-edit text-cyan-600"></i> Crear/Editar Método de Envío
                    </h2>
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heading text-cyan-500"></i> Nombre
                            </label>
                            <input type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="Ej: Envío Express">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-dollar-sign text-green-500"></i> Costo
                            </label>
                            <input type="number" step="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="0.00">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clock text-orange-500"></i> Tiempo Estimado
                            </label>
                            <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="Ej: 1-2 días">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-green-500"></i> Estado
                            </label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-cyan-500"></i> Descripción
                            </label>
                            <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="Descripción del método de envío..."></textarea>
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

                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
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
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">1</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold">Envío Express</td>
                                    <td class="px-6 py-4 text-sm text-green-600 font-semibold">$5.00</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">1-2 días</td>
                                    <td class="px-6 py-4 text-sm"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span></td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition"><i class="fas fa-edit"></i></button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MÉTODOS DE PAGO -->
            <div id="tab-metodos-pago" class="tab-content hidden">
                <div class="mb-6">
                    <button onclick="toggleFormulario('pago')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
                        <i class="fas fa-plus"></i> Nuevo Método de Pago
                    </button>
                </div>

                <div id="formulario-pago" class="hidden bg-white rounded-lg shadow-lg p-8 mb-8">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">
                        <i class="fas fa-edit text-cyan-600"></i> Crear/Editar Método de Pago
                    </h2>
                    <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heading text-cyan-500"></i> Nombre
                            </label>
                            <input type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="Ej: Tarjeta de Crédito">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-toggle-on text-green-500"></i> Estado
                            </label>
                            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition">
                                <option value="activo">Activo</option>
                                <option value="inactivo">Inactivo</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-cyan-500"></i> Descripción
                            </label>
                            <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-cyan-500 focus:border-transparent outline-none transition" placeholder="Descripción del método de pago..."></textarea>
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

                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-600 to-cyan-800 text-white p-4">
                        <h2 class="text-xl font-bold"><i class="fas fa-list mr-2"></i> Métodos de Pago</h2>
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
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">1</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold"><i class="fas fa-credit-card text-blue-500 mr-2"></i>Tarjeta de Crédito</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Visa, MasterCard</td>
                                    <td class="px-6 py-4 text-sm"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span></td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition"><i class="fas fa-edit"></i></button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-700">2</td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-semibold"><i class="fas fa-mobile-alt text-green-500 mr-2"></i>Transferencia</td>
                                    <td class="px-6 py-4 text-sm text-gray-600">Transferencia bancaria</td>
                                    <td class="px-6 py-4 text-sm"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span></td>
                                    <td class="px-6 py-4 text-sm text-center">
                                        <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition"><i class="fas fa-edit"></i></button>
                                        <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

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