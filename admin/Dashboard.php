<?php
/**
 * Dashboard Administrativo
 * Solo accesible para Administrador (rol 1) y Vendedor (rol 2)
 */

require_once '../core/sesiones.php';
require_once '../core/conexion.php';
require_once '../core/validador_inactividad.php';

// Verificar autenticación
if (!usuarioAutenticado()) {
    header("Location: ../pages/login.php");
    exit();
}

// Verificar permisos: solo rol 1 (admin) y rol 2 (vendedor)
if ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2) {
    // Usuario sin permisos, redirigir a index
    header("Location: ../index.php");
    exit();
}

// Obtener datos del usuario autenticado
$usuario = obtenerDatosUsuario();

// Cargar configuración general para colores y nombre
$res_cfg_admin = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");
$cfg_admin = ($res_cfg_admin && mysqli_num_rows($res_cfg_admin) > 0) ? mysqli_fetch_assoc($res_cfg_admin) : [];

// Símbolo de moneda
$cfg_moneda_cod = $cfg_admin['moneda'] ?? 'HNL';
$simbolos_moneda = ['USD' => '$', 'EUR' => '€', 'MXN' => '$', 'COP' => '$', 'ARS' => '$', 'GTQ' => 'Q', 'HNL' => 'L', 'CRC' => '₡'];
$cfg_moneda = $simbolos_moneda[$cfg_moneda_cod] ?? $cfg_moneda_cod;

// ==================== OBTENER ESTADÍSTICAS DE LA BASE DE DATOS ====================

// Total de productos
$res_productos = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM productos");
$total_productos = ($res_productos && mysqli_num_rows($res_productos) > 0) ? mysqli_fetch_assoc($res_productos)['total'] : 0;

// Total de clientes
$res_clientes = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM clientes");
$total_clientes = ($res_clientes && mysqli_num_rows($res_clientes) > 0) ? mysqli_fetch_assoc($res_clientes)['total'] : 0;

// Pedidos de hoy
$hoy = date('Y-m-d');
$res_pedidos_hoy = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM pedidos WHERE DATE(fecha_pedido) = '$hoy'");
$pedidos_hoy = ($res_pedidos_hoy && mysqli_num_rows($res_pedidos_hoy) > 0) ? mysqli_fetch_assoc($res_pedidos_hoy)['total'] : 0;

// Ingresos de hoy (solo pedidos confirmados/entregados = pagados)
$res_ingresos_hoy = mysqli_query($conexion, "SELECT SUM(total) AS total FROM pedidos WHERE DATE(fecha_pedido) = '$hoy' AND estado IN ('confirmado', 'enviado', 'entregado')");
$resultado_ingresos = mysqli_fetch_assoc($res_ingresos_hoy);
$ingresos_hoy = !empty($resultado_ingresos['total']) ? floatval($resultado_ingresos['total']) : 0;

// Estadísticas de pedidos por estado
$res_pendientes = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM pedidos WHERE estado = 'pendiente'");
$pedidos_pendientes = ($res_pendientes && mysqli_num_rows($res_pendientes) > 0) ? mysqli_fetch_assoc($res_pendientes)['total'] : 0;

$res_confirmados = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM pedidos WHERE estado = 'confirmado'");
$pedidos_confirmados = ($res_confirmados && mysqli_num_rows($res_confirmados) > 0) ? mysqli_fetch_assoc($res_confirmados)['total'] : 0;

$res_enviados = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM pedidos WHERE estado = 'enviado'");
$pedidos_enviados = ($res_enviados && mysqli_num_rows($res_enviados) > 0) ? mysqli_fetch_assoc($res_enviados)['total'] : 0;

$res_entregados = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM pedidos WHERE estado = 'entregado'");
$pedidos_entregados = ($res_entregados && mysqli_num_rows($res_entregados) > 0) ? mysqli_fetch_assoc($res_entregados)['total'] : 0;

// Total general de pedidos
$total_pedidos = $pedidos_pendientes + $pedidos_confirmados + $pedidos_enviados + $pedidos_entregados;

// Calcular porcentajes para las barras
$porcentaje_pendientes = $total_pedidos > 0 ? round(($pedidos_pendientes / $total_pedidos) * 100) : 0;
$porcentaje_confirmados = $total_pedidos > 0 ? round(($pedidos_confirmados / $total_pedidos) * 100) : 0;
$porcentaje_enviados = $total_pedidos > 0 ? round(($pedidos_enviados / $total_pedidos) * 100) : 0;
$porcentaje_entregados = $total_pedidos > 0 ? round(($pedidos_entregados / $total_pedidos) * 100) : 0;

