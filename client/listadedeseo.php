<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Lista de Deseos - RetailCMS</title>
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
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;
        }
        .heart-active {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;
            color: #ef4444;
        }
    </style>
<div class="max-w-7xl mx-auto px-4 py-8">
<div class="flex items-center justify-between mb-8">
<div>

<h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Lista de Deseos</h1>
<p class="text-slate-500 mt-1">Tienes 4 artículos guardados en tu lista.</p>
</div>
<button class="flex items-center gap-2 text-sm font-semibold text-primary hover:bg-primary/5 px-4 py-2 rounded-lg transition-colors">
<span class="material-symbols-outlined text-lg">share</span>
            Compartir Lista
        </button>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-xl transition-shadow">
<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-800">
<img alt="Zapatillas Deportivas" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC2QotxqjndSB5qkZ6hi7xoXmCQ1AhcKczkQ1ArMLAFi3o2Pc8HvOehknCvnLosMNLKXSdJuYpb1ydJBqe-M2_wRZv6aFJaYy5XPLnaiHvXXeXqiUM0Z7h9p3TPeV34oSz5WbolLunyugPcZjuuV-Mm1xvFBxEgM4m_FTV5Fj8iKI-1XOlxOYPNu0O9_3BU8Eyd5m8whfdz1Y-5QBX_tpEDETOjizK_4OPsehfx76gY1pyigoSZHW7EA5CpervyDDNVCgftkQJr0yU"/>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/90 dark:bg-slate-900/90 rounded-full flex items-center justify-center text-primary shadow-sm">
<span class="material-symbols-outlined heart-active text-xl">favorite</span>
</button>
</div>
<div class="p-5">
<p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-1">Calzado</p>
<h3 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-1">Zapatillas Ultra Boost v2</h3>
<div class="flex items-baseline gap-2 mb-4">
<span class="text-xl font-black text-slate-900 dark:text-white">129,90 €</span>
<span class="text-sm text-slate-400 line-through">159,00 €</span>
</div>
<div class="space-y-2">
<button class="w-full py-2.5 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
<span class="material-icons text-lg">shopping_cart</span>
                        Agregar al Carrito
                    </button>
<button class="w-full py-2 text-slate-400 hover:text-red-500 text-xs font-medium transition-colors flex items-center justify-center gap-1">
<span class="material-icons text-sm">delete_outline</span>
                        Eliminar
                    </button>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-xl transition-shadow">
<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-800">
<img alt="Smartwatch" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDdVuwQnq77nE2Q8JQjl4_F5fUS8wcMNBp_ptSJ1huX_zxGEZ-yg2WWFT98LtNB8ay1vSrbFxKApz4QIwz0ysORY3METn3ll4YShVwHvh0RfIsjjamk7zuNbEjFnNOIUUPgksVp91DZQt5FiBESkJKOFUBaae9WqvZXPcPogEDIqQcr997C9nxEJVfSLwcQ_jyy3t-6xzNmU8SG5Gy0edGwS2MZRtDiPJMtTakNk9gG4_psPcniKhXA0tXXy9hWZzqqvnD8rYKVKjU"/>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/90 dark:bg-slate-900/90 rounded-full flex items-center justify-center text-primary shadow-sm">
<span class="material-symbols-outlined heart-active text-xl">favorite</span>
</button>
</div>
<div class="p-5">
<p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-1">Tecnología</p>
<h3 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-1">Smartwatch Series 5 Pro</h3>
<div class="flex items-baseline gap-2 mb-4">
<span class="text-xl font-black text-slate-900 dark:text-white">89,00 €</span>
</div>
<div class="space-y-2">
<button class="w-full py-2.5 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
<span class="material-icons text-lg">shopping_cart</span>
                        Agregar al Carrito
                    </button>
<button class="w-full py-2 text-slate-400 hover:text-red-500 text-xs font-medium transition-colors flex items-center justify-center gap-1">
<span class="material-icons text-sm">delete_outline</span>
                        Eliminar
                    </button>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-xl transition-shadow">
