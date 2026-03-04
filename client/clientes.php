<?php

require_once '../core/sesiones.php';
require_once '../core/conexion.php';

/* procesar formulario */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    header('Content-Type: application/json; charset=utf-8');

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conexion->set_charset("utf8mb4");

    try {

        $nombre   = trim($_POST['nombre']);
        $correo   = trim($_POST['correo']);
        $password = $_POST['password'];
        $estado   = $_POST['estado'];

        // validar nombre
        if(strlen($nombre) < 3){
            echo json_encode([
                "success" => false,
                "field"   => "nombre",
                "message" => "El nombre debe tener al menos 3 caracteres"
            ]);
            exit();
        }

        // validar correo formato
        if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
            echo json_encode([
                "success" => false,
                "field"   => "correo",
                "message" => "Correo no válido"
            ]);
            exit();
        }

        // validar contraseña fuerte
        if(strlen($password) < 8 ||
           !preg_match('/[A-Z]/', $password) ||
           !preg_match('/[0-9]/', $password)){

            echo json_encode([
                "success" => false,
                "field"   => "password",
                "message" => "La contraseña debe tener 8 caracteres, una mayúscula y un número"
            ]);
            exit();
        }

        if (empty($nombre) || empty($correo) || empty($password) || empty($estado)) {
            echo json_encode([
                "success" => false,
                "message" => "Datos incompletos"
            ]);
            exit();
        }

        // verificar correo existente
        $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $resultadoCorreo = $stmt->get_result();

        if ($resultadoCorreo->num_rows > 0) {
            echo json_encode([
                "success" => false,
                "field"   => "correo",
                "message" => "El correo ya existe"
            ]);
            exit();
        }

        $conexion->begin_transaction();

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contraseña, estado, id_rol) VALUES (?, ?, ?, ?, 3)");
        $stmt->bind_param("ssss", $nombre, $correo, $passwordHash, $estado);
        $stmt->execute();

        $id_usuario = $conexion->insert_id;

        $stmt2 = $conexion->prepare("INSERT INTO clientes (id_usuario, nombre, estado) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $id_usuario, $nombre, $estado);
        $stmt2->execute();

        $conexion->commit();

        echo json_encode([
            "success" => true,
            "message" => "Cliente registrado correctamente"
        ]);
        exit();

    } catch (Exception $e) {

        $conexion->rollback();

        echo json_encode([
            "success" => false,
            "message" => "Error al guardar cliente"
        ]);
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
                        <input type="text" name="nombre" id="nombre"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Juan Pérez">
                        <p id="error-nombre" class="text-red-500 text-sm mt-1 hidden"></p>
                    </div>
 
                    <!-- Correo -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-envelope text-indigo-500"></i> Correo Electrónico
                        </label>
                       <input type="email" name="correo" id="correo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="juan@example.com">
                        <p id="error-correo" class="text-red-500 text-sm mt-1 hidden"></p>  
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock text-indigo-500"></i> Contraseña
                        </label>
                       <input type="password" name="password" id="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="••••••••">
                            <p id="error-password" class="text-red-500 text-sm mt-1 hidden"></p>
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

                    <td class="px-6 py-4 text-sm text-center">
                        <button 
                            class="btn-editar bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded mr-2 transition"
                            data-id="<?php echo $fila['id_usuario']; ?>">
                            <i class="fas fa-edit"></i>
                        </button>

                        <button 
                            class="btn-eliminar bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded transition"
                            data-id="<?php echo $fila['id_usuario']; ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<!-- MODAL EDITAR -->
<div id="modalEditar" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Editar Cliente</h2>

        <form id="formEditar">
            <input type="hidden" name="id" id="edit_id">

            <div class="mb-3">
                <input type="text" name="nombre" id="edit_nombre" 
                class="w-full border px-3 py-2 rounded" required>
            </div>

            <div class="mb-3">
                <input type="email" name="correo" id="edit_correo" 
                class="w-full border px-3 py-2 rounded" required>
            </div>

            <div class="mb-3">
                <select name="estado" id="edit_estado"
                class="w-full border px-3 py-2 rounded">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                 class="flex-1 bg-indigo-600 text-white py-2 rounded">
                Guardar
                    </button>

                <button type="button"
                onclick="cerrarModal()"
                class="flex-1 bg-gray-400 text-white py-2 rounded">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>
<!-- MODAL ELIMINAR -->
<div id="modalEliminar" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm text-center">
        <h2 class="text-lg font-bold mb-4">¿Eliminar cliente?</h2>

        <input type="hidden" id="delete_id">

        <div class="flex gap-3">
            <button onclick="confirmarEliminar()"
            class="flex-1 bg-red-600 text-white py-2 rounded">
                Sí, eliminar
            </button>

            <button onclick="cerrarModal()"
            class="flex-1 bg-gray-400 text-white py-2 rounded">
                Cancelar
            </button>
        </div>
    </div>
</div>

<script>
// TOGGLE FORMULARIO
function toggleFormulario(type) {
    const formulario = document.getElementById('formulario-' + type);
    if (formulario) {
        formulario.classList.toggle('hidden');
    }
}

// CREAR CLIENTE (SPA SAFE)
document.addEventListener("submit", function(e){

    if(e.target && e.target.id === "formCliente"){

        if(e.target.dataset.enviando === "true"){
            return;
        }

        e.target.dataset.enviando = "true";

        e.preventDefault();

        const formData = new FormData(e.target);
        const nombre   = formData.get("nombre").trim();
        const correo   = formData.get("correo").trim();
        const password = formData.get("password");

        limpiarTodosErrores();

        let valido = true;

        if(nombre.length < 3){
            mostrarError("nombre", "Debe tener al menos 3 caracteres");
            valido = false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!emailRegex.test(correo)){
            mostrarError("correo", "Correo electrónico no válido");
            valido = false;
        }

        if(password.length < 8){
            mostrarError("password", "Debe tener mínimo 8 caracteres");
            valido = false;
        }

        if(!valido){
            e.target.dataset.enviando = "false";
            return;
        }

        fetch("/PAGINA-WED/client/clientes.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => {

            e.target.dataset.enviando = "false";

            if(data.success){

                toggleFormulario('crear');
                showSuccessModal(data.message);

                setTimeout(() => {
                    window.location.reload();
                }, 1500);
                

            }else {

            if(data.field){
                mostrarError(data.field, data.message);
            } else {
                alert(data.message);
            }

        }
        })
        .catch(error => {
            e.target.dataset.enviando = "false";
            console.error(error);
        });

    }

});

