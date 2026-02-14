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
<button class="inline-flex items-center justify-center gap-2 bg-primary hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg shadow-sm shadow-blue-200 dark:shadow-none transition-all font-semibold text-sm">
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
<select class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg py-1.5 pl-3 pr-8 text-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-200">
<option>Todos</option>
<option>Admin</option>
<option>Vendedor</option>
<option>Editor</option>
</select>
</div>
<div class="flex items-center gap-2">
<label class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Estado:</label>
<select class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg py-1.5 pl-3 pr-8 text-sm focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-200">
<option>Todos</option>
<option>Activo</option>
<option>Inactivo</option>
</select>
</div>
<button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-colors" title="Limpiar filtros">
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
<tbody class="divide-y divide-slate-100 dark:divide-slate-700">
<tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors group">
<td class="px-6 py-4 text-sm font-medium text-slate-400">#001</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="h-8 w-8 rounded-full bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-red-600 dark:text-red-400 font-bold text-xs">JA</div>
<span class="text-sm font-semibold text-slate-900 dark:text-white">Juan Administrador</span>
</div>
</td>
<td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">admin@minegocio.com</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-100 dark:border-red-900/50">
<span class="material-symbols-outlined !text-[14px]">workspace_premium</span>
                                Admin
                            </span>
</td>
<td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 text-center">13 Feb 2026</td>
<td class="px-6 py-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Activo
                            </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="p-2 text-slate-400 hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all" title="Editar">
<span class="material-symbols-outlined">edit</span>
</button>
<button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Eliminar">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors group">
<td class="px-6 py-4 text-sm font-medium text-slate-400">#002</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="h-8 w-8 rounded-full bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-xs">MV</div>
<span class="text-sm font-semibold text-slate-900 dark:text-white">María Vendedora</span>
</div>
</td>
<td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">maria@minegocio.com</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50">
<span class="material-symbols-outlined !text-[14px]">person</span>
                                Vendedor
                            </span>
</td>
<td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 text-center">10 Feb 2026</td>
<td class="px-6 py-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400">
<span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                                Activo
                            </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="p-2 text-slate-400 hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all" title="Editar">
<span class="material-symbols-outlined">edit</span>
</button>
<button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Eliminar">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50/80 dark:hover:bg-slate-700/30 transition-colors group">
<td class="px-6 py-4 text-sm font-medium text-slate-400">#003</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="h-8 w-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold text-xs">CI</div>
<span class="text-sm font-semibold text-slate-900 dark:text-white">Carlos Inactivo</span>
</div>
</td>
<td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">carlos@minegocio.com</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50">
<span class="material-symbols-outlined !text-[14px]">person</span>
                                Vendedor
                            </span>
</td>
<td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 text-center">01 Feb 2026</td>
<td class="px-6 py-4 text-center">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
<span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span>
                                Inactivo
                            </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
<button class="p-2 text-slate-400 hover:text-primary hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-all" title="Editar">
<span class="material-symbols-outlined">edit</span>
</button>
<button class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Eliminar">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="px-6 py-4 bg-slate-50/80 dark:bg-slate-900/50 border-t border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-center gap-4">
<p class="text-sm text-slate-500 dark:text-slate-400">
                Mostrando <span class="font-semibold text-slate-900 dark:text-white">1</span> a <span class="font-semibold text-slate-900 dark:text-white">3</span> de <span class="font-semibold text-slate-900 dark:text-white">3</span> usuarios
            </p>
<div class="flex items-center gap-2">
<button class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 disabled:opacity-50 transition-colors" disabled="">
<span class="material-symbols-outlined !text-[16px]">chevron_left</span>
                    Anterior
                </button>
<div class="flex gap-1">
<button class="h-8 w-8 flex items-center justify-center rounded-lg bg-primary text-white text-xs font-bold">1</button>
</div>
<button class="flex items-center gap-1 px-3 py-1.5 text-xs font-semibold border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    Siguiente
                    <span class="material-symbols-outlined !text-[16px]">chevron_right</span>
</button>
</div>
</div>
</div>
