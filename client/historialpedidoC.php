<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Mis Pedidos - RetailCMS</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 48;
        }
        .status-badge-entregado { background-color: #d1fae5; color: #047857; }
        .dark .status-badge-entregado { background-color: rgba(5, 150, 105, 0.3); color: #6ee7b7; }
        .status-badge-camino { background-color: #dbeafe; color: #1e40af; }
        .dark .status-badge-camino { background-color: rgba(30, 58, 138, 0.3); color: #60a5fa; }
        .status-badge-procesando { background-color: #fef3c7; color: #b45309; }
        .dark .status-badge-procesando { background-color: rgba(180, 83, 9, 0.3); color: #fbbf24; }
    </style>
<main class="flex-grow max-w-7xl mx-auto px-4 py-8 md:py-12 w-full">
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
<div>
<h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">Historial de Mis Pedidos</h1>
<p class="text-slate-500 dark:text-slate-400 mt-1">Revisa el estado de tus compras y descarga tus facturas.</p>
</div>
<div class="flex items-center gap-2 text-sm text-slate-500 bg-white dark:bg-slate-800 px-4 py-2 rounded-lg border border-slate-200 dark:border-slate-700">
<span class="material-icons text-base">filter_list</span>
<span>Últimos 6 meses</span>
<span class="material-icons text-base">expand_more</span>
</div>
</div>
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">ID del Pedido</th>
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Fecha</th>
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Estado</th>
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500">Total</th>
<th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-500 text-right">Acciones</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-slate-800">
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-5 whitespace-nowrap">
<span class="font-bold text-slate-900 dark:text-white">#12345</span>
</td>
<td class="px-6 py-5 whitespace-nowrap text-slate-600 dark:text-slate-400 text-sm">
                            12 de Octubre, 2024
                        </td>
<td class="px-6 py-5 whitespace-nowrap">
<span class="px-3 py-1 rounded-full text-xs font-bold status-badge-entregado uppercase">Entregado</span>
</td>
<td class="px-6 py-5 whitespace-nowrap font-semibold">
                            264,87 €
                        </td>
<td class="px-6 py-5 whitespace-nowrap text-right">
<button class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:hover:bg-primary text-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold transition-all">
                                Ver Detalles
                                <span class="material-icons text-sm">visibility</span>
</button>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-5 whitespace-nowrap">
<span class="font-bold text-slate-900 dark:text-white">#12340</span>
</td>
<td class="px-6 py-5 whitespace-nowrap text-slate-600 dark:text-slate-400 text-sm">
                            05 de Octubre, 2024
                        </td>
<td class="px-6 py-5 whitespace-nowrap">
<span class="px-3 py-1 rounded-full text-xs font-bold status-badge-camino uppercase">En Camino</span>
</td>
<td class="px-6 py-5 whitespace-nowrap font-semibold">
                            89,00 €
                        </td>
<td class="px-6 py-5 whitespace-nowrap text-right">
<button class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:hover:bg-primary text-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold transition-all">
                                Ver Detalles
                                <span class="material-icons text-sm">visibility</span>
</button>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-5 whitespace-nowrap">
<span class="font-bold text-slate-900 dark:text-white">#12338</span>
</td>
<td class="px-6 py-5 whitespace-nowrap text-slate-600 dark:text-slate-400 text-sm">
                            28 de Septiembre, 2024
                        </td>
<td class="px-6 py-5 whitespace-nowrap">
<span class="px-3 py-1 rounded-full text-xs font-bold status-badge-procesando uppercase">Procesando</span>
</td>
<td class="px-6 py-5 whitespace-nowrap font-semibold">
                            45,50 €
                        </td>
<td class="px-6 py-5 whitespace-nowrap text-right">
<button class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:hover:bg-primary text-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold transition-all">
                                Ver Detalles
                                <span class="material-icons text-sm">visibility</span>
</button>
</td>
</tr>
<tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
<td class="px-6 py-5 whitespace-nowrap">
<span class="font-bold text-slate-900 dark:text-white">#12299</span>
</td>
<td class="px-6 py-5 whitespace-nowrap text-slate-600 dark:text-slate-400 text-sm">
                            15 de Agosto, 2024
                        </td>
<td class="px-6 py-5 whitespace-nowrap">
<span class="px-3 py-1 rounded-full text-xs font-bold status-badge-entregado uppercase">Entregado</span>
</td>
<td class="px-6 py-5 whitespace-nowrap font-semibold">
                            120,00 €
                        </td>
<td class="px-6 py-5 whitespace-nowrap text-right">
<button class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-primary hover:text-white dark:bg-slate-800 dark:hover:bg-primary text-slate-700 dark:text-slate-300 rounded-lg text-sm font-bold transition-all">
                                Ver Detalles
                                <span class="material-icons text-sm">visibility</span>
</button>
</td>
</tr>
</tbody>
</table>
</div>
<div class="p-6 bg-slate-50 dark:bg-slate-800/20 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
<p class="text-sm text-slate-500">Mostrando 4 pedidos</p>
<div class="flex gap-2">
<button class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-white dark:hover:bg-slate-700 disabled:opacity-50" disabled="">
<span class="material-icons text-lg leading-none">chevron_left</span>
</button>
<button class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg bg-primary text-white font-bold px-4 text-sm">1</button>
<button class="p-2 border border-slate-200 dark:border-slate-700 rounded-lg hover:bg-white dark:hover:bg-slate-700">
<span class="material-icons text-lg leading-none">chevron_right</span>
</button>
</div>
</div>
</div>
</main>

</body></html>
