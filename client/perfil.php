<?php
require_once '../core/sesiones.php';

// Verificar autenticación
if (!usuarioAutenticado()) {
    http_response_code(401);
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit();
}

$usuario = obtenerDatosUsuario();

// Validar que el cliente exista
if (!isset($usuario['id_cliente']) || !$usuario['id_cliente']) {
    http_response_code(401);
    echo "<script>window.location='?modulo=login';</script>";
    exit();
}
?>
<main class="flex-grow max-w-7xl mx-auto px-4 py-8 md:py-12 w-full">
<div class="mb-10">
<h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Configuración de Cuenta</h1>
<p class="text-slate-500 dark:text-slate-400 mt-1">Gestiona tu información personal y preferencias de seguridad.</p>
</div>
<div class="lg:hidden bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 mb-6 overflow-x-auto shadow-sm">
<div class="flex gap-0">
<button class="nav-tab-mobile flex-1 px-3 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm font-semibold text-center border-b-2 border-primary text-primary whitespace-nowrap" data-tab="personal">
<span class="material-symbols-outlined text-lg sm:text-xl inline sm:block">person</span>
<span class="hidden sm:inline">Información</span>
</button>
<button class="nav-tab-mobile flex-1 px-3 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 border-b-2 border-transparent hover:text-slate-900 dark:hover:text-white text-center whitespace-nowrap" data-tab="direcciones">
<span class="material-symbols-outlined text-lg sm:text-xl inline sm:block">location_on</span>
<span class="hidden sm:inline">Direcciones</span>
</button>
<button class="nav-tab-mobile flex-1 px-3 sm:px-4 py-3 sm:py-4 text-xs sm:text-sm font-medium text-slate-600 dark:text-slate-400 border-b-2 border-transparent hover:text-slate-900 dark:hover:text-white text-center whitespace-nowrap" data-tab="seguridad">
<span class="material-symbols-outlined text-lg sm:text-xl inline sm:block">shield</span>
<span class="hidden sm:inline">Seguridad</span>
</button>
</div>
</div>
<div class="flex flex-col lg:flex-row gap-8">
<aside class="hidden lg:block w-full lg:w-64 flex-shrink-0">
<nav class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm sticky top-4">
<ul class="flex flex-col">
<li>
<a class="flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4 text-sm font-semibold nav-link-active cursor-pointer nav-tab" data-tab="personal" href="javascript:void(0)">
<span class="material-symbols-outlined text-lg sm:text-xl">person</span>
<span class="hidden sm:inline">Información Personal</span>
</a>
</li>
<li>
<a class="flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer nav-tab" data-tab="direcciones" href="javascript:void(0)">
<span class="material-symbols-outlined text-lg sm:text-xl">location_on</span>
<span class="hidden sm:inline">Direcciones de Envío</span>
</a>
</li>
<li>
<a class="flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-t border-slate-100 dark:border-slate-800 cursor-pointer nav-tab" data-tab="seguridad" href="javascript:void(0)">
<span class="material-symbols-outlined text-lg sm:text-xl">shield</span>
<span class="hidden sm:inline">Seguridad</span>
</a>
</li>
</ul>
</nav>
</aside>
<div class="flex-grow">
<div id="tab-personal" class="tab-content bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
<div class="p-6 md:p-8 border-b border-slate-100 dark:border-slate-800">
<h2 class="text-xl font-bold text-slate-900 dark:text-white">Información Personal</h2>
<p class="text-sm text-slate-500 mt-1">Actualiza tus datos básicos para mejorar tu experiencia.</p>
</div>
<div class="p-6 md:p-8">
<form id="form-perfil-modal" class="space-y-6">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="text-sm font-bold text-slate-700 dark:text-slate-300" for="full-name">Nombre completo</label>
<input class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" id="full-name" name="nombre" type="text" value="<?php echo htmlspecialchars($usuario['nombre']); ?>"/>
</div>
<div class="space-y-2">
<label class="text-sm font-bold text-slate-700 dark:text-slate-300" for="email">Correo electrónico</label>
<input class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" id="email" name="correo" type="email" value="<?php echo htmlspecialchars($usuario['correo']); ?>"/>
</div>
</div>
<div id="mensaje-perfil-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg"></div>
<div id="mensaje-perfil-exito" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg"></div>
<div class="pt-6 flex justify-end gap-4 border-t border-slate-100 dark:border-slate-800 mt-8">
<button class="px-6 py-2.5 text-sm font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors" type="button" onclick="location.reload()">Cancelar</button>
<button class="px-8 py-2.5 bg-primary hover:bg-blue-600 text-white text-sm font-bold rounded-lg shadow-sm transition-colors" type="submit">Guardar Cambios</button>
</div>
</form>
</div>
</div>
<div id="tab-seguridad" class="tab-content hidden bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
<div class="p-6 md:p-8 border-b border-slate-100 dark:border-slate-800">
<h2 class="text-xl font-bold text-slate-900 dark:text-white">Cambiar Contraseña</h2>
<p class="text-sm text-slate-500 mt-1">Asegúrate de usar una contraseña segura que no utilices en otros sitios.</p>
</div>
<div class="p-6 md:p-8">
<form id="form-contraseña-modal" class="space-y-6">
<div class="max-w-md space-y-6">
<div class="space-y-2">
<label class="text-sm font-bold text-slate-700 dark:text-slate-300" for="current-password">Contraseña actual</label>
<input class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" id="current-password" name="contraseña_actual" placeholder="••••••••••••" type="password"/>
</div>
<div class="space-y-2">
<label class="text-sm font-bold text-slate-700 dark:text-slate-300" for="new-password">Nueva contraseña</label>
<input class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" id="new-password" name="contraseña_nueva" placeholder="Mínimo 6 caracteres" type="password"/>
</div>
<div class="space-y-2">
<label class="text-sm font-bold text-slate-700 dark:text-slate-300" for="confirm-password">Confirmar nueva contraseña</label>
<input class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" id="confirm-password" name="confirmar_contraseña" placeholder="Repite la nueva contraseña" type="password"/>
</div>
</div>
<div id="mensaje-contraseña-error" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg"></div>
<div id="mensaje-contraseña-exito" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg"></div>
<div class="pt-6 flex justify-start border-t border-slate-100 dark:border-slate-800">
<button class="px-8 py-2.5 bg-primary hover:bg-blue-600 text-white text-sm font-bold rounded-lg shadow-sm transition-colors" type="submit">Actualizar Contraseña</button>
</div>
</form>
</div>
<div class="mt-8 p-6 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-2xl flex items-start gap-4">
<span class="material-symbols-outlined text-red-500 mt-0.5">warning</span>
<div>
<h4 class="font-bold text-red-800 dark:text-red-400">Eliminar cuenta</h4>
<p class="text-sm text-red-600 dark:text-red-500/80 mt-1">Una vez que elimines tu cuenta, no hay vuelta atrás. Por favor, asegúrate.</p>
<button class="mt-3 text-sm font-bold text-red-600 hover:text-red-700 underline" type="button" onclick="eliminarCuenta()">Quiero eliminar mi cuenta</button>
</div>
</div>
</div>
</div>
<div id="tab-direcciones" class="tab-content hidden bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
<div class="p-6 md:p-8 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
<div>
<h2 class="text-xl font-bold text-slate-900 dark:text-white">Mis Direcciones de Envío</h2>
<p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Gestiona tus lugares de entrega para un proceso de compra más rápido.</p>
</div>
<button onclick="abrirModalDireccion()" 
class="flex items-center justify-center gap-2 px-4 py-2 bg-primary hover:bg-blue-600 text-white font-bold rounded-lg text-sm transition-all active:scale-95">
<span class="material-symbols-outlined text-base">add</span>
<span class="hidden sm:inline">Añadir</span>
</button>
</div>
<div class="p-6 md:p-8">
<div id="contenedor-direcciones" 
     class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
