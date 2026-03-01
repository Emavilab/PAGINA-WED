<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Confirmación de Pedido - RetailCMS</title>
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
<style type="text/tailwindcss">
        body {
            font-family: 'Inter', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;
        }
    </style>

<main class="max-w-4xl mx-auto px-4 py-12 md:py-20">
<div class="text-center mb-12">
<div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-500 mb-6">
<span class="material-symbols-outlined text-6xl">check_circle</span>
</div>
<h1 class="text-4xl font-extrabold text-slate-900 dark:text-white mb-2 tracking-tight">¡Gracias por tu compra!</h1>
<p class="text-lg text-slate-500 dark:text-slate-400 mb-4">Tu pedido ha sido recibido y ya estamos preparando el envío.</p>
<div class="inline-block bg-slate-100 dark:bg-slate-800 px-4 py-2 rounded-full border border-slate-200 dark:border-slate-700">
<span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Orden <span class="text-primary">#12345</span></span>
</div>
</div>
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden">
<div class="p-8 border-b border-slate-100 dark:border-slate-800">
<h2 class="text-xl font-bold mb-6">Detalles del Pedido</h2>
<div class="space-y-6">
<div class="flex items-center gap-4">
<img alt="Zapatillas Deportivas" class="w-20 h-20 rounded-xl object-cover border border-slate-100 dark:border-slate-800" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC2QotxqjndSB5qkZ6hi7xoXmCQ1AhcKczkQ1ArMLAFi3o2Pc8HvOehknCvnLosMNLKXSdJuYpb1ydJBqe-M2_wRZv6aFJaYy5XPLnaiHvXXeXqiUM0Z7h9p3TPeV34oSz5WbolLunyugPcZjuuV-Mm1xvFBxEgM4m_FTV5Fj8iKI-1XOlxOYPNu0O9_3BU8Eyd5m8whfdz1Y-5QBX_tpEDETOjizK_4OPsehfx76gY1pyigoSZHW7EA5CpervyDDNVCgftkQJr0yU"/>
<div class="flex-1">
<h4 class="font-bold text-slate-900 dark:text-white">Zapatillas Ultra Boost v2</h4>
<p class="text-sm text-slate-500">Talla: 42 | Color: Rojo</p>
<p class="text-sm font-medium mt-1">Cantidad: 1</p>
</div>
<div class="text-right">
<p class="font-bold">129,90 €</p>
</div>
</div>
<div class="flex items-center gap-4">
<img alt="Smartwatch" class="w-20 h-20 rounded-xl object-cover border border-slate-100 dark:border-slate-800" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDdVuwQnq77nE2Q8JQjl4_F5fUS8wcMNBp_ptSJ1huX_zxGEZ-yg2WWFT98LtNB8ay1vSrbFxKApz4QIwz0ysORY3METn3ll4YShVwHvh0RfIsjjamk7zuNbEjFnNOIUUPgksVp91DZQt5FiBESkJKOFUBaae9WqvZXPcPogEDIqQcr997C9nxEJVfSLwcQ_jyy3t-6xzNmU8SG5Gy0edGwS2MZRtDiPJMtTakNk9gG4_psPcniKhXA0tXXy9hWZzqqvnD8rYKVKjU"/>
<div class="flex-1">
<h4 class="font-bold text-slate-900 dark:text-white">Smartwatch Series 5</h4>
<p class="text-sm text-slate-500">Talla: Única | Color: Blanco</p>
<p class="text-sm font-medium mt-1">Cantidad: 1</p>
</div>
<div class="text-right">
<p class="font-bold">89,00 €</p>
</div>
</div>
</div>
</div>
<div class="grid md:grid-cols-2 gap-8 p-8 bg-slate-50/50 dark:bg-slate-800/20">
<div>
<h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
<span class="material-icons text-lg">local_shipping</span> Dirección de Envío
                </h3>
<div class="text-slate-600 dark:text-slate-300 leading-relaxed">
<p class="font-bold text-slate-900 dark:text-white">Juan Pérez</p>
<p>Calle de Velázquez, 12, 4º Derecha</p>
<p>28001, Madrid</p>
<p>España</p>
<p class="mt-2 flex items-center gap-1"><span class="material-icons text-sm">phone</span> +34 600 000 000</p>
</div>
</div>
<div class="space-y-3">
<h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
<span class="material-icons text-lg">receipt_long</span> Resumen de Pago
                </h3>
<div class="flex justify-between text-sm">
<span class="opacity-70">Subtotal</span>
<span>218,90 €</span>
</div>
<div class="flex justify-between text-sm">
<span class="opacity-70">Envío (Estándar)</span>
<span class="text-emerald-500 font-medium">Gratis</span>
</div>
<div class="flex justify-between text-sm">
<span class="opacity-70">Impuestos</span>
<span>45,97 €</span>
</div>
<div class="flex justify-between items-center pt-4 border-t border-slate-200 dark:border-slate-700 mt-4">
<span class="text-lg font-bold">Total Pagado</span>
<span class="text-2xl font-black text-primary">264,87 €</span>
</div>
<div class="flex items-center gap-2 text-xs text-slate-500 mt-4">
<span class="material-icons text-sm">credit_card</span>
<span>Pagado con Tarjeta de Crédito (**** 4242)</span>
</div>
</div>
</div>
</div>
<div class="mt-12 flex flex-col items-center gap-6">
<button class="w-full md:w-auto px-12 py-4 bg-primary hover:bg-primary/90 text-white rounded-xl font-bold text-lg shadow-xl shadow-primary/25 transition-all flex items-center justify-center gap-2 group">
<span class="material-icons group-hover:translate-x-1 transition-transform">local_shipping</span>
            Rastrear mi pedido
        </button>
<a class="text-primary font-semibold hover:underline flex items-center gap-2" href="#">
<span class="material-icons text-sm">arrow_back</span>
            Seguir comprando
        </a>
</div>
<div class="mt-16 text-center text-sm text-slate-400 max-w-lg mx-auto leading-relaxed">
<p>Hemos enviado un correo de confirmación a <strong>usuario@ejemplo.com</strong> con los detalles de tu compra y el enlace de seguimiento.</p>
<p class="mt-2">¿Tienes alguna pregunta? Contáctanos a <a class="text-primary hover:underline" href="#">soporte@retailcms.com</a></p>
</div>
</main>
</body></html>