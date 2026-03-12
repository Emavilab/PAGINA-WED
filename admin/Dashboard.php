<?php
/*
========================================================
MODULO: DASHBOARD ADMINISTRATIVO
========================================================

Este archivo corresponde al panel principal del sistema
administrativo de la tienda o negocio.

FUNCIONES PRINCIPALES:
✔ Verificar que el usuario esté autenticado
✔ Validar permisos de acceso (Administrador o Vendedor)
✔ Obtener datos del usuario activo
✔ Cargar configuración general del sistema
✔ Obtener estadísticas del sistema desde la base de datos
✔ Mostrar resumen visual de productos, clientes y pedidos
✔ Mostrar ingresos del día
✔ Mostrar estados de pedidos con porcentajes
✔ Controlar navegación dinámica entre módulos

ROLES PERMITIDOS:
1 - Administrador
2 - Vendedor

MODULOS ACCESIBLES DESDE EL DASHBOARD:
- Dashboard
- Productos
- Categorías
- Clientes
- Pedidos
- Usuarios
- Mensajería
- Compras
- Reportes
- Configuración

TECNOLOGIAS UTILIZADAS:
- PHP
- MySQL
- TailwindCSS
- JavaScript
- Material Icons
- FontAwesome

AUTOR: Sistema Web
========================================================
*/


/* ====================================================
   CARGA DE ARCHIVOS DEL SISTEMA
   - sesiones.php controla autenticación
   - conexion.php conecta a la base de datos
   - validador_inactividad.php controla sesiones
==================================================== */

require_once '../core/sesiones.php';
require_once '../core/conexion.php';
require_once '../core/validador_inactividad.php';



/* ====================================================
   VALIDAR SI EL USUARIO ESTA AUTENTICADO
   Si no lo está se redirige al login
==================================================== */

if (!usuarioAutenticado()) {
    header("Location: ../pages/login.php");
    exit();
}



/* ====================================================
   VALIDAR PERMISOS DEL USUARIO
   Solo Administrador (1) y Vendedor (2)
==================================================== */

if ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2) {

    // Usuario sin permisos
    header("Location: ../index.php");
    exit();
}



/* ====================================================
   OBTENER DATOS DEL USUARIO ACTIVO
==================================================== */

$usuario = obtenerDatosUsuario();



/* ====================================================
   CARGAR CONFIGURACION GENERAL DEL SISTEMA
   Se obtienen datos como:
   - nombre del negocio
   - moneda
   - colores del panel administrativo
==================================================== */

$res_cfg_admin = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");

$cfg_admin = ($res_cfg_admin && mysqli_num_rows($res_cfg_admin) > 0)
    ? mysqli_fetch_assoc($res_cfg_admin)
    : [];



/* ====================================================
   CONFIGURACION DE MONEDA
==================================================== */

$cfg_moneda_cod = $cfg_admin['moneda'] ?? 'HNL';

$simbolos_moneda = [
    'USD' => '$',
    'EUR' => '€',
    'MXN' => '$',
    'COP' => '$',
    'ARS' => '$',
    'GTQ' => 'Q',
    'HNL' => 'L',
    'CRC' => '₡'
];

$cfg_moneda = $simbolos_moneda[$cfg_moneda_cod] ?? $cfg_moneda_cod;



/* ====================================================
   OBTENER ESTADISTICAS DEL SISTEMA
   Estas estadísticas se muestran en el dashboard
==================================================== */



/* -------------------------
   TOTAL DE PRODUCTOS
------------------------- */

$res_productos = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM productos");

$total_productos = ($res_productos && mysqli_num_rows($res_productos) > 0)
    ? mysqli_fetch_assoc($res_productos)['total']
    : 0;



/* -------------------------
   TOTAL DE CLIENTES
------------------------- */

$res_clientes = mysqli_query($conexion, "SELECT COUNT(*) AS total FROM clientes");

$total_clientes = ($res_clientes && mysqli_num_rows($res_clientes) > 0)
    ? mysqli_fetch_assoc($res_clientes)['total']
    : 0;



/* -------------------------
   PEDIDOS DEL DIA
------------------------- */

$hoy = date('Y-m-d');

$res_pedidos_hoy = mysqli_query(
    $conexion,
    "SELECT COUNT(*) AS total FROM pedidos WHERE DATE(fecha_pedido) = '$hoy'"
);

$pedidos_hoy = ($res_pedidos_hoy && mysqli_num_rows($res_pedidos_hoy) > 0)
    ? mysqli_fetch_assoc($res_pedidos_hoy)['total']
    : 0;



/* -------------------------
   INGRESOS DEL DIA
   Solo pedidos pagados
------------------------- */

$res_ingresos_hoy = mysqli_query(
    $conexion,
    "SELECT SUM(total) AS total
     FROM pedidos
     WHERE DATE(fecha_pedido) = '$hoy'
     AND estado IN ('confirmado','enviado','entregado')"
);