</div>
<button onclick="abrirModalDireccion()" 
class="w-full bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700 p-6 flex flex-col items-center justify-center text-slate-400 hover:text-primary hover:border-primary transition-all group">
<span class="material-symbols-outlined text-4xl mb-2 group-hover:scale-110 transition-transform">
add_circle
</span>
<span class="font-bold text-sm">
Añadir otra dirección
</span>
</button>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
<!-- Modal Agregar Dirección -->
<div id="modal-direccion" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-lg p-8 shadow-xl relative">
        <h3 class="text-xl font-bold mb-6">Agregar Nueva Dirección</h3>

        <form id="form-direccion" class="space-y-4">

            <div>
                <label class="text-sm font-semibold">Dirección</label>
                <input type="text" name="direccion" required class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="text-sm font-semibold">Ciudad</label>
                <input type="text" name="ciudad" required class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="text-sm font-semibold">Departamento <span class="text-red-500">*</span></label>
                <select name="id_departamento" id="departamento" required class="w-full border rounded-lg px-4 py-2">
                    <option value="">Seleccione departamento</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Código Postal</label>
                <input type="text" name="codigo_postal" class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="text-sm font-semibold">Teléfono</label>
                <input type="text" name="telefono" class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="text-sm font-semibold">Referencia</label>
                <input type="text" name="referencia" class="w-full border rounded-lg px-4 py-2">
            </div>

            <div id="mensaje-direccion" class="hidden text-sm rounded-lg px-3 py-2"></div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="cerrarModalDireccion()" class="px-4 py-2 border rounded-lg">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg">
                    Guardar
                </button>
            </div>

        </form>
    </div>
