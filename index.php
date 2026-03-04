<?php
require_once 'core/sesiones.php';
require_once 'core/conexion.php';

$usuarioAutenticado = usuarioAutenticado();
$datosUsuario = $usuarioAutenticado ? obtenerDatosUsuario() : null;

// Cargar configuración general del negocio
$res_cfg = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");
$cfg = ($res_cfg && mysqli_num_rows($res_cfg) > 0) ? mysqli_fetch_assoc($res_cfg) : [];
$cfg_redes = !empty($cfg['redes_sociales']) ? json_decode($cfg['redes_sociales'], true) : [];

// Menú del header y columnas del footer configurables (JSON)
$cfg_header_menu = [];
if (!empty($cfg['header_menu'])) {
    $tmpHeader = json_decode($cfg['header_menu'], true);
    if (is_array($tmpHeader)) {
        $cfg_header_menu = $tmpHeader;
    }
}

$cfg_footer_columns = [];
if (!empty($cfg['footer_columns'])) {
    $tmpFooter = json_decode($cfg['footer_columns'], true);
    if (is_array($tmpFooter)) {
        $cfg_footer_columns = $tmpFooter;
    }
}
$cfg_nombre = htmlspecialchars($cfg['nombre_negocio'] ?? 'Mi Negocio');
$cfg_logo = $cfg['logo'] ?? '';
$cfg_correo = htmlspecialchars($cfg['correo'] ?? '');
$cfg_telefono = htmlspecialchars($cfg['telefono'] ?? '');
$cfg_direccion = htmlspecialchars($cfg['direccion'] ?? '');
$cfg_moneda_cod = $cfg['moneda'] ?? 'HNL';
$simbolos_moneda = ['USD' => '$', 'EUR' => '€', 'MXN' => '$', 'COP' => '$', 'ARS' => '$', 'GTQ' => 'Q', 'HNL' => 'L', 'CRC' => '₡'];
$cfg_moneda = $simbolos_moneda[$cfg_moneda_cod] ?? $cfg_moneda_cod;
$cfg_slogan = htmlspecialchars($cfg['slogan'] ?? '');
$cfg_pie = htmlspecialchars($cfg['pie_pagina'] ?? '');

// Colores del tema (con valores por defecto y validación rápida)
function normalizar_color_publico($valor, $defecto) {
    if (!is_string($valor)) return $defecto;
    $valor = trim($valor);
    if ($valor === '') return $defecto;
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) return $defecto;
    return strtoupper($valor);
}

$cfg_color_primary = normalizar_color_publico($cfg['color_primary'] ?? '#137fec', '#137FEC');
$cfg_color_primary_dark = normalizar_color_publico($cfg['color_primary_dark'] ?? '#0d66c2', '#0D66C2');
$cfg_color_bg_light = normalizar_color_publico($cfg['color_background_light'] ?? '#f6f7f8', '#F6F7F8');
$cfg_color_bg_dark = normalizar_color_publico($cfg['color_background_dark'] ?? '#101922', '#101922');
?>
<!DOCTYPE html>
<html lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo $cfg_nombre; ?></title>
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
                "primary": "<?php echo $cfg_color_primary; ?>",
                "primary-dark": "<?php echo $cfg_color_primary_dark; ?>",
                "background-light": "<?php echo $cfg_color_bg_light; ?>",
                "background-dark": "<?php echo $cfg_color_bg_dark; ?>",
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
        .hero-slide { will-change: opacity; }
    </style>
</head>
<body class="bg-white dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display transition-colors duration-300">
<?php if(!empty($cfg['texto_banner_superior'])): ?>
<div class="bg-primary text-white py-2 text-center text-sm font-medium">
<p><?php echo htmlspecialchars($cfg['texto_banner_superior']); ?></p>
</div>
<?php endif; ?>
<header class="sticky top-0 z-50 bg-white dark:bg-background-dark border-b border-slate-200 dark:border-slate-800 shadow-sm">
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center justify-between h-20 gap-6">
<div class="flex items-center gap-2 flex-shrink-0 cursor-pointer" onclick="if(typeof loadHome==='function'){loadHome();}else{location.reload();}" tabindex="0" role="button" aria-label="Ir al inicio">
<?php if(!empty($cfg_logo)): ?>
<img src="img/<?php echo $cfg_logo; ?>" alt="<?php echo $cfg_nombre; ?>" class="w-10 h-10 object-contain rounded-lg">
<?php else: ?>
<div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
<span class="material-icons-outlined text-white text-2xl">shopping_bag</span>
</div>
<?php endif; ?>
<span class="text-2xl font-bold tracking-tight text-primary"><?php echo $cfg_nombre; ?></span>
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
<button onclick="abrirCarrito()" class="flex flex-col items-center text-slate-600 dark:text-slate-300 hover:text-primary transition-colors relative group">
<span class="material-symbols-outlined text-2xl">shopping_cart</span>
<span class="text-[11px] font-semibold uppercase mt-0.5">Carrito</span>
<span id="cartBadge" class="absolute -top-1 -right-1 bg-primary text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center">0</span>
</button>
</div>
</div>
<nav class="hidden lg:flex items-center space-x-10 py-3 border-t border-slate-100 dark:border-slate-800">
<?php
// Si no hay menú configurado, usar un conjunto por defecto
$menu_items = $cfg_header_menu;
if (empty($menu_items)) {
    $menu_items = [
        ['label' => 'Categorías', 'path' => '/categorias', 'icon' => 'grid_view'],
        ['label' => 'Ofertas', 'path' => '/ofertas', 'icon' => 'sell'],
        ['label' => 'Contáctanos', 'path' => '/contacto', 'icon' => 'contact_support'],
    ];
}

foreach ($menu_items as $item) {
    $label = trim($item['label'] ?? '');
    $path = trim($item['path'] ?? '');
    if ($label === '' || $path === '') continue;

    $labelEsc = htmlspecialchars($label);
    $icon = trim($item['icon'] ?? '');

    $isInternal = false;
    $onclick = '';

    if ($path !== '' && $path[0] === '/') {
        $isInternal = true;
        switch ($path) {
            case '/categorias':
                $onclick = 'loadCategoriasPanel()';
                break;
            case '/ofertas':
                $onclick = 'loadOfertas()';
                break;
            case '/productos':
                $onclick = 'loadProductos()';
                if ($icon === '') $icon = 'inventory_2';
                break;
            case '/contacto':
            case '/contactanos':
                $onclick = 'loadContactanos()';
                break;
            case '/lista-deseos':
            case '/lista_deseos':
                $onclick = 'loadListaDeseos()';
                break;
            case '/pedidos':
            case '/mis-pedidos':
                $onclick = 'loadHistorialPedidos()';
                break;
            case '/carrito':
                $onclick = 'abrirCarrito()';
                break;
            case '/inicio':
            case '/home':
                $onclick = 'if(typeof loadHome===\'function\'){loadHome();}else{location.href=\'index.php\';}';
                break;
            default:
                $isInternal = false; // no hay mapeo, tratar como link normal
                break;
        }

        // Iconos por defecto según ruta si no se definió uno
        if ($icon === '') {
            switch ($path) {
                case '/categorias':
                    $icon = 'grid_view';
                    break;
                case '/ofertas':
                    $icon = 'sell';
                    break;
                case '/productos':
                    $icon = 'inventory_2';
                    break;
                case '/contacto':
                case '/contactanos':
                    $icon = 'contact_support';
                    break;
                case '/lista-deseos':
                case '/lista_deseos':
                    $icon = 'favorite';
                    break;
                case '/pedidos':
                case '/mis-pedidos':
                    $icon = 'package_2';
                    break;
                case '/carrito':
                    $icon = 'shopping_cart';
                    break;
                case '/inicio':
                case '/home':
                    $icon = 'home';
                    break;
            }
        }
    }

    if ($isInternal && $onclick !== '') {
        echo '<button onclick="' . $onclick . '" class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary flex items-center gap-2 bg-none border-none cursor-pointer">';
        if ($icon !== '') {
            echo '<span class="material-symbols-outlined text-xl">' . htmlspecialchars($icon) . '</span> ';
        }
        echo $labelEsc . '</button>';
    } else {
        $href = htmlspecialchars($path);
        echo '<a href="' . $href . '" class="text-sm font-semibold text-slate-600 dark:text-slate-400 hover:text-primary flex items-center gap-2">';
        if ($icon !== '') {
            echo '<span class="material-symbols-outlined text-xl">' . htmlspecialchars($icon) . '</span> ';
        }
        echo $labelEsc . '</a>';
    }
}
?>
</nav>
</div>
</header>
<main id="mainContent">
<!-- ========== HERO CARRUSEL ========== -->
<section id="heroCarousel" class="relative bg-slate-100 dark:bg-slate-900 group overflow-hidden">
<div class="relative h-[500px] lg:h-[600px] w-full">
    <!-- Slides container -->
    <div id="heroSlides" class="h-full w-full relative">
        <!-- Slide por defecto (config general) hasta que carguen banners -->
        <div class="hero-slide absolute inset-0 opacity-100 transition-opacity duration-700 ease-in-out">
            <div class="absolute inset-0 flex flex-col lg:flex-row items-center">
                <div class="flex-1 px-8 lg:px-20 py-16 text-center lg:text-left z-10">
                    <?php if(!empty($cfg['hero_etiqueta'])): ?>
                    <span class="inline-block px-4 py-1.5 rounded-full bg-primary/10 text-primary font-bold text-sm mb-6 uppercase tracking-wider"><?php echo htmlspecialchars($cfg['hero_etiqueta']); ?></span>
                    <?php endif; ?>
                    <h1 class="text-4xl lg:text-6xl font-extrabold text-slate-900 dark:text-white leading-tight mb-6">
                        <?php echo htmlspecialchars($cfg['hero_titulo'] ?? 'Bienvenido'); ?><br/>
                        <span class="text-primary"><?php echo htmlspecialchars($cfg['hero_subtitulo'] ?? $cfg_nombre); ?></span>
                    </h1>
                    <?php if(!empty($cfg['hero_descripcion'])): ?>
                    <p class="text-lg text-slate-600 dark:text-slate-400 mb-8 max-w-xl"><?php echo htmlspecialchars($cfg['hero_descripcion']); ?></p>
                    <?php endif; ?>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <button onclick="loadProductos()" class="px-10 py-4 bg-primary text-white font-bold rounded-lg shadow-lg hover:bg-primary-dark transition-all"><?php echo htmlspecialchars($cfg['hero_btn_primario'] ?? 'Comprar Ahora'); ?></button>
                        <button onclick="loadCategoriasPanel()" class="px-10 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 font-bold rounded-lg hover:bg-slate-50 transition-all"><?php echo htmlspecialchars($cfg['hero_btn_secundario'] ?? 'Ver Catálogo'); ?></button>
                    </div>
                </div>
                <div class="flex-1 w-full lg:w-1/2 h-full relative overflow-hidden">
                    <?php $hero_img = !empty($cfg['hero_imagen']) ? 'img/' . $cfg['hero_imagen'] : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&q=80'; ?>
                    <img alt="Hero" class="w-full h-full object-cover" src="<?php echo $hero_img; ?>"/>
                    <div class="absolute inset-0 bg-gradient-to-r from-slate-100 dark:from-slate-900 via-transparent to-transparent lg:block hidden"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Flechas -->
    <button id="heroPrev" onclick="heroNav(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-700 text-slate-800 dark:text-white p-3 rounded-full shadow-lg transition-all z-20 opacity-0 group-hover:opacity-100">
        <span class="material-symbols-outlined block">arrow_back_ios_new</span>
    </button>
    <button id="heroNext" onclick="heroNav(1)" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/80 dark:bg-slate-800/80 hover:bg-white dark:hover:bg-slate-700 text-slate-800 dark:text-white p-3 rounded-full shadow-lg transition-all z-20 opacity-0 group-hover:opacity-100">
        <span class="material-symbols-outlined block">arrow_forward_ios</span>
    </button>
    <!-- Dots -->
    <div id="heroDots" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-20"></div>
</div>
</section>
<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" id="banners-section">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="banners-grid">
    <div class="col-span-full flex justify-center py-8">
        <div class="animate-spin rounded-full h-10 w-10 border-4 border-primary border-t-transparent"></div>
    </div>
</div>
</section>
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
<div class="flex items-center justify-between mb-10 border-l-4 border-primary pl-4">
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">Comprar por Departamento</h2>
<a class="text-primary font-semibold flex items-center hover:underline cursor-pointer" onclick="loadCategoriasPanel()">
                    Ver Todo <span class="material-symbols-outlined text-lg ml-1">arrow_forward</span>
</a>
</div>
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6" id="categorias-grid">
    <div class="col-span-full flex justify-center py-8">
        <div class="animate-spin rounded-full h-10 w-10 border-4 border-primary border-t-transparent"></div>
    </div>
</div>
</section>
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-100 dark:border-slate-800">
<div class="flex items-center justify-between mb-10 border-l-4 border-primary pl-4">
<h2 class="text-2xl font-bold text-slate-900 dark:text-white">Productos Destacados</h2>
<a onclick="loadProductos()" class="text-primary font-semibold flex items-center hover:underline cursor-pointer">
                    Ver Todos <span class="material-symbols-outlined text-lg ml-1">arrow_forward</span>
</a>
</div>
<div id="productos-destacados-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
    <div class="col-span-full flex justify-center py-12">
        <div class="animate-spin rounded-full h-10 w-10 border-4 border-primary border-t-transparent"></div>
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
<?php if(!empty($cfg_logo)): ?>
<img src="img/<?php echo $cfg_logo; ?>" alt="<?php echo $cfg_nombre; ?>" class="w-8 h-8 object-contain rounded">
<?php else: ?>
<div class="w-8 h-8 bg-primary rounded flex items-center justify-center">
<span class="material-icons-outlined text-white text-xl">shopping_bag</span>
</div>
<?php endif; ?>
<span class="text-xl font-bold text-primary"><?php echo $cfg_nombre; ?></span>
</div>
<p class="text-slate-500 dark:text-slate-400 text-sm mb-6 leading-relaxed">
                        <?php echo $cfg_slogan ?: 'La mejor calidad, servicio y precios del mercado.'; ?>
                    </p>