function mostrarError(inputId, mensaje){

    const input = document.getElementById(inputId);
    const error = document.getElementById("error-" + inputId);

    input.classList.remove("border-gray-300");
    input.classList.add("border-red-500");

    error.textContent = mensaje;
    error.classList.remove("hidden");
}

function limpiarError(inputId){

    const input = document.getElementById(inputId);
    const error = document.getElementById("error-" + inputId);

    input.classList.remove("border-red-500");
    input.classList.add("border-gray-300");

    error.textContent = "";
    error.classList.add("hidden");
}

function limpiarTodosErrores(){
    limpiarError("nombre");
    limpiarError("correo");
    limpiarError("password");
}
document.addEventListener("input", function(e){

    if(e.target.id === "nombre"){
        limpiarError("nombre");
    }

    if(e.target.id === "correo"){
        limpiarError("correo");
    }

    if(e.target.id === "password"){
        limpiarError("password");
    }

});
</script>
<script>

// CLICK GLOBAL (SPA SAFE)
document.addEventListener("click", function(e){

    // EDITAR
    const btnEditar = e.target.closest(".btn-editar");
    if(btnEditar){
        const id = btnEditar.dataset.id;
        abrirModalEditar(id);
    }

    // ELIMINAR
    const btnEliminar = e.target.closest(".btn-eliminar");
    if(btnEliminar){
        const id = btnEliminar.dataset.id;
        document.getElementById("delete_id").value = id;

        const modal = document.getElementById("modalEliminar");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    }

});


// ABRIR MODAL EDITAR
function abrirModalEditar(id){

    fetch("/PAGINA-WED/client/clientes_obtener.php?id=" + id)
    .then(res => res.json())
    .then(data => {

        if(!data){
            alert("No se pudo obtener el cliente");
            return;
        }

        document.getElementById("edit_id").value = data.id_usuario;
        document.getElementById("edit_nombre").value = data.nombre;
        document.getElementById("edit_correo").value = data.correo;
        document.getElementById("edit_estado").value = data.estado;

        const modal = document.getElementById("modalEditar");
        modal.classList.remove("hidden");
        modal.classList.add("flex");

    })
    .catch(err => {
        console.error(err);
        alert("Error al abrir modal");
    });
}


// CERRAR MODAL
function cerrarModal(){

    document.getElementById("modalEditar").classList.add("hidden");
    document.getElementById("modalEditar").classList.remove("flex");

    document.getElementById("modalEliminar").classList.add("hidden");
    document.getElementById("modalEliminar").classList.remove("flex");

}

