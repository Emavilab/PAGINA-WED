<?php
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

$sql = "SELECT 
            clientes.id_usuario,
            clientes.nombre,
            clientes.estado,
            clientes.fecha_registro,
            usuarios.correo
        FROM clientes
        INNER JOIN usuarios 
            ON clientes.id_usuario = usuarios.id_usuario";

$resultado = mysqli_query($conexion, $sql);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Clientes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">

        <main class="max-w-7xl mx-auto px-4 py-8">
            <!-- Botón Crear -->
            <div class="mb-6">
                <button onclick="toggleFormulario('crear')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
                    <i class="fas fa-plus"></i> Nuevo Cliente
                </button>
            </div>

            <!-- Formulario Crear/Editar -->
            <div id="formulario-crear" class="hidden bg-white rounded-lg shadow-lg p-8 mb-8">
                <h2 class="text-2xl font-bold mb-6 text-gray-800">
                    <i class="fas fa-edit text-indigo-600"></i> Crear/Editar Cliente
                </h2>
                <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-indigo-500"></i> Nombre Completo
                        </label>
                        <input type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition" placeholder="Juan Pérez">
                    </div>

                    <!-- Correo -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-indigo-500"></i> Correo Electrónico
                        </label>
                        <input type="email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition" placeholder="juan@example.com">
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone text-indigo-500"></i> Teléfono
                        </label>
                        <input type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition" placeholder="123-456-7890">
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-indigo-500"></i> Contraseña
                        </label>
                        <input type="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition" placeholder="••••••••">
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-toggle-on text-green-500"></i> Estado
                        </label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent outline-none transition">
                            <option value="activo">Activo</option>
                            <option value="bloqueado">Bloqueado</option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="md:col-span-2 flex gap-4">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                            <i class="fas fa-save mr-2"></i> Guardar Cliente
                        </button>
                        <button type="button" onclick="toggleFormulario('crear')" class="flex-1 bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
                            <i class="fas fa-times mr-2"></i> Cancelar
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tabla de Clientes -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-800 text-white p-4">
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i class="fas fa-list"></i> Lista de Clientes
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100 border-b-2 border-gray-300">
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">ID</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Nombre</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Correo</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Fecha Registro</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Estado</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold text-gray-700">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
<?php while($fila = mysqli_fetch_assoc($resultado)) { ?>
<tr class="border-b border-gray-200 hover:bg-gray-50 transition">
    
    <td class="px-6 py-4 text-sm text-gray-700">
        <?php echo $fila['id_usuario']; ?>
    </td>

    <td class="px-6 py-4 text-sm text-gray-700 font-semibold">
        <?php echo $fila['nombre']; ?>
    </td>

    <td class="px-6 py-4 text-sm text-gray-700">
        <?php echo $fila['correo']; ?>
    </td>

    <td class="px-6 py-4 text-sm text-gray-600">
        <?php echo $fila['fecha_registro']; ?>
    </td>

    <td class="px-6 py-4 text-sm">
        <?php if($fila['estado'] == 'activo') { ?>
            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span>
        <?php } else { ?>
            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">Inactivo</span>
        <?php } ?>
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
<?php } ?>
</tbody>

                    </table>
        </div>
    </div>
</div>