<div class="flex gap-4">
<?php if(!empty($cfg_redes['facebook'])): ?>
<a class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="<?php echo htmlspecialchars($cfg_redes['facebook']); ?>" target="_blank">
<span class="material-icons-outlined text-xl">facebook</span>
</a>
<?php endif; ?>
<?php if(!empty($cfg_redes['instagram'])): ?>
<a class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="<?php echo htmlspecialchars($cfg_redes['instagram']); ?>" target="_blank">
<span class="material-symbols-outlined text-xl">camera</span>
</a>
<?php endif; ?>
<?php if(!empty($cfg_redes['twitter'])): ?>
<a class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="<?php echo htmlspecialchars($cfg_redes['twitter']); ?>" target="_blank">
<span class="material-icons-outlined text-xl">public</span>
</a>
<?php endif; ?>
<?php if(!empty($cfg_redes['whatsapp'])): ?>
<a class="w-10 h-10 flex items-center justify-center rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-primary hover:text-white transition-all" href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $cfg_redes['whatsapp']); ?>" target="_blank">
<span class="material-symbols-outlined text-xl">chat</span>
</a>
<?php endif; ?>
</div>
</div>
<?php
// Columnas dinámicas del footer
$footer_cols = $cfg_footer_columns;
if (empty($footer_cols)) {
    $footer_cols = [
        [
            'title' => 'Sobre Nosotros',
            'links' => [
                ['label' => 'Nuestra Historia', 'path' => '/nosotros'],
                ['label' => 'Bolsa de Trabajo', 'path' => '/empleos'],
                ['label' => 'Sostenibilidad', 'path' => '/sustentabilidad'],
            ],
        ],
        [
            'title' => 'Servicio al Cliente',
            'links' => [
                ['label' => 'Centro de Ayuda', 'path' => '/ayuda'],
                ['label' => 'Políticas de Envío', 'path' => '/envios'],
                ['label' => 'Devoluciones', 'path' => '/devoluciones'],
            ],
        ],
        [
            'title' => 'Información Legal',
            'links' => [
                ['label' => 'Términos de Uso', 'path' => '/terminos'],
                ['label' => 'Política de Privacidad', 'path' => '/privacidad'],
            ],
        ],
    ];
}

foreach ($footer_cols as $col) {
    $title = trim($col['title'] ?? '');
    if ($title === '') continue;
    $titleEsc = htmlspecialchars($title);
    $links = is_array($col['links'] ?? null) ? $col['links'] : [];
    echo '<div>';
    echo '<h4 class="font-bold text-slate-900 dark:text-white mb-6 uppercase text-xs tracking-wider">' . $titleEsc . '</h4>';
    echo '<ul class="space-y-4">';
    foreach ($links as $lnk) {
        $lbl = trim($lnk['label'] ?? '');
        $pth = trim($lnk['path'] ?? '');
        if ($lbl === '' || $pth === '') continue;
        $lblEsc = htmlspecialchars($lbl);

        $isInternal = false;
        $onclick = '';

        if ($pth !== '' && $pth[0] === '/') {
            $isInternal = true;
            switch ($pth) {
                case '/categorias':
                    $onclick = 'loadCategoriasPanel()';
                    break;
                case '/ofertas':
                    $onclick = 'loadOfertas()';
                    break;
                case '/productos':
                    $onclick = 'loadProductos()';
                    break;
                case '/contacto':
                case '/contactanos':
                    $onclick = 'loadContactanos()';
                    break;
                case '/lista-deseos':
                case '/lista_deseos':
                    $onclick = 'loadListaDeseos()';
                    break;
                case '/pedidos':
                case '/mis-pedidos':
                    $onclick = 'loadHistorialPedidos()';
                    break;
                case '/carrito':
                    $onclick = 'abrirCarrito()';
                    break;
                case '/inicio':
                case '/home':
                    $onclick = 'if(typeof loadHome===\'function\'){loadHome();}else{location.href=\'index.php\';}';
                    break;
                default:
                    $isInternal = false;
                    break;
            }
        }

        if ($isInternal && $onclick !== '') {
            echo '<li><button onclick="' . $onclick . '" class="text-left bg-none border-none cursor-pointer text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors w-full">' . $lblEsc . '</button></li>';
        } else {
            $href = htmlspecialchars($pth);
            echo '<li><a class="text-slate-500 dark:text-slate-400 text-sm hover:text-primary transition-colors" href="' . $href . '">' . $lblEsc . '</a></li>';
        }
    }
    echo '</ul>';
    echo '</div>';
}
?>
</div>
<div class="pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-400">
<p><?php echo $cfg_pie ?: '© ' . date('Y') . ' ' . $cfg_nombre . '. Todos los derechos reservados.'; ?></p>
<div class="flex gap-8">
<span>Diseñado para la Calidad</span>
<span>Proyecto de clases lenguanjes de programacion</span>
</div>
</div>
</div>
<!-- Overlay y Carrito Modal -->
<div id="cartOverlay" class="cart-overlay hidden fixed inset-0 bg-slate-900/60 modal-blur z-[110]" onclick="cerrarCarrito()"></div>
<aside id="cartSidebar" class="cart-sidebar hidden fixed top-0 right-0 h-full w-full max-w-md bg-white dark:bg-slate-900 z-[120] shadow-2xl flex flex-col animate-in slide-in-from-right duration-300 transition-transform">
<div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary text-2xl">shopping_cart</span>
<h2 class="text-xl font-bold text-slate-900 dark:text-white">Tu Carrito</h2>
<span id="cartSidebarBadge" class="bg-primary/10 text-primary text-xs font-bold px-2 py-0.5 rounded-full">0 ítems</span>
</div>
<button onclick="cerrarCarrito()" class="p-2 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
<span class="material-symbols-outlined block">close</span>
</button>
</div>
<div id="cartItemsContainer" class="flex-1 overflow-y-auto p-6 space-y-4">
<div class="text-center py-12"><span class="material-symbols-outlined text-5xl text-slate-200">shopping_cart</span><p class="text-slate-400 mt-3">Tu carrito está vacío</p></div>
</div>
<div id="cartFooter" class="p-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-800">
<div class="space-y-3 mb-6">
<div class="flex justify-between text-sm text-slate-600 dark:text-slate-400">
<span>Subtotal</span>
<span id="cartSubtotal">0.00</span>
</div>
<div class="flex justify-between text-sm text-slate-600 dark:text-slate-400">
<span id="cartImpuestoLabel">Impuestos</span>
<span id="cartImpuesto">0.00</span>
</div>
<div class="flex justify-between text-lg font-bold text-slate-900 dark:text-white pt-3 border-t border-slate-200 dark:border-slate-700">
<span>Total</span>
<span id="cartTotal">0.00</span>
</div>
</div>
<div class="space-y-3">
<button 
    onclick="loadFinalizarCompra()" 
    id="cartBtnFinalizar"
    class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-xl font-bold transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
    
    <span class="material-symbols-outlined">payments</span>
    Finalizar Compra
</button>
<button onclick="vaciarCarrito()" id="cartBtnVaciar" class="w-full border-2 border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 py-3 rounded-xl font-bold hover:bg-red-50 dark:hover:bg-red-900/20 transition-all flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-lg">delete_sweep</span> Vaciar Carrito
            </button>
</div>
</div>
</aside>

<script>
// --- Configuración del negocio (desde BD) ---
window._cfgMoneda = '<?php echo addslashes($cfg_moneda); ?>';
window._cfgNombre = '<?php echo addslashes($cfg_nombre); ?>';
window._cfgCorreo = '<?php echo addslashes($cfg_correo); ?>';
window._cfgTelefono = '<?php echo addslashes($cfg_telefono); ?>';
window._cfgDireccion = '<?php echo addslashes($cfg_direccion); ?>';
window._cfgSlogan = '<?php echo addslashes($cfg_slogan); ?>';
window._cfgRedes = <?php echo json_encode($cfg_redes, JSON_UNESCAPED_SLASHES); ?>;
// Indicar si el usuario está autenticado (usado por acciones que requieren login)
window._usuarioAutenticado = <?php echo $usuarioAutenticado ? 'true' : 'false'; ?>;

// --- Mapa de iconos FA a Material Symbols ---
const iconoFaToMaterial = {
    'fa-bolt': 'bolt',
    'fa-cheese': 'lunch_dining',
    'fa-couch': 'chair',
    'fa-laptop': 'laptop',
    'fa-tshirt': 'checkroom',
    'fa-baby': 'child_care',
    'fa-pills': 'medication',
    'fa-futbol': 'sports_soccer',
    'fa-home': 'home',
    'fa-utensils': 'restaurant',
    'fa-car': 'directions_car',
    'fa-book': 'menu_book',
    'fa-gift': 'redeem',
    'fa-heart': 'favorite',
    'fa-shopping-bag': 'shopping_bag',
    'fa-tools': 'build',
    'fa-paint-brush': 'brush',
    'fa-music': 'music_note',
    'fa-gamepad': 'sports_esports',
    'fa-paw': 'pets',
    'fa-seedling': 'eco',
    'fa-wine-bottle': 'liquor',
    'fa-broom': 'cleaning_services',
    'fa-dumbbell': 'fitness_center',
};

