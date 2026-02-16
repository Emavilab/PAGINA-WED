<?php
require_once '../core/sesiones.php';

if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {
    header("Location: ../index1.php");
    exit();
}
?>
<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Gestión de Productos | Admin CMS</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#137fec",
                        "background-light": "#f6f7f8",
                        "background-dark": "#101922",
                    },
                    fontFamily: {
                        "display": ["Inter"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 min-h-screen font-display">
<main class="p-8">
<header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
<div>
<h1 class="text-2xl font-bold mb-1">Lista de Productos</h1>
<p class="text-sm text-slate-500 dark:text-slate-400">Gestiona el catálogo de productos de tus 4 sedes de negocio.</p>
</div>
<div class="flex items-center gap-3">
<button onclick="openFormModal()" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold rounded-lg hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
<span class="material-icons text-sm">add</span>
<span>Agregar Nuevo Producto</span>
</button>
</div>
</header>
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-xl">
<div class="text-sm text-slate-500 mb-1">Total Productos</div>
<div class="text-2xl font-bold">1,284</div>
<div class="text-xs text-green-500 mt-2 flex items-center gap-1">
<span class="material-icons text-xs">trending_up</span> +12% desde el mes pasado
                </div>
</div>
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-xl">
<div class="text-sm text-slate-500 mb-1">Listados Activos</div>
<div class="text-2xl font-bold">942</div>
<div class="text-xs text-slate-500 mt-2">Estado activo actual</div>
</div>
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-5 rounded-xl">
<div class="text-sm text-slate-500 mb-1">Valor Total</div>
<div class="text-2xl font-bold">$42,500.00</div>
<div class="text-xs text-slate-500 mt-2">Valor de mercado en stock</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden shadow-sm">
<div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-center gap-4">
<div class="relative w-full sm:w-96">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">search</span>
<input class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-primary focus:border-transparent transition-all outline-none" placeholder="Buscar por nombre, categoría o marca..." type="text"/>
</div>
<div class="flex items-center gap-2 w-full sm:w-auto">
<button class="flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-sm font-medium transition-colors">
<span class="material-icons text-lg">filter_list</span>
<span>Filtrar</span>
</button>
<button class="flex items-center gap-2 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-sm font-medium transition-colors">
<span class="material-icons text-lg">file_download</span>
<span>Exportar</span>
</button>
</div>
</div>
<div class="overflow-x-auto scrollbar-hide">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50/50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider font-semibold">
<th class="px-6 py-4">ID</th>
<th class="px-6 py-4">Nombre</th>
<th class="px-6 py-4">Categoría</th>
<th class="px-6 py-4">Marca</th>
<th class="px-6 py-4">Precio</th>
<th class="px-6 py-4">Stock</th>
<th class="px-6 py-4">Estado</th>
<th class="px-6 py-4 text-right">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-200 dark:divide-slate-800">
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
<td class="px-6 py-4 text-sm text-slate-500">1</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDD-5hRvnLtNvyWWhJmNnCHhupYO-aLMPXVA8TE61xRmS0nVA2mwN5Zf-60Fkjbmk7ti9OEqEMoFTq8ynnQbAFdEIxGlZRwD1eyPol4-a5epkfuXJfL8LWxpxnyTLL9h5HVy7RjD7vKhWVMySht5Dh-pl4T31TXS7DKz-_AvInN9fMVmdmC75FxIHLRWOqTcd0EsWmUvRQFM1H0csCfj4caVZX2rwsUhUMWSlPqaB900E_LYuUiM2SnzJ2G_txMB5O4w8Y2po00C0Y"/>
</div>
<div class="font-semibold text-slate-900 dark:text-white">Aura Smart Watch Pro</div>
</div>
</td>
<td class="px-6 py-4">
<span class="text-sm px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-md font-medium">Electrónica</span>
</td>
<td class="px-6 py-4 text-sm">Apple</td>
<td class="px-6 py-4 text-sm font-semibold text-emerald-600 dark:text-emerald-500">$299.00</td>
<td class="px-6 py-4 text-sm">
<span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full text-xs font-medium">15</span>
</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
<span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Disponible
                                </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-2">
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Vista previa">
<span class="material-symbols-outlined text-xl">visibility</span>
</button>
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Editar">
<span class="material-symbols-outlined text-xl">edit</span>
</button>
<button class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Borrar">
<span class="material-symbols-outlined text-xl">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
<td class="px-6 py-4 text-sm text-slate-500">2</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBTdiaa5GcZ22iqIQa7-vnwBLb5H6hy1mlLcstuwKYLdn5CL3_XzDr1P-3JBP7RqJZKBk51t9EUxMeRZrka8xqtrTlQSp4zF9501zokWuClieZIbK6S5Ls9Q4WE4EwhRYfvc0pF6ORiixgQHL4udZpGXnJo4QiPI2BpAD-5vtGv7ARuNkwTCOZQNDsbfsB7CDRGolT_guXYak1jeRDsc1Vda9RgqZaMqFNRiLPlwcDl7QVpIv7r05FZDVpYAXFyWAVcuCW7rTrGtN0"/>
</div>
<div class="font-semibold text-slate-900 dark:text-white">Sonic Wave Headphones</div>
</div>
</td>
<td class="px-6 py-4">
<span class="text-sm px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-md font-medium">Electrónica</span>
</td>
<td class="px-6 py-4 text-sm">Sony</td>
<td class="px-6 py-4 text-sm font-semibold text-emerald-600 dark:text-emerald-500">$189.50</td>
<td class="px-6 py-4 text-sm">
<span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full text-xs font-medium">45</span>
</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
<span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Agotado
                                </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-2">
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Vista previa">
<span class="material-symbols-outlined text-xl">visibility</span>
</button>
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Editar">
<span class="material-symbols-outlined text-xl">edit</span>
</button>
<button class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Borrar">
<span class="material-symbols-outlined text-xl">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
<td class="px-6 py-4 text-sm text-slate-500">3</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCWHEGuiV-8wq79Ev1KaDFa067CXJ60MHkW2Ia2yrldApEJwiMtGoPju0ZuOQm2Ti2Z7BYvmryb2Y-RDoacVOswHhCi0DLxzoIeOvK8ixycAUpQpfv0SULOQ3KT4Q_SwRlttJD_Q5TQu4E1-IrJxkqq1O8ORZ7mrWpqMZ_BB1AP85DZG8gs9kuCg5LWe_1XqKiXQGhhT7XN0moGfrd2lMjiMqKuTunJf7PAqCInExj6Zd4e1sMeI5O8wcnNrGTNQp0piXeckYolqw0"/>
</div>
<div class="font-semibold text-slate-900 dark:text-white">Velocity Run Shoes</div>
</div>
</td>
<td class="px-6 py-4">
<span class="text-sm px-2.5 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-md font-medium">Calzado</span>
</td>
<td class="px-6 py-4 text-sm">Nike</td>
<td class="px-6 py-4 text-sm font-semibold text-emerald-600 dark:text-emerald-500">$120.00</td>
<td class="px-6 py-4 text-sm">
<span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full text-xs font-medium">8</span>
</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
<span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Disponible
                                </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-2">
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Vista previa">
<span class="material-symbols-outlined text-xl">visibility</span>
</button>
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Editar">
<span class="material-symbols-outlined text-xl">edit</span>
</button>
<button class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Borrar">
<span class="material-symbols-outlined text-xl">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
<td class="px-6 py-4 text-sm text-slate-500">4</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCEcGuXFp2fZUYgEME6ljmrkPE5N-RAhCFYOEOrA4Gda-2vFUK6k4NSGwnHiCfaiQONTQf6bIBJL3-GrB8eqbQ1-0DEIzgs0tOzafEiigC97u2NyOxnPd_v7RhN05eFH0c7OzduyrMEfKFoVadQvDF8N7MAYhKCId9GlKYLy44KI58u2cBC2GUb8ve92G5RMuMK8jdjfvVAWJ8yTvrfswVKy6Dq6HPfwo-WXp9q8alQ5yWGTfvHyF-8i22SAdl4M4pBgOCVYQpOVzQ"/>
</div>
<div class="font-semibold text-slate-900 dark:text-white">Retro Vision Cam</div>
</div>
</td>
<td class="px-6 py-4">
<span class="text-sm px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-md font-medium">Electrónica</span>
</td>
<td class="px-6 py-4 text-sm">Fujifilm</td>
<td class="px-6 py-4 text-sm font-semibold text-emerald-600 dark:text-emerald-500">$450.00</td>
<td class="px-6 py-4 text-sm">
<span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full text-xs font-medium">3</span>
</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
<span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Disponible
                                </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-2">
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Vista previa">
<span class="material-symbols-outlined text-xl">visibility</span>
</button>
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Editar">
<span class="material-symbols-outlined text-xl">edit</span>
</button>
<button class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Borrar">
<span class="material-symbols-outlined text-xl">delete</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
<td class="px-6 py-4 text-sm text-slate-500">5</td>
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex-shrink-0 overflow-hidden border border-slate-200 dark:border-slate-700">
<img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCrqm89cLU89CqfMPw4aDUD-DIbGwZbsLGd0Fnf7LcYK3s92xs8oIRGMiR-1BJ-SkgLrQ8y8OGZrsuLv7ApNlNAkxkhxbFcM8SZ6Re3pe8EbCMX7DmLf0detb3dt_d07yZmbWw7Qr7ALpGEFMFi4u50I7v-QBTGJYJyv82Kdpde4ITXEN_HZrE0ff9m-Cy_rnhn7Psu8zOHQHcXD4wRROZHy0ubqNByMCNJqBbg4CGKi3vnfbqGDi6e0DR1-WhuYluZ85Sj8x8eV48"/>
</div>
<div class="font-semibold text-slate-900 dark:text-white">Eco-Life Bamboo Bottle</div>
</div>
</td>
<td class="px-6 py-4">
<span class="text-sm px-2.5 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-md font-medium">Estilo de Vida</span>
</td>
<td class="px-6 py-4 text-sm">EcoBrand</td>
<td class="px-6 py-4 text-sm font-semibold text-emerald-600 dark:text-emerald-500">$34.99</td>
<td class="px-6 py-4 text-sm">
<span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full text-xs font-medium">120</span>
</td>
<td class="px-6 py-4">
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
<span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Disponible
                                </span>
</td>
<td class="px-6 py-4 text-right">
<div class="flex items-center justify-end gap-2">
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Vista previa">
<span class="material-symbols-outlined text-xl">visibility</span>
</button>
<button class="p-2 text-slate-400 hover:text-primary transition-colors" title="Editar">
<span class="material-symbols-outlined text-xl">edit</span>
</button>
<button class="p-2 text-slate-400 hover:text-red-500 transition-colors" title="Borrar">
<span class="material-symbols-outlined text-xl">delete</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
<div class="text-sm text-slate-500 dark:text-slate-400">
                    Mostrando <span class="font-medium text-slate-900 dark:text-white">1</span> a <span class="font-medium text-slate-900 dark:text-white">5</span> de <span class="font-medium text-slate-900 dark:text-white">128</span> productos
                </div>
<div class="flex items-center gap-1">
<button class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-400 hover:bg-white dark:hover:bg-slate-800 transition-colors disabled:opacity-50" disabled="">
<span class="material-icons text-xl">chevron_left</span>
</button>
<button class="w-9 h-9 flex items-center justify-center rounded-lg bg-primary text-white font-medium">1</button>
<button class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">2</button>
<button class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">3</button>
<span class="px-2 text-slate-400">...</span>
<button class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">26</button>
<button class="w-9 h-9 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 text-slate-400 hover:bg-white dark:hover:bg-slate-800 transition-colors">
<span class="material-icons text-xl">chevron_right</span>
</button>
</div>
</div>
</div>
<div class="mt-8 p-4 bg-primary/5 border border-primary/20 rounded-xl flex items-start gap-3">
<span class="material-icons text-primary">info</span>
<div>
<p class="text-sm text-slate-700 dark:text-slate-300 font-medium">Consejo: Acciones en masa</p>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Selecciona varios elementos usando la casilla a la izquierda del nombre para realizar actualizaciones de estado o eliminaciones masivas.</p>
</div>
</div>
</main>

<!-- Modal Crear/Editar Producto -->
<div id="formModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50">
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden max-w-2xl w-full max-h-[90vh] overflow-y-auto">
<div class="p-6 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between sticky top-0 bg-white dark:bg-slate-800">
<div class="flex items-center gap-2">
<span class="material-icons text-primary">edit_note</span>
<h2 class="text-xl font-bold text-slate-800 dark:text-white">Crear/Editar Producto</h2>
</div>
<button onclick="closeFormModal()" class="p-1 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors">
<span class="material-icons">close</span>
</button>
</div>
<form class="p-8 space-y-6">
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">label</span>
Nombre del Producto
</label>
<input class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all dark:placeholder-slate-500" placeholder="Ej: Laptop Dell XPS 13" type="text"/>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">description</span>
Descripción
</label>
<textarea class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition-all dark:placeholder-slate-500" placeholder="Descripción detallada del producto..." rows="4"></textarea>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">category</span>
Categoría
</label>
<select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none appearance-none">
<option value="">Seleccionar categoría</option>
<option>Electrónica</option>
<option>Hogar</option>
<option>Moda</option>
</select>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">branding_watermark</span>
Marca
</label>
<select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none appearance-none">
<option value="">Seleccionar marca</option>
<option>Dell</option>
<option>Apple</option>
<option>HP</option>
</select>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">payments</span>
Precio
</label>
<input class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none" placeholder="0.00" step="0.01" type="number"/>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">inventory_2</span>
Stock
</label>
<input class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none" placeholder="0" type="number"/>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">percent</span>
Precio Descuento
</label>
<input class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none" placeholder="0.00" step="0.01" type="number"/>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">sell</span>
En Oferta
</label>
<select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none">
<option value="no">No</option>
<option value="si">Sí</option>
</select>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">calendar_today</span>
Fecha Inicio Oferta
</label>
<input class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none dark:[color-scheme:dark]" type="date"/>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">event</span>
Fecha Fin Oferta
</label>
<input class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none dark:[color-scheme:dark]" type="date"/>
</div>
</div>
<div class="space-y-2 max-w-md">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">check_circle</span>
Estado
</label>
<select class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 bg-transparent focus:ring-2 focus:ring-primary outline-none">
<option>Disponible</option>
<option>Agotado</option>
<option>Descontinuado</option>
</select>
</div>
<div class="space-y-2">
<label class="flex items-center text-sm font-semibold text-slate-600 dark:text-slate-400">
<span class="material-icons text-sm" style="margin-right: 4px; font-size: 1.1rem;">image</span>
Imagen del Producto
</label>
<div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-10 flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-900/50 hover:bg-slate-100 dark:hover:bg-slate-900 transition-colors cursor-pointer">
<span class="material-icons text-5xl text-slate-400 mb-3">cloud_upload</span>
<p class="text-slate-500 dark:text-slate-400 text-sm">Arrastra la imagen o haz clic para seleccionar</p>
</div>
</div>
<div class="flex flex-col sm:flex-row gap-4 pt-4">
<button class="flex-1 bg-primary hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-bold flex items-center justify-center gap-2 transition-all shadow-md" type="submit">
<span class="material-icons">save</span>
Guardar Producto
</button>
<button onclick="closeFormModal()" class="flex-1 bg-slate-400 hover:bg-slate-500 text-white py-3 px-6 rounded-lg font-bold flex items-center justify-center gap-2 transition-all shadow-md" type="button">
<span class="material-icons">close</span>
Cancelar
</button>
</div>
</form>
</div>
</div>

<script>
function openFormModal() {
    const modal = document.getElementById('formModal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeFormModal() {
    const modal = document.getElementById('formModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    const formModal = document.getElementById('formModal');
    
    if (formModal) {
        // Cerrar modal al hacer clic fuera del contenido
        formModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeFormModal();
            }
        });
    }
    
    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFormModal();
        }
    });
});
</script>
</body></html>