function normalizar_color_admin($valor, $defecto) {
    if (!is_string($valor)) return $defecto;
    $valor = trim($valor);
    if ($valor === '') return $defecto;
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) return $defecto;
    return strtoupper($valor);
}

$admin_primary = normalizar_color_admin($cfg_admin['color_primary'] ?? '#3b82f6', '#3B82F6');
$admin_bg_light = normalizar_color_admin($cfg_admin['color_background_light'] ?? '#f8fafc', '#F8FAFC');
$admin_bg_dark = normalizar_color_admin($cfg_admin['color_background_dark'] ?? '#0f172a', '#0F172A');
$admin_sidebar_dark = '#1e293b';
$admin_nombre = htmlspecialchars($cfg_admin['nombre_negocio'] ?? 'Mi Negocio');
?>
<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Dashboard Administrativo Profesional</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
<script>
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "<?php echo $admin_primary; ?>",
              "background-light": "<?php echo $admin_bg_light; ?>",
              "background-dark": "<?php echo $admin_bg_dark; ?>",
              "sidebar-dark": "<?php echo $admin_sidebar_dark; ?>",
            },
            fontFamily: {
              display: ["Inter", "sans-serif"],
            },
            borderRadius: {
              DEFAULT: "0.75rem",
            },
          },
        },
      };
      function toggleDarkMode() {
        document.documentElement.classList.toggle('dark');
      }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-active {
            background-color: rgba(59, 130, 246, 0.1);
            border-left: 4px solid <?php echo $admin_primary; ?>;
            color: <?php echo $admin_primary; ?>;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 transition-colors duration-200">
<div class="flex h-screen overflow-hidden">
<aside class="w-64 bg-sidebar-dark text-white flex-shrink-0 flex flex-col hidden lg:flex">
<div class="p-6 flex items-center gap-3">
<div class="bg-primary p-2 rounded-lg">
<span class="material-icons-round text-white">store</span>
</div>
<div>
<h1 class="font-bold text-lg leading-tight"><?php echo $admin_nombre; ?></h1>
<p class="text-xs text-slate-400">Admin Panel</p>
</div>
</div>
<nav class="flex-1 mt-4 px-3 space-y-1">
<a class="flex items-center gap-3 px-4 py-3 sidebar-active rounded-r-none rounded-lg transition-all nav-link" href="#" onclick="loadPage('Dashboard.php', event)">
<span class="material-icons-round">dashboard</span>
<span class="font-medium">Dashboard</span>
</a>
<?php if ($_SESSION['id_rol'] == 1): // Solo para administrador ?>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('../admin/gestion_productos.php', event)">
<span class="material-icons-round">inventory_2</span>
<span class="font-medium">Productos</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('../client/categoria.php', event)">
<span class="material-icons-round">category</span>
<span class="font-medium">Categorías</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('../client/clientes.php', event)">
<span class="material-icons-round">people</span>
<span class="font-medium">Clientes</span>
</a>
<?php endif; ?>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('./pedidosadmin.php', event)">
<span class="material-icons-round">shopping_cart</span>
<span class="font-medium">Pedidos</span>
</a>
<?php if ($_SESSION['id_rol'] == 1): // Solo para administrador ?>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('./usuarios.php', event)">
<span class="material-icons-round">manage_accounts</span>
<span class="font-medium">Usuarios</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('../client/mensajeria.php', event)">
<span class="material-icons-round">mail</span>
<span class="font-medium">Mensajería</span>
</a>
<!-- MODULO COMPRAS -->
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link"
href="#"
onclick="loadPage('./admin_compras.php', event)">
<span class="material-icons-round">shopping_cart</span>
<span class="font-medium">Compras</span>
</a>
<!-- MODULO REPORTES -->
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link"
href="#"
onclick="loadPage('./admin_reportes.php', event)">
<span class="material-icons-round">bar_chart</span>
<span class="font-medium">Reportes</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-all nav-link" href="#" onclick="loadPage('configuracion.php', event)">
<span class="material-icons-round">settings</span>
<span class="font-medium">Configuraciones</span>
</a>
<?php endif; ?>
</nav>
<div class="p-4 mt-auto">
<button class="flex items-center gap-3 px-4 py-3 text-slate-400 hover:text-red-400 w-full transition-all" onclick="cerrarSesion();">
<span class="material-icons-round">logout</span>
<span class="font-medium">Cerrar Sesión</span>
</button>
</div>
</aside>
<div class="flex-1 flex flex-col overflow-y-auto">
<header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-8 flex items-center justify-between sticky top-0 z-10">
<h2 id="page-title" class="text-xl font-bold dark:text-white">Dashboard</h2>
<div class="flex items-center gap-6">
<button class="p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors" onclick="toggleDarkMode()">
<span class="material-icons-round dark:hidden">dark_mode</span>
<span class="material-icons-round hidden dark:block text-yellow-400">light_mode</span>
</button>
<button class="relative p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-full transition-colors">
<span class="material-icons-round">notifications</span>
<span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full border-2 border-white dark:border-slate-900"></span>
</button>
<div class="flex items-center gap-3 pl-4 border-l border-slate-200 dark:border-slate-800">
<div class="text-right hidden sm:block">
<p class="text-sm font-semibold dark:text-white"><?php echo htmlspecialchars($usuario['nombre'] ?? 'Usuario'); ?></p>
<p class="text-xs text-slate-500"><?php echo htmlspecialchars($usuario['nombre_rol'] ?? 'Sin rol'); ?></p>
</div>
<img alt="Profile" class="w-10 h-10 rounded-full object-cover border-2 border-primary/20" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDjLIRD3VjeXzAexA8RNlnwfNuxo9_3LYfXncJ7KNLB2OF5dsOzX1SN7Zzpzlo_wtsHyINXyxAjAZmxkms2AFqLczyXft-Hnlc08GhRdmd8XTPhvyfNz3B8r6mk1iL_snNH5pI3nM5ZR6cLOLJhkTijYUDTB4f758oIfwnZ7h2KsElVYJ9PspnA99elI_-NFEedvH4HJ6EyKyzESoCOE6CK2Afsa-xQLsKnrMu7yCghMeb__nwsxlrOxKL0be4Q3wzePnWYI89B-24"/>
</div>
</div>
</header>
<main id="main-content" class="p-8">
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border-l-4 border-blue-500 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start">
<div>
<p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total Productos</p>
<h3 class="text-2xl font-bold mt-1"><?php echo number_format($total_productos); ?></h3>
<p class="text-xs text-green-500 mt-2 flex items-center font-medium">
<span class="material-icons-round text-xs mr-1">trending_up</span> 12% este mes
                                </p>
</div>
<div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-500 rounded-xl">
<span class="material-icons-round">inventory_2</span>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border-l-4 border-indigo-500 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start">
<div>
<p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Total Clientes</p>
<h3 class="text-2xl font-bold mt-1"><?php echo number_format($total_clientes); ?></h3>
<p class="text-xs text-slate-500 mt-2 flex items-center font-medium">
<span class="material-icons-round text-xs mr-1">person_add</span> Registrados en el sistema
                                </p>
</div>
<div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-500 rounded-xl">
<span class="material-icons-round">people</span>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border-l-4 border-orange-500 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start">
<div>
<p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Pedidos Hoy</p>
<h3 class="text-2xl font-bold mt-1"><?php echo $pedidos_hoy; ?></h3>
<p class="text-xs text-slate-500 mt-2 flex items-center font-medium">
<span class="material-icons-round text-xs mr-1">today</span> <?php echo date('d/m/Y'); ?>
                                </p>
</div>
<div class="p-3 bg-orange-50 dark:bg-orange-900/30 text-orange-500 rounded-xl">
<span class="material-icons-round">shopping_cart</span>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl border-l-4 border-green-500 shadow-sm hover:shadow-md transition-shadow">
<div class="flex justify-between items-start">
<div>
<p class="text-sm text-slate-500 dark:text-slate-400 font-medium">Ingresos Hoy</p>
<h3 class="text-2xl font-bold mt-1"><?php echo htmlspecialchars($cfg_moneda); ?><?php echo number_format($ingresos_hoy, 2); ?></h3>
<p class="text-xs text-green-500 mt-2 flex items-center font-medium">
<span class="material-icons-round text-xs mr-1">trending_up</span> 15% vs ayer
                                </p>
</div>
<div class="p-3 bg-green-50 dark:bg-green-900/30 text-green-500 rounded-xl">
<span class="material-icons-round">attach_money</span>
</div>
</div>
</div>
</div> 
<div clss="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
<div class="lg:col-span-2 bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm">
<div class="flex items-center justify-between mb-6">
<h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
<span class="material-icons-round text-primary">show_chart</span>
                                Ventas Este Mes
                            </h4>
<select class="text-sm bg-slate-50 dark:bg-slate-800 border-none rounded-lg focus:ring-primary">
<option>Últimos 30 días</option>
<option>Últimos 7 días</option>
</select>
</div>
<div class="h-64 bg-slate-50 dark:bg-slate-800/50 rounded-xl flex items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-700">
<div class="text-center">
<span class="material-icons-round text-4xl text-slate-300 dark:text-slate-600">bar_chart</span>
<p class="text-slate-400 dark:text-slate-500 mt-2 text-sm font-medium">Gráfico de ventas</p>
</div>
</div>
</div>
<div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm">
<h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-6">
<span class="material-icons-round text-primary">list_alt</span>
                            Resumen General de Estadísticas
                        </h4>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/40 p-6 rounded-lg">
<div class="flex items-center justify-between mb-4">
<span class="text-sm text-slate-600 dark:text-slate-300 font-semibold">Pedidos Pendientes</span>
<span class="material-icons-round text-blue-500">schedule</span>
</div>
<h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo $pedidos_pendientes; ?></h3>
<p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Requieren atención inmediata</p>
</div>

<div class="bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/40 p-6 rounded-lg">
<div class="flex items-center justify-between mb-4">
<span class="text-sm text-slate-600 dark:text-slate-300 font-semibold">Pedidos Confirmados</span>
<span class="material-icons-round text-green-500">check_circle</span>
</div>
<h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo $pedidos_confirmados; ?></h3>
<p class="text-xs text-slate-600 dark:text-slate-400 mt-2">En proceso de preparación</p>
</div>

<div class="bg-gradient-to-br from-orange-50 to-orange-100 dark:from-orange-900/20 dark:to-orange-900/40 p-6 rounded-lg">
<div class="flex items-center justify-between mb-4">
<span class="text-sm text-slate-600 dark:text-slate-300 font-semibold">Pedidos Enviados</span>
<span class="material-icons-round text-orange-500">local_shipping</span>
</div>
<h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo $pedidos_enviados; ?></h3>
<p class="text-xs text-slate-600 dark:text-slate-400 mt-2">En tránsito hacia clientes</p>
</div>

<div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/40 p-6 rounded-lg">
<div class="flex items-center justify-between mb-4">
<span class="text-sm text-slate-600 dark:text-slate-300 font-semibold">Pedidos Entregados</span>
<span class="material-icons-round text-emerald-500">task_alt</span>
</div>
<h3 class="text-3xl font-bold text-slate-900 dark:text-white"><?php echo $pedidos_entregados; ?></h3>
<p class="text-xs text-slate-600 dark:text-slate-400 mt-2">Completados exitosamente</p>
</div>
</div>
</div>

<div class="bg-white dark:bg-slate-900 p-6 rounded-xl shadow-sm">
<h4 class="font-bold text-slate-800 dark:text-white flex items-center gap-2 mb-6">
<span class="material-icons-round text-primary">list_alt</span>
                            Estados de Pedidos
                        </h4>
<div class="space-y-6">
<div>
<div class="flex justify-between mb-2">
<span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Pendientes</span>
<span class="text-sm font-bold"><?php echo $pedidos_pendientes; ?> (<?php echo $porcentaje_pendientes; ?>%)</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
<div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo $porcentaje_pendientes; ?>%"></div>
</div>
</div>
<div>
<div class="flex justify-between mb-2">
<span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Confirmados</span>
<span class="text-sm font-bold"><?php echo $pedidos_confirmados; ?> (<?php echo $porcentaje_confirmados; ?>%)</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
<div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $porcentaje_confirmados; ?>%"></div>
</div>
</div>
<div>
<div class="flex justify-between mb-2">
<span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Enviados</span>
<span class="text-sm font-bold"><?php echo $pedidos_enviados; ?> (<?php echo $porcentaje_enviados; ?>%)</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
<div class="bg-orange-500 h-2 rounded-full" style="width: <?php echo $porcentaje_enviados; ?>%"></div>
</div>
</div>
<div>
<div class="flex justify-between mb-2">
<span class="text-sm text-slate-600 dark:text-slate-400 font-medium">Entregados</span>
<span class="text-sm font-bold"><?php echo $pedidos_entregados; ?> (<?php echo $porcentaje_entregados; ?>%)</span>
</div>
<div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2">
<div class="bg-emerald-500 h-2 rounded-full" style="width: <?php echo $porcentaje_entregados; ?>%"></div>
</div>
</div>
</div>
</div>
</main>
</div>
</div>

<!-- Modal para cerrar sesión -->
<div id="modalCerrarSesion" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-lg p-8 max-w-sm w-full mx-4 animate-fade-in">
        <div class="flex items-center justify-center mb-4">
            <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-full">
                <span class="material-icons-round text-red-600 dark:text-red-400 text-2xl">logout</span>
            </div>
        </div>
        <h3 class="text-xl font-bold text-center text-slate-900 dark:text-white mb-2">Cerrar Sesión</h3>
        <p class="text-center text-slate-600 dark:text-slate-400 mb-6">¿Estás seguro que deseas cerrar sesión?</p>
        <div class="flex gap-3">
            <button onclick="cerrarModalSesion()" class="flex-1 px-4 py-2 bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 rounded-lg font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                Cancelar
            </button>
            <button onclick="confirmarCerrarSesion()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition-colors">
                Cerrar Sesión
            </button>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out;
    }
