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