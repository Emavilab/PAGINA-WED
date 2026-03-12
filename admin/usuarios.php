<?php
require_once '../core/sesiones.php';
require_once '../core/conexion.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    header("Location: ../index.php");
    exit();
}

// Obtener usuarios iniciales
$query = "SELECT u.id_usuario, u.nombre, u.correo, u.id_rol, r.nombre as nombre_rol, 
                 u.estado, u.fecha_creacion 
          FROM usuarios u 
          LEFT JOIN roles r ON u.id_rol = r.id_rol 
          ORDER BY u.fecha_creacion DESC 
          LIMIT 10";

$result = $conexion->query($query);
$usuarios_iniciales = [];
while ($row = $result->fetch_assoc()) {
    $usuarios_iniciales[] = $row;
}

$total_usuarios = 0;
$count_result = $conexion->query("SELECT COUNT(*) as total FROM usuarios");
if ($count_row = $count_result->fetch_assoc()) {
    $total_usuarios = $count_row['total'];
}
?>
<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Gestión Avanzada de Usuarios con Filtros</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet"/>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#3b82f6",
                        "brand-dark": "#0f172a",
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"],
                    },
                },
            },
        };
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
        }
    </script>
<style type="text/tailwindcss">
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 20;
            font-size: 20px;
        }
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

</div>
</div>
</nav>
<main class="container mx-auto px-6 py-8 max-w-7xl">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
<div>
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">Lista de Usuarios</h2>
<p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Administre los accesos, roles y estados de los usuarios del sistema.</p>
</div>
<button onclick="abrirModalUsuario()" class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg shadow-sm shadow-blue-200 dark:shadow-none transition-all font-semibold text-sm">
<span class="material-symbols-outlined">add</span>
            NUEVO USUARIO
        </button>
</div>
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
<div class="p-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 flex flex-wrap items-center gap-4">
<div class="relative flex-grow max-w-md">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
<input class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none text-slate-700 dark:text-slate-200" placeholder="Buscar por nombre o correo..." type="text"/>
</div>
<div class="flex items-center gap-3">
<div class="flex items-center gap-2">
<label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Rol:</label>
<select id="filtro-rol" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg py-1.5 pl-3 pr-8 text-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-200">
<option value="">Todos</option>
<option value="1">Admin</option>
<option value="2">Vendedor</option>
<option value="3">Cliente</option>
</select>
</div>
<div class="flex items-center gap-2">
<label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Estado:</label>
<select id="filtro-estado" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg py-1.5 pl-3 pr-8 text-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-200">
<option value="">Todos</option>
<option value="activo">Activo</option>
<option value="inactivo">Inactivo</option>
</select>
</div>
<button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Limpiar filtros" onclick="limpiarFiltros()">
<span class="material-symbols-outlined">filter_alt_off</span>
</button>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
<th class="px-6 py-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">ID</th>
<th class="px-6 py-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Nombre</th>
<th class="px-6 py-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Correo</th>
<th class="px-6 py-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Rol</th>
<th class="px-6 py-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Creación</th>
<th class="px-6 py-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-center">Estado</th>
<th class="px-6 py-4 text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest text-right">Acciones</th>
</tr>
</thead>
<tbody id="tabla-usuarios" class="divide-y divide-slate-100 dark:divide-slate-700">
    <!-- Se cargará dinámicamente con JavaScript -->
    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors">
        <td colspan="7" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Cargando usuarios...</td>
    </tr>
</tbody>
</table>
</div>
<div class="px-6 py-4 bg-slate-50/80 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-center gap-4">
<p class="text-sm text-slate-500 dark:text-slate-400">
    Mostrando <span id="inicio-registro" class="font-semibold text-slate-900 dark:text-white">0</span> a <span id="fin-registro" class="font-semibold text-slate-900 dark:text-white">0</span> de <span id="total-usuarios" class="font-semibold text-slate-900 dark:text-white">0</span> usuarios
