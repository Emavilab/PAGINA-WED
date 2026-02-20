<?php

require_once '../core/sesiones.php';
require_once '../core/conexion.php';

/* procesar formulario */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // activar reporte de errores
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conexion->set_charset("utf8mb4");

    try {

        // obtener datos
        $nombre   = trim($_POST['nombre']);
        $correo   = trim($_POST['correo']);
        $password = $_POST['password'];
        $estado   = $_POST['estado'];

        // validar campos vacios
        if (empty($nombre) || empty($correo) || empty($password) || empty($estado)) {
            echo "<script>alert('datos incompletos');</script>";
            exit();
        }

        // verificar si correo existe
        $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultadoCorreo = $stmt->get_result();

        if ($resultadoCorreo->num_rows > 0) {
            echo "<script>alert('el correo ya existe');</script>";
            exit();
        }

        // encriptar clave
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // insertar usuario
        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contraseña, estado, id_rol) VALUES (?, ?, ?, ?, 3)");
        $stmt->bind_param("ssss", $nombre, $correo, $passwordHash, $estado);
        $stmt->execute();

        // obtener id generado
        $id_usuario = $conexion->insert_id;

        // insertar cliente
        $stmt2 = $conexion->prepare("INSERT INTO clientes (id_usuario, nombre, estado) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $id_usuario, $nombre, $estado);
        $stmt2->execute();

        echo "<script>alert('cliente registrado correctamente');</script>";

    } catch (Exception $e) {

        // mostrar error real
        echo "<script>alert('error al guardar');</script>";
        exit();
    }
}
/*   CONSULTA PARA TABLA */

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
<script>
document.querySelector("form").addEventListener("submit", function(e) {
    alert("EL FORM SE ESTA ENVIANDO");
});
</script>
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
                <form id="formCliente">
                    <!-- Nombre -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-indigo-500"></i> Nombre Completo
                        </label>
                        <input type="text" name="nombre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Juan Pérez">
                        <input type="hidden" name="test" value="123">
                    </div>

                    <!-- Correo -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-indigo-500"></i> Correo Electrónico
                        </label>
                        <input type="email" name="correo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="juan@example.com">
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-indigo-500"></i> Contraseña
                        </label>
                        <input type="password" name="password" required class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="••••••••">
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-toggle-on text-green-500"></i> Estado
                        </label>
                       <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>

                    <!-- Botones -->
                    <div class="md:col-span-2 flex gap-4">
                        <input type="submit" value="Guardar Cliente"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md">
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
<script>
document.getElementById("formCliente").addEventListener("submit", function(e) {

    e.preventDefault();

    const formData = new FormData(this);

    fetch("../client/clientes.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        document.getElementById("main-content").innerHTML = data;
    })
    .catch(error => {
        alert("error al enviar datos");
        console.error(error);
    });

});
</script>
</tbody>

                    </table>
        </div>
    </div>
</div>