</style>

<script>
function loadPage(page, event) {
    if (event) {
        event.preventDefault();
    }
    
    const mainContent = document.getElementById('main-content');
    const pageTitle = document.getElementById('page-title');
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Actualizar el título según la página (verificar solo el nombre del archivo)
    if (page.includes('Dashboard.php')) {
        pageTitle.textContent = 'Dashboard';
    } else if (page.includes('gestion_productos.php') || page.includes('productos.php')) {
        pageTitle.textContent = 'Productos';
    } else if (page.includes('categoria.php')) {
        pageTitle.textContent = 'Categorías';
    } else if (page.includes('clientes.php')) {
        pageTitle.textContent = 'Clientes';
    } else if (page.includes('pedidosadmin.php')) {
        pageTitle.textContent = 'Pedidos';
    } else if (page.includes('usuarios.php')) {
        pageTitle.textContent = 'Usuarios';
    } else if (page.includes('mensajeria.php')) {
        pageTitle.textContent = 'Mensajería';
    } else if (page.includes('admin_compras.php')) {
    pageTitle.textContent = 'Compras';
    }else if (page.includes('admin_reportes.php')) {
    pageTitle.textContent = 'Reportes';                      
    } else if (page.includes('configuracion.php')) {
        pageTitle.textContent = 'Configuraciones';
    }
    
    // Actualizar el estado activo del sidebar (extraer solo el nombre del archivo)
    const fileName = page.split('/').pop(); // Obtener solo el nombre del archivo
    navLinks.forEach(link => {
        link.classList.remove('sidebar-active');
        link.classList.add('text-slate-400', 'hover:text-white', 'hover:bg-slate-800');
        
        const onclick = link.getAttribute('onclick');
        if (onclick && (onclick.includes(fileName) || onclick.includes(page))) {
            link.classList.remove('text-slate-400', 'hover:text-white', 'hover:bg-slate-800');
            link.classList.add('sidebar-active');
        }
    });
    
    // Cargar el contenido dinámicamente
    if (page.includes('Dashboard.php')) {
        // Mostrar el dashboard por defecto
        location.reload();
    } else {
        fetch(page)
            .then(response => response.text())
            .then(data => {
                // Crear un contenedor temporal para parsear el HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data;
                
                // Extraer estilos
                const styles = tempDiv.querySelectorAll('style');
                styles.forEach(style => {
                    document.head.appendChild(style.cloneNode(true));
                });
                
                // Obtener solo el contenido HTML (sin estilos)
                let htmlContent = data;
                htmlContent = htmlContent.replace(/<style[\s\S]*?<\/style>/g, '');
                
                // Extraer scripts para ejecutarlos después
                const scripts = [];
                const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
                let match;
                while ((match = scriptRegex.exec(data)) !== null) {
                    scripts.push(match[1]);
                }
                
                // Remover scripts del HTML
                htmlContent = htmlContent.replace(/<script[\s\S]*?<\/script>/g, '');
                
                mainContent.innerHTML = htmlContent;
                
                // Limpiar scripts dinámicos anteriores antes de inyectar nuevos
                document.querySelectorAll('script[data-dynamic-page]').forEach(s => s.remove());
                
                // Ejecutar scripts después de insertar el HTML
                scripts.forEach(scriptContent => {
                    const script = document.createElement('script');
                    script.textContent = scriptContent;
                    script.setAttribute('data-dynamic-page', 'true');
                    document.body.appendChild(script);
                });
            })
            .catch(error => {
                mainContent.innerHTML = '<div class="text-center text-red-500"><p>Error al cargar la página</p></div>';
                console.error('Error:', error);
            });
    }
}

