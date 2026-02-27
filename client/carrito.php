<!DOCTYPE html>
<html lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>NexusRetail | Tu Supermercado de Confianza</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              colors: {
                "primary": "#137fec",
                "primary-dark": "#0d66c2",
                "background-light": "#f6f7f8",
                "background-dark": "#101922",
                "neutral-light": "#e2e8f0",
                "neutral-dark": "#1e293b",
              },
              fontFamily: {
                "display": ["Inter", "sans-serif"]
              },
              borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
            },
          },
        }
    </script>
<style type="text/tailwindcss">
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 }
    </style>
</head>
<body class="bg-white dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display transition-colors duration-300 relative min-h-screen">
<aside class="fixed top-0 right-0 h-full w-full max-w-md bg-white dark:bg-slate-900 z-[120] shadow-2xl flex flex-col animate-in slide-in-from-right duration-300">
<div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary text-2xl">shopping_cart</span>
<h2 class="text-xl font-bold text-slate-900 dark:text-white">Tu Carrito</h2>
<span class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">3 ítems</span>
</div>
<button class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
<span class="material-symbols-outlined block">close</span>
</button>
</div>
<div class="flex-1 overflow-y-auto p-6 space-y-6">
<div class="flex gap-4 group">
<div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-lg overflow-hidden flex-shrink-0">
<img alt="Reloj Minimalista" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyMui6hcPTjNBKyHfNzFAYeygXtkWmyHWn_C4wfn7rFaCjoq0M9SOHWEGdEm3vJS9fCaRyrLFWl8rJPlNYpJo0mMFbNQvNwvC2G_1L-8yDyBNd0hxhpxq8_qejsD0xdiz06FkU-STszocNHnaZYyupjQEbkKeQMKkKYzo6PzT8vcaUNYB2Dm-ZN5SOkaRnBc2hkkASEtDayluznVaXBeb9S_iHpz--Wa-OPMFapelO1RAPkyovvh282UPQfDEP-BAKPx3gJlapjOU"/>
</div>
<div class="flex-1 flex flex-col justify-between">
<div class="flex justify-between items-start">
<h3 class="font-bold text-slate-900 dark:text-white text-sm line-clamp-1">Reloj Minimalista</h3>
<button class="text-slate-400 hover:text-red-500 transition-colors">
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
<div class="flex items-center justify-between mt-2">
<div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-md overflow-hidden">
<button class="px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
<span class="material-symbols-outlined text-xs block">remove</span>
</button>
<span class="px-3 py-1 text-xs font-bold">1</span>
<button class="px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
<span class="material-symbols-outlined text-xs block">add</span>
</button>
</div>
<span class="font-bold text-slate-900 dark:text-white">$120.00</span>
</div>
</div>
</div>
<div class="flex gap-4 group">
<div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-lg overflow-hidden flex-shrink-0">
<img alt="Auriculares Premium" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCT7e4-xYKK9BEORzJsQInY6ov1KHRukfrtqYr8Bp805kbglQeSiGjGQ2eT3nWfKzLTSloP9zwOezy9bAUAIS_4SXGvj-13II1E3PrOFYNu1pODtrSm50StPhQoN2msoexJckY7D95lSQMJaPDHrc_8kXJtj5hjPNRTL3F356QfhcTHLI2cExTAfCGKsbBYzKqbD2Z1CBES4lQH_t9JIfGL09fhaFy8j5dvGNcDOuNIpe1lx938j2EUP1KMOihctbCZm8qemH1jPbc"/>
</div>
<div class="flex-1 flex flex-col justify-between">
<div class="flex justify-between items-start">
<h3 class="font-bold text-slate-900 dark:text-white text-sm line-clamp-1">Auriculares Premium</h3>
<button class="text-slate-400 hover:text-red-500 transition-colors">
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
<div class="flex items-center justify-between mt-2">
<div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-md overflow-hidden">
<button class="px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
<span class="material-symbols-outlined text-xs block">remove</span>
</button>
<span class="px-3 py-1 text-xs font-bold">1</span>
<button class="px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
<span class="material-symbols-outlined text-xs block">add</span>
</button>
</div>
<span class="font-bold text-slate-900 dark:text-white">$299.99</span>
</div>
</div>
</div>
<div class="flex gap-4 group">
<div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-lg overflow-hidden flex-shrink-0">
<img alt="Zapatillas Deportivas" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDLd2pqhfzNagD1xOa8CIB4G1pzg9eyxfH2W3Sxrg88_p1clkkq40CX3SusjDeKqho2ZD1fOlz4wpnKYSyhlNPq68GJIAcgR53qKXnuGBJhJEKVKKk-Ijq8I7OfT7AcxNbxet_se8LhCCptkSyhdHvbfhujVtMk0yUn7QpqSN0CqY2q0o9QeUDX3oxV9Hs1xtMjiPXGggXMmd0ajay0NHlW2ty3ZAMUKiSLToSEbdR1DuXiao4GA2qf93IkNY4Lqa5ulQY44iARKqE"/>
</div>
<div class="flex-1 flex flex-col justify-between">
<div class="flex justify-between items-start">
<h3 class="font-bold text-slate-900 dark:text-white text-sm line-clamp-1">Zapatillas Deportivas</h3>
<button class="text-slate-400 hover:text-red-500 transition-colors">
<span class="material-symbols-outlined text-lg">delete</span>
</button>
</div>
<div class="flex items-center justify-between mt-2">
<div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-md overflow-hidden">
<button class="px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
<span class="material-symbols-outlined text-xs block">remove</span>
</button>
<span class="px-3 py-1 text-xs font-bold">1</span>
<button class="px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
<span class="material-symbols-outlined text-xs block">add</span>
</button>
</div>
<span class="font-bold text-slate-900 dark:text-white">$110.00</span>
</div>
</div>
</div>
</div>
<div class="p-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800">
<div class="space-y-3 mb-6">
<div class="flex justify-between text-sm text-slate-600 dark:text-slate-400">
<span>Subtotal</span>
<span>$529.99</span>
</div>
<div class="flex justify-between text-sm text-slate-600 dark:text-slate-400">
<span>Impuestos (15%)</span>
<span>$79.50</span>
</div>
<div class="flex justify-between text-lg font-bold text-slate-900 dark:text-white pt-3 border-t border-slate-200 dark:border-slate-700">
<span>Total</span>
<span>$609.49</span>
</div>
</div>
<div class="space-y-3">
<button id="btnFinalizarCompra"
    class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
    <span class="material-symbols-outlined">payments</span>
    Finalizar Compra
</button>
</div>
<p class="text-center text-xs text-slate-500 mt-4">Envío gratis aplicado para este pedido 🚚</p>
</div>
</aside>
</body></html>