// EDITAR
document.getElementById("formEditar").addEventListener("submit", function(e){

    console.log("SUBMIT DIRECTO");

    e.preventDefault();

    const formData = new FormData(this);

    fetch("/PAGINA-WED/client/clientes_editar.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {

        if(data.success){

    cerrarModal();
    showSuccessModal("Cliente actualizado correctamente");

    setTimeout(() => {
        window.location.reload();
    }, 1500);

} else {
    alert(data.message);
}

    })
    .catch(err => {
        console.error(err);
        alert("Error en la petición");
    });

});
// ELIMINAR
function confirmarEliminar(){

    const id = document.getElementById("delete_id").value;

<<<<<<< HEAD
    fetch("/PAGINA-WED/client/clientes_eliminar.php", {
=======
    fetch("../client/clientes_eliminar.php", {
>>>>>>> 6e9611b5eabbbf86633436d47c5c95e13abe50b1
        method:"POST",
        headers:{"Content-Type":"application/json"},
        body: JSON.stringify({id:id})
    })
    .then(res => res.json())
    .then(data => {

        if(data.success){

    cerrarModal();
    showSuccessModal("Cliente eliminado correctamente");

    setTimeout(() => {
        if (data.redirect) {
            // Si hay redirect, enviar al usuario eliminado a index
            window.location.href = data.redirect;
        } else {
            // Si es admin eliminando a otro, solo recargar
            window.location.reload();
        }
    }, 1500);

} else if (data.message && data.message.toLowerCase().includes("autenticaci")) {
    // Mensaje de autenticación
    cerrarModal();
    CustomModal.show('info', 'Autenticación Requerida', data.message, function() {
        window.location.href = "?modulo=login";
    });
} else {
    CustomModal.show('error', 'Error', data.message || 'Error al eliminar cliente');
}

    })
    .catch(err => {
        console.error(err);
        CustomModal.show('error', 'Error', 'Error al eliminar cliente');
    });

}
// MODAL ÉXITO
function showSuccessModal(message = "Cliente agregado correctamente") {

    const modal = document.getElementById("modalSuccess");
    modal.querySelector("p").innerText = message;

    modal.classList.remove("hidden");
    modal.classList.add("flex");

    setTimeout(() => {
        closeSuccessModal();
    }, 2000);
}

function closeSuccessModal() {
    const modal = document.getElementById("modalSuccess");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}

// CustomModal fallback si no está definido en Dashboard
if (typeof CustomModal === 'undefined') {
    const CustomModal = {
        show: function(type, title, message, callback) {
            let modal = document.getElementById('customModalClientes');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'customModalClientes';
                modal.innerHTML = `
                    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]">
                        <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full mx-4">
                            <div class="p-6">
                                <div class="flex items-start gap-4">
                                    <div id="modalIcon" class="flex-shrink-0"></div>
                                    <div class="flex-1">
                                        <h3 id="modalTitle" class="text-lg font-bold text-slate-900 mb-2"></h3>
                                        <p id="modalMessage" class="text-sm text-slate-600"></p>
                                    </div>
                                </div>
                            </div>
                            <div id="modalButtons" class="flex gap-3 p-6 bg-slate-50 border-t border-slate-100 rounded-b-xl"></div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            const iconMap = {
                'success': '<span style="font-size: 2rem; color: #10b981;">✓</span>',
                'error': '<span style="font-size: 2rem; color: #ef4444;">✕</span>',
                'info': '<span style="font-size: 2rem; color: #3b82f6;">ℹ</span>'
            };

            document.getElementById('modalIcon').innerHTML = iconMap[type] || iconMap['info'];
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalMessage').textContent = message;

            const buttonsDiv = document.getElementById('modalButtons');
            buttonsDiv.innerHTML = '';

            const okBtn = document.createElement('button');
            if (type === 'info') {
                okBtn.className = 'w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition-colors';
            } else if (type === 'error') {
                okBtn.className = 'w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded transition-colors';
            } else {
                okBtn.className = 'w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded transition-colors';
            }
            okBtn.textContent = 'Aceptar';
            okBtn.onclick = () => {
                modal.remove();
                if (callback) callback();
            };
            buttonsDiv.appendChild(okBtn);
        }
    };
    window.CustomModal = CustomModal;
}

</script>
</div> <!-- FIN MODAL ELIMINAR -->
<!-- MODAL ÉXITO -->
<div id="modalSuccess" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-sm text-center">
        <h2 class="text-lg font-bold mb-4 text-green-600">
            Operación Exitosa
        </h2>

        <p class="text-gray-600 mb-4">
            Cliente agregado correctamente
        </p>

        <button onclick="closeSuccessModal()"
        class="bg-green-600 text-white px-4 py-2 rounded">
            Aceptar
        </button>
    </div>
</div>
</body>
</html>