<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-800">
<img alt="Auriculares" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAgp7PR6oROIXnp0mQKVYTRylCnEblzXAEtL98_peJUCIFtGnzTpuyW6bsCRlymafZIPl499Nt0jSA17DuGZoRlwOPtfq9eEdJQS11O3_ZlfX0-7U1rqY_OeRBDl-urnXezhwHLkimOtnIYZzku9TzLi2-jIvE-6g4yOrsTPxT9-YVdL0Y4naQh9A-yJLyRa_DOg72qDaKXo1IYsKJzUMYcO44m88RhVDqNOf4hgkWNOz2hvhF8QYJVeHbyYGgcjD1o8Lh82ykgZsE"/>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/90 dark:bg-slate-900/90 rounded-full flex items-center justify-center text-primary shadow-sm">
<span class="material-symbols-outlined heart-active text-xl">favorite</span>
</button>
<div class="absolute bottom-3 left-3 bg-red-500 text-white text-[10px] font-black px-2 py-1 rounded">AGOTADO</div>
</div>
<div class="p-5">
<p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-1">Sonido</p>
<h3 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-1">Auriculares Wireless Studio</h3>
<div class="flex items-baseline gap-2 mb-4">
<span class="text-xl font-black text-slate-900 dark:text-white">199,99 €</span>
</div>
<div class="space-y-2">
<button class="w-full py-2.5 bg-slate-200 dark:bg-slate-800 text-slate-400 rounded-lg font-bold text-sm cursor-not-allowed flex items-center justify-center gap-2" disabled="">
<span class="material-icons text-lg">notifications</span>
                        Avisarme
                    </button>
<button class="w-full py-2 text-slate-400 hover:text-red-500 text-xs font-medium transition-colors flex items-center justify-center gap-1">
<span class="material-icons text-sm">delete_outline</span>
                        Eliminar
                    </button>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-xl transition-shadow">
<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-800">
<img alt="Mochila" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAsI9Nb_Oem_ixqRUHEzA5woWqa5TehqCjAKuBWQ2zXSpPRawC7bdNWV9x6y07KrleQh8_x_h852eF_7njbAGIUlrkWDS26uMI_sFcKmz3CyVo8MBPKLt1vvAGTpAqoSOIgYnTt_nsOXtESqtZGQqgcOtxS63CRpYj8kh8Go_pYqnw3a3_7T0jTk1AcyuH7z3gSxxfV1Qr-yBLjM5pjI44DBpHw_TcAVwIP58JRp8meBi7snNXmXHOl7OCOmGVP_5kfGiB7bHqa9Gs"/>
<button class="absolute top-3 right-3 w-8 h-8 bg-white/90 dark:bg-slate-900/90 rounded-full flex items-center justify-center text-primary shadow-sm">
<span class="material-symbols-outlined heart-active text-xl">favorite</span>
</button>
</div>
<div class="p-5">
<p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-1">Accesorios</p>
<h3 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-1">Mochila Explorer 25L</h3>
<div class="flex items-baseline gap-2 mb-4">
<span class="text-xl font-black text-slate-900 dark:text-white">45,00 €</span>
</div>
<div class="space-y-2">
<button class="w-full py-2.5 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
<span class="material-icons text-lg">shopping_cart</span>
                        Agregar al Carrito
                    </button>
<button class="w-full py-2 text-slate-400 hover:text-red-500 text-xs font-medium transition-colors flex items-center justify-center gap-1">
<span class="material-icons text-sm">delete_outline</span>
                        Eliminar
                    </button>
</div>
</div>
</div>
</div>
<div class="hidden mt-12 text-center py-20 bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">
<span class="material-symbols-outlined text-6xl text-slate-300 mb-4">favorite_border</span>
<h2 class="text-xl font-bold mb-2">Tu lista está vacía</h2>
<p class="text-slate-500 mb-8">¡Explora nuestra tienda y guarda tus productos favoritos!</p>
<button class="px-8 py-3 bg-primary text-white rounded-xl font-bold">Empezar a comprar</button>
</div>
<div class="mt-12 flex flex-col items-center gap-6">
<a class="text-primary font-semibold hover:underline flex items-center gap-2" href="#">
<span class="material-icons text-sm">arrow_back</span>
            Volver a la tienda
        </a>
</div>
</div>