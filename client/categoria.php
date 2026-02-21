<?php
require_once '../core/sesiones.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    header("Location: ../index1.php");
    exit();
}
require_once '../core/conexion.php';
// INSERTAR
if(isset($_POST['guardar'])){
    $nombre = $_POST['nombre'];
    $icono = $_POST['icono'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    $conexion->query("INSERT INTO categorias (nombre, icono, descripcion, estado)
                      VALUES ('$nombre','$icono','$descripcion','$estado')");

    header("Location: categorias.php");
    exit();
}

// ELIMINAR
if(isset($_GET['eliminar'])){
    $id = $_GET['eliminar'];
    $conexion->query("DELETE FROM categorias WHERE id=$id");

    header("Location: categorias.php");
    exit();
}

// EDITAR (CARGAR DATOS)
$editar = null;
if(isset($_GET['editar'])){
    $id = $_GET['editar'];
    $resultado = $conexion->query("SELECT * FROM categorias WHERE id=$id");
    $editar = $resultado->fetch_assoc();
}

// ACTUALIZAR
if(isset($_POST['actualizar'])){
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $icono = $_POST['icono'];
    $descripcion = $_POST['descripcion'];
    $estado = $_POST['estado'];

    $conexion->query("UPDATE categorias SET
                      nombre='$nombre',
                      icono='$icono',
                      descripcion='$descripcion',
                      estado='$estado'
                      WHERE id=$id");

    header("Location: categorias.php");
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
                <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div> 
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-heading text-purple-500"></i> Nombre de la Categoría
                        </label>
                        <input type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" placeholder="Ej: Electrónica">
                    </div>
                    <input type="hidden" name="id" value="<?php echo $editar['id'] ?? ''; ?>">

                    <input type="text" name="nombre" required
                    value="<?php echo $editar['nombre'] ?? ''; ?>"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">

                    <!-- Ícono -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-icons text-purple-500"></i> Ícono (Font Awesome)
                        </label>
                        <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" placeholder="Ej: fa-laptop">
                      <input type="text" name="icono"
                        value="<?php echo $editar['icono'] ?? ''; ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    
                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-purple-500"></i> Descripción
                        </label>
                        <textarea rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" placeholder="Descripción de la categoría..."></textarea>
                        <textarea name="descripcion" rows="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php echo $editar['descripcion'] ?? ''; ?></textarea>
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
                        <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="activo" <?php if(($editar['estado'] ?? '')=='activo') echo 'selected'; ?>>Activo</option>
                        <option value="inactivo" <?php if(($editar['estado'] ?? '')=='inactivo') echo 'selected'; ?>>Inactivo</option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <?php if($editar){ ?>
                    <button type="submit" name="actualizar"
                     class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                     <i class="fas fa-edit mr-2"></i> Actualizar Categoría
                    </button>
                    <?php } else { ?>
                     <button type="submit" name="guardar"
                     class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg">
                     <i class="fas fa-save mr-2"></i> Guardar Categoría
                    </button>
                    <?php } ?>
       
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
                    <tbody>
                <?php
                $resultado = $conexion->query("SELECT * FROM categorias");

                while($fila = $resultado->fetch_assoc()){
                ?>
               <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-6 py-4"><?php echo $fila['id']; ?></td>
                <td class="px-6 py-4 font-semibold"><?php echo $fila['nombre']; ?></td>
                <td class="px-6 py-4">
                 <i class="fas <?php echo $fila['icono']; ?> text-lg"></i>
                 </td>
                 <td class="px-6 py-4"><?php echo $fila['descripcion']; ?></td>
                 <td class="px-6 py-4">
                 <?php if($fila['estado']=='activo'){ ?>
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs">Activo</span>
                <?php } else { ?>
                <span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs">Inactivo</span>
                 <?php } ?>
                 </td>
                 <td class="px-6 py-4 text-center">
                <a href="?editar=<?php echo $fila['id']; ?>"
                class="bg-blue-500 text-white px-3 py-1 rounded mr-2">
                <i class="fas fa-edit"></i>
                 </a>

                <a href="?eliminar=<?php echo $fila['id']; ?>"
                onclick="return confirm('¿Eliminar esta categoría?')"
                class="bg-red-500 text-white px-3 py-1 rounded">
                <i class="fas fa-trash"></i>
                </a>
             </td>
             </tr>
            <?php } ?>
            </tbody>      