</div>
<!-- Modal Editar Dirección -->
<div id="modal-editar-direccion" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-lg p-8 shadow-xl relative">
        <h3 class="text-xl font-bold mb-6">Editar Dirección</h3>

        <form id="form-editar-direccion" class="space-y-4">

            <input type="hidden" name="id_direccion" id="edit-id">

            <div>
                <label class="text-sm font-semibold">Dirección</label>
                <input type="text" name="direccion" id="edit-direccion" required class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="text-sm font-semibold">Ciudad</label>
                <input type="text" name="ciudad" id="edit-ciudad" required class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="text-sm font-semibold">Departamento <span class="text-red-500">*</span></label>
                <select name="id_departamento" id="edit-id_departamento" required class="w-full border rounded-lg px-4 py-2">
                    <option value="">Seleccione departamento</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-semibold">Código Postal</label>
                <input type="text" name="codigo_postal" id="edit-cp" class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="text-sm font-semibold">Teléfono</label>
                <input type="text" name="telefono" id="edit-telefono" class="w-full border rounded-lg px-4 py-2">
            </div>

            <div>
                <label class="text-sm font-semibold">Referencia</label>
                <input type="text" name="referencia" id="edit-referencia" class="w-full border rounded-lg px-4 py-2">
            </div>

            <div id="mensaje-editar-direccion" class="hidden text-sm rounded-lg px-3 py-2"></div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="cerrarEditarDireccion()" class="px-4 py-2 border rounded-lg">
                    Cancelar
                </button>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg">
                    Guardar Cambios
                </button>
            </div>

        </form>
    </div>
</div>
<!-- Modal Confirmar Eliminación Dirección -->
<div id="modal-eliminar-direccion" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md p-8 shadow-xl relative">
        
        <h3 class="text-xl font-bold mb-4 text-slate-900 dark:text-white">
            Confirmar eliminación
        </h3>

        <p class="text-sm text-slate-600 dark:text-slate-400 mb-6">
            ¿Seguro que deseas eliminar esta dirección?
        </p>

        <div class="flex justify-end gap-3">
            <button 
                type="button"
                onclick="cerrarModalEliminarDireccion()"
                class="px-4 py-2 border rounded-lg">
                Cancelar
            </button>

            <button 
                type="button"
                onclick="confirmarEliminarDireccion()"
                class="px-6 py-2 bg-red-600 text-white rounded-lg">
                Eliminar
            </button>
        </div>

    </div>
</div>
<script>
// ===============================
// MANEJO DE TABS
// ===============================
let direccionAEliminar = null;
function initTabs() {
    // Tabs de desktop
    const tabs = document.querySelectorAll('.nav-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });

    // Tabs de mobile
    const mobileTabButtons = document.querySelectorAll('.nav-tab-mobile');
    mobileTabButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });
}

