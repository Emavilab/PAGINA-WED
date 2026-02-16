<?php
require_once '../core/sesiones.php';

// Verificar autenticación
if (!usuarioAutenticado()) {
    http_response_code(401);
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit();
}

$usuario = obtenerDatosUsuario();
?>
<main class="flex-grow max-w-7xl mx-auto px-4 py-8 md:py-12 w-full">
<div class="mb-10">
<h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Configuración de Cuenta</h1>
<p class="text-slate-500 dark:text-slate-400 mt-1">Gestiona tu información personal y preferencias de seguridad.</p>
</div>
<div class="flex flex-col lg:flex-row gap-8">
<aside class="w-full lg:w-64 flex-shrink-0">
<nav class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
<ul class="flex flex-col">
<li>
<a class="flex items-center gap-3 px-6 py-4 text-sm font-semibold nav-link-active cursor-pointer nav-tab" data-tab="personal" href="javascript:void(0)">
<span class="material-symbols-outlined text-[20px]">person</span>
Información Personal
</a>
</li>
<li>
<a class="flex items-center gap-3 px-6 py-4 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors" href="#">
<span class="material-symbols-outlined text-[20px]">location_on</span>
Direcciones de Envío
</a>
</li>
<li>
<a class="flex items-center gap-3 px-6 py-4 text-sm font-medium text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-t border-slate-100 dark:border-slate-800 cursor-pointer nav-tab" data-tab="seguridad" href="javascript:void(0)">
<span class="material-symbols-outlined text-[20px]">shield</span>
Seguridad
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
</div>
<div class="mt-8 p-6 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-2xl flex items-start gap-4">
<span class="material-icons text-red-500 mt-0.5">warning</span>
<div>
<h4 class="font-bold text-red-800 dark:text-red-400">Eliminar cuenta</h4>
<p class="text-sm text-red-600 dark:text-red-500/80 mt-1">Una vez que elimines tu cuenta, no hay vuelta atrás. Por favor, asegúrate.</p>
<button class="mt-3 text-sm font-bold text-red-600 hover:text-red-700 underline" type="button" onclick="eliminarCuenta()">Quiero eliminar mi cuenta</button>
</div>
</div>
</div>
</div>
</div>
</main>

<script>
// Manejo de tabs
function initTabs() {
    const tabs = document.querySelectorAll('.nav-tab');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.getAttribute('data-tab');
            showTab(tabName);
        });
    });
}

function showTab(tabName) {
    // Esconder todos los tabs
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remover clase activa de todos los botones
    const tabs = document.querySelectorAll('.nav-tab');
    tabs.forEach(tab => {
        tab.classList.remove('nav-link-active');
        tab.classList.add('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-50', 'dark:hover:bg-slate-800');
    });
    
    // Mostrar el tab seleccionado
    const activeContent = document.getElementById('tab-' + tabName);
    if (activeContent) {
        activeContent.classList.remove('hidden');
    }
    
    // Agregar clase activa al botón clickeado
    const activeTab = document.querySelector('.nav-tab[data-tab="' + tabName + '"]');
    if (activeTab) {
        activeTab.classList.add('nav-link-active');
        activeTab.classList.remove('text-slate-600', 'dark:text-slate-400', 'hover:bg-slate-50', 'dark:hover:bg-slate-800');
    }
}

// Manejo del formulario de perfil
function setupPerfilForm() {
    const form = document.getElementById('form-perfil-modal');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Limpiar mensajes previos
        const mensajeError = document.getElementById('mensaje-perfil-error');
        const mensajeExito = document.getElementById('mensaje-perfil-exito');
        if (mensajeError) mensajeError.classList.add('hidden');
        if (mensajeExito) mensajeExito.classList.add('hidden');
        
        // Obtener datos del formulario
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/api_actualizar_perfil.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.exito) {
                // Mostrar mensaje de éxito
                if (mensajeExito) {
                    mensajeExito.textContent = data.mensaje;
                    mensajeExito.classList.remove('hidden');
                }
                
                // Recargar la página después de 2 segundos
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                // Mostrar errores
                if (mensajeError) {
                    if (data.errores && Array.isArray(data.errores)) {
                        mensajeError.innerHTML = '<strong>Errores:</strong><ul class="mt-2">' + 
                            data.errores.map(e => '<li>• ' + e + '</li>').join('') + '</ul>';
                    } else {
                        mensajeError.textContent = data.mensaje || 'Error desconocido';
                    }
                    mensajeError.classList.remove('hidden');
                }
            }
        } catch (error) {
            if (mensajeError) {
                mensajeError.textContent = 'Error al conectar con el servidor: ' + error.message;
                mensajeError.classList.remove('hidden');
            }
        }
    });
}

// Manejo del formulario de cambiar contraseña
function setupContraseñaForm() {
    const form = document.getElementById('form-contraseña-modal');
    if (!form) return;
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Limpiar mensajes previos
        const mensajeError = document.getElementById('mensaje-contraseña-error');
        const mensajeExito = document.getElementById('mensaje-contraseña-exito');
        if (mensajeError) mensajeError.classList.add('hidden');
        if (mensajeExito) mensajeExito.classList.add('hidden');
        
        // Obtener datos del formulario
        const formData = new FormData(this);
        
        try {
            const response = await fetch('api/api_cambiar_contraseña.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.exito) {
                // Mostrar mensaje de éxito
                if (mensajeExito) {
                    mensajeExito.textContent = data.mensaje;
                    mensajeExito.classList.remove('hidden');
                }
                
                // Limpiar formulario
                form.reset();
                
                // Recargar la página después de 2 segundos
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                // Mostrar errores
                if (mensajeError) {
                    if (data.errores && Array.isArray(data.errores)) {
                        mensajeError.innerHTML = '<strong>Errores:</strong><ul class="mt-2">' + 
                            data.errores.map(e => '<li>• ' + e + '</li>').join('') + '</ul>';
                    } else {
                        mensajeError.textContent = data.mensaje || 'Error desconocido';
                    }
                    mensajeError.classList.remove('hidden');
                }
            }
        } catch (error) {
            if (mensajeError) {
                mensajeError.textContent = 'Error al conectar con el servidor: ' + error.message;
                mensajeError.classList.remove('hidden');
            }
        }
    });
}

// Función para eliminar cuenta
function eliminarCuenta() {
    if (!confirm('¿Estás completamente seguro? Esta acción no se puede deshacer.')) {
        return;
    }
    
    if (!confirm('Se eliminarán todos tus datos, pedidos e información personal. ¿Continuar?')) {
        return;
    }
    
    fetch('api/api_eliminar_cuenta.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            alert(data.mensaje);
            window.location.href = data.redirect;
        } else {
            alert('Error: ' + data.mensaje);
        }
    })
    .catch(error => {
        alert('Error al eliminar la cuenta: ' + error.message);
    });
}

// Ejecutar cuando todo esté listo
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        initTabs();
        setupPerfilForm();
        setupContraseñaForm();
    });
} else {
    initTabs();
    setupPerfilForm();
    setupContraseñaForm();
}
</script>