function cargarCategorias() {
    fetch('api/obtener_categorias.php')
        .then(r => r.json())
        .then(categorias => {
            const grid = document.getElementById('categorias-grid');
            if (!categorias.length) {
                grid.innerHTML = '<p class="col-span-full text-center text-slate-400 py-8">No hay categorías registradas.</p>';
                return;
            }
            let html = '';
            categorias.forEach(cat => {
                const materialIcon = iconoFaToMaterial[cat.icono] || 'category';
                const nombre = cat.nombre.charAt(0).toUpperCase() + cat.nombre.slice(1);
                html += '<a class="group block text-center category-card cursor-pointer" onclick="loadProductosPorCategoria(' + cat.id_categoria + ', \'' + nombre.replace(/'/g, "\\'") + '\')">' +
                    '<div class="aspect-square bg-slate-50 dark:bg-slate-800 rounded-2xl flex items-center justify-center mb-4 overflow-hidden border border-slate-100 dark:border-slate-700 transition-all hover:shadow-md">' +
                        '<span class="material-symbols-outlined text-5xl text-primary transition-transform category-image">' + materialIcon + '</span>' +
                    '</div>' +
                    '<h3 class="font-semibold text-slate-900 dark:text-white group-hover:text-primary uppercase text-xs tracking-wide">' + nombre + '</h3>' +
                '</a>';
            });
            grid.innerHTML = html;
        })
        .catch(err => {
            console.error('Error al cargar categorías:', err);
            document.getElementById('categorias-grid').innerHTML = '<p class="col-span-full text-center text-red-500 py-8">Error al cargar categorías</p>';
        });
}

// --- Panel de Categorías estilo sidebar ---
function loadCategoriasPanel() {
    document.getElementById('mainContent').innerHTML = '<div class="flex justify-center items-center py-20"><div class="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent"></div></div>';
    fetch('api/obtener_categorias.php')
        .then(r => r.json())
        .then(categorias => {
            window._categoriasPanel = categorias;
            let html = `
            <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8 border-l-4 border-primary pl-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary text-3xl">grid_view</span> Todas las Categorías
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">${categorias.length} departamentos</p>
                    </div>
                    <button onclick="loadHome()" class="text-primary hover:underline font-semibold flex items-center gap-1 bg-none border-none cursor-pointer">
                        <span class="material-symbols-outlined text-lg">arrow_back</span> Volver al inicio
                    </button>
                </div>

                <div class="flex border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden bg-white dark:bg-slate-900 shadow-lg min-h-[520px]">
                    <!-- Sidebar Categorías principales -->
                    <div class="w-72 flex-shrink-0 border-r border-slate-200 dark:border-slate-700 overflow-y-auto bg-slate-50 dark:bg-slate-800/50" id="cat-sidebar">
                        ${categorias.map((cat, i) => {
                            const materialIcon = iconoFaToMaterial[cat.icono] || 'category';
                            const nombre = cat.nombre.charAt(0).toUpperCase() + cat.nombre.slice(1);
                            return '<button onclick="seleccionarCategoria(' + i + ', ' + cat.id_categoria + ', this)" ' +
                                'onmouseenter="seleccionarCategoria(' + i + ', ' + cat.id_categoria + ', this)" ' +
                                'class="cat-sidebar-item w-full text-left px-4 py-3 flex items-center gap-3 text-sm border-l-4 border-transparent transition-all duration-200 hover:bg-white dark:hover:bg-slate-800 hover:border-primary ' +
                                (i === 0 ? 'bg-white dark:bg-slate-800 border-primary text-primary font-bold' : 'text-slate-700 dark:text-slate-300') + '">' +
                                '<span class="material-symbols-outlined text-xl ' + (i === 0 ? 'text-primary' : 'text-slate-400') + '">' + materialIcon + '</span> ' +
                                '<span class="flex-1 truncate">' + nombre + '</span>' +
                                '<span class="material-symbols-outlined text-base text-primary opacity-0 cat-arrow">arrow_forward_ios</span>' +
                            '</button>';
                        }).join('')}
                    </div>

                    <!-- Panel derecho: subcategorías -->
                    <div class="flex-1 p-8 overflow-y-auto" id="cat-subcategorias-panel">
                        <div class="flex justify-center items-center h-full">
                            <div class="animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent"></div>
                        </div>
                    </div>
                </div>
            </section>`;
            document.getElementById('mainContent').innerHTML = html;
            window.scrollTo(0, 0);
            // Cargar subcategorías de la primera categoría
            if (categorias.length > 0) {
                seleccionarCategoria(0, categorias[0].id_categoria, document.querySelector('.cat-sidebar-item'));
            }
        })
        .catch(err => {
            console.error('Error al cargar categorías:', err);
            document.getElementById('mainContent').innerHTML = '<div class="text-center py-20 text-red-500"><span class="material-symbols-outlined text-5xl">error</span><p class="mt-2">Error al cargar categorías</p></div>';
        });
}

function seleccionarCategoria(index, idCategoria, btn) {
    // Actualizar estilo del sidebar
    document.querySelectorAll('.cat-sidebar-item').forEach(function(item, i) {
        if (i === index) {
            item.classList.add('bg-white', 'dark:bg-slate-800', 'border-primary', 'text-primary', 'font-bold');
            item.classList.remove('border-transparent', 'text-slate-700', 'dark:text-slate-300');
            item.querySelector('.material-symbols-outlined').classList.add('text-primary');
            item.querySelector('.material-symbols-outlined').classList.remove('text-slate-400');
            item.querySelector('.cat-arrow').classList.remove('opacity-0');
        } else {
            item.classList.remove('bg-white', 'dark:bg-slate-800', 'border-primary', 'text-primary', 'font-bold');
            item.classList.add('border-transparent', 'text-slate-700', 'dark:text-slate-300');
            item.querySelector('.material-symbols-outlined').classList.remove('text-primary');
            item.querySelector('.material-symbols-outlined').classList.add('text-slate-400');
            item.querySelector('.cat-arrow').classList.add('opacity-0');
        }
    });

    const cat = window._categoriasPanel[index];
    const nombre = cat.nombre.charAt(0).toUpperCase() + cat.nombre.slice(1);
    const panel = document.getElementById('cat-subcategorias-panel');
    panel.innerHTML = '<div class="flex justify-center items-center h-32"><div class="animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent"></div></div>';

    fetch('api/obtener_subcategorias.php?id=' + idCategoria)
        .then(r => r.json())
        .then(subcats => {
            let html = '<div class="mb-6 flex items-center justify-between">' +
                '<h3 class="text-xl font-bold text-slate-900 dark:text-white">' + nombre + '</h3>' +
                '<a onclick="loadProductosPorCategoria(' + idCategoria + ', \'' + nombre.replace(/'/g, "\\'") + '\')" class="text-primary text-sm font-semibold hover:underline cursor-pointer flex items-center gap-1">Ver todo <span class="material-symbols-outlined text-base">arrow_forward</span></a>' +
            '</div>';

            if (subcats.length === 0) {
                html += '<div class="flex flex-col items-center justify-center py-12 text-slate-400">' +
                    '<span class="material-symbols-outlined text-5xl mb-3">folder_open</span>' +
                    '<p class="text-sm">No hay subcategorías.</p>' +
                    '<button onclick="loadProductosPorCategoria(' + idCategoria + ', \'' + nombre.replace(/'/g, "\\'") + '\')" class="mt-4 bg-primary text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-primary-dark transition-colors flex items-center gap-2">' +
                        '<span class="material-symbols-outlined text-lg">visibility</span> Ver productos de ' + nombre +
                    '</button>' +
                '</div>';
            } else {
                html += '<div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-3">';
                subcats.forEach(sub => {
                    const subNombre = sub.nombre.charAt(0).toUpperCase() + sub.nombre.slice(1);
                    const totalProd = sub.total_productos ? ' (' + sub.total_productos + ')' : '';
                    html += '<a onclick="loadProductosPorCategoria(' + sub.id_categoria + ', \'' + subNombre.replace(/'/g, "\\'") + '\')" ' +
                        'class="text-sm text-slate-700 dark:text-slate-300 hover:text-primary cursor-pointer py-1.5 font-medium transition-colors flex items-center gap-1 group">' +
                        '<span class="material-symbols-outlined text-xs text-slate-300 group-hover:text-primary transition-colors">chevron_right</span>' +
                        subNombre + '<span class="text-slate-400 text-xs">' + totalProd + '</span>' +
                    '</a>';
                });
                html += '</div>';
            }

            panel.innerHTML = html;
        })
        .catch(err => {
            console.error('Error subcategorías:', err);
            panel.innerHTML = '<p class="text-red-500 text-sm">Error al cargar subcategorías</p>';
        });
}

function loadProductosPorCategoria(idCategoria, nombreCategoria) {
    document.getElementById('mainContent').innerHTML = '<div class="flex justify-center items-center py-20"><div class="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent"></div></div>';
    Promise.all([
        fetch('api/obtener_productos.php').then(r => r.json()),
        fetch('api/obtener_categorias_hijas.php?id=' + idCategoria).then(r => r.json())
    ]).then(function([productos, subcatIds]) {
            const idsValidos = [idCategoria, ...subcatIds];
            const filtrados = productos.filter(p => idsValidos.includes(parseInt(p.id_categoria)));
            window._productosData = filtrados;

            let html = '<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">' +
                '<div class="flex items-center justify-between mb-8 border-l-4 border-primary pl-4">' +
                    '<div><h2 class="text-2xl font-bold text-slate-900 dark:text-white">' + nombreCategoria + '</h2>' +
                    '<p class="text-sm text-slate-500 dark:text-slate-400 mt-1">' + filtrados.length + ' productos</p></div>' +
                    '<div class="flex items-center gap-3">' +
                        '<button onclick="loadCategoriasPanel()" class="text-slate-500 hover:text-primary font-semibold flex items-center gap-1 bg-none border-none cursor-pointer text-sm">' +
                            '<span class="material-symbols-outlined text-lg">grid_view</span> Categorías</button>' +
                        '<button onclick="loadHome()" class="text-primary hover:underline font-semibold flex items-center gap-1 bg-none border-none cursor-pointer text-sm">' +
                            '<span class="material-symbols-outlined text-lg">arrow_back</span> Inicio</button>' +
                    '</div>' +
                '</div>';

            // Modal de vista previa
            html += '<div id="modalVistaPrevia" class="fixed inset-0 z-[9999] hidden">' +
                '<div class="absolute inset-0 bg-black/60 modal-blur" onclick="cerrarVistaPrevia()"></div>' +
                '<div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">' +
                    '<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto pointer-events-auto relative">' +
                        '<button onclick="cerrarVistaPrevia()" class="absolute top-4 right-4 z-10 w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-600 hover:text-red-500 hover:bg-red-50 transition-colors"><span class="material-symbols-outlined">close</span></button>' +
                        '<div class="flex flex-col md:flex-row">' +
                            '<div class="md:w-1/2 p-6"><div class="relative aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 mb-4"><img id="prevImgPrincipal" src="" alt="" class="w-full h-full object-cover"/>' +
                                '<button id="prevBtnLeft" onclick="cambiarImgPrevia(-1)" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md"><span class="material-symbols-outlined text-sm">arrow_back_ios_new</span></button>' +
                                '<button id="prevBtnRight" onclick="cambiarImgPrevia(1)" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md"><span class="material-symbols-outlined text-sm">arrow_forward_ios</span></button>' +
                            '</div><div id="prevMiniaturas" class="flex gap-2 overflow-x-auto pb-2"></div></div>' +
                            '<div class="md:w-1/2 p-6 md:pl-2 flex flex-col justify-center">' +
                                '<span id="prevCategoria" class="text-xs text-primary font-semibold uppercase tracking-wider mb-2"></span>' +
                                '<h2 id="prevNombre" class="text-2xl font-bold text-slate-900 dark:text-white mb-3"></h2>' +
                                '<p id="prevMarca" class="text-sm text-slate-400 mb-3"></p>' +
                                '<p id="prevDescripcion" class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed"></p>' +
                                '<div class="flex items-center gap-3 mb-4"><span id="prevPrecio" class="text-3xl font-bold text-slate-900 dark:text-white"></span><span id="prevPrecioOriginal" class="text-lg text-slate-400 line-through hidden"></span><span id="prevBadgeOferta" class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded hidden">OFERTA</span></div>' +
                                '<div class="flex items-center gap-2 mb-6"><span id="prevStockIcon" class="material-symbols-outlined text-lg"></span><span id="prevStock" class="text-sm font-medium"></span></div>' +
                                '<div class="flex items-center gap-3 mb-4">' +
                                    '<span class="text-sm font-medium text-slate-700 dark:text-slate-300">Cantidad:</span>' +
                                    '<div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">' +
                                        '<button onclick="prevCantidad(-1)" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-lg font-bold">−</button>' +
                                        '<input id="prevCantidadInput" type="number" value="1" min="1" class="w-12 h-9 text-center border-x border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-bold text-slate-900 dark:text-white focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" onchange="validarPrevCantidad()"/>' +
                                        '<button onclick="prevCantidad(1)" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-lg font-bold">+</button>' +
                                    '</div>' +
                                '</div>' +
                                '<button onclick="agregarAlCarritoDesdePreview()" class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-lg flex items-center justify-center gap-2 font-bold transition-colors"><span class="material-symbols-outlined">shopping_cart</span> Agregar al Carrito</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

            html += '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">';

            if (filtrados.length === 0) {
                html += '<div class="col-span-full text-center py-16"><span class="material-symbols-outlined text-6xl text-slate-300">inventory_2</span><p class="text-slate-500 mt-4 text-lg">No hay productos en esta categoría.</p></div>';
            } else {
                filtrados.forEach(function(prod, index) {
                    const imgSrc = prod.imagen_principal || 'https://via.placeholder.com/300x300?text=Sin+Imagen';
                    const precioOriginal = parseFloat(prod.precio).toFixed(2);
                    const enOferta = prod.en_oferta == 1 && prod.precio_descuento;
                    const precioFinal = enOferta ? parseFloat(prod.precio_descuento).toFixed(2) : precioOriginal;

                    html += '<div class="product-card group bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-all duration-300">' +
                        '<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-700">' +
                            '<img alt="' + prod.nombre + '" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="' + imgSrc + '" onerror="this.src=\'https://via.placeholder.com/300x300?text=Sin+Imagen\'"/>' +
                            '<div class="product-actions absolute inset-0 bg-black/5 flex items-center justify-center gap-3 opacity-0 translate-y-4 transition-all duration-300">' +
                                '<button onclick="toggleWishlist(this,' + prod.id_producto + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Lista de deseos"><span class="material-symbols-outlined">favorite</span></button>' +
                                '<button onclick="abrirVistaPrevia(' + index + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Vista previa"><span class="material-symbols-outlined">visibility</span></button>' +
                            '</div>' +
                            (enOferta ? '<div class="absolute top-3 left-3"><span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase">Oferta</span></div>' : '') +
                        '</div>' +
                        '<div class="p-5">' +
                            '<h3 class="font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors truncate">' + prod.nombre + '</h3>' +
                            '<p class="text-slate-500 dark:text-slate-400 text-sm mb-4 line-clamp-2">' + (prod.descripcion || '') + '</p>' +
                            '<div class="flex items-center gap-2 mb-3">' +
                                '<span class="text-xs text-slate-500 dark:text-slate-400">Cant:</span>' +
                                '<div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">' +
                                    '<button onclick="cardCantidad(this,-1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-bold">−</button>' +
                                    '<input type="number" value="1" min="1" class="card-qty w-10 h-7 text-center border-x border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"/>' +
                                    '<button onclick="cardCantidad(this,1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-bold">+</button>' +
                                '</div>' +
                            '</div>' +
                            '<div class="flex items-center justify-between gap-4">' +
                                '<div class="flex flex-col">' +
                                    '<span class="text-xl font-bold text-slate-900 dark:text-white">' + window._cfgMoneda + ' ' + precioFinal + '</span>' +
                                    (enOferta ? '<span class="text-xs text-slate-400 line-through">' + window._cfgMoneda + ' ' + precioOriginal + '</span>' : '') +
                                '</div>' +
                                '<button onclick="agregarAlCarritoDesdeCard(this,' + prod.id_producto + ')" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold transition-colors">' +
                                    '<span class="material-symbols-outlined text-lg">shopping_cart</span> Agregar</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                });
            }

            html += '</div></section>';
            document.getElementById('mainContent').innerHTML = html;
            window.scrollTo(0, 0);
        })
        .catch(err => {
            console.error('Error:', err);
            document.getElementById('mainContent').innerHTML = '<div class="text-center py-20 text-red-500"><span class="material-symbols-outlined text-5xl">error</span><p class="mt-2">Error al cargar productos</p></div>';
        });
}

// Cargar categorías al iniciar
cargarCategorias();

// Cargar productos destacados al iniciar
cargarProductosDestacados();

// Cargar hero carrusel al iniciar
cargarHeroCarousel();

// Cargar tarjetas de banners
cargarBannerCards();

function cargarProductosDestacados() {
    fetch('api/obtener_productos.php')
        .then(r => r.json())
        .then(data => {
            var grid = document.getElementById('productos-destacados-grid');
            if (!grid) return;
            if (!data || data.length === 0) {
                grid.innerHTML = '<div class="col-span-full text-center py-12"><span class="material-symbols-outlined text-6xl text-slate-300">inventory_2</span><p class="text-slate-500 mt-4">No hay productos registrados aún.</p></div>';
                return;
            }
            // Guardar para vista previa
            window._todosProductos = data;
            // Los productos destacados deben ser la fuente de la vista previa
            // para que abrirVistaPrevia(index) use el índice correcto
            // Tomar los últimos 8 productos (más recientes)
            var destacados = data.slice(0, 8);
            // Establecer el array que usa la vista previa
            window._productosData = destacados;
            var mon = window._cfgMoneda || 'L';
            var html = '';
            destacados.forEach(function(prod, idx) {
                var imgSrc = prod.imagen_principal || 'https://via.placeholder.com/300x300?text=Sin+Imagen';
                var precioOriginal = parseFloat(prod.precio).toFixed(2);
                var enOferta = prod.en_oferta == 1 && prod.precio_descuento && parseFloat(prod.precio_descuento) > 0;
                var precioFinal = enOferta ? parseFloat(prod.precio_descuento).toFixed(2) : precioOriginal;

                html += '<div class="product-card group bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-all duration-300">' +
                    '<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-700">' +
                        '<img alt="' + prod.nombre + '" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="' + imgSrc + '" onerror="this.src=\'https://via.placeholder.com/300x300?text=Sin+Imagen\'"/>' +
                        '<div class="product-actions absolute inset-0 bg-black/5 flex items-center justify-center gap-3 opacity-0 translate-y-4 transition-all duration-300">' +
                            '<button onclick="toggleWishlist(this,' + prod.id_producto + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Lista de deseos"><span class="material-symbols-outlined">favorite</span></button>' +
                            '<button onclick="abrirVistaPrevia(' + idx + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Vista previa"><span class="material-symbols-outlined">visibility</span></button>' +
                        '</div>' +
                        (enOferta ? '<div class="absolute top-3 left-3"><span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase">Oferta</span></div>' : '') +
                    '</div>' +
                    '<div class="p-5">' +
                        '<h3 class="font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors truncate">' + prod.nombre + '</h3>' +
                        '<p class="text-slate-500 dark:text-slate-400 text-sm mb-4 line-clamp-2">' + (prod.descripcion || '') + '</p>' +
                        '<div class="flex items-center gap-2 mb-3">' +
                            '<span class="text-xs text-slate-500 dark:text-slate-400">Cant:</span>' +
                            '<div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">' +
                                '<button onclick="cardCantidad(this,-1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-bold">\u2212</button>' +
                                '<input type="number" value="1" min="1" class="card-qty w-10 h-7 text-center border-x border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"/>' +
                                '<button onclick="cardCantidad(this,1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-bold">+</button>' +
                            '</div>' +
                        '</div>' +
                        '<div class="flex items-center justify-between gap-4">' +
                            '<div class="flex flex-col">' +
                                '<span class="text-xl font-bold text-slate-900 dark:text-white">' + mon + ' ' + precioFinal + '</span>' +
                                (enOferta ? '<span class="text-xs text-slate-400 line-through">' + mon + ' ' + precioOriginal + '</span>' : '') +
                            '</div>' +
                            '<button onclick="agregarAlCarritoDesdeCard(this,' + prod.id_producto + ')" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold transition-colors">' +
                                '<span class="material-symbols-outlined text-lg">shopping_cart</span> Agregar</button>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            grid.innerHTML = html;
        })
        .catch(function(err) {
            var grid = document.getElementById('productos-destacados-grid');
            if (grid) grid.innerHTML = '<div class="col-span-full text-center py-12 text-red-500"><span class="material-symbols-outlined text-5xl">error</span><p class="mt-2">Error al cargar productos</p></div>';
        });
}

/* ========== HERO CARRUSEL ========== */
var _heroSlides = [];
var _heroActual = 0;
var _heroTimer = null;
var _heroTotal = 1;

function cargarHeroCarousel() {
    fetch('api/obtener_hero_slides.php')
        .then(r => r.json())
        .then(resp => {
            var data = resp.data || resp;
            if (!data || !Array.isArray(data) || data.length === 0) {
                heroRenderDots();
                return;
            }
            _heroSlides = data;
            var container = document.getElementById('heroSlides');
            if (!container) return;

            data.forEach(function(b) {
                var imgSrc = b.imagen ? 'img/slides/' + b.imagen : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=800&q=80';
                var slide = document.createElement('div');
                slide.className = 'hero-slide absolute inset-0 opacity-0 transition-opacity duration-700 ease-in-out pointer-events-none';
                slide.innerHTML =
                    '<div class="absolute inset-0">' +
                        '<img alt="' + (b.titulo || 'Banner') + '" class="w-full h-full object-cover" src="' + imgSrc + '"/>' +
                        '<div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>' +
                    '</div>' +
                    '<div class="relative z-10 h-full flex items-center">' +
                        '<div class="px-8 lg:px-20 max-w-2xl">' +
                            '<h2 class="text-3xl lg:text-5xl font-extrabold text-white leading-tight mb-4">' + (b.titulo || '') + '</h2>' +
                            (b.subtitulo ? '<p class="text-lg text-white/80 mb-8">' + b.subtitulo + '</p>' : '') +
                            (b.texto_boton ? '<a ' + (b.enlace ? 'href="' + b.enlace + '"' : 'href="#"') + ' class="inline-block px-8 py-3 bg-primary hover:bg-primary-dark text-white font-bold rounded-lg shadow-lg transition-all">' + b.texto_boton + '</a>' : '') +
                        '</div>' +
                    '</div>';
                container.appendChild(slide);
            });

            _heroTotal = 1 + data.length;
            _heroActual = 0;
            heroRenderDots();
            heroAutoPlay();
        })
        .catch(function(err) {
            console.error('Error hero carousel:', err);
            heroRenderDots();
        });
}

function heroGoTo(index) {
    var slides = document.querySelectorAll('#heroSlides .hero-slide');
    if (slides.length === 0) return;
    if (index < 0) index = slides.length - 1;
    if (index >= slides.length) index = 0;
    slides.forEach(function(s, i) {
        if (i === index) {
            s.classList.remove('opacity-0', 'pointer-events-none');
            s.classList.add('opacity-100', 'pointer-events-auto');
        } else {
            s.classList.add('opacity-0', 'pointer-events-none');
            s.classList.remove('opacity-100', 'pointer-events-auto');
        }
    });
    _heroActual = index;
    var dots = document.querySelectorAll('#heroDots .hero-dot');
    dots.forEach(function(d, i) {
        if (i === index) {
            d.classList.remove('bg-white/50');
            d.classList.add('bg-primary', 'scale-110');
        } else {
            d.classList.add('bg-white/50');
            d.classList.remove('bg-primary', 'scale-110');
        }
    });
}

function heroNav(dir) {
    heroGoTo(_heroActual + dir);
    heroResetTimer();
}

function heroRenderDots() {
    var container = document.getElementById('heroDots');
    if (!container) return;
    var slides = document.querySelectorAll('#heroSlides .hero-slide');
    if (slides.length <= 1) { container.innerHTML = ''; return; }
    var html = '';
    for (var i = 0; i < slides.length; i++) {
        html += '<button onclick="heroGoTo(' + i + ');heroResetTimer()" class="hero-dot w-3 h-3 rounded-full transition-all duration-300 ' + (i === 0 ? 'bg-primary scale-110' : 'bg-white/50') + ' hover:bg-white cursor-pointer"></button>';
    }
    container.innerHTML = html;
}

function heroAutoPlay() {
    _heroTimer = setInterval(function() {
        heroGoTo(_heroActual + 1);
    }, 5000);
}

function heroResetTimer() {
    if (_heroTimer) clearInterval(_heroTimer);
    heroAutoPlay();
}

function cargarBannerCards() {
    fetch('api/obtener_banners.php')
        .then(r => r.json())
        .then(resp => {
            var section = document.getElementById('banners-section');
            var grid = document.getElementById('banners-grid');
            if (!grid || !section) return;
            var data = resp.data || resp;
            if (!data || !Array.isArray(data) || data.length === 0) {
                section.style.display = 'none';
                return;
            }
            var cols = data.length === 1 ? 'grid-cols-1' : 'grid-cols-1 md:grid-cols-2';
            grid.className = 'grid ' + cols + ' gap-6';
            var html = '';
            data.forEach(function(b) {
                var imgSrc = b.imagen ? 'img/banners/' + b.imagen : 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=600&q=80';
                var onclickAttr = '';
                if (b.enlace) {
                    if (b.enlace.startsWith('#')) {
                        onclickAttr = ' onclick="procesarHash(\'' + b.enlace.replace(/'/g, "\\'") + '\')"';
                    } else {
                        onclickAttr = ' onclick="window.location.href=\'' + b.enlace.replace(/'/g, "\\'") + '\'"';
                    }
                }
                html += '<div class="relative h-64 lg:h-80 rounded-2xl overflow-hidden group cursor-pointer"' + onclickAttr + '>' +
                    '<img alt="' + (b.titulo || 'Banner') + '" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="' + imgSrc + '"/>' +
                    '<div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center p-8">' +
                        '<div class="text-white">' +
                            '<h3 class="text-2xl font-bold mb-2">' + (b.titulo || '') + '</h3>' +
                            '<p class="text-white/80 mb-4">' + (b.descripcion || '') + '</p>' +
                            (b.texto_boton ? '<span class="inline-block px-6 py-2 bg-white text-slate-900 font-bold rounded-lg group-hover:bg-primary group-hover:text-white transition-colors">' + b.texto_boton + '</span>' : '') +
                        '</div>' +
                    '</div>' +
                '</div>';
            });
            grid.innerHTML = html;
        })
        .catch(function(err) {
            console.error('Error banner cards:', err);
            var section = document.getElementById('banners-section');
            if (section) section.style.display = 'none';
        });
}

function loadContacto() {
    loadContactanos();
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

function initCheckout() {
    console.log("TEST INIT CHECKOUT FUNCIONA");
    cargarDireccionesCheckout();

}
window.loadFinalizarCompra = function () {

    console.log("Se ejecutó loadFinalizarCompra");

    cerrarCarrito();

    fetch('client/finalizarcompra.php')
        .then(response => response.text())
        .then(data => {

            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;

            const bodyContent = tempDiv.querySelector('body')?.innerHTML || data;

            document.getElementById('mainContent').innerHTML = bodyContent;

            //  EJECUTAR SCRIPTS
            const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
            let scriptMatch;

            while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                const script = document.createElement('script');
                script.textContent = scriptMatch[1];
                document.body.appendChild(script);
            }

            // Ahora sí existe cargarDireccionesCheckout
            if (typeof initCheckout === "function") {
                initCheckout();
            }

            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar finalizarcompra:', error));
};
document.addEventListener("click", function (e) {

    const btnFinalizar = e.target.closest("button[data-action='finalizar-compra']");

    if (btnFinalizar) {
        console.log("CLICK FINALIZAR DETECTADO");
        window.loadFinalizarCompra();
    }

});
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
            // Insertar el HTML
            document.getElementById('mainContent').innerHTML = data;

            // Extraer y ejecutar scripts inline del HTML (para inicializar fetchWishlist())
            const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
            let scriptMatch;
            while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                try {
                    const script = document.createElement('script');
                    script.textContent = scriptMatch[1];
                    document.body.appendChild(script);
                } catch (e) { console.error('Error ejecutando script de listadedeseo:', e); }
            }

            // Garantizar que fetchWishlist() se ejecute aunque por alguna razón los scripts inline no se registren inmediatamente
            setTimeout(() => {
                try { if (typeof fetchWishlist === 'function') fetchWishlist(); }
                catch(e) { console.error('Error llamando fetchWishlist tras inyectar listadedeseo:', e); }
            }, 80);

            // Scroll hacia arriba para ver el contenido
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar listadedeseo:', error));
}

function loadPerfil() {
    fetch('client/perfil.php')
        .then(response => response.text())
        .then(data => {

            // Si devuelve JSON de error (no autenticado)
            if (data.includes('"exito"')) {
                window.location.href = 'index.php';
                return;
            }

            // Insertar directamente el HTML
            document.getElementById('mainContent').innerHTML = data;

            // Extraer y ejecutar scripts internos
            const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
            let scriptMatch;
            while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                const script = document.createElement('script');
                script.textContent = scriptMatch[1];
                document.body.appendChild(script);
            }

            // 🔥 IMPORTANTE EN SPA:
            // Esperar un micro-delay y luego inicializar la vista
            setTimeout(() => {
                if (typeof iniciarPerfil === 'function') {
                    iniciarPerfil();
                }
            }, 50);

            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error al cargar perfil:', error));
}
function loadContactanos() {
    var redes = window._cfgRedes || {};
    var html = '<main class="max-w-7xl mx-auto px-4 py-12">' +
        '<div class="text-center mb-16">' +
            '<h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-4">Estamos para ayudarte</h1>' +
            '<p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto text-lg leading-relaxed">' +
                '¿Tienes alguna duda sobre nuestros productos o necesitas asistencia? Completa el formulario y nuestro equipo se pondrá en contacto contigo en breve.' +
            '</p>' +
        '</div>' +
        '<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">' +
            '<div class="lg:col-span-2">' +
                '<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-8">' +
                    '<h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6 flex items-center gap-2">' +
                        '<span class="material-symbols-outlined text-primary">email</span> Envíanos un mensaje' +
                    '</h2>' +
                    '<form id="formContacto" class="space-y-6">' +
                        '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">' +
                            '<div class="space-y-2">' +
                                '<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="nombre">Nombre completo</label>' +
                                '<input required class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all" id="nombre" name="nombre" type="text" placeholder="Ej. Juan Pérez">' +
                            '</div>' +
                            '<div class="space-y-2">' +
                                '<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="correo">Correo electrónico</label>' +
                                '<input required class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all" id="correo" name="correo" type="email" placeholder="juan@ejemplo.com">' +
                            '</div>' +
                        '</div>' +
                        '<div class="space-y-2">' +
                            '<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="telefono">Teléfono</label>' +
                            '<input class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all" id="telefono" name="telefono" type="text" placeholder="Ej. 9999-9999">' +
                        '</div>' +
                        '<div class="space-y-2">' +
                            '<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="asunto">Asunto</label>' +
                            '<input required class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all" id="asunto" name="asunto" type="text" placeholder="¿En qué podemos ayudarte?">' +
                        '</div>' +
                        '<div class="space-y-2">' +
                            '<label class="text-sm font-semibold text-slate-700 dark:text-slate-300" for="mensaje">Mensaje</label>' +
                            '<textarea required class="w-full px-4 py-3 rounded border-slate-200 dark:border-slate-700 dark:bg-slate-800 focus:ring-2 focus:ring-primary focus:border-primary transition-all resize-none" id="mensaje" name="mensaje" rows="5" placeholder="Escribe aquí los detalles de tu consulta..."></textarea>' +
                        '</div>' +
                        '<div class="flex items-center gap-3">' +
                            '<input required class="rounded text-primary focus:ring-primary border-slate-300 dark:border-slate-700 dark:bg-slate-800" id="privacidad" type="checkbox">' +
                            '<label class="text-sm text-slate-600 dark:text-slate-400" for="privacidad">He leído y acepto la política de privacidad.</label>' +
                        '</div>' +
                        '<button class="w-full md:w-auto bg-primary hover:bg-primary/90 text-white font-bold py-4 px-8 rounded-lg shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2" type="submit">' +
                            '<span>Enviar Mensaje</span><span class="material-symbols-outlined text-sm">send</span>' +
                        '</button>' +
                    '</form>' +
                '</div>' +
            '</div>' +
            // Panel derecho: info de contacto
            '<div class="space-y-6">' +
                // Dirección
                '<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:border-primary transition-colors">' +
                    '<div class="flex items-start gap-4">' +
                        '<div class="bg-primary/10 dark:bg-primary/20 p-3 rounded-lg text-primary"><span class="material-symbols-outlined">location_on</span></div>' +
                        '<div>' +
                            '<h3 class="font-bold text-slate-900 dark:text-white mb-1">Nuestra Dirección</h3>' +
                            '<p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">' + (window._cfgDireccion || 'No especificada') + '</p>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                // Teléfono
                '<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:border-primary transition-colors">' +
                    '<div class="flex items-start gap-4">' +
                        '<div class="bg-primary/10 dark:bg-primary/20 p-3 rounded-lg text-primary"><span class="material-symbols-outlined">phone</span></div>' +
                        '<div>' +
                            '<h3 class="font-bold text-slate-900 dark:text-white mb-1">Teléfono de Atención</h3>' +
                            '<p class="text-sm text-slate-600 dark:text-slate-400">' +
                                '<a href="tel:' + (window._cfgTelefono || '') + '" class="hover:text-primary font-semibold">' + (window._cfgTelefono || 'No especificado') + '</a>' +
                            '</p>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                // Correo
                '<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm group hover:border-primary transition-colors">' +
                    '<div class="flex items-start gap-4">' +
                        '<div class="bg-primary/10 dark:bg-primary/20 p-3 rounded-lg text-primary"><span class="material-symbols-outlined">alternate_email</span></div>' +
                        '<div>' +
                            '<h3 class="font-bold text-slate-900 dark:text-white mb-1">Correo Electrónico</h3>' +
                            '<p class="text-sm text-slate-600 dark:text-slate-400">' +
                                '<a href="mailto:' + (window._cfgCorreo || '') + '" class="hover:text-primary">' + (window._cfgCorreo || 'No especificado') + '</a>' +
                            '</p>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                // Redes Sociales
                '<div class="bg-primary p-6 rounded-xl shadow-lg shadow-primary/20">' +
                    '<h3 class="font-bold text-white mb-4">Síguenos en redes</h3>' +
                    '<div class="flex gap-4 flex-wrap">';

    if (redes.facebook) {
        html += '<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="' + redes.facebook + '" target="_blank" title="Facebook">' +
            '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>';
    }
    if (redes.instagram) {
        html += '<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="' + redes.instagram + '" target="_blank" title="Instagram">' +
            '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12s.014 3.668.072 4.948c.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24s3.668-.014 4.948-.072c4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg></a>';
    }
    if (redes.twitter) {
        html += '<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="' + redes.twitter + '" target="_blank" title="X / Twitter">' +
            '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg></a>';
    }
    if (redes.whatsapp) {
        var waNum = (redes.whatsapp || '').replace(/[^0-9]/g, '');
        html += '<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="https://wa.me/' + waNum + '" target="_blank" title="WhatsApp">' +
            '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>';
    }
    if (redes.tiktok) {
        html += '<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="' + redes.tiktok + '" target="_blank" title="TikTok">' +
            '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg></a>';
    }
    if (redes.youtube) {
        html += '<a class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors text-white" href="' + redes.youtube + '" target="_blank" title="YouTube">' +
            '<svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>';
    }

    html += '</div></div>' +
            '</div>' +
        '</div>' +
    '</main>';

    document.getElementById('mainContent').innerHTML = html;
    window.scrollTo(0, 0);

    // Inicializar el formulario de contacto
    setTimeout(function() {
        var formContacto = document.getElementById('formContacto');
        if (formContacto) {
            formContacto.addEventListener('submit', function(e) {
                e.preventDefault();
                var nombre = document.getElementById('nombre').value.trim();
                var correo = document.getElementById('correo').value.trim();
                var telefono = document.getElementById('telefono').value.trim();
                var asunto = document.getElementById('asunto').value.trim();
                var mensaje = document.getElementById('mensaje').value.trim();
                var privacidad = document.getElementById('privacidad').checked;

                if (!nombre || !correo || !asunto || !mensaje || !privacidad) {
                    if (typeof CustomModal !== 'undefined') {
                        CustomModal.show('warning', 'Campos incompletos', 'Por favor completa todos los campos requeridos');
                    } else {
                        alert('Por favor completa todos los campos requeridos');
                    }
                    return;
                }

                var formData = new FormData();
                formData.append('nombre', nombre);
                formData.append('correo', correo);
                formData.append('telefono', telefono);
                formData.append('asunto', asunto);
                formData.append('mensaje', mensaje);

                fetch('pages/guardar_mensaje.php', { method: 'POST', body: formData })
                .then(function(r) { return r.text(); })
                .then(function(resp) {
                    if (resp.trim() === 'ok') {
                        if (typeof CustomModal !== 'undefined') {
                            CustomModal.show('success', 'Éxito', 'Mensaje enviado correctamente. ¡Nos pondremos en contacto pronto!');
                        } else {
                            alert('Mensaje enviado correctamente');
                        }
                        formContacto.reset();
                    } else {
                        if (typeof CustomModal !== 'undefined') {
                            CustomModal.show('error', 'Error', 'Error al guardar el mensaje: ' + resp);
                        } else {
                            alert('Error al guardar el mensaje');
                        }
                    }
                })
                .catch(function(err) {
                    if (typeof CustomModal !== 'undefined') {
                        CustomModal.show('error', 'Error', 'Error de conexión con el servidor');
                    } else {
                        alert('Error de conexión con el servidor');
                    }
                });
            });
        }
    }, 100);
}

function loadOfertas() {
    document.getElementById('mainContent').innerHTML = '<div class="flex justify-center items-center py-20"><div class="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent"></div></div>';
    fetch('api/obtener_productos.php')
        .then(r => r.json())
        .then(data => {
            // Solo productos en oferta
            window._ofTodosProductos = data.filter(p => p.en_oferta == 1 && p.precio_descuento);
            window._ofProductosFiltrados = [];

            let html = '<style>.promotion-gradient{background:linear-gradient(135deg,#1e293b 0%,#0f172a 100%)}</style>' +
            '<main class="max-w-7xl mx-auto px-4 py-8">' +

            '<!-- Banner -->' +
            '<section class="promotion-gradient rounded-2xl overflow-hidden relative mb-12 shadow-2xl border border-slate-700">' +
                '<div class="absolute inset-0 opacity-20 pointer-events-none"><div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary via-transparent to-transparent"></div></div>' +
                '<div class="relative z-10 p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">' +
                    '<div class="text-center md:text-left">' +
                        '<span class="bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block">Flash Sale</span>' +
                        '<h1 class="text-4xl md:text-6xl font-black text-white mb-4 leading-tight">Venta Flash <br/><span class="text-primary">Hasta -70%</span></h1>' +
                        '<p class="text-slate-300 text-lg mb-6 max-w-md">Solo por tiempo limitado. Descuentos exclusivos en todos los departamentos. ¡No dejes que se escapen!</p>' +
                    '</div>' +
                    '<div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 md:p-8 rounded-2xl text-white text-center">' +
                        '<p class="text-xs uppercase font-semibold tracking-tighter mb-4 opacity-80">La oferta termina en:</p>' +
                        '<div class="flex gap-4 items-start">' +
                            '<div><div class="text-3xl md:text-4xl font-black tabular-nums" id="of-hours">00</div><div class="text-[10px] uppercase opacity-70">Horas</div></div>' +
                            '<div class="text-3xl md:text-4xl font-black opacity-50">:</div>' +
                            '<div><div class="text-3xl md:text-4xl font-black tabular-nums" id="of-mins">00</div><div class="text-[10px] uppercase opacity-70">Minutos</div></div>' +
                            '<div class="text-3xl md:text-4xl font-black opacity-50">:</div>' +
                            '<div><div class="text-3xl md:text-4xl font-black tabular-nums" id="of-secs">00</div><div class="text-[10px] uppercase opacity-70">Segundos</div></div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</section>' +

            '<div class="flex flex-col lg:flex-row gap-8">' +

            '<!-- Sidebar filtros -->' +
            '<aside class="w-full lg:w-64 space-y-8"><div>' +
                '<h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2"><span class="material-symbols-outlined text-primary text-xl">filter_list</span> Filtros</h3>' +
                '<div class="space-y-4 mb-8"><p class="text-sm font-bold text-slate-700 dark:text-slate-300">Categoría</p><div class="space-y-2" id="of-filtro-categorias"><p class="text-xs text-slate-400">Cargando...</p></div></div>' +

                '<div class="space-y-4 mb-8"><p class="text-sm font-bold text-slate-700 dark:text-slate-300">Rango de Precio</p>' +
                    '<div class="flex items-center gap-2">' +
                        '<input id="of-precio-min" type="number" min="0" placeholder="Min" class="w-full text-sm border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1.5 focus:ring-primary focus:border-primary dark:bg-slate-800"/>' +
                        '<span class="text-slate-400">-</span>' +
                        '<input id="of-precio-max" type="number" min="0" placeholder="Max" class="w-full text-sm border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1.5 focus:ring-primary focus:border-primary dark:bg-slate-800"/>' +
                    '</div>' +
                    '<button onclick="ofAplicarFiltros()" class="w-full text-xs bg-primary text-white py-1.5 rounded-lg font-medium hover:bg-primary/90 transition-colors mt-1">Aplicar precio</button>' +
                '</div>' +

                '<div class="space-y-4"><p class="text-sm font-bold text-slate-700 dark:text-slate-300">Descuento</p><div class="space-y-2">' +
                    '<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary"><input class="text-primary focus:ring-primary border-slate-300" name="of-discount" type="radio" value="0" checked onchange="ofAplicarFiltros()"/> Todos</label>' +
                    '<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary"><input class="text-primary focus:ring-primary border-slate-300" name="of-discount" type="radio" value="10" onchange="ofAplicarFiltros()"/> -10% o más</label>' +
                    '<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary"><input class="text-primary focus:ring-primary border-slate-300" name="of-discount" type="radio" value="30" onchange="ofAplicarFiltros()"/> -30% o más</label>' +
                    '<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary"><input class="text-primary focus:ring-primary border-slate-300" name="of-discount" type="radio" value="50" onchange="ofAplicarFiltros()"/> -50% o más</label>' +
                '</div></div>' +
            '</div>' +
            '<button onclick="ofLimpiarFiltros()" class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors">Limpiar Filtros</button>' +
            '<button onclick="loadHome()" class="w-full py-2 mt-3 text-primary border border-primary rounded-lg text-sm font-medium hover:bg-primary hover:text-white transition-colors flex items-center justify-center gap-1"><span class="material-symbols-outlined text-lg">arrow_back</span> Volver al inicio</button>' +
            '</aside>' +

            '<!-- Productos -->' +
            '<div class="flex-1">' +
                '<div class="flex items-center justify-between mb-6">' +
                    '<p class="text-sm text-slate-500" id="of-count-text">Cargando ofertas...</p>' +
                    '<select id="of-ordenar" onchange="ofAplicarFiltros()" class="bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-sm rounded-lg focus:ring-primary">' +
                        '<option value="relevancia">Ordenar por: Relevancia</option>' +
                        '<option value="precio-asc">Precio: Menor a Mayor</option>' +
                        '<option value="precio-desc">Precio: Mayor a Menor</option>' +
                        '<option value="descuento">Mayor Descuento</option>' +
                    '</select>' +
                '</div>' +
                '<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6" id="of-productos-grid"></div>' +
            '</div>' +

            '</div></main>';

            document.getElementById('mainContent').innerHTML = html;
            ofGenerarFiltroCategorias();
            ofAplicarFiltros();
            ofIniciarTimer();
            window.scrollTo(0, 0);
        })
        .catch(error => {
            console.error('Error al cargar ofertas:', error);
            document.getElementById('mainContent').innerHTML = '<div class="text-center py-20 text-red-500"><span class="material-symbols-outlined text-5xl">error</span><p class="mt-2">Error al cargar ofertas</p></div>';
        });
}

// --- Funciones de Ofertas ---
function ofGenerarFiltroCategorias() {
    const categorias = [...new Set(window._ofTodosProductos.map(p => p.categoria_nombre).filter(Boolean))];
    const container = document.getElementById('of-filtro-categorias');
    if (!container) return;
    if (categorias.length === 0) {
        container.innerHTML = '<p class="text-xs text-slate-400">Sin categorías</p>';
        return;
    }
    container.innerHTML = categorias.map(cat =>
        '<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">' +
        '<input class="rounded text-primary focus:ring-primary border-slate-300 of-cat-check" type="checkbox" value="' + cat + '" checked onchange="ofAplicarFiltros()"/> ' + cat +
        '</label>'
    ).join('');
}

function ofAplicarFiltros() {
    const catChecks = document.querySelectorAll('.of-cat-check:checked');
    const catsSeleccionadas = Array.from(catChecks).map(c => c.value);
    const precioMin = parseFloat(document.getElementById('of-precio-min')?.value) || 0;
    const precioMax = parseFloat(document.getElementById('of-precio-max')?.value) || Infinity;
    const descRadio = document.querySelector('input[name="of-discount"]:checked');
    const descMin = descRadio ? parseInt(descRadio.value) || 0 : 0;

    window._ofProductosFiltrados = window._ofTodosProductos.filter(p => {
        const precioDesc = parseFloat(p.precio_descuento);
        const precioOrig = parseFloat(p.precio);
        const pctDesc = Math.round(((precioOrig - precioDesc) / precioOrig) * 100);
        const catOk = catsSeleccionadas.length === 0 || catsSeleccionadas.includes(p.categoria_nombre);
        const precioOk = precioDesc >= precioMin && precioDesc <= precioMax;
        const descOk = pctDesc >= descMin;
        return catOk && precioOk && descOk;
    });

    const orden = document.getElementById('of-ordenar')?.value || 'relevancia';
    if (orden === 'precio-asc') {
        window._ofProductosFiltrados.sort((a, b) => parseFloat(a.precio_descuento) - parseFloat(b.precio_descuento));
    } else if (orden === 'precio-desc') {
        window._ofProductosFiltrados.sort((a, b) => parseFloat(b.precio_descuento) - parseFloat(a.precio_descuento));
    } else if (orden === 'descuento') {
        window._ofProductosFiltrados.sort((a, b) => {
            const dA = ((parseFloat(a.precio) - parseFloat(a.precio_descuento)) / parseFloat(a.precio)) * 100;
            const dB = ((parseFloat(b.precio) - parseFloat(b.precio_descuento)) / parseFloat(b.precio)) * 100;
            return dB - dA;
        });
    }

    ofRenderProductos();
}

function ofRenderProductos() {
    const grid = document.getElementById('of-productos-grid');
    const countEl = document.getElementById('of-count-text');
    if (!grid) return;
    if (countEl) countEl.textContent = 'Mostrando ' + window._ofProductosFiltrados.length + ' de ' + window._ofTodosProductos.length + ' ofertas disponibles';

    if (window._ofProductosFiltrados.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-16"><span class="material-symbols-outlined text-6xl text-slate-300">search_off</span><p class="text-slate-500 mt-4 text-lg">No se encontraron ofertas con estos filtros.</p></div>';
        return;
    }

    // Para que la vista previa funcione
    window._productosData = window._ofProductosFiltrados;

    let html = '';
    window._ofProductosFiltrados.forEach(function(prod, idx) {
        const precioOrig = parseFloat(prod.precio).toFixed(2);
        const precioDesc = parseFloat(prod.precio_descuento).toFixed(2);
        const pctDesc = Math.round(((parseFloat(prod.precio) - parseFloat(prod.precio_descuento)) / parseFloat(prod.precio)) * 100);
        const imgSrc = prod.imagen_principal || 'https://via.placeholder.com/300x300?text=Sin+Imagen';
        const catNombre = prod.categoria_nombre || 'Sin categoría';

        html += '<div class="product-card group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">' +
            '<div class="relative aspect-square overflow-hidden bg-slate-100">' +
                '<img alt="' + prod.nombre + '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="' + imgSrc + '" onerror="this.src=\'https://via.placeholder.com/300x300?text=Sin+Imagen\'"/>' +
                '<span class="absolute top-3 left-3 bg-red-500 text-white px-2 py-1 rounded-lg text-xs font-bold">-' + pctDesc + '%</span>' +
                '<div class="product-actions absolute inset-0 bg-black/5 flex items-center justify-center gap-3 opacity-0 translate-y-4 transition-all duration-300">' +
                            '<button onclick="toggleWishlist(this,' + prod.id_producto + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-red-500 shadow-lg transition-colors" title="Lista de deseos"><span class="material-symbols-outlined">favorite</span></button>' +
                    '<button onclick="abrirVistaPrevia(' + idx + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Vista previa"><span class="material-symbols-outlined">visibility</span></button>' +
                '</div>' +
            '</div>' +
            '<div class="p-4">' +
                '<p class="text-xs text-slate-500 uppercase tracking-widest mb-1">' + catNombre + '</p>' +
                '<h4 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">' + prod.nombre + '</h4>' +
                '<div class="flex items-center gap-2 mb-3">' +
                    '<span class="text-red-500 font-bold text-lg">' + window._cfgMoneda + ' ' + precioDesc + '</span>' +
                    '<span class="text-slate-400 line-through text-sm">' + window._cfgMoneda + ' ' + precioOrig + '</span>' +
                '</div>' +
                '<div class="flex items-center gap-2 mb-3">' +
                    '<span class="text-xs text-slate-500 dark:text-slate-400">Cant:</span>' +
                    '<div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">' +
                        '<button onclick="cardCantidad(this,-1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-bold">\u2212</button>' +
                        '<input type="number" value="1" min="1" class="card-qty w-10 h-7 text-center border-x border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"/>' +
                        '<button onclick="cardCantidad(this,1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-bold">+</button>' +
                    '</div>' +
                '</div>' +
                '<button onclick="agregarAlCarritoDesdeCard(this,' + prod.id_producto + ')" class="w-full bg-slate-900 dark:bg-primary text-white py-2 rounded-lg font-medium text-sm hover:opacity-90 transition-opacity flex items-center justify-center gap-2"><span class="material-symbols-outlined text-lg">shopping_cart</span> Añadir al carrito</button>' +
            '</div>' +
        '</div>';
    });

    grid.innerHTML = html;
}

function ofLimpiarFiltros() {
    document.querySelectorAll('.of-cat-check').forEach(c => c.checked = true);
    const minEl = document.getElementById('of-precio-min');
    const maxEl = document.getElementById('of-precio-max');
    if (minEl) minEl.value = '';
    if (maxEl) maxEl.value = '';
    const radioAll = document.querySelector('input[name="of-discount"][value="0"]');
    if (radioAll) radioAll.checked = true;
    const ordenEl = document.getElementById('of-ordenar');
    if (ordenEl) ordenEl.value = 'relevancia';
    ofAplicarFiltros();
}

function ofIniciarTimer() {
    const ahora = new Date();
    const fin = new Date(ahora);
    fin.setHours(23, 59, 59, 0);
    function tick() {
        const diff = Math.max(0, fin - new Date());
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        const hEl = document.getElementById('of-hours');
        const mEl = document.getElementById('of-mins');
        const sEl = document.getElementById('of-secs');
        if (hEl) hEl.textContent = String(h).padStart(2, '0');
        if (mEl) mEl.textContent = String(m).padStart(2, '0');
        if (sEl) sEl.textContent = String(s).padStart(2, '0');
    }
    tick();
    if (window._ofTimerInterval) clearInterval(window._ofTimerInterval);
    window._ofTimerInterval = setInterval(tick, 1000);
}

function loadProductos() {
    document.getElementById('mainContent').innerHTML = '<div class="flex justify-center items-center py-20"><div class="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent"></div></div>';
    fetch('api/obtener_productos.php')
        .then(response => response.json())
        .then(productos => {
            // Guardar todos los productos
            window._todosProductos = productos;
            window._productosData = productos;

            // Extraer categorías padre únicas
            const catsPadre = [];
            const catsPadreSet = new Set();
            productos.forEach(p => {
                if (p.categoria_padre_nombre && !catsPadreSet.has(p.categoria_padre_nombre)) {
                    catsPadreSet.add(p.categoria_padre_nombre);
                    catsPadre.push({ nombre: p.categoria_padre_nombre, id: p.categoria_padre_id });
                }
            });
            catsPadre.sort((a, b) => a.nombre.localeCompare(b.nombre));

            // Extraer marcas únicas
            const marcas = [...new Set(productos.map(p => p.marca_nombre).filter(Boolean))].sort();

            let html = `
            <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between mb-8 border-l-4 border-primary pl-4">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Todos los Productos</h2>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1" id="prod-count-text">${productos.length} productos disponibles</p>
                    </div>
                    <button onclick="loadHome()" class="text-primary hover:underline font-semibold flex items-center gap-1 bg-none border-none cursor-pointer">
                        <span class="material-symbols-outlined text-lg">arrow_back</span> Volver al inicio
                    </button>
                </div>

                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Sidebar Filtros -->
                    <aside class="w-full lg:w-60 flex-shrink-0 space-y-6">
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-xl">filter_list</span> Filtros
                            </h3>

                            <!-- Filtro Categoría Principal -->
                            <div class="mb-6">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Categoría</p>
                                <div class="space-y-2" id="prod-filtro-cats">
                                    <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
                                        <input class="text-primary focus:ring-primary border-slate-300 prod-catpadre-radio" type="radio" name="prod-cat-padre" value="" checked onchange="prodCambioCatPadre()"/> Todas
                                    </label>
                                    ${catsPadre.map(c => '<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary"><input class="text-primary focus:ring-primary border-slate-300 prod-catpadre-radio" type="radio" name="prod-cat-padre" value="' + c.nombre + '" data-id="' + c.id + '" onchange="prodCambioCatPadre()"/> ' + c.nombre.charAt(0).toUpperCase() + c.nombre.slice(1) + '</label>').join('')}
                                </div>
                            </div>

                            <!-- Filtro Subcategoría (dinámico) -->
                            <div class="mb-6 hidden" id="prod-filtro-subcats-wrapper">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Subcategoría</p>
                                <div class="space-y-2" id="prod-filtro-subcats"></div>
                            </div>

                            <!-- Filtro Marca -->
                            <div class="mb-6">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Marca</p>
                                <div class="space-y-2" id="prod-filtro-marcas">
                                    ${marcas.length === 0 ? '<p class="text-xs text-slate-400">Sin marcas</p>' :
                                        marcas.map(m => '<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary"><input class="rounded text-primary focus:ring-primary border-slate-300 prod-marca-check" type="checkbox" value="' + m + '" checked onchange="prodAplicarFiltros()"/> ' + m.charAt(0).toUpperCase() + m.slice(1) + '</label>').join('')
                                    }
                                </div>
                            </div>

                            <!-- Ordenar -->
                            <div class="mb-6">
                                <p class="text-sm font-bold text-slate-700 dark:text-slate-300 mb-3">Ordenar por</p>
                                <select id="prod-ordenar" onchange="prodAplicarFiltros()" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-sm rounded-lg focus:ring-primary focus:border-primary py-2 px-3">
                                    <option value="recientes">Más recientes</option>
                                    <option value="precio-asc">Precio: Menor a Mayor</option>
                                    <option value="precio-desc">Precio: Mayor a Menor</option>
                                    <option value="nombre-asc">Nombre: A - Z</option>
                                    <option value="nombre-desc">Nombre: Z - A</option>
                                </select>
                            </div>

                            <button onclick="prodLimpiarFiltros()" class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors">
                                Limpiar Filtros
                            </button>
                        </div>
                    </aside>

                    <!-- Grid de productos -->
                    <div class="flex-1">
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-8" id="prod-grid"></div>
                    </div>
                </div>
            </section>`;

            // Modal de vista previa
            html += `
            <div id="modalVistaPrevia" class="fixed inset-0 z-[9999] hidden">
                <div class="absolute inset-0 bg-black/60 modal-blur" onclick="cerrarVistaPrevia()"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto pointer-events-auto relative">
                        <button onclick="cerrarVistaPrevia()" class="absolute top-4 right-4 z-10 w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-600 hover:text-red-500 hover:bg-red-50 transition-colors">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                        <div class="flex flex-col md:flex-row">
                            <div class="md:w-1/2 p-6">
                                <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 mb-4">
                                    <img id="prevImgPrincipal" src="" alt="" class="w-full h-full object-cover"/>
                                    <button id="prevBtnLeft" onclick="cambiarImgPrevia(-1)" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md transition-colors">
                                        <span class="material-symbols-outlined text-sm">arrow_back_ios_new</span>
                                    </button>
                                    <button id="prevBtnRight" onclick="cambiarImgPrevia(1)" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md transition-colors">
                                        <span class="material-symbols-outlined text-sm">arrow_forward_ios</span>
                                    </button>
                                </div>
                                <div id="prevMiniaturas" class="flex gap-2 overflow-x-auto pb-2"></div>
                            </div>
                            <div class="md:w-1/2 p-6 md:pl-2 flex flex-col justify-center">
                                <span id="prevCategoria" class="text-xs text-primary font-semibold uppercase tracking-wider mb-2"></span>
                                <h2 id="prevNombre" class="text-2xl font-bold text-slate-900 dark:text-white mb-3"></h2>
                                <p id="prevMarca" class="text-sm text-slate-400 mb-3"></p>
                                <p id="prevDescripcion" class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed"></p>
                                <div class="flex items-center gap-3 mb-4">
                                    <span id="prevPrecio" class="text-3xl font-bold text-slate-900 dark:text-white"></span>
                                    <span id="prevPrecioOriginal" class="text-lg text-slate-400 line-through hidden"></span>
                                    <span id="prevBadgeOferta" class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded hidden">OFERTA</span>
                                </div>
                                <div class="flex items-center gap-2 mb-6">
                                    <span id="prevStockIcon" class="material-symbols-outlined text-lg"></span>
                                    <span id="prevStock" class="text-sm font-medium"></span>
                                </div>
                                <div class="flex items-center gap-3 mb-4">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Cantidad:</span>
                                    <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                                        <button onclick="prevCantidad(-1)" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-lg font-bold">−</button>
                                        <input id="prevCantidadInput" type="number" value="1" min="1" class="w-12 h-9 text-center border-x border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-bold text-slate-900 dark:text-white focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" onchange="validarPrevCantidad()"/>
                                        <button onclick="prevCantidad(1)" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-lg font-bold">+</button>
                                    </div>
                                </div>
                                <button onclick="agregarAlCarritoDesdePreview()" class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-lg flex items-center justify-center gap-2 font-bold transition-colors">
                                    <span class="material-symbols-outlined">shopping_cart</span>
                                    Agregar al Carrito
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;

            document.getElementById('mainContent').innerHTML = html;
            prodAplicarFiltros();
            window.scrollTo(0, 0);
        })
        .catch(error => {
            console.error('Error al cargar productos:', error);
            document.getElementById('mainContent').innerHTML = '<div class="text-center py-20 text-red-500"><span class="material-symbols-outlined text-5xl">error</span><p class="mt-2">Error al cargar los productos</p></div>';
        });
}

// --- Cuando cambia la categoría padre seleccionada ---
function prodCambioCatPadre() {
    const radio = document.querySelector('input[name="prod-cat-padre"]:checked');
    const catPadreNombre = radio ? radio.value : '';
    const wrapper = document.getElementById('prod-filtro-subcats-wrapper');
    const container = document.getElementById('prod-filtro-subcats');

    if (!catPadreNombre) {
        // "Todas" seleccionada — ocultar subcategorías
        wrapper.classList.add('hidden');
        container.innerHTML = '';
    } else {
        // Buscar subcategorías de esa categoría padre entre los productos
        const subcats = [];
        const subcatSet = new Set();
        window._todosProductos.forEach(p => {
            // Producto pertenece a esta cat padre y tiene subcategoría (su categoria_nombre != categoria_padre_nombre)
            if (p.categoria_padre_nombre === catPadreNombre && p.categoria_nombre !== p.categoria_padre_nombre && p.categoria_nombre && !subcatSet.has(p.categoria_nombre)) {
                subcatSet.add(p.categoria_nombre);
                subcats.push(p.categoria_nombre);
            }
        });
        subcats.sort();

        if (subcats.length > 0) {
            wrapper.classList.remove('hidden');
            container.innerHTML =
                '<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">' +
                    '<input class="rounded text-primary focus:ring-primary border-slate-300 prod-subcat-check" type="checkbox" value="" checked onchange="prodAplicarFiltros()"/> Todas' +
                '</label>' +
                subcats.map(s =>
                    '<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">' +
                        '<input class="rounded text-primary focus:ring-primary border-slate-300 prod-subcat-check" type="checkbox" value="' + s + '" checked onchange="prodAplicarFiltros()"/> ' +
                        s.charAt(0).toUpperCase() + s.slice(1) +
                    '</label>'
                ).join('');
        } else {
            wrapper.classList.add('hidden');
            container.innerHTML = '';
        }
    }

    prodAplicarFiltros();
}

// --- Filtros de productos ---
function prodAplicarFiltros() {
    const catPadreRadio = document.querySelector('input[name="prod-cat-padre"]:checked');
    const catPadreNombre = catPadreRadio ? catPadreRadio.value : '';

    // Subcategorías seleccionadas
    const subcatChecks = document.querySelectorAll('.prod-subcat-check:checked');
    const subcatsSeleccionadas = Array.from(subcatChecks).map(c => c.value).filter(Boolean);
    const todasSubcatsChecked = Array.from(subcatChecks).some(c => c.value === '');

    // Marcas seleccionadas
    const marcaChecks = document.querySelectorAll('.prod-marca-check:checked');
    const marcasSeleccionadas = Array.from(marcaChecks).map(m => m.value);

    const orden = document.getElementById('prod-ordenar').value;

    let filtrados = window._todosProductos.filter(p => {
        // Filtro categoría padre
        let catOk = true;
        if (catPadreNombre) {
            catOk = p.categoria_padre_nombre === catPadreNombre;
            // Si hay subcategorías seleccionadas (y no está "Todas" marcada)
            if (catOk && subcatsSeleccionadas.length > 0 && !todasSubcatsChecked) {
                catOk = subcatsSeleccionadas.includes(p.categoria_nombre);
            }
        }

        // Filtro marca
        const marcaOk = marcasSeleccionadas.length === 0 || marcasSeleccionadas.includes(p.marca_nombre);

        return catOk && marcaOk;
    });

    // Ordenar
    if (orden === 'precio-asc') {
        filtrados.sort((a, b) => parseFloat(a.precio) - parseFloat(b.precio));
    } else if (orden === 'precio-desc') {
        filtrados.sort((a, b) => parseFloat(b.precio) - parseFloat(a.precio));
    } else if (orden === 'nombre-asc') {
        filtrados.sort((a, b) => a.nombre.localeCompare(b.nombre));
    } else if (orden === 'nombre-desc') {
        filtrados.sort((a, b) => b.nombre.localeCompare(a.nombre));
    }

    // Actualizar datos para vista previa
    window._productosData = filtrados;

    // Actualizar contador
    document.getElementById('prod-count-text').textContent = filtrados.length + ' de ' + window._todosProductos.length + ' productos';

    // Renderizar
    const grid = document.getElementById('prod-grid');
    if (filtrados.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-16"><span class="material-symbols-outlined text-6xl text-slate-300">search_off</span><p class="text-slate-500 mt-4 text-lg">No se encontraron productos con estos filtros.</p></div>';
        return;
    }

    let cards = '';
    filtrados.forEach(function(prod, index) {
        const imgSrc = prod.imagen_principal || 'https://via.placeholder.com/300x300?text=Sin+Imagen';
        const precioOriginal = parseFloat(prod.precio).toFixed(2);
        const enOferta = prod.en_oferta == 1 && prod.precio_descuento;
        const precioFinal = enOferta ? parseFloat(prod.precio_descuento).toFixed(2) : precioOriginal;

        cards += '<div class="product-card group bg-white dark:bg-slate-800 rounded-xl overflow-hidden border border-slate-100 dark:border-slate-700 hover:shadow-xl transition-all duration-300">' +
            '<div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-700">' +
                '<img alt="' + prod.nombre + '" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" src="' + imgSrc + '" onerror="this.src=\'https://via.placeholder.com/300x300?text=Sin+Imagen\'"/>' +
                '<div class="product-actions absolute inset-0 bg-black/5 flex items-center justify-center gap-3 opacity-0 translate-y-4 transition-all duration-300">' +
                    '<button onclick="toggleWishlist(this,' + prod.id_producto + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Lista de deseos"><span class="material-symbols-outlined">favorite</span></button>' +
                    '<button onclick="abrirVistaPrevia(' + index + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Vista previa"><span class="material-symbols-outlined">visibility</span></button>' +
                '</div>' +
                (enOferta ? '<div class="absolute top-3 left-3"><span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded uppercase">Oferta</span></div>' : '') +
            '</div>' +
            '<div class="p-5">' +
                '<h3 class="font-bold text-slate-900 dark:text-white mb-1 group-hover:text-primary transition-colors truncate">' + prod.nombre + '</h3>' +
                '<p class="text-slate-500 dark:text-slate-400 text-sm mb-4 line-clamp-2">' + (prod.descripcion || '') + '</p>' +
                '<div class="flex items-center gap-2 mb-3">' +
                    '<span class="text-xs text-slate-500 dark:text-slate-400">Cant:</span>' +
                    '<div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">' +
                        '<button onclick="cardCantidad(this,-1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-bold">\u2212</button>' +
                        '<input type="number" value="1" min="1" class="card-qty w-10 h-7 text-center border-x border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-900 dark:text-white focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"/>' +
                        '<button onclick="cardCantidad(this,1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-sm font-bold">+</button>' +
                    '</div>' +
                '</div>' +
                '<div class="flex items-center justify-between gap-4">' +
                    '<div class="flex flex-col">' +
                        '<span class="text-xl font-bold text-slate-900 dark:text-white">' + window._cfgMoneda + ' ' + precioFinal + '</span>' +
                        (enOferta ? '<span class="text-xs text-slate-400 line-through">' + window._cfgMoneda + ' ' + precioOriginal + '</span>' : '') +
                    '</div>' +
                    '<button onclick="agregarAlCarritoDesdeCard(this,' + prod.id_producto + ')" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-bold transition-colors">' +
                        '<span class="material-symbols-outlined text-lg">shopping_cart</span> Agregar</button>' +
                '</div>' +
            '</div>' +
        '</div>';
    });

    grid.innerHTML = cards;
}

function prodLimpiarFiltros() {
    const radioTodas = document.querySelector('input[name="prod-cat-padre"][value=""]');
    if (radioTodas) radioTodas.checked = true;
    document.querySelectorAll('.prod-marca-check').forEach(m => m.checked = true);
    document.getElementById('prod-ordenar').value = 'recientes';
    // Ocultar subcategorías
    const wrapper = document.getElementById('prod-filtro-subcats-wrapper');
    if (wrapper) wrapper.classList.add('hidden');
    const container = document.getElementById('prod-filtro-subcats');
    if (container) container.innerHTML = '';
    prodAplicarFiltros();
}

// --- Control de cantidad en tarjetas ---
function cardCantidad(btn, delta) {
    // Buscar el input numérico dentro del mismo control de cantidad
    const wrapper = btn.parentElement || btn.closest('div');
    if (!wrapper) return;
    const input = wrapper.querySelector('input[type="number"]');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    val = Math.max(1, val + delta);
    input.value = val;
}

// --- Control de cantidad en vista previa ---
function prevCantidad(delta) {
    const input = document.getElementById('prevCantidadInput');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    val = Math.max(1, val + delta);
    input.value = val;
}
function validarPrevCantidad() {
    const input = document.getElementById('prevCantidadInput');
    if (!input) return;
    let val = parseInt(input.value) || 1;
    if (val < 1) val = 1;
    input.value = val;
}

// Asegura que exista en el DOM un modal global de vista previa usado por abrirVistaPrevia()
function ensurePreviewModal() {
    if (document.getElementById('modalVistaPrevia')) return;
    const modalHtml = `
    <div id="modalVistaPrevia" class="fixed inset-0 z-[9999] hidden">
        <div class="absolute inset-0 bg-black/60 modal-blur" onclick="cerrarVistaPrevia()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto pointer-events-auto relative">
                <button onclick="cerrarVistaPrevia()" class="absolute top-4 right-4 z-10 w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-600 hover:text-red-500 hover:bg-red-50 transition-colors"><span class="material-symbols-outlined">close</span></button>
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/2 p-6">
                        <div class="relative aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 mb-4">
                            <img id="prevImgPrincipal" src="" alt="" class="w-full h-full object-cover"/>
                            <button id="prevBtnLeft" onclick="cambiarImgPrevia(-1)" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md"><span class="material-symbols-outlined text-sm">arrow_back_ios_new</span></button>
                            <button id="prevBtnRight" onclick="cambiarImgPrevia(1)" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md"><span class="material-symbols-outlined text-sm">arrow_forward_ios</span></button>
                        </div>
                        <div id="prevMiniaturas" class="flex gap-2 overflow-x-auto pb-2"></div>
                    </div>
                    <div class="md:w-1/2 p-6 md:pl-2 flex flex-col justify-center">
                        <span id="prevCategoria" class="text-xs text-primary font-semibold uppercase tracking-wider mb-2"></span>
                        <h2 id="prevNombre" class="text-2xl font-bold text-slate-900 dark:text-white mb-3"></h2>
                        <p id="prevMarca" class="text-sm text-slate-400 mb-3"></p>
                        <p id="prevDescripcion" class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed"></p>
                        <div class="flex items-center gap-3 mb-4"><span id="prevPrecio" class="text-3xl font-bold text-slate-900 dark:text-white"></span><span id="prevPrecioOriginal" class="text-lg text-slate-400 line-through hidden"></span><span id="prevBadgeOferta" class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded hidden">OFERTA</span></div>
                        <div class="flex items-center gap-2 mb-6"><span id="prevStockIcon" class="material-symbols-outlined text-lg"></span><span id="prevStock" class="text-sm font-medium"></span></div>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Cantidad:</span>
                            <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                                <button onclick="prevCantidad(-1)" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-lg font-bold">−</button>
                                <input id="prevCantidadInput" type="number" value="1" min="1" class="w-12 h-9 text-center border-x border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm font-bold text-slate-900 dark:text-white focus:outline-none [appearance:textfield]" onchange="validarPrevCantidad()"/>
                                <button onclick="prevCantidad(1)" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors text-lg font-bold">+</button>
                            </div>
                        </div>
                        <button onclick="agregarAlCarritoDesdePreview()" class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-lg flex items-center justify-center gap-2 font-bold transition-colors"><span class="material-symbols-outlined">shopping_cart</span> Agregar al Carrito</button>
                    </div>
                </div>
            </div>
        </div>
    </div>`;
    const div = document.createElement('div');
    div.innerHTML = modalHtml;
    document.body.appendChild(div.firstElementChild);
}

// --- Vista previa de producto ---
let _prevImgIndex = 0;
let _prevImagenes = [];
let _prevProductoActual = null;

function abrirVistaPrevia(index) {
    ensurePreviewModal();
    const prod = window._productosData[index];
    if (!prod) return;
    _prevProductoActual = prod;

    const modal = document.getElementById('modalVistaPrevia');
    const enOferta = prod.en_oferta == 1 && prod.precio_descuento;
    const precioOriginal = parseFloat(prod.precio).toFixed(2);
    const precioFinal = enOferta ? parseFloat(prod.precio_descuento).toFixed(2) : precioOriginal;

    // Info
    document.getElementById('prevNombre').textContent = prod.nombre;
    document.getElementById('prevCategoria').textContent = prod.categoria_nombre || 'Sin categoría';
    document.getElementById('prevMarca').textContent = prod.marca_nombre ? 'Marca: ' + prod.marca_nombre : '';
    document.getElementById('prevDescripcion').textContent = prod.descripcion || 'Sin descripción disponible.';
    document.getElementById('prevPrecio').textContent = window._cfgMoneda + ' ' + precioFinal;

    if (enOferta) {
        document.getElementById('prevPrecioOriginal').textContent = window._cfgMoneda + ' ' + precioOriginal;
        document.getElementById('prevPrecioOriginal').classList.remove('hidden');
        document.getElementById('prevBadgeOferta').classList.remove('hidden');
    } else {
        document.getElementById('prevPrecioOriginal').classList.add('hidden');
        document.getElementById('prevBadgeOferta').classList.add('hidden');
    }

    // Stock
    const stockIcon = document.getElementById('prevStockIcon');
    const stockText = document.getElementById('prevStock');
    if (prod.stock > 0) {
        stockIcon.textContent = 'check_circle';
        stockIcon.className = 'material-symbols-outlined text-lg text-green-500';
        stockText.textContent = 'En stock (' + prod.stock + ' disponibles)';
        stockText.className = 'text-sm font-medium text-green-600';
    } else {
        stockIcon.textContent = 'cancel';
        stockIcon.className = 'material-symbols-outlined text-lg text-red-500';
        stockText.textContent = 'Agotado';
        stockText.className = 'text-sm font-medium text-red-500';
    }

    // Imágenes
    _prevImagenes = (prod.imagenes && prod.imagenes.length > 0)
        ? prod.imagenes
        : (prod.imagen_principal ? [prod.imagen_principal] : ['https://via.placeholder.com/400x400?text=Sin+Imagen']);
    _prevImgIndex = 0;

    actualizarImgPrevia();

    // Resetear cantidad a 1
    const cantInput = document.getElementById('prevCantidadInput');
    if (cantInput) cantInput.value = 1;

    // Miniaturas
    const minContainer = document.getElementById('prevMiniaturas');
    minContainer.innerHTML = '';
    if (_prevImagenes.length > 1) {
        _prevImagenes.forEach((img, i) => {
            minContainer.innerHTML += '<button onclick="seleccionarImgPrevia(' + i + ')" class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 ' + (i === 0 ? 'border-primary' : 'border-transparent hover:border-slate-300') + ' transition-colors"><img src="' + img + '" class="w-full h-full object-cover" onerror="this.src=\'https://via.placeholder.com/100x100?text=Error\'"/></button>';
        });
    }

    // Flechas
    document.getElementById('prevBtnLeft').style.display = _prevImagenes.length > 1 ? 'flex' : 'none';
    document.getElementById('prevBtnRight').style.display = _prevImagenes.length > 1 ? 'flex' : 'none';

    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarVistaPrevia() {
    document.getElementById('modalVistaPrevia').classList.add('hidden');
    document.body.style.overflow = '';
}

function cambiarImgPrevia(dir) {
    _prevImgIndex = (_prevImgIndex + dir + _prevImagenes.length) % _prevImagenes.length;
    actualizarImgPrevia();
    actualizarMiniaturas();
}

function seleccionarImgPrevia(i) {
    _prevImgIndex = i;
    actualizarImgPrevia();
    actualizarMiniaturas();
}

function actualizarImgPrevia() {
    document.getElementById('prevImgPrincipal').src = _prevImagenes[_prevImgIndex];
}

function actualizarMiniaturas() {
    const btns = document.getElementById('prevMiniaturas').querySelectorAll('button');
    btns.forEach((btn, i) => {
        btn.className = 'flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-colors ' + (i === _prevImgIndex ? 'border-primary' : 'border-transparent hover:border-slate-300');
    });
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

// ========== CARRITO DE COMPRAS ==========
function abrirCarrito() {
    document.getElementById('cartOverlay').classList.remove('hidden');
    document.getElementById('cartSidebar').classList.remove('hidden');
    cargarCarrito();
}

function cerrarCarrito() {
    document.getElementById('cartOverlay').classList.add('hidden');
    document.getElementById('cartSidebar').classList.add('hidden');
}

function actualizarBadge(cantidad) {
    const badge = document.getElementById('cartBadge');
    const sidebarBadge = document.getElementById('cartSidebarBadge');
    if (badge) badge.textContent = cantidad;
    if (sidebarBadge) sidebarBadge.textContent = cantidad + ' ítems';
}

function cargarCarrito() {
    fetch('api/api_carrito.php?accion=listar')
        .then(r => r.json())
        .then(data => {
            if (data.exito) {
                renderizarCarrito(data.carrito);
            } else {
                // No autenticado o error
                cerrarCarrito();
                showAuthModal("Debes iniciar sesión para usar el carrito");
                actualizarBadge(0);
            }
        })
        .catch(() => {});
}

function renderizarCarrito(data) {
    const cont = document.getElementById('cartItemsContainer');
    const items = data.items || [];
    const mon = window._cfgMoneda || 'L';

    actualizarBadge(data.total_cantidad || 0);

    if (items.length === 0) {
        cont.innerHTML = '<div class="text-center py-12"><span class="material-symbols-outlined text-5xl text-slate-200">shopping_cart</span><p class="text-slate-400 mt-3">Tu carrito está vacío</p></div>';
        document.getElementById('cartSubtotal').textContent = mon + ' 0.00';
        document.getElementById('cartImpuesto').textContent = mon + ' 0.00';
        document.getElementById('cartTotal').textContent = mon + ' 0.00';
        document.getElementById('cartBtnFinalizar').disabled = true;
        return;
    }

    document.getElementById('cartBtnFinalizar').disabled = false;
    let html = '';
    items.forEach(item => {
            let imgSrc = 'https://via.placeholder.com/80?text=Sin+Imagen';
            if (item.imagen && typeof item.imagen === 'string' && item.imagen.trim() !== '') {
                if (item.imagen.includes('img/productos/')) {
                    imgSrc = item.imagen;
                } else {
                    imgSrc = 'img/productos/' + item.imagen;
                }
            }
        html += '<div class="flex gap-4 group">' +
            '<div class="w-20 h-20 bg-slate-100 dark:bg-slate-800 rounded-lg overflow-hidden flex-shrink-0">' +
                '<img alt="' + (item.nombre || '') + '" class="w-full h-full object-cover" src="' + imgSrc + '"/>' +
            '</div>' +
            '<div class="flex-1 flex flex-col justify-between">' +
                '<div class="flex justify-between items-start">' +
                    '<h3 class="font-bold text-slate-900 dark:text-white text-sm line-clamp-1">' + (item.nombre || '') + '</h3>' +
                    '<button onclick="eliminarDelCarrito(' + item.id_carrito_detalle + ')" class="text-slate-400 hover:text-red-500 transition-colors">' +
                        '<span class="material-symbols-outlined text-lg">delete</span>' +
                    '</button>' +
                '</div>' +
                '<div class="flex items-center justify-between mt-2">' +
                    '<div class="flex items-center border border-slate-200 dark:border-slate-700 rounded-md overflow-hidden">' +
                        '<button onclick="actualizarCantidadCarrito(' + item.id_carrito_detalle + ',' + (item.cantidad - 1) + ')" class="px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">' +
                            '<span class="material-symbols-outlined text-xs block">remove</span>' +
                        '</button>' +
                        '<span class="px-3 py-1 text-xs font-bold">' + item.cantidad + '</span>' +
                        '<button onclick="actualizarCantidadCarrito(' + item.id_carrito_detalle + ',' + (item.cantidad + 1) + ')" class="px-2 py-1 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">' +
                            '<span class="material-symbols-outlined text-xs block">add</span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="text-right">' +
                        '<span class="font-bold text-slate-900 dark:text-white">' + mon + ' ' + parseFloat(item.subtotal).toFixed(2) + '</span>' +
                        (item.tasa_impuesto > 0 ? '<br><span class="text-[10px] text-slate-400">IVA ' + item.tasa_impuesto + '%</span>' : '') +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    });
    cont.innerHTML = html;

    document.getElementById('cartSubtotal').textContent = mon + ' ' + parseFloat(data.subtotal).toFixed(2);
    document.getElementById('cartImpuesto').textContent = mon + ' ' + parseFloat(data.impuesto_total).toFixed(2);
    document.getElementById('cartTotal').textContent = mon + ' ' + parseFloat(data.total).toFixed(2);
}

function agregarAlCarritoDesdeCard(btn, idProducto) {
    // Verificar autenticación primero
    if (!window._usuarioAutenticado) {
        showAuthModal("Debes iniciar sesión para agregar productos al carrito");
        return;
    }
    
    // Buscar la tarjeta del producto y dentro de ella el input de cantidad
    const card = btn.closest('.product-card') || btn.closest('[data-product-id]') || btn.closest('section');
    const cantInput = card ? card.querySelector('.card-qty, input[type="number"]') : null;
    const cantidad = cantInput ? parseInt(cantInput.value) || 1 : 1;
    agregarAlCarrito(idProducto, cantidad, btn);
}

function agregarAlCarritoDesdePreview() {
    // Verificar autenticación primero
    if (!window._usuarioAutenticado) {
        showAuthModal("Debes iniciar sesión para agregar productos al carrito");
        return;
    }
    
    const prevQtyEl = document.getElementById('prevCantidadInput');
    const cantidad = prevQtyEl ? parseInt(prevQtyEl.value) || 1 : 1;
    if (!_prevProductoActual || !_prevProductoActual.id_producto) {
        CustomModal.show('warning', 'Carrito', 'No se pudo identificar el producto.');
        return;
    }
    agregarAlCarrito(_prevProductoActual.id_producto, cantidad);
}

function agregarAlCarrito(idProducto, cantidad, btnElement) {
    if (!window._usuarioAutenticado) {
        showAuthModal("Debes iniciar sesión para agregar productos al carrito");
        return;
    }
    if (btnElement) {
        btnElement.disabled = true;
        btnElement.innerHTML = '<span class="material-symbols-outlined text-lg animate-spin">sync</span>';
    }

    const formData = new FormData();
    formData.append('accion', 'agregar');
    formData.append('id_producto', idProducto);
    formData.append('cantidad', cantidad || 1);

    fetch('api/api_carrito.php', { method: 'POST', body: formData, credentials: 'same-origin' })
        .then(r => r.json())
        .then(data => {
            if (data.exito) {
                actualizarBadge(data.carrito ? data.carrito.total_cantidad || 0 : 0);
                // Mostrar mini notificación
                mostrarToastCarrito('Producto agregado al carrito');
                // Si el sidebar está abierto, refrescar
                if (!document.getElementById('cartSidebar').classList.contains('hidden')) {
                    cargarCarrito();
                }
            } else {
                // Verificar si es error de autenticación
                if (data.error && data.error.toLowerCase().includes('autenticaci')) {
                    showAuthModal(data.error);
                } else {
                    showAuthModal(data.error || 'Debes iniciar sesión para usar esta función');
                }
            }
        })
        .catch(() => {
            showAuthModal('Debes iniciar sesión para agregar productos al carrito');
        })
        .finally(() => {
            if (btnElement) {
                btnElement.disabled = false;
                btnElement.innerHTML = '<span class="material-symbols-outlined text-lg">shopping_cart</span> Agregar';
            }
        });
}

function actualizarCantidadCarrito(idDetalle, nuevaCantidad) {
    const formData = new FormData();
    formData.append('accion', 'actualizar');
    formData.append('id_carrito_detalle', idDetalle);
    formData.append('cantidad', nuevaCantidad);

    fetch('api/api_carrito.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.exito) cargarCarrito();
            else showAuthModal(data.error || 'Debes iniciar sesión para usar esta función');
        })
        .catch(() => {});
}

function eliminarDelCarrito(idDetalle) {
    const formData = new FormData();
    formData.append('accion', 'eliminar');
    formData.append('id_carrito_detalle', idDetalle);

    fetch('api/api_carrito.php', { method: 'POST', body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.exito) cargarCarrito();
        })
        .catch(() => {});
}

function vaciarCarrito() {
    CustomModal.show('confirm', 'Vaciar Carrito', '¿Estás seguro de que deseas vaciar todo el carrito?', function(confirm) {
        if (!confirm) return;
        const formData = new FormData();
        formData.append('accion', 'vaciar');
        fetch('api/api_carrito.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.exito) cargarCarrito();
            })
            .catch(() => {});
    });
}

function mostrarToastCarrito(mensaje) {
    let toast = document.getElementById('cartToast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'cartToast';
        toast.className = 'fixed bottom-6 right-6 z-[200] bg-green-600 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-2 text-sm font-semibold transition-all duration-300 translate-y-20 opacity-0';
        document.body.appendChild(toast);
    }
    toast.innerHTML = '<span class="material-symbols-outlined text-lg">check_circle</span> ' + mensaje;
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 2500);
    });
}

// Toggle / agregar producto a Lista de Deseos (requiere autenticación)
function toggleWishlist(btn, idProducto) {
    if (!window._usuarioAutenticado) {
        showAuthModal("Debes iniciar sesión para usar la lista de deseos");
        return;
    }

    try { btn.disabled = true; } catch(e) {}

    const formData = new FormData();
    formData.append('accion', 'agregar');
    formData.append('id_producto', idProducto);

    fetch('api/api_lista_deseos.php', { method: 'POST', body: formData, credentials: 'same-origin' })
        .then(response => {
            // Intentar parsear JSON; si falla, devolver el texto para mostrarlo
            return response.text().then(text => {
                try { return JSON.parse(text); } catch (e) { throw new Error(text || 'Respuesta no válida del servidor'); }
            });
        })
        .then(data => {
            if (data.exito) {
                // Si la API responde con un mensaje indicando que ya existe, mostrarlo claramente
                if (data.mensaje && /ya en la lista/i.test(data.mensaje)) {
                    CustomModal.show('info', 'Lista de deseos', data.mensaje);
                } else {
                    mostrarToastCarrito('Guardado en Lista de Deseos');
                    try { btn.classList.add('text-red-500'); } catch(e) {}
                }
            } else {
                // Mostrar modal de autenticación
                showAuthModal(data.error || 'Debes iniciar sesión para usar esta función');
            }
        })
        .catch(err => {
            showAuthModal(err.message || 'Debes iniciar sesión para usar esta función');
        })
        .finally(() => { try { btn.disabled = false; } catch(e) {} });
}

// Cargar conteo del carrito al iniciar
document.addEventListener('DOMContentLoaded', function() {
    fetch('api/api_carrito.php?accion=contar')
        .then(r => r.json())
        .then(data => {
            if (data.exito) actualizarBadge(data.total || 0);
        })
        .catch(() => {});
});
// ========== FIN CARRITO ==========

</script>
<script>
document.body.addEventListener("click", function (e) {

    const btn = e.target.closest(".btn-ver-detalle");

    if (btn) {

        const id = btn.dataset.id;

        fetch("client/obtener_detalle_pedido.php?id=" + id)
            .then(res => res.text())
            .then(data => {

                const modal = document.getElementById("modalPedido");
                const contenido = document.getElementById("contenidoModal");

                if (modal && contenido) {
                    contenido.innerHTML = data;
                    modal.classList.remove("hidden");
                    modal.classList.add("flex");
                }

            });

    }

});

function cerrarModal() {
    const modal = document.getElementById("modalPedido");
    if (modal) {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }
}

// === MANEJADOR DE ANCHORS/HASHES ===
function procesarHash(hash) {
    // Mapear anchors a funciones
    const hashMap = {
        '#productos': loadProductos,
        '#ofertas': loadOfertas,
        '#categorias': loadCategoriasPanel,
        '#': loadHome,
        '': loadHome
    };
    
    const handler = hashMap[hash];
    if (handler && typeof handler === 'function') {
        setTimeout(() => handler(), 100);
    }
}

// Procesar hash SOLO cuando cambia (al hacer clic en un enlace)
window.addEventListener('hashchange', function() {
    const hash = window.location.hash || '#';
    procesarHash(hash);
});

// === MODAL DE AUTENTICACIÓN ===
function showAuthModal(message = "Debes iniciar sesión para continuar") {
    const modal = document.getElementById("modalAuth");
    if (modal) {
        const messageElement = modal.querySelector("#authMessage");
        if (messageElement) {
            messageElement.innerText = message;
        }
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    }
}

function closeAuthModal() {
    const modal = document.getElementById("modalAuth");
    if (modal) {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }
}

function irAlLogin() {
    closeAuthModal();
    loadLogin();
}
</script>

<!-- MODAL AUTENTICACIÓN -->
<div id="modalAuth" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[10000]" onclick="if(event.target === this) closeAuthModal()">
    <div class="bg-white dark:bg-slate-900 rounded-lg p-8 w-full max-w-sm text-center shadow-xl">
        <p id="authMessage" class="text-slate-700 dark:text-slate-300 mb-6 text-lg">
            Debes iniciar sesión para continuar
        </p>

        <div class="flex items-center justify-center gap-2">
            <span class="text-slate-600 dark:text-slate-400">¿Quieres iniciar sesión?</span>
            <button onclick="irAlLogin()" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-semibold cursor-pointer underline transition">
                Aquí
            </button>
        </div>
    </div>
</div>

</body></html>