function showTab(tabName) {
    // Ocultar todos los tabs
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });

    // Desactivar todos los tabs desktop
    const tabs = document.querySelectorAll('.nav-tab');
    tabs.forEach(tab => {
        tab.classList.remove('nav-link-active');
        tab.classList.add('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-50', 'dark:hover:bg-slate-800');
    });

    // Desactivar todos los tabs mobile
    const mobileTabButtons = document.querySelectorAll('.nav-tab-mobile');
    mobileTabButtons.forEach(btn => {
        btn.classList.remove('border-primary', 'text-primary', 'font-semibold');
        btn.classList.add('border-transparent', 'text-slate-600', 'dark:text-slate-400', 'font-medium');
    });

    // Mostrar tab activo
    const activeContent = document.getElementById('tab-' + tabName);
    if (activeContent) {
        activeContent.classList.remove('hidden');
    }

    // Activar tab desktop
    const activeTab = document.querySelector('.nav-tab[data-tab="' + tabName + '"]');
    if (activeTab) {
        activeTab.classList.add('nav-link-active');
        activeTab.classList.remove('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-50', 'dark:hover:bg-slate-800');
    }

    // Activar tab mobile
    const activeMobileTab = document.querySelector('.nav-tab-mobile[data-tab="' + tabName + '"]');
    if (activeMobileTab) {
        activeMobileTab.classList.remove('border-transparent', 'text-slate-600', 'dark:text-slate-400', 'font-medium');
        activeMobileTab.classList.add('border-primary', 'text-primary', 'font-semibold');
    }
}


// ===============================
// MODAL DIRECCIÓN
// ===============================

