<?php
require_once 'core/sesiones.php';

$usuarioAutenticado = usuarioAutenticado();
$datosUsuario = $usuarioAutenticado ? obtenerDatosUsuario() : null;
?>
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
        .category-card:hover .category-image { transform: scale(1.05); }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24 }
        .carousel-container {
            position: relative;
            overflow: hidden;
        }
        .carousel-slide {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .product-card:hover .product-actions { opacity: 1; transform: translateY(0); }
        .modal-blur { backdrop-filter: blur(8px); }
        .cart-overlay.hidden { display: none; }
        .cart-sidebar.hidden { transform: translateX(100%); }
    </style>
</head>
<body class="bg-white dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display transition-colors duration-300">
<div class="bg-primary text-white py-2 text-center text-sm font-medium">
<p>🚚 ¡Envío gratis en pedidos superiores a 3000lps! Compra nuestras novedades hoy.</p>
</div>
<header class="sticky top-0 z-50 bg-white dark:bg-background-dark border-b border-slate-200 dark:border-slate-800 shadow-sm">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center justify-between h-20 gap-6">
<div class="flex items-center gap-2 flex-shrink-0">
<div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
<span class="material-icons-outlined text-white text-2xl">shopping_bag</span>
</div>
<span class="text-2xl font-bold tracking-tight text-primary">ControlPlus</span>
</div>
<div class="hidden md:flex flex-1 max-w-xl relative">
<input class="w-full pl-4 pr-12 py-2.5 rounded-full border-slate-200 focus:border-primary focus:ring-1 focus:ring-primary dark:bg-slate-800 dark:border-slate-700 transition-all text-sm" placeholder="Buscar productos, marcas o departamentos..." type="text"/>
<button class="absolute right-2 top-1/2 -translate-y-1/2 bg-primary text-white p-1.5 rounded-full hover:bg-primary-dark transition-colors">
<span class="material-symbols-outlined block text-xl">search</span>
</button>
</div>
<div class="flex items-center gap-5 lg:gap-8">
<div class="relative group">
<button onclick="toggleAccountMenu()" class="flex flex-col items-center text-slate-600 dark:text-slate-300 hover:text-primary transition-colors">
<span class="material-symbols-outlined">person</span>
<span class="text-[11px] font-semibold uppercase mt-0.5" id="account-button-text">Cuenta</span>
</button>
<!-- Dropdown Menu -->
<div id="accountMenu" class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-slate-900 rounded-lg shadow-lg border border-slate-200 dark:border-slate-800 z-50">
<?php if ($usuarioAutenticado && $datosUsuario): ?>
    <div class="px-4 py-3 text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800">
        <p class="text-sm font-semibold"><?php echo htmlspecialchars($datosUsuario['nombre']); ?></p>
        <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($datosUsuario['correo']); ?></p>
    </div>
    <button onclick="loadPerfil(); toggleAccountMenu();" class="w-full text-left flex items-center gap-3 px-4 py-3 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 border-b border-slate-100 dark:border-slate-800 transition-colors bg-none border-none cursor-pointer">
        <span class="material-symbols-outlined text-lg">person</span>
        <span class="text-sm font-semibold">Ver Perfil</span>
    </button>
    <button onclick="cerrarSesionCliente(); toggleAccountMenu();" class="w-full text-left flex items-center gap-3 px-4 py-3 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors bg-none border-none cursor-pointer text-red-600 dark:text-red-400">
        <span class="material-symbols-outlined text-lg">logout</span>
        <span class="text-sm font-semibold">Cerrar Sesión</span>
    </button>
<?php else: ?>
    <button onclick="loadLogin(); toggleAccountMenu();" class="w-full text-left flex items-center gap-3 px-4 py-3 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 border-b border-slate-100 dark:border-slate-800 transition-colors bg-none border-none cursor-pointer">
        <span class="material-symbols-outlined text-lg">login</span>
        <span class="text-sm font-semibold">Iniciar Sesión</span>
    </button>
    <button onclick="loadRegistrarse(); toggleAccountMenu();" class="w-full text-left flex items-center gap-3 px-4 py-3 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors bg-none border-none cursor-pointer">
        <span class="material-symbols-outlined text-lg">person_add</span>
        <span class="text-sm font-semibold">Registrarse</span>
    </button>
<?php endif; ?>
</div>
</div>
<button onclick="loadListaDeseos()" class="flex flex-col items-center text-slate-600 dark:text-slate-300 hover:text-primary transition-colors group bg-none border-none cursor-pointer">
<span class="material-symbols-outlined">favorite</span>
<span class="text-[11px] font-semibold uppercase mt-0.5 whitespace-nowrap">Lista de Deseos</span>
</button>
<button onclick="loadHistorialPedidos()" class="flex flex-col items-center text-slate-600 dark:text-slate-300 hover:text-primary transition-colors group bg-none border-none cursor-pointer">
<span class="material-symbols-outlined">package_2</span>
<span class="text-[11px] font-semibold uppercase mt-0.5 whitespace-nowrap">Mis Pedidos</span>
</button>
<button onclick="document.getElementById('cartOverlay').classList.remove('hidden'); document.getElementById('cartSidebar').classList.remove('hidden');" class="flex flex-col items-center text-slate-600 dark:text-slate-300 hover:text-primary transition-colors relative group">
<span class="material-symbols-outlined text-2xl">shopping_cart</span>
<span class="text-[11px] font-semibold uppercase mt-0.5">Carrito</span>
<span class="absolute -top-1 -right-1 bg-primary text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center">0</span>
</button>
</div>
</div>
<nav class="hidden lg:flex items-center space-x-10 py-3 border-t border-slate-100 dark:border-slate-800">
<a class="text-sm font-bold text-slate-700 dark:text-slate-200 hover:text-primary flex items-center gap-2" href="#">
<span class="material-symbols-outlined text-xl">grid_view</span> Categorías
                </a>
<button onclick="loadOfertas()" class="text-sm font-semibold text-red-600 dark:text-red-400 hover:underline flex items-center gap-1 bg-none border-none cursor-pointer">
<span class="material-symbols-outlined text-xl">sell</span> Ofertas
                </button>
<button onclick="loadContactanos()"
        class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary">
    Contáctanos
</button>
<a class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary" href="#">Sucursales</a>
</nav>
</div>
</header>
<main id="mainContent">
<section class="relative bg-slate-100 dark:bg-slate-900 group">
<div class="carousel-container relative h-[500px] lg:h-[600px] w-full">
<div class="absolute inset-0 flex flex-col lg:flex-row items-center">
<div class="flex-1 px-8 lg:px-20 py-16 text-center lg:text-left z-10">
<span class="inline-block px-4 py-1.5 rounded-full bg-primary/10 text-primary font-bold text-sm mb-6 uppercase tracking-wider">Ofertas Exclusivas Online</span>
<h1 class="text-4xl lg:text-6xl font-extrabold text-slate-900 dark:text-white leading-tight mb-6">
                            Frescura y Calidad <br/>
<span class="text-primary">Entregado a su Puerta</span>
</h1>
<p class="text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-xl">
                            Experimente la conveniencia de comprar sus alimentos y artículos esenciales favoritos en línea. Ahorre tiempo y dinero.
                        </p>
<div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
<button class="px-10 py-4 bg-primary text-white font-bold rounded-lg shadow-lg hover:bg-primary-dark transition-all">Comprar Ahora</button>
<button class="px-10 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold rounded-lg hover:bg-slate-50 transition-all">Ver Catálogo</button>
</div>
</div>
<div class="flex-1 w-full lg:w-1/2 h-full relative overflow-hidden">
<img alt="Hero Promoción" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAYZxkpIqkoEpRFEv_tLZg9P3zWbsmUQ8UVd2x1JvKC6sen391tN9PPYiaE6mPT8SzgtUQDwILtLB_6ydhgL_02Jz9FW62ACheK3TKgjsMeTXslrqKyqrGNqrV3HGOnnWvLB5edYXagt0HVPqIE4uzoiu8GvjRbx9UAYAGGduitPHOxTxk01n4ELvKK4KY7_It5WzRqX1OI-HSVjZSODx8FALdkrDyWF58RxzGCislBEKbaL79xHwOADItLvNiPFHcuGWvtsMNFJcA"/>
<div class="absolute inset-0 bg-gradient-to-r from-slate-100 via-transparent to-transparent lg:block hidden"></div>
</div>
</div>
<button class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-slate-800 p-3 rounded-full shadow-lg transition-all z-20 opacity-0 group-hover:opacity-100">
<span class="material-symbols-outlined block">arrow_back_ios_new</span>
</button>
<button class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 hover:bg-white text-slate-800 p-3 rounded-full shadow-lg transition-all z-20 opacity-0 group-hover:opacity-100">
<span class="material-symbols-outlined block">arrow_forward_ios</span>
</button>
<div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-20">
<div class="w-3 h-3 rounded-full bg-primary"></div>
<div class="w-3 h-3 rounded-full bg-slate-300 hover:bg-slate-400 cursor-pointer"></div>
<div class="w-3 h-3 rounded-full bg-slate-300 hover:bg-slate-400 cursor-pointer"></div>
</div>
</div>
</section>
<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="relative h-64 lg:h-80 rounded-2xl overflow-hidden group cursor-pointer">
<img alt="Promoción Frutas" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAThONAHYHO8b0FK3HK4DP5_NmlzOnEgS-jOxPpan9dU_H8TSGmtLgI_iVXKMF_tmeror0jOsxc2cX4qnj7s9yYzyHIoJqY1viF01Os_iYJDvnl3PByyUJuS3kd_DgIIjh0dJdwJz9R2HaLncixe0mTvtdLSa81R6SPEVxAsGsoyhGvQe4xwmq0bcrSkvIy9Qa2W3woEkZSedhQpvBS_Hm0SdqydnXmtd7wlXYoDCG4yz-hOtyexqe-p9S53QjLZTuN4vIBtGe6E78"/>
<div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center p-8">
<div class="text-white">
<h3 class="text-2xl font-bold mb-2">Frutas y Verduras</h3>
<p class="text-white/80 mb-4">Hasta 30% de descuento en frescos</p>
<span class="inline-block px-6 py-2 bg-white text-slate-900 font-bold rounded-lg group-hover:bg-primary group-hover:text-white transition-colors">Explorar</span>
</div>
</div>
</div>
<div class="relative h-64 lg:h-80 rounded-2xl overflow-hidden group cursor-pointer">
<img alt="Promoción Despensa" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAk-FCFWWZm29yAU-o40q_8E7Lx0vQ7erYz27hyFWW99vAq7T7xHa2tb74Z0dF5Ga7iR26Y_HfKqZG7ZqBpys5Xd1MsMVmvOYpGumka9FLuGurKql_87vxXwG3x3btm_VC6SMdB8Dl-kgFtp0oHaBU-CfvUcSdaxjEg7W9SAiPJN4gI7qJxQ4tAdyp2JVmJ6yyrOwfuxl_aFa5BI6oEOpOyuZls4X8keCBDAoA3-Pfv4fLeA9yju7UWRZjtA_f_Hn7cr65m5h8g4E8"/>
<div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center p-8">
<div class="text-white">
<h3 class="text-2xl font-bold mb-2">Básicos del Hogar</h3>
<p class="text-white/80 mb-4">Llena tu despensa al mejor precio</p>
<span class="inline-block px-6 py-2 bg-white text-slate-900 font-bold rounded-lg group-hover:bg-primary group-hover:text-white transition-colors">Ver Ofertas</span>
</div>
</div>
</div>
</div>
</section>
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center justify-between mb-10 border-l-4 border-primary pl-4">
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">Comprar por Departamento</h2>
<a class="text-primary font-semibold flex items-center hover:underline" href="#">
                    Ver Todo <span class="material-symbols-outlined text-lg ml-1">arrow_forward</span>
</a>
</div>
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
<a class="group block text-center category-card" href="#">
<div class="aspect-square bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4 overflow-hidden border border-slate-100 dark:border-slate-700 transition-all hover:shadow-md">
<span class="material-symbols-outlined text-5xl text-primary transition-transform category-image">grocery</span>
</div>
<h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-primary uppercase text-xs tracking-wide">Abarrotes</h3>
</a>
<a class="group block text-center category-card" href="#">
<div class="aspect-square bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4 overflow-hidden border border-slate-100 dark:border-slate-700 transition-all hover:shadow-md">
<span class="material-symbols-outlined text-5xl text-primary transition-transform category-image">medical_services</span>
</div>
<h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-primary uppercase text-xs tracking-wide">Farmacia</h3>
</a>
<a class="group block text-center category-card" href="#">
<div class="aspect-square bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4 overflow-hidden border border-slate-100 dark:border-slate-700 transition-all hover:shadow-md">
<span class="material-symbols-outlined text-5xl text-primary transition-transform category-image">devices</span>
</div>
<h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-primary uppercase text-xs tracking-wide">Electrónica</h3>
</a>
<a class="group block text-center category-card" href="#">
<div class="aspect-square bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4 overflow-hidden border border-slate-100 dark:border-slate-700 transition-all hover:shadow-md">
<span class="material-symbols-outlined text-5xl text-primary transition-transform category-image">chair</span>
</div>
<h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-primary uppercase text-xs tracking-wide">Hogar y Decoración</h3>
</a>
<a class="group block text-center category-card" href="#">
<div class="aspect-square bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4 overflow-hidden border border-slate-100 dark:border-slate-700 transition-all hover:shadow-md">
<span class="material-symbols-outlined text-5xl text-primary transition-transform category-image">styler</span>
</div>
<h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-primary uppercase text-xs tracking-wide">Moda</h3>
</a>
<a class="group block text-center category-card" href="#">
<div class="aspect-square bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4 overflow-hidden border border-slate-100 dark:border-slate-700 transition-all hover:shadow-md">
<span class="material-symbols-outlined text-5xl text-primary transition-transform category-image">toys</span>
</div>
<h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-primary uppercase text-xs tracking-wide">Juguetes y Bebé</h3>
</a>
</div>
</section>
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-100 dark:border-slate-800">
<div class="flex items-center justify-between mb-10 border-l-4 border-primary pl-4">
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">Productos Destacados</h2>
<a class="text-primary font-semibold flex items-center hover:underline" href="#">
                    Ver Todos <span class="material-symbols-outlined text-lg ml-1">arrow_forward</span>
</a>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
<div class="product-card group bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-all duration-300">
<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-700">
<img alt="Producto 1" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDyMui6hcPTjNBKyHfNzFAYeygXtkWmyHWn_C4wfn7rFaCjoq0M9SOHWEGdEm3vJS9fCaRyrLFWl8rJPlNYpJo0mMFbNQvNwvC2G_1L-8yDyBNd0hxhpxq8_qejsD0xdiz06FkU-STszocNHnaZYyupjQEbkKeQMKkKYzo6PzT8vcaUNYB2Dm-ZN5SOkaRnBc2hkkASEtDayluznVaXBeb9S_iHpz--Wa-OPMFapelO1RAPkyovvh282UPQfDEP-BAKPx3gJlapjOU"/>
<div class="product-actions absolute inset-0 bg-black/5 flex items-center justify-center gap-3 opacity-0 translate-y-4 transition-all duration-300">
<button class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Lista de deseos">
<span class="material-symbols-outlined">favorite</span>
</button>
<button class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Vista previa">
<span class="material-symbols-outlined">visibility</span>
</button>
</div>
<div class="absolute top-3 left-3">
<span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase">Oferta</span>
</div>
</div>
<div class="p-5">
<h3 class="font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors truncate">Reloj Minimalista</h3>
<p class="text-slate-500 dark:text-slate-400 text-sm mb-4 line-clamp-2">Elegancia y precisión en cada detalle, perfecto para cualquier ocasión.</p>
<div class="flex items-center justify-between gap-4">
<div class="flex flex-col">
<span class="text-xl font-bold text-slate-900 dark:text-white">$120.00</span>
<span class="text-xs text-slate-400 line-through">$150.00</span>
</div>
<button class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold transition-colors">
<span class="material-symbols-outlined text-lg">shopping_cart</span>
                                Agregar
                            </button>
</div>
</div>
</div>
<div class="product-card group bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-all duration-300">
<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-700">
<img alt="Producto 2" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCT7e4-xYKK9BEORzJsQInY6ov1KHRukfrtqYr8Bp805kbglQeSiGjGQ2eT3nWfKzLTSloP9zwOezy9bAUAIS_4SXGvj-13II1E3PrOFYNu1pODtrSm50StPhQoN2msoexJckY7D95lSQMJaPDHrc_8kXJtj5hjPNRTL3F356QfhcTHLI2cExTAfCGKsbBYzKqbD2Z1CBES4lQH_t9JIfGL09fhaFy8j5dvGNcDOuNIpe1lx938j2EUP1KMOihctbCZm8qemH1jPbc"/>
<div class="product-actions absolute inset-0 bg-black/5 flex items-center justify-center gap-3 opacity-0 translate-y-4 transition-all duration-300">
<button class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Lista de deseos">
<span class="material-symbols-outlined">favorite</span>
</button>
<button class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Vista previa">
<span class="material-symbols-outlined">visibility</span>
</button>
</div>
</div>
<div class="p-5">
<h3 class="font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors truncate">Auriculares Premium</h3>
<p class="text-slate-500 dark:text-slate-400 text-sm mb-4 line-clamp-2">Cancelación activa de ruido y sonido de alta fidelidad para melómanos.</p>
<div class="flex items-center justify-between gap-4">
<span class="text-xl font-bold text-slate-900 dark:text-white">$299.99</span>
<button class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold transition-colors">
<span class="material-symbols-outlined text-lg">shopping_cart</span>
                                Agregar
                            </button>
</div>
</div>
</div>
<div class="product-card group bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-all duration-300">
<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-700">
<img alt="Producto 3" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBCjVJkDgAbb4ZIOI6G8zQhzolvzxnbb_MK9MmtnP20pnN_2_LLa6dYy-meWzrx1ehjA3QLqDy4hqq78-sFHESmJaiy9PIItmuOLnTU_JE1dro16YXub6WMsJqnyDSnmegUxoxsNHPQ8EC1aeV8VC5bJnnEVMRnXQ5-FCjpvZ8OBsk5C0MXlPviNMwTstkHBnYweTNg4-u31BR7T10eI-mt8KM3vc1MY-PWAxdLyQ_yqhSfMPk-R98azLfi1_wZrXWxMyNgjuFW-cQ"/>
<div class="product-actions absolute inset-0 bg-black/5 flex items-center justify-center gap-3 opacity-0 translate-y-4 transition-all duration-300">
<button class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Lista de deseos">
<span class="material-symbols-outlined">favorite</span>
</button>
<button class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Vista previa">
<span class="material-symbols-outlined">visibility</span>
</button>
</div>
<div class="absolute top-3 left-3">
<span class="bg-primary text-white text-[10px] font-bold px-2 py-1 rounded uppercase">Nuevo</span>
</div>
</div>
<div class="p-5">
<h3 class="font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors truncate">Cámara Retro 35mm</h3>
<p class="text-slate-500 dark:text-slate-400 text-sm mb-4 line-clamp-2">Captura momentos especiales con el estilo clásico de la fotografía analógica.</p>
<div class="flex items-center justify-between gap-4">
<span class="text-xl font-bold text-slate-900 dark:text-white">$85.00</span>
<button class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold transition-colors">
<span class="material-symbols-outlined text-lg">shopping_cart</span>
                                Agregar
                            </button>
</div>
</div>
</div>
<div class="product-card group bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-all duration-300">
<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-700">
<img alt="Producto 4" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDLd2pqhfzNagD1xOa8CIB4G1pzg9eyxfH2W3Sxrg88_p1clkkq40CX3SusjDeKqho2ZD1fOlz4wpnKYSyhlNPq68GJIAcgR53qKXnuGBJhJEKVKKk-Ijq8I7OfT7AcxNbxet_se8LhCCptkSyhdHvbfhujVtMk0yUn7QpqSN0CqY2q0o9QeUDX3oxV9Hs1xtMjiPXGggXMmd0ajay0NHlW2ty3ZAMUKiSLToSEbdR1DuXiao4GA2qf93IkNY4Lqa5ulQY44iARKqE"/>
<div class="product-actions absolute inset-0 bg-black/5 flex items-center justify-center gap-3 opacity-0 translate-y-4 transition-all duration-300">
<button class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Lista de deseos">
<span class="material-symbols-outlined">favorite</span>
</button>
<button class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Vista previa">
<span class="material-symbols-outlined">visibility</span>
</button>
</div>
</div>
<div class="p-5">
<h3 class="font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors truncate">Zapatillas Deportivas</h3>
<p class="text-slate-500 dark:text-slate-400 text-sm mb-4 line-clamp-2">Máximo confort y rendimiento para tus entrenamientos diarios.</p>
<div class="flex items-center justify-between gap-4">
<span class="text-xl font-bold text-slate-900 dark:text-white">$110.00</span>
<button class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold transition-colors">
<span class="material-symbols-outlined text-lg">shopping_cart</span>
                                Agregar
                            </button>
</div>
</div>
</div>
</div>
</section>
<section class="bg-background-light dark:bg-slate-900/50 py-12">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
<div class="flex items-center gap-4">
<span class="material-symbols-outlined text-primary text-4xl">local_shipping</span>
<div>
<h4 class="font-bold text-sm">Entrega Rápida</h4>
<p class="text-xs text-slate-500">A su hogar u oficina</p>
</div>
</div>
<div class="flex items-center gap-4">
<span class="material-symbols-outlined text-primary text-4xl">verified_user</span>
<div>
<h4 class="font-bold text-sm">Pago Seguro</h4>
<p class="text-xs text-slate-500">Checkout 100% seguro</p>
</div>
</div>
<div class="flex items-center gap-4">
<span class="material-symbols-outlined text-primary text-4xl">workspace_premium</span>
<div>
<h4 class="font-bold text-sm">Calidad Garantizada</h4>
<p class="text-xs text-slate-500">Productos frescos cada día</p>
</div>
</div>
<div class="flex items-center gap-4">
<span class="material-symbols-outlined text-primary text-4xl">support_agent</span>
<div>
<h4 class="font-bold text-sm">Soporte 24/7</h4>
<p class="text-xs text-slate-500">Centro de ayuda dedicado</p>
</div>
</div>
</div>
</section>
</main>
<footer class="bg-white dark:bg-background-dark border-t border-slate-200 dark:border-slate-800 pt-16 pb-8">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
<div>
<div class="flex items-center gap-2 mb-6">
<div class="w-8 h-8 bg-primary rounded flex items-center justify-center">
<span class="material-icons-outlined text-white text-xl">shopping_bag</span>
</div>
<span class="text-xl font-bold text-primary">ControlPlus</span>
</div>
<p class="text-slate-500 dark:text-slate-400 text-sm mb-6 leading-relaxed">
                        Llevando el supermercado a la puerta de su casa. La mejor calidad, servicio y precios del mercado.
                    </p>
<div class="flex gap-4">
<a class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="#">
<span class="material-icons-outlined text-xl">facebook</span>
</a>
<a class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="#">
<span class="material-icons-outlined text-xl">public</span>
</a>
<a class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="#">
<span class="material-symbols-outlined text-xl">camera</span>
</a>
</div>
</div>
<div>
<h4 class="font-bold text-slate-900 dark:text-white mb-6 uppercase text-xs tracking-wider">Sobre Nosotros</h4>
<ul class="space-y-4">
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Nuestra Historia</a></li>
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Responsabilidad Corporativa</a></li>
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Localizador de Tiendas</a></li>
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Bolsa de Trabajo</a></li>
</ul>
</div>
<div>
<h4 class="font-bold text-slate-900 dark:text-white mb-6 uppercase text-xs tracking-wider">Servicio al Cliente</h4>
<ul class="space-y-4">
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Centro de Ayuda</a></li>
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Envíos y Devoluciones</a></li>
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Métodos de Pago</a></li>
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Tarjetas de Regalo</a></li>
</ul>
</div>
<div>
<h4 class="font-bold text-slate-900 dark:text-white mb-6 uppercase text-xs tracking-wider">Información Corporativa</h4>
<ul class="space-y-4">
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Relaciones con Inversionistas</a></li>
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Términos de Uso</a></li>
<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="#">Política de Privacidad</a></li>
<li class="pt-2">
<div class="flex items-center gap-2 grayscale opacity-60">
<span class="material-icons-outlined text-2xl">credit_card</span>
<span class="material-icons-outlined text-2xl">account_balance_wallet</span>
<span class="material-icons-outlined text-2xl">payments</span>
</div>
</li>
</ul>
</div>
</div>
<div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-400">
<p>© 2026 controlplus Inc. Todos los derechos reservados.</p>
<div class="flex gap-8">
<span>Diseñado para la Calidad</span>
<span>Proyecto de clases lenguanjes de programacion</span>
</div>
</div>
</div>
<!-- Overlay y Carrito Modal -->
<div id="cartOverlay" class="cart-overlay hidden fixed inset-0 bg-slate-900/60 modal-blur z-[110]" onclick="document.getElementById('cartOverlay').classList.add('hidden'); document.getElementById('cartSidebar').classList.add('hidden');"></div>
<aside id="cartSidebar" class="cart-sidebar hidden fixed top-0 right-0 h-full w-full max-w-md bg-white dark:bg-slate-900 z-[120] shadow-2xl flex flex-col animate-in slide-in-from-right duration-300 transition-transform">
<div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary text-2xl">shopping_cart</span>
<h2 class="text-xl font-bold text-slate-900 dark:text-white">Tu Carrito</h2>
<span class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">3 ítems</span>
</div>
<button onclick="document.getElementById('cartOverlay').classList.add('hidden'); document.getElementById('cartSidebar').classList.add('hidden');" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
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
<button onclick="loadFinalizarCompra()" class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
<span class="material-symbols-outlined">payments</span>
                Finalizar Compra
            </button>
<button class="w-full border-2 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 py-3 rounded-xl font-bold hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                Ver Carrito
            </button>
</div>
<p class="text-center text-xs text-slate-500 mt-4">Envío gratis aplicado para este pedido 🚚</p>
</div>
</aside>

<script>
function loadContacto() {
    fetch('pages/contactanos.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('mainContent').innerHTML = data;
            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar contactanos:', error));
}

function loadLogin() {
    fetch('pages/login.php')
        .then(response => response.text())
        .then(data => {
            // Crear un contenedor temporal para parsear el HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;
            
            // Extraer solo el body content
            const bodyContent = tempDiv.querySelector('body')?.innerHTML || data;
            
            // Insertar el contenido en mainContent
            document.getElementById('mainContent').innerHTML = bodyContent;
            
            // Extraer y ejecutar scripts
            const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
            let scriptMatch;
            while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                const script = document.createElement('script');
                script.textContent = scriptMatch[1];
                document.body.appendChild(script);
            }
            
            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar login:', error));
}

function loadRegistrarse() {
    fetch('pages/crear_cuenta.php')
        .then(response => response.text())
        .then(data => {
            // Crear un contenedor temporal para parsear el HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;
            
            // Extraer solo el body content
            const bodyContent = tempDiv.querySelector('body')?.innerHTML || data;
            
            // Insertar el contenido en mainContent
            document.getElementById('mainContent').innerHTML = bodyContent;
            
            // Extraer y ejecutar scripts
            const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
            let scriptMatch;
            while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                const script = document.createElement('script');
                script.textContent = scriptMatch[1];
                document.body.appendChild(script);
            }
            
            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar registro:', error));
}

function loadFinalizarCompra() {
    // Cerrar el carrito
    document.getElementById('cartOverlay').classList.add('hidden');
    document.getElementById('cartSidebar').classList.add('hidden');
    
    // Cargar la página de finalizar compra
    fetch('client/finalizarcompra.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('mainContent').innerHTML = data;
            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar finalizarcompra:', error));
}

function loadHistorialPedidos() {
    fetch('client/historialpedidoC.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('mainContent').innerHTML = data;
            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar historialpedidoC:', error));
}

function loadListaDeseos() {
    fetch('client/listadedeseo.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('mainContent').innerHTML = data;
            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar listadedeseo:', error));
}

function loadPerfil() {
    fetch('client/perfil.php')
        .then(response => response.text())
        .then(data => {
            // Si es un error JSON, redirigir al login
            if (data.includes('"exito"')) {
                window.location.href = 'index.php';
                return;
            }
            
            // Extraer solo el contenido del PHP
            // Remover etiquetas PHP
            let bodyContent = data.replace(/<\?php[\s\S]*?\?>/g, '');
            
            // Insertar el contenido envuelto en main dentro de mainContent
            document.getElementById('mainContent').innerHTML = bodyContent;
            
            // Extraer y ejecutar scripts
            const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
            let scriptMatch;
            while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                const script = document.createElement('script');
                script.textContent = scriptMatch[1];
                document.body.appendChild(script);
            }
            
            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar perfil:', error));
}

function loadContactanos() {
    fetch('pages/contactanos.php')
        .then(response => response.text())
        .then(data => {
            // Crear un contenedor temporal para parsear el HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;
            
            // Extraer solo el body content
            const bodyContent = tempDiv.querySelector('body')?.innerHTML || data;
            
            // Insertar el contenido en mainContent
            document.getElementById('mainContent').innerHTML = bodyContent;
            
            // Extraer y ejecutar scripts
            const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
            let scriptMatch;
            while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                const script = document.createElement('script');
                script.textContent = scriptMatch[1];
                document.body.appendChild(script);
            }
            
            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar contactanos:', error));
}

function loadOfertas() {
    fetch('pages/ofertas.php')
        .then(response => response.text())
        .then(data => {
            // Crear un contenedor temporal para parsear el HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;
            
            // Extraer solo el body content
            const bodyContent = tempDiv.querySelector('body')?.innerHTML || data;
            
            // Insertar el contenido en mainContent
            document.getElementById('mainContent').innerHTML = bodyContent;
            
            // Extraer y ejecutar scripts
            const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
            let scriptMatch;
            while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                const script = document.createElement('script');
                script.textContent = scriptMatch[1];
                document.body.appendChild(script);
            }
            
            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar ofertas:', error));
}

function cerrarSesionCliente() {
    // Confirmar antes de cerrar sesión
    CustomModal.show('confirm', 'Cerrar Sesión', '¿Estás seguro que deseas cerrar sesión?', (confirmed) => {
        if (confirmed) {
            window.location.href = 'core/cerrar_sesion.php';
        }
    });
}

// Función para volver al contenido original (opcional)
function loadHome() {
    location.reload();
}
// Función para toggle del menú de cuenta
function toggleAccountMenu() {
    const menu = document.getElementById('accountMenu');
    menu.classList.toggle('hidden');
}

// Cerrar el menú cuando se hace clic fuera de él
document.addEventListener('click', function(event) {
    const accountMenu = document.getElementById('accountMenu');
    const accountButton = event.target.closest('button');
    
    if (!accountButton || !accountButton.textContent.includes('Cuenta')) {
        if (accountMenu && !accountMenu.contains(event.target)) {
            accountMenu.classList.add('hidden');
        }
    }
});

// Modal personalizado para alertas
const CustomModal = {
    show: function(type, title, message, callback) {
        let modal = document.getElementById('customModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'customModal';
            modal.innerHTML = `
                <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-[9999]">
                    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl max-w-sm w-full mx-4">
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div id="modalIcon" class="flex-shrink-0"></div>
                                <div class="flex-1">
                                    <h3 id="modalTitle" class="text-lg font-bold text-slate-900 dark:text-white mb-2"></h3>
                                    <p id="modalMessage" class="text-sm text-slate-600 dark:text-slate-400"></p>
                                </div>
                            </div>
                        </div>
                        <div id="modalButtons" class="flex gap-3 p-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800 rounded-b-xl"></div>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        const iconMap = {
            'success': '<span class="material-symbols-outlined text-3xl text-green-500">check_circle</span>',
            'error': '<span class="material-symbols-outlined text-3xl text-red-500">error</span>',
            'warning': '<span class="material-symbols-outlined text-3xl text-yellow-500">warning</span>',
            'info': '<span class="material-symbols-outlined text-3xl text-blue-500">info</span>',
            'confirm': '<span class="material-symbols-outlined text-3xl text-blue-500">help</span>'
        };

        document.getElementById('modalIcon').innerHTML = iconMap[type] || iconMap['info'];
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;

        const buttonsDiv = document.getElementById('modalButtons');
        buttonsDiv.innerHTML = '';

        if (type === 'confirm') {
            const confirmBtn = document.createElement('button');
            confirmBtn.className = 'flex-1 bg-primary hover:bg-primary-dark text-white font-semibold py-2 px-4 rounded transition-colors';
            confirmBtn.textContent = 'Aceptar';
            confirmBtn.onclick = () => {
                modal.remove();
                if (callback) callback(true);
            };

            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'flex-1 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-900 dark:text-white font-semibold py-2 px-4 rounded transition-colors';
            cancelBtn.textContent = 'Cancelar';
            cancelBtn.onclick = () => {
                modal.remove();
                if (callback) callback(false);
            };

            buttonsDiv.appendChild(cancelBtn);
            buttonsDiv.appendChild(confirmBtn);
        } else {
            const okBtn = document.createElement('button');
            okBtn.className = 'w-full bg-primary hover:bg-primary-dark text-white font-semibold py-2 px-4 rounded transition-colors';
            okBtn.textContent = 'Aceptar';
            okBtn.onclick = () => {
                modal.remove();
                if (callback) callback();
            };
            buttonsDiv.appendChild(okBtn);
        }
    }
};

// Reemplazar window.alert
window.alert = function(message) {
    CustomModal.show('info', 'Mensaje', message);
};

// Reemplazar window.confirm
window.confirm = function(message) {
    return new Promise(resolve => {
        CustomModal.show('confirm', 'Confirmación', message, resolve);
    });
};

// Hacer CustomModal disponible globalmente
window.CustomModal = CustomModal;
</script>

</body></html>