$resultado_ingresos = mysqli_fetch_assoc($res_ingresos_hoy);

$ingresos_hoy = !empty($resultado_ingresos['total'])
    ? floatval($resultado_ingresos['total'])
    : 0;



/* ====================================================
   ESTADISTICAS POR ESTADO DE PEDIDOS
==================================================== */

$res_pendientes = mysqli_query($conexion,"SELECT COUNT(*) AS total FROM pedidos WHERE estado='pendiente'");
$pedidos_pendientes = ($res_pendientes && mysqli_num_rows($res_pendientes)>0) ? mysqli_fetch_assoc($res_pendientes)['total'] : 0;

$res_confirmados = mysqli_query($conexion,"SELECT COUNT(*) AS total FROM pedidos WHERE estado='confirmado'");
$pedidos_confirmados = ($res_confirmados && mysqli_num_rows($res_confirmados)>0) ? mysqli_fetch_assoc($res_confirmados)['total'] : 0;

$res_enviados = mysqli_query($conexion,"SELECT COUNT(*) AS total FROM pedidos WHERE estado='enviado'");
$pedidos_enviados = ($res_enviados && mysqli_num_rows($res_enviados)>0) ? mysqli_fetch_assoc($res_enviados)['total'] : 0;

$res_entregados = mysqli_query($conexion,"SELECT COUNT(*) AS total FROM pedidos WHERE estado='entregado'");
$pedidos_entregados = ($res_entregados && mysqli_num_rows($res_entregados)>0) ? mysqli_fetch_assoc($res_entregados)['total'] : 0;



/* ====================================================
   TOTAL GENERAL DE PEDIDOS
==================================================== */

$total_pedidos =
    $pedidos_pendientes +
    $pedidos_confirmados +
    $pedidos_enviados +
    $pedidos_entregados;



/* ====================================================
   CALCULO DE PORCENTAJES PARA GRAFICOS
==================================================== */

$porcentaje_pendientes  = $total_pedidos>0 ? round(($pedidos_pendientes/$total_pedidos)*100) : 0;
$porcentaje_confirmados = $total_pedidos>0 ? round(($pedidos_confirmados/$total_pedidos)*100) : 0;
$porcentaje_enviados    = $total_pedidos>0 ? round(($pedidos_enviados/$total_pedidos)*100) : 0;
$porcentaje_entregados  = $total_pedidos>0 ? round(($pedidos_entregados/$total_pedidos)*100) : 0;



/* ====================================================
   FUNCION PARA VALIDAR COLORES HEXADECIMALES
   Se usa para personalizar colores del dashboard
==================================================== */

function normalizar_color_admin($valor,$defecto){

    if(!is_string($valor)) return $defecto;

    $valor = trim($valor);

    if($valor==='') return $defecto;

    if(!preg_match('/^#[0-9A-Fa-f]{6}$/',$valor)) return $defecto;

    return strtoupper($valor);
}



/* ====================================================
   COLORES DEL PANEL ADMINISTRATIVO
==================================================== */

$admin_primary = normalizar_color_admin($cfg_admin['color_primary'] ?? '#3b82f6','#3B82F6');

$admin_bg_light = normalizar_color_admin($cfg_admin['color_background_light'] ?? '#f8fafc','#F8FAFC');

$admin_bg_dark = normalizar_color_admin($cfg_admin['color_background_dark'] ?? '#0f172a','#0F172A');

$admin_sidebar_dark = '#1e293b';



/* ====================================================
   NOMBRE DEL NEGOCIO
==================================================== */

$admin_nombre = htmlspecialchars($cfg_admin['nombre_negocio'] ?? 'Mi Negocio');
?>
<!DOCTYPE html>
<html class="light" lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Dashboard Administrativo Profesional</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
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
      
      // ===================== FUNCIONES SIDEBAR MÓVIL =====================
      function toggleSidebarMobile() {
          const sidebar = document.getElementById('sidebarMobile');
          const overlay = document.getElementById('sidebarOverlay');
          
          if (sidebar && overlay) {
              if (sidebar.classList.contains('translate-x-0')) {
                  // Cerrar
                  sidebar.classList.remove('translate-x-0');
                  sidebar.classList.add('-translate-x-full');
                  overlay.classList.add('hidden');
                  document.body.style.overflow = '';
              } else {
                  // Abrir
                  sidebar.classList.remove('-translate-x-full');
                  sidebar.classList.add('translate-x-0');
                  overlay.classList.remove('hidden');
                  document.body.style.overflow = 'hidden';
              }
          }
      }
      
      function closeSidebarMobile() {
          const sidebar = document.getElementById('sidebarMobile');
          const overlay = document.getElementById('sidebarOverlay');
          if (sidebar && overlay) {
              sidebar.classList.add('-translate-x-full');
              sidebar.classList.remove('translate-x-0');
              overlay.classList.add('hidden');
              document.body.style.overflow = '';
          }
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
<!-- SIDEBAR DESKTOP -->
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

?>