function abrirModalDireccion() {
    const modal = document.getElementById('modal-direccion');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModalDireccion() {
    const modal = document.getElementById('modal-direccion');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
function abrirEditarDireccion(id, direccion, ciudad, cp, telefono, referencia, id_departamento) {

    document.getElementById("edit-id").value = id;
    document.getElementById("edit-direccion").value = direccion;
    document.getElementById("edit-ciudad").value = ciudad;
    document.getElementById("edit-cp").value = cp;
    document.getElementById("edit-telefono").value = telefono;
    document.getElementById("edit-referencia").value = referencia;
    const selDep = document.getElementById("edit-id_departamento");
    if (selDep && id_departamento) selDep.value = id_departamento;

    const modal = document.getElementById("modal-editar-direccion");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

function cerrarEditarDireccion() {
    const modal = document.getElementById("modal-editar-direccion");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}
function abrirModalEliminarDireccion(id) {
    direccionAEliminar = id;

    const modal = document.getElementById("modal-eliminar-direccion");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

function cerrarModalEliminarDireccion() {
    direccionAEliminar = null;

    const modal = document.getElementById("modal-eliminar-direccion");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}
async function confirmarEliminarDireccion() {

    if (!direccionAEliminar) return;

    const formData = new FormData();
    formData.append("id_direccion", direccionAEliminar);

    const response = await fetch("api/api_eliminar_direccion.php", {
        method: "POST",
        body: formData,
        credentials: "include"
    });

    const data = await response.json();

    if (data.success) {
        cerrarModalEliminarDireccion();
        cargarDirecciones();
    }
}

// ===============================
// INICIALIZADOR DE LA VISTA (SPA)
// ===============================
async function cargarDepartamentosPerfil() {
    try {
        const response = await fetch("api/api_obtener_departamentos.php", { credentials: "include" });
        const data = await response.json();
        if (!data.success || !data.departamentos || !data.departamentos.length) return;
        const optionsHtml = '<option value="">Seleccione departamento</option>' +
            data.departamentos.map(d => `<option value="${d.id_departamento}">${d.nombre_departamento}</option>`).join('');
        const selNuevo = document.getElementById("departamento");
        const selEdit = document.getElementById("edit-id_departamento");
        if (selNuevo) selNuevo.innerHTML = optionsHtml;
        if (selEdit) selEdit.innerHTML = optionsHtml;
    } catch (err) {
        console.error("Error cargando departamentos:", err);
    }
}

function iniciarPerfil() {

    initTabs();
    setupPerfilForm();
    setupContraseñaForm();
    cargarDepartamentosPerfil();
    cargarDirecciones();


    const formDireccion = document.getElementById("form-direccion");

    if (formDireccion) {
        formDireccion.addEventListener("submit", async function (e) {
            e.preventDefault();

            const mensaje = document.getElementById("mensaje-direccion");
            mensaje.classList.add("hidden");

            const formData = new FormData(this);

            try {

                const response = await fetch("api/api_crear_direccion.php", {
                    method: "POST",
                    body: formData,
                    credentials: "include" // 🔥 ENVÍA LA SESIÓN
                });

                const data = await response.json();

                if (data.success) { // 🔥 ahora coincide con el backend
                    mensaje.classList.remove("hidden");
                    mensaje.classList.remove("bg-red-100", "text-red-700");
                    mensaje.classList.add("bg-green-100", "text-green-700");
                    mensaje.textContent = data.message;

                   setTimeout(() => {
                        cerrarModalDireccion();
                        this.reset();
                        cargarDirecciones();
                    }, 500);
                } else {
                    mensaje.classList.remove("hidden");
                    mensaje.classList.add("bg-red-100", "text-red-700");
                    mensaje.textContent = data.message;
                }

            } catch (error) {

                mensaje.classList.remove("hidden");
                mensaje.classList.add("bg-red-100", "text-red-700");
                mensaje.textContent = "Error del servidor";
                console.error(error);
            }

        });
    }
    const formEditar = document.getElementById("form-editar-direccion");

if (formEditar) {

    formEditar.addEventListener("submit", async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        const response = await fetch("api/api_editar_direccion.php", {
            method: "POST",
            body: formData,
            credentials: "include"
        });

        const data = await response.json();

        if (data.success) {
            cerrarEditarDireccion();
            cargarDirecciones();
        }
    });
}
}

// ===============================
// FORMULARIO PERFIL
// ===============================

function setupPerfilForm() {
    const form = document.getElementById('form-perfil-modal');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const mensajeError = document.getElementById('mensaje-perfil-error');
        const mensajeExito = document.getElementById('mensaje-perfil-exito');
        if (mensajeError) mensajeError.classList.add('hidden');
        if (mensajeExito) mensajeExito.classList.add('hidden');
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/api_actualizar_perfil.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.exito) {
                if (mensajeExito) {
                    mensajeExito.textContent = data.mensaje;
                    mensajeExito.classList.remove('hidden');
                }
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                if (mensajeError) {
                    mensajeError.textContent = data.mensaje || 'Error desconocido';
                    mensajeError.classList.remove('hidden');
                }
            }
        } catch (error) {
            if (mensajeError) {
                mensajeError.textContent = 'Error al conectar con el servidor';
                mensajeError.classList.remove('hidden');
            }
        }
    });
}
async function cargarDirecciones() {

    const contenedor = document.getElementById("contenedor-direcciones");

    try {

        const response = await fetch("api/api_obtener_direcciones.php", {
            credentials: "include"
        });

        const data = await response.json();

        if (!data.success) return;

        contenedor.innerHTML = "";

        data.direcciones.forEach(dir => {

            contenedor.innerHTML += `
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm p-6">
                    
                    <h4 class="font-bold text-slate-900 dark:text-white mb-2">
                        ${dir.direccion}
                    </h4>

                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        ${dir.ciudad}
                    </p>

                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Departamento: ${dir.nombre_departamento || '-'}
                    </p>

                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        CP: ${dir.codigo_postal || '-'}
                    </p>

                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Tel: ${dir.telefono || '-'}
                    </p>

                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Ref: ${dir.referencia || '-'}
                    </p>

                    <div class="flex gap-3 mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">

                        <button 
                            type="button"
                            class="btn-editar flex-1 py-2 text-sm font-bold rounded-lg border border-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800"
                            data-id="${dir.id_direccion}"
                            data-direccion="${dir.direccion}"
                            data-ciudad="${dir.ciudad}"
                            data-cp="${dir.codigo_postal || ''}"
                            data-telefono="${dir.telefono || ''}"
                            data-referencia="${dir.referencia || ''}"
                            data-id-departamento="${dir.id_departamento || ''}">
                            Editar
                        </button>

                        <button 
                            type="button"
                            class="btn-eliminar flex-1 py-2 text-sm font-bold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg"
                            data-id="${dir.id_direccion}">
                            Eliminar
                        </button>

                    </div>

                </div>
            `;
        });

    } catch (error) {
        console.error("Error cargando direcciones:", error);
    }
}
function eliminarDireccion(id) {

    CustomModal.show(
        'warning',
        'Confirmación',
        '¿Seguro que deseas eliminar esta dirección?',
        async (confirmed) => {

            if (!confirmed) return;

            const formData = new FormData();
            formData.append("id_direccion", id);

            const response = await fetch("api/api_eliminar_direccion.php", {
                method: "POST",
                body: formData,
                credentials: "include"
            });

            const data = await response.json();

            if (data.success) {
                cargarDirecciones();
            }
        }
    );
}

