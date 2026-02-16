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
<title>Administración de Lista de Pedidos</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#D9480F", // Vibrant burnt orange from image
                        "background-light": "#F8FAFC",
                        "background-dark": "#0F172A",
                    },
                    fontFamily: {
                        display: ["Inter", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.5rem",
                    },
                },
            },
        };
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
        }
    </script>
<style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .table-container {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        }
    </style>

<main class="max-w-7xl mx-auto px-6 pb-12">
<header class="mb-8 flex justify-between items-end">
<div>

<h2 class="text-3xl font-bold">Administración de Lista de Pedidos</h2>
</div>
<div class="flex gap-3">
<button class="px-4 py-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2 font-medium transition-colors">
<span class="material-icons-outlined text-lg">filter_list</span>
                    Filtrar
                </button>
<button class="px-4 py-2 bg-primary hover:bg-orange-700 text-white rounded-lg flex items-center gap-2 font-medium transition-colors">
<span class="material-icons-outlined text-lg">add</span>
                    Nuevo Pedido
                </button>
</div>
</header>
<div class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden table-container border border-slate-200 dark:border-slate-700">
<div class="bg-primary px-6 py-4 flex items-center gap-3">
<span class="material-icons-outlined text-white">list</span>
<h3 class="text-white font-bold text-lg">Lista de Pedidos</h3>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
<th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">ID Pedido</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Cliente</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Fecha</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Subtotal</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Estado</th>
<th class="px-6 py-4 text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-200 dark:divide-slate-700">
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
<td class="px-6 py-5 font-semibold text-slate-700 dark:text-slate-200">#1001</td>
<td class="px-6 py-5 text-slate-600 dark:text-slate-300">Juan Pérez</td>
<td class="px-6 py-5 text-slate-600 dark:text-slate-300">13-02-2026</td>
<td class="px-6 py-5 text-slate-600 dark:text-slate-300">$1,200.00</td>
<td class="px-6 py-5 font-bold text-green-600 dark:text-green-400">$1,320.00</td>
<td class="px-6 py-5">
<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    Pendiente
                                </span>
</td>
<td class="px-6 py-5">
<div class="flex justify-center gap-2">
<button class="w-8 h-8 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center shadow-sm transition-all hover:scale-110" title="Ver Detalle">
<span class="material-icons-outlined text-sm">visibility</span>
</button>
<button class="w-8 h-8 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center shadow-sm transition-all hover:scale-110" title="Cambiar Estado">
<span class="material-icons-outlined text-sm">swap_horiz</span>
</button>
<button class="w-8 h-8 rounded-full bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center shadow-sm transition-all hover:scale-110" title="Cancelar">
<span class="material-icons-outlined text-sm">close</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
<td class="px-6 py-5 font-semibold text-slate-700 dark:text-slate-200">#1002</td>
<td class="px-6 py-5 text-slate-600 dark:text-slate-300">María García</td>
<td class="px-6 py-5 text-slate-600 dark:text-slate-300">12-02-2026</td>
<td class="px-6 py-5 text-slate-600 dark:text-slate-300">$450.00</td>
<td class="px-6 py-5 font-bold text-green-600 dark:text-green-400">$500.00</td>
<td class="px-6 py-5">
<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    Confirmado
                                </span>
</td>
<td class="px-6 py-5">
<div class="flex justify-center gap-2">
<button class="w-8 h-8 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center shadow-sm transition-all hover:scale-110">
<span class="material-icons-outlined text-sm">visibility</span>
</button>
<button class="w-8 h-8 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center shadow-sm transition-all hover:scale-110">
<span class="material-icons-outlined text-sm">swap_horiz</span>
</button>
<button class="w-8 h-8 rounded-full bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center shadow-sm transition-all hover:scale-110">
<span class="material-icons-outlined text-sm">close</span>
</button>
</div>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition-colors">
<td class="px-6 py-5 font-semibold text-slate-700 dark:text-slate-200">#1003</td>
<td class="px-6 py-5 text-slate-600 dark:text-slate-300">Carlos López</td>
<td class="px-6 py-5 text-slate-600 dark:text-slate-300">11-02-2026</td>
<td class="px-6 py-5 text-slate-600 dark:text-slate-300">$800.00</td>
<td class="px-6 py-5 font-bold text-green-600 dark:text-green-400">$880.00</td>
<td class="px-6 py-5">
<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                    Enviado
                                </span>
</td>
<td class="px-6 py-5">
<div class="flex justify-center gap-2">
<button class="w-8 h-8 rounded-full bg-emerald-500 hover:bg-emerald-600 text-white flex items-center justify-center shadow-sm transition-all hover:scale-110">
<span class="material-icons-outlined text-sm">visibility</span>
</button>
<button class="w-8 h-8 rounded-full bg-orange-500 hover:bg-orange-600 text-white flex items-center justify-center shadow-sm transition-all hover:scale-110">
<span class="material-icons-outlined text-sm">swap_horiz</span>
</button>
<button class="w-8 h-8 rounded-full bg-rose-500 hover:bg-rose-600 text-white flex items-center justify-center shadow-sm transition-all hover:scale-110">
<span class="material-icons-outlined text-sm">close</span>
</button>
</div>
</td>
</tr>
</tbody>
</table>
</div>
<div class="px-6 py-4 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
<p class="text-sm text-slate-500 dark:text-slate-400">Mostrando 3 de 150 pedidos</p>
<div class="flex gap-2">
<button class="px-3 py-1 border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 disabled:opacity-50">Anterior</button>
<button class="px-3 py-1 bg-primary text-white rounded">1</button>
<button class="px-3 py-1 border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50">2</button>
<button class="px-3 py-1 border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50">Siguiente</button>
</div>
</div>
</div>
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-12">
<div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Pedidos Totales</p>
<h4 class="text-2xl font-bold">1,452</h4>
<div class="mt-2 text-xs text-green-600 flex items-center">
<span class="material-icons-outlined text-sm mr-1">trending_up</span>
                    +12% este mes
                </div>
</div>
<div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Ventas Brutas</p>
<h4 class="text-2xl font-bold">$42,850.00</h4>
<div class="mt-2 text-xs text-green-600 flex items-center">
<span class="material-icons-outlined text-sm mr-1">trending_up</span>
                    +8.5% este mes
                </div>
</div>
<div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Pendientes</p>
<h4 class="text-2xl font-bold">24</h4>
<div class="mt-2 text-xs text-orange-600 flex items-center">
<span class="material-icons-outlined text-sm mr-1">schedule</span>
                    Requieren atención
                </div>
</div>
<div class="bg-white dark:bg-slate-800 p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
<p class="text-slate-500 dark:text-slate-400 text-sm font-medium mb-1">Tasa de Entrega</p>
<h4 class="text-2xl font-bold">98.2%</h4>
<div class="mt-2 text-xs text-blue-600 flex items-center">
<span class="material-icons-outlined text-sm mr-1">check_circle</span>
                    Excelente desempeño
                </div>
</div>
</div>
