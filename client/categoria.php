<?php
require_once '../core/sesiones.php';

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
    <title>Gestión de Categorías</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <main class="max-w-7xl mx-auto px-4 py-8">
            <!-- Botón Crear -->
            <div class="mb-6">
                <button onclick="toggleFormulario('crear')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </button>
            </div>

            <!-- Formulario Crear/Editar -->
            <div id="formulario-crear" class="hidden bg-white rounded-lg shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">
                    <i class="fas fa-edit text-purple-600"></i> Crear/Editar Categoría
                </h2>
                <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-heading text-purple-500"></i> Nombre de la Categoría
                        </label>
                        <input type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" placeholder="Ej: Electrónica">
                    </div>

                    <!-- Ícono -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-icons text-purple-500"></i> Ícono (Font Awesome)
                        </label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" placeholder="Ej: fa-laptop">
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-purple-500"></i> Descripción
                        </label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" placeholder="Descripción de la categoría..."></textarea>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-toggle-on text-green-500"></i> Estado
                        </label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="md:col-span-2 flex gap-4">
                        <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                            <i class="fas fa-save mr-2"></i> Guardar Categoría
                        </button>
                        <button type="button" onclick="toggleFormulario('crear')" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabla de Categorías -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white p-4">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i class="fas fa-list"></i> Lista de Categorías
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-300">
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Ícono</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Descripción</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-700">1</td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-semibold">Electrónica</td>
                                <td class="px-6 py-4 text-sm">
                                    <i class="fas fa-laptop text-blue-500 text-lg"></i>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">Productos electrónicos y gadgets</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-700">2</td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-semibold">Ropa</td>
                                <td class="px-6 py-4 text-sm">
                                    <i class="fas fa-shirt text-red-500 text-lg"></i>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">Prendas de vestir para todas las edades</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-sm text-gray-700">3</td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-semibold">Alimentos</td>
                                <td class="px-6 py-4 text-sm">
                                    <i class="fas fa-utensils text-yellow-500 text-lg"></i>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">Alimentos y bebidas de calidad</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">Inactivo</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-center">
                                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>