</p>
<div class="flex items-center gap-2">
<button id="btn-anterior" class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors" onclick="irPagina(paginaActual - 1)">
<span class="material-symbols-outlined !text-[16px]">chevron_left</span>
Anterior
</button>
<div id="paginacion" class="flex gap-1">
<!-- Se llena dinámicamente -->
</div>
<button id="btn-siguiente" class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors" onclick="irPagina(paginaActual + 1)">
Siguiente
<span class="material-symbols-outlined !text-[16px]">chevron_right</span>
</button>
</div>
</div>
</div>
<!-- Modal para crear/editar usuario -->
<div id="modal-usuario" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl max-w-md w-full">
        <!-- Header -->
        <div class="p-6 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
            <h3 id="titulo-modal" class="text-lg font-bold text-slate-900 dark:text-white">Crear Nuevo Usuario</h3>
            <button onclick="cerrarModalUsuario()" class="text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <!-- Form -->
        <form id="form-crear-usuario" class="p-6 space-y-4">
            <input type="hidden" id="id_usuario_edit" name="id_usuario" value="">
            
            <!-- Nombre -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Nombre</label>
                <input type="text" name="nombre" required placeholder="Ej. Juan Pérez" 
                    class="w-full px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <div id="error-nombre" class="text-red-500 text-sm hidden"></div>
            </div>

            <!-- Correo -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Correo Electrónico</label>
                <input type="email" name="correo" required placeholder="Ej. usuario@ejemplo.com" 
                    class="w-full px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <div id="error-correo" class="text-red-500 text-sm hidden"></div>
            </div>

            <!-- Contraseña -->
            <div>
                <label id="label-contraseña" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Contraseña</label>
                <input type="password" id="input-contraseña" name="contraseña" placeholder="••••••••" 
                    class="w-full px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                <p id="ayuda-contraseña" class="text-xs text-slate-500 dark:text-slate-400 mt-1 hidden">Déjalo vacío para no cambiar la contraseña</p>
                <div id="error-contraseña" class="text-red-500 text-sm hidden"></div>
            </div>

            <!-- Rol -->
            <div>
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Rol</label>
                <select name="id_rol" required 
                    class="w-full px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                    <option value="">Seleccionar rol...</option>
                    <option value="1">Administrador</option>
                    <option value="2">Vendedor</option>
                    <option value="3">Cliente</option>
                </select>
                <div id="error-rol" class="text-red-500 text-sm hidden"></div>
            </div>

            <!-- Estado (solo en edición) -->
            <div id="div-estado" class="hidden">
                <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Estado</label>
                <select name="estado" 
                    class="w-full px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none">
                    <option value="activo">Activo</option>
                    <option value="inactivo">Inactivo</option>
                </select>
            </div>

            <!-- Mensaje de error general -->
            <div id="mensaje-error-form" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-3 py-2 rounded-lg text-sm"></div>

            <!-- Mensaje de éxito -->
            <div id="mensaje-exito-form" class="hidden bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-3 py-2 rounded-lg text-sm"></div>

            <!-- Botones -->
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="cerrarModalUsuario()" 
                    class="flex-1 px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 font-semibold transition-colors">
                    Cancelar
                </button>
                <button type="submit" id="btn-enviar-modal"
                    class="flex-1 px-4 py-2 bg-primary hover:bg-blue-600 text-white rounded-lg font-semibold transition-colors">
                    Crear Usuario
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Namespace para evitar conflictos globales
(function initUsuarios() {
    'use strict';
    
    // Variables locales al IIFE
    let paginaActual = 1;
    let datosUsuarios = {
        usuarios: <?php echo !empty($usuarios_iniciales) ? json_encode($usuarios_iniciales) : '[]'; ?>,
        total: <?php echo $total_usuarios; ?>,
        pagina: 1,
        por_pagina: 10,
        total_paginas: <?php echo $total_usuarios > 0 ? ceil($total_usuarios / 10) : 1; ?>
    };
    
    // Exponer funciones globalmente para que se puedan llamar desde HTML
    window.obtenerInicialesNombre = obtenerInicialesNombre;
    window.limpiarFiltros = limpiarFiltros;
    window.getColorRol = getColorRol;
    window.getColorBg = getColorBg;
    window.formatearFecha = formatearFecha;
    window.renderizarTabla = renderizarTabla;
    window.renderizarPaginacion = renderizarPaginacion;
    window.actualizarContadores = actualizarContadores;
    window.irPagina = irPagina;
    window.cargarUsuarios = cargarUsuarios;
    window.abrirModalUsuario = abrirModalUsuario;
    window.abrirModalEditar = abrirModalEditar;
    window.cerrarModalUsuario = cerrarModalUsuario;
    window.confirmarEliminar = confirmarEliminar;
    window.eliminarUsuario = eliminarUsuario;
    
    // Funciones de utilidad
    function obtenerInicialesNombre(nombre) {
        return nombre.split(' ').map(part => part[0]).join('').toUpperCase();
    }

    function limpiarFiltros() {
        document.querySelector('input[placeholder*="Buscar"]').value = '';
        document.getElementById('filtro-rol').value = '';
        document.getElementById('filtro-estado').value = '';
        paginaActual = 1;
        cargarUsuarios();
    }

    function getColorRol(idRol) {
        switch(idRol) {
            case 1:
                return { bg: 'bg-red-50', text: 'text-red-700', darkBg: 'dark:bg-red-900/30', darkText: 'dark:text-red-400', border: 'border-red-100 dark:border-red-900/50', icon: 'workspace_premium' };
            case 2:
                return { bg: 'bg-blue-50', text: 'text-blue-700', darkBg: 'dark:bg-blue-900/30', darkText: 'dark:text-blue-400', border: 'border-blue-100 dark:border-blue-900/50', icon: 'person' };
            case 3:
                return { bg: 'bg-green-50', text: 'text-green-700', darkBg: 'dark:bg-green-900/30', darkText: 'dark:text-green-400', border: 'border-green-100 dark:border-green-900/50', icon: 'shopping_cart' };
            default:
                return { bg: 'bg-slate-50', text: 'text-slate-700', darkBg: 'dark:bg-slate-900/30', darkText: 'dark:text-slate-400', border: 'border-slate-100 dark:border-slate-900/50', icon: 'help' };
        }
    }

    function getColorBg(nombre) {
        const hslColors = [
            'hsl(0, 100%, 92%)',
            'hsl(220, 100%, 92%)',
            'hsl(120, 100%, 92%)',
            'hsl(280, 100%, 92%)',
            'hsl(40, 100%, 92%)'
        ];
        const index = nombre.charCodeAt(0) % hslColors.length;
        return hslColors[index];
    }

    function formatearFecha(fecha) {
        const date = new Date(fecha);
        const opciones = { year: 'numeric', month: 'short', day: 'numeric' };
        return date.toLocaleDateString('es-ES', opciones);
    }

    function renderizarTabla(usuarios) {
        const tbody = document.getElementById('tabla-usuarios');
        
        if (usuarios.length === 0) {
            tbody.innerHTML = '<tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors"><td colspan="7" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">No hay usuarios registrados</td></tr>';
            return;
        }

        tbody.innerHTML = usuarios.map(usuario => {
            const color = getColorRol(usuario.id_rol);
            const iniciales = obtenerInicialesNombre(usuario.nombre);
            const estado = usuario.estado === 'activo' ? 'Activo' : 'Inactivo';
            const fechaFormato = formatearFecha(usuario.fecha_creacion);

            return `
            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors group">
                <td class="px-6 py-4 text-sm font-medium text-slate-400">#${String(usuario.id_usuario).padStart(3, '0')}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-full ${color.bg} ${color.darkBg} flex items-center justify-center ${color.text} ${color.darkText} font-bold text-xs">
                            ${iniciales}
                        </div>
                        <span class="text-sm font-semibold text-slate-900 dark:text-white">${usuario.nombre}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">${usuario.correo}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider ${color.bg} ${color.text} ${color.darkBg} ${color.darkText} border ${color.border}">
                        <span class="material-symbols-outlined !text-[14px]">${color.icon}</span>
                        ${usuario.nombre_rol || 'Sin rol'}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 text-center">${fechaFormato}</td>
                <td class="px-6 py-4 text-center">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${usuario.estado === 'activo' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400'}">
                        <span class="w-1.5 h-1.5 rounded-full ${usuario.estado === 'activo' ? 'bg-emerald-500' : 'bg-slate-400'} mr-1.5"></span>
                        ${estado}
                    </span>
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button onclick="abrirModalEditar(${usuario.id_usuario})" class="p-2 text-slate-400 hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all" title="Editar">
                            <span class="material-symbols-outlined">edit</span>
                        </button>
                        <button onclick="confirmarEliminar(${usuario.id_usuario}, '${usuario.nombre}')" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Eliminar">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
            `;
        }).join('');
    }

function renderizarPaginacion() {
    const paginacion = document.getElementById('paginacion');
    const { total_paginas, pagina } = datosUsuarios;
    
    let html = '';
    const maxBotones = 5;
    let inicio = Math.max(1, pagina - Math.floor(maxBotones / 2));
    let fin = Math.min(total_paginas, inicio + maxBotones - 1);
    
    if (fin - inicio + 1 < maxBotones) {
        inicio = Math.max(1, fin - maxBotones + 1);
    }

    for (let i = inicio; i <= fin; i++) {
        html += `<button onclick="irPagina(${i})" class="h-8 w-8 flex items-center justify-center rounded-lg ${i === pagina ? 'bg-primary text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700'} text-xs font-bold transition-colors">${i}</button>`;
    }

    paginacion.innerHTML = html;

    // Actualizar botones anterior siguiente
    document.getElementById('btn-anterior').disabled = pagina === 1;
    document.getElementById('btn-siguiente').disabled = pagina === total_paginas;
}

function actualizarContadores() {
    const { usuarios, total, pagina, por_pagina } = datosUsuarios;
    const inicio = (pagina - 1) * por_pagina + 1;
    const fin = Math.min(pagina * por_pagina, total);
    
    document.getElementById('inicio-registro').textContent = total === 0 ? 0 : inicio;
    document.getElementById('fin-registro').textContent = fin;
    document.getElementById('total-usuarios').textContent = total;
}

function irPagina(pagina) {
    if (pagina < 1 || pagina > datosUsuarios.total_paginas) return;
    paginaActual = pagina;
    cargarUsuarios();
}

async function cargarUsuarios() {
    const busqueda = document.querySelector('input[placeholder*="Buscar"]').value;
    const rol = document.getElementById('filtro-rol').value;
    const estado = document.getElementById('filtro-estado').value;

    const params = new URLSearchParams({
        pagina: paginaActual,
        busqueda: busqueda,
        rol: rol,
        estado: estado
    });

    try {
        const response = await fetch(`obtener_usuarios.php?${params}`, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const text = await response.text();
        
        // Intentar parsear como JSON
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            // Si no es JSON válido, probablemente sea un error de sesión
            console.error('Respuesta no JSON:', text);
            throw new Error('Respuesta del servidor no válida. Probablemente necesitas volver a iniciar sesión.');
        }

        if (data.exito) {
            datosUsuarios = data;
            renderizarTabla(data.usuarios);
            renderizarPaginacion();
            actualizarContadores();
        } else {
            console.error('Error:', data.mensaje);
            document.getElementById('tabla-usuarios').innerHTML = '<tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors"><td colspan="7" class="px-6 py-8 text-center text-red-500">Error: ' + data.mensaje + '</td></tr>';
        }
    } catch (error) {
        console.error('Error al cargar usuarios:', error);
        document.getElementById('tabla-usuarios').innerHTML = '<tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors"><td colspan="7" class="px-6 py-8 text-center text-red-500">Error: ' + error.message + '. Por favor recarga la página.</td></tr>';
    }
}

// Cargar usuarios al iniciar
// Nota: No usamos DOMContentLoaded porque esta página se carga dinámicamente en el Dashboard
// Esperamos 150ms para asegurar que el DOM esté completamente listo

setTimeout(() => {
    // Reset de paginación cuando se carga la página
    paginaActual = 1;

    // Agregar event listeners a filtros ANTES de cargar datos
    const searchInput = document.querySelector('input[placeholder*="Buscar"]');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            paginaActual = 1;
            cargarUsuarios();
        });
    }

    const filtroRol = document.getElementById('filtro-rol');
    const filtroEstado = document.getElementById('filtro-estado');

    if (filtroRol) {
        filtroRol.addEventListener('change', () => {
            paginaActual = 1;
            cargarUsuarios();
        });
    }

    if (filtroEstado) {
        filtroEstado.addEventListener('change', () => {
            paginaActual = 1;
            cargarUsuarios();
        });
    }

    // Mostrar que está cargando
    document.getElementById('tabla-usuarios').innerHTML = '<tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors"><td colspan="7" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">Cargando usuarios...</td></tr>';

    // SIEMPRE cargar desde la API para asegurar datos coherentes
    cargarUsuarios();
}, 150);

    // Resto de funciones existentes para modal
    let modoEdicion = false;

    function abrirModalUsuario() {
        modoEdicion = false;
        document.getElementById('titulo-modal').textContent = 'Crear Nuevo Usuario';
        document.getElementById('btn-enviar-modal').textContent = 'Crear Usuario';
        document.getElementById('id_usuario_edit').value = '';
        document.getElementById('input-contraseña').required = true;
        document.getElementById('label-contraseña').textContent = 'Contraseña';
        document.getElementById('ayuda-contraseña').classList.add('hidden');
        document.getElementById('div-estado').classList.add('hidden');
        document.getElementById('form-crear-usuario').reset();
        document.getElementById('modal-usuario').classList.remove('hidden');
    }

    function abrirModalEditar(idUsuario) {
        modoEdicion = true;
        document.getElementById('titulo-modal').textContent = 'Editar Usuario';
        document.getElementById('btn-enviar-modal').textContent = 'Guardar Cambios';
        document.getElementById('input-contraseña').required = false;
        document.getElementById('label-contraseña').textContent = 'Contraseña (opcional)';
        document.getElementById('ayuda-contraseña').classList.remove('hidden');
        document.getElementById('div-estado').classList.remove('hidden');
        
        // Buscar el usuario en los datos cargados
        const usuario = datosUsuarios.usuarios.find(u => u.id_usuario == idUsuario);
        
        if (usuario) {
            document.getElementById('id_usuario_edit').value = usuario.id_usuario;
            document.querySelector('input[name="nombre"]').value = usuario.nombre;
            document.querySelector('input[name="correo"]').value = usuario.correo;
            document.querySelector('input[name="contraseña"]').value = '';
            document.querySelector('select[name="id_rol"]').value = usuario.id_rol;
            document.querySelector('select[name="estado"]').value = usuario.estado;
            document.getElementById('modal-usuario').classList.remove('hidden');
            document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));
            document.getElementById('mensaje-error-form').classList.add('hidden');
            document.getElementById('mensaje-exito-form').classList.add('hidden');
        } else {
            CustomModal.show('warning', 'No encontrado', 'Usuario no encontrado');
        }
    }

    function cerrarModalUsuario() {
        document.getElementById('modal-usuario').classList.add('hidden');
        document.getElementById('form-crear-usuario').reset();
        document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));
        document.getElementById('mensaje-error-form').classList.add('hidden');
        document.getElementById('mensaje-exito-form').classList.add('hidden');
        modoEdicion = false;
    }

    function confirmarEliminar(idUsuario, nombre) {
        CustomModal.show('confirm', 'Confirmar eliminación', `¿Estás seguro de que deseas eliminar al usuario "${nombre}"? Esta acción es irreversible.`, (confirmed) => {
            if (confirmed) {
                eliminarUsuario(idUsuario);
            }
        });
    }

    async function eliminarUsuario(idUsuario) {
        const formData = new FormData();
        formData.append('id_usuario', idUsuario);

        try {
            const response = await fetch('./eliminar_usuario_admin.php', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.exito) {
                CustomModal.show('success', 'Éxito', data.mensaje, () => {
                    paginaActual = 1;
                    cargarUsuarios();
                });
            } else {
                CustomModal.show('error', 'Error', 'Error: ' + data.mensaje);
            }
        } catch (error) {
            CustomModal.show('error', 'Error', 'Error al eliminar usuario: ' + error.message);
        }
    }

    // Manejo del formulario
    document.getElementById('form-crear-usuario').addEventListener('submit', async function(e) {
        e.preventDefault();

        // Limpiar mensajes previos
        document.querySelectorAll('[id^="error-"]').forEach(el => el.classList.add('hidden'));
        document.getElementById('mensaje-error-form').classList.add('hidden');
        document.getElementById('mensaje-exito-form').classList.add('hidden');

        // Obtener datos del formulario
        const formData = new FormData(this);
        
        // Determinar endpoint según el modo
        const endpoint = modoEdicion ? './editar_usuario_admin.php' : './crear_usuario_admin.php';

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });

            const data = await response.json();

            if (data.exito) {
                document.getElementById('mensaje-exito-form').textContent = data.mensaje;
                document.getElementById('mensaje-exito-form').classList.remove('hidden');

                // Limpiar formulario
                this.reset();

                // Cerrar modal y recargar tabla
                setTimeout(() => {
                    cerrarModalUsuario();
                    paginaActual = 1;
                    cargarUsuarios();
                }, 1500);
            } else {
                if (data.errores && Array.isArray(data.errores)) {
                    document.getElementById('mensaje-error-form').innerHTML = '<strong>Errores:</strong><ul class="mt-2">' + 
                        data.errores.map(e => '<li>• ' + e + '</li>').join('') + '</ul>';
                } else {
                    document.getElementById('mensaje-error-form').textContent = data.mensaje || 'Error desconocido';
                }
                document.getElementById('mensaje-error-form').classList.remove('hidden');
            }
        } catch (error) {
            document.getElementById('mensaje-error-form').textContent = 'Error al conectar con el servidor: ' + error.message;
            document.getElementById('mensaje-error-form').classList.remove('hidden');
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            cerrarModalUsuario();
        }
    });

// Cerrar IIFE
})();
</script>

</body></html>