// Funciones para el modal de productos
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

// Función para toggle de formularios en categorías y otras páginas
function toggleFormulario(type) {
    const formulario = document.getElementById('formulario-' + type);
    if (formulario) {
        formulario.classList.toggle('hidden');
    }
}

// Cargar el dashboard por defecto al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    const mainContent = document.getElementById('main-content');
    // El dashboard ya tiene contenido por defecto
});

// Listener global para cerrar modales con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeFormModal();
        cerrarModalSesion();
    }
});

// Función para cerrar sesión
function cerrarSesion() {
    document.getElementById('modalCerrarSesion').classList.remove('hidden');
}

// Función para cerrar el modal sin cerrar sesión
function cerrarModalSesion() {
    document.getElementById('modalCerrarSesion').classList.add('hidden');
}

// Función para confirmar y cerrar sesión
function confirmarCerrarSesion() {
    window.location.href = '../core/cerrar_sesion.php';
}

// ========= CustomModal - Modal personalizado para alertas/confirmaciones =========
const CustomModal = {
    show: function(type, title, message, callback) {
        let modal = document.getElementById('customModal');
        if (modal) modal.remove();
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

        const iconMap = {
            'success': '<i class="fas fa-check-circle text-3xl text-green-500"></i>',
            'error': '<i class="fas fa-times-circle text-3xl text-red-500"></i>',
            'warning': '<i class="fas fa-exclamation-triangle text-3xl text-yellow-500"></i>',
            'info': '<i class="fas fa-info-circle text-3xl text-blue-500"></i>',
            'confirm': '<i class="fas fa-question-circle text-3xl text-blue-500"></i>'
        };

        document.getElementById('modalIcon').innerHTML = iconMap[type] || iconMap['info'];
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;

        const buttonsDiv = document.getElementById('modalButtons');
        buttonsDiv.innerHTML = '';

        if (type === 'confirm') {
            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'flex-1 bg-slate-200 hover:bg-slate-300 text-slate-900 font-semibold py-2 px-4 rounded-lg transition-colors';
            cancelBtn.textContent = 'Cancelar';
            cancelBtn.onclick = () => { modal.remove(); if (callback) callback(false); };

            const confirmBtn = document.createElement('button');
            confirmBtn.className = 'flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors';
            confirmBtn.textContent = 'Aceptar';
            confirmBtn.onclick = () => { modal.remove(); if (callback) callback(true); };

            buttonsDiv.appendChild(cancelBtn);
            buttonsDiv.appendChild(confirmBtn);
        } else {
            const okBtn = document.createElement('button');
            okBtn.className = 'w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition-colors';
            okBtn.textContent = 'Aceptar';
            okBtn.onclick = () => { modal.remove(); if (callback) callback(); };
            buttonsDiv.appendChild(okBtn);
        }
    }
};
window.CustomModal = CustomModal;
</script>
<!-- Modal Éxito -->
<div id="modalSuccess" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg w-96 p-6 text-center">
    <h2 class="text-xl font-bold text-green-600 mb-4">
      ✅ Cliente agregado correctamente
    </h2>

    <p class="text-gray-600 mb-6">
      El cliente fue registrado exitosamente en el sistema.
    </p>

    <button onclick="closeSuccessModal()" 
      class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
      Aceptar
    </button>
  </div>
</div>

<!-- Modal de Advertencia de Sesión -->
<?php include '../core/modal_advertencia_sesion.html'; ?>

<!-- Script de Sistema de Advertencia de Sesión -->
<script src="../js/advertencia_sesion.js"></script>

</body></html>