// ===============================
// FORMULARIO CONTRASEÑA
// ===============================

function setupContraseñaForm() {
    const form = document.getElementById('form-contraseña-modal');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const mensajeError = document.getElementById('mensaje-contraseña-error');
        const mensajeExito = document.getElementById('mensaje-contraseña-exito');
        if (mensajeError) mensajeError.classList.add('hidden');
        if (mensajeExito) mensajeExito.classList.add('hidden');
        
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/api_cambiar_contraseña.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.exito) {
                if (mensajeExito) {
                    mensajeExito.textContent = data.mensaje;
                    mensajeExito.classList.remove('hidden');
                }
                
                form.reset();

                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                if (mensajeError) {
                    mensajeError.textContent = data.mensaje || 'Error desconocido';
                    mensajeError.classList.remove('hidden');
                }
            }
        } catch (error) {
            if (mensajeError) {
                mensajeError.textContent = 'Error al conectar con el servidor';
                mensajeError.classList.remove('hidden');
            }
        }
    });
}


// ===============================
// ELIMINAR CUENTA
// ===============================

function eliminarCuenta() {
    if (!confirm('¿Estás completamente seguro de que deseas eliminar tu cuenta? Esta acción no se puede deshacer.')) {
        return;
    }
    
    if (!confirm('Se eliminarán TODOS tus datos (pedidos, direcciones, carrito, etc). ¿Continuar?')) {
        return;
    }
    
    fetch('api/api_eliminar_cuenta.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if (data.debug) {
            console.log('Debug steps:', data.debug);
        }
        
        if (data.exito) {
            alert(data.mensaje);
            // Redirigir después de un pequeño delay para asegurar que el servidor procese
            setTimeout(() => {
                window.location.href = '/PAGINA WED/index.php';
            }, 500);
        } else {
            const mensaje = data.mensaje || 'Error desconocido';
            if (typeof showAuthModal !== 'undefined' && mensaje.toLowerCase().includes('autenticaci')) {
                showAuthModal(mensaje);
            } else {
                alert('Error: ' + mensaje);
            }
            if (data.debug) {
                console.log('Error pasos:', data.debug);
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión: ' + error.message);
    });
}
document.addEventListener("click", function(e) {

    const btnEditar = e.target.closest(".btn-editar");
    const btnEliminar = e.target.closest(".btn-eliminar");

    if (btnEditar) {
        abrirEditarDireccion(
            btnEditar.dataset.id,
            btnEditar.dataset.direccion,
            btnEditar.dataset.ciudad,
            btnEditar.dataset.cp,
            btnEditar.dataset.telefono,
            btnEditar.dataset.referencia,
            btnEditar.dataset.idDepartamento || ''
        );
    }

    if (btnEliminar) {
       abrirModalEliminarDireccion(btnEliminar.dataset.id);
    }
});

// Inicializar perfil cuando se carga la página
document.addEventListener('DOMContentLoaded', function() {
    iniciarPerfil();
});
</script>