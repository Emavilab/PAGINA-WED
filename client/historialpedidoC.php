<?php

/*
====================================================================
MÓDULO: HISTORIAL DE PEDIDOS DEL CLIENTE
====================================================================

DESCRIPCIÓN:
Este módulo permite a los clientes visualizar el historial completo
de sus pedidos dentro del sistema de comercio electrónico.

FUNCIONALIDADES PRINCIPALES:
✔ Ver listado de pedidos realizados
✔ Mostrar fecha del pedido
✔ Mostrar estado del pedido
✔ Mostrar total del pedido
✔ Ver detalles de cada pedido
✔ Cancelar pedidos pendientes dentro de un tiempo límite
✔ Sistema de paginación de pedidos
✔ Contador de tiempo para cancelación automática

CARACTERÍSTICAS:
- Seguridad mediante sesión de usuario
- Paginación para optimizar consultas
- Interfaz moderna usando TailwindCSS
- Contador dinámico para cancelación de pedidos
- Modal dinámico para ver detalles del pedido
- Modal de confirmación para cancelar pedidos

DEPENDENCIAS:
- ../core/sesiones.php
- ../core/conexion.php
- Base de datos con tablas:
    pedidos
    clientes
    configuracion

AUTOR: Sistema RetailCMS
====================================================================
*/

/*
---------------------------------------------------------
CONFIGURACIÓN DE ZONA HORARIA
---------------------------------------------------------
Se establece la zona horaria del sistema para que
las fechas y horas coincidan con Honduras.
*/
date_default_timezone_set('America/Tegucigalpa');

/*
---------------------------------------------------------
CARGAR SISTEMA DE SESIONES
---------------------------------------------------------
Este archivo permite:
- verificar si el usuario está autenticado
- obtener datos del usuario logueado
*/
require_once '../core/sesiones.php';

/*
---------------------------------------------------------
VERIFICAR AUTENTICACIÓN DEL USUARIO
---------------------------------------------------------
Si el usuario no está autenticado se redirige al login.
*/
if (!usuarioAutenticado()) {
    echo "<script>window.location='?modulo=login';</script>";
    exit();
}

/*
---------------------------------------------------------
OBTENER DATOS DEL USUARIO AUTENTICADO
---------------------------------------------------------
*/
$usuario = obtenerDatosUsuario();
$id_cliente = $usuario['id_cliente'] ?? null;

/*
---------------------------------------------------------
VALIDAR QUE EL USUARIO TENGA CLIENTE ASOCIADO
---------------------------------------------------------
*/
if (!$id_cliente) {
    echo "<script>window.location='?modulo=login';</script>";
    exit();
}


/*
====================================================================
CONFIGURACIÓN DE PAGINACIÓN
====================================================================
Se utiliza paginación para evitar cargar demasiados
registros al mismo tiempo.
*/

$por_pagina = 10;

/* Página actual */
$pagina_actual = (isset($_GET['page']) && (int)$_GET['page'] > 0) ? (int)$_GET['page'] : 1;

/* Offset para la consulta SQL */
$offset = ($pagina_actual - 1) * $por_pagina;


/*
---------------------------------------------------------
OBTENER TOTAL DE PEDIDOS DEL CLIENTE
---------------------------------------------------------
Esto se usa para calcular el número de páginas.
*/
$sql_total = "SELECT COUNT(*) AS total FROM pedidos WHERE id_cliente = ?";

$stmt_total = $conexion->prepare($sql_total);
$stmt_total->bind_param("i", $id_cliente);
$stmt_total->execute();
$res_total = $stmt_total->get_result();

$total_pedidos = 0;

if ($row_total = $res_total->fetch_assoc()) {
    $total_pedidos = (int)$row_total['total'];
}

/*
---------------------------------------------------------
CALCULAR TOTAL DE PÁGINAS
---------------------------------------------------------
*/
$total_paginas = max(1, ceil($total_pedidos / $por_pagina));


/*
====================================================================
CONSULTA DE PEDIDOS PAGINADOS
====================================================================
Se obtienen únicamente los pedidos correspondientes
a la página actual.
*/

$sql = "SELECT id_pedido, fecha_pedido, estado, total 
        FROM pedidos 
        WHERE id_cliente = ? 
        ORDER BY fecha_pedido DESC
        LIMIT ? OFFSET ?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("iii", $id_cliente, $por_pagina, $offset);
$stmt->execute();
$resultado = $stmt->get_result();


/*
====================================================================
CARGAR CONFIGURACIÓN GENERAL DEL SISTEMA
====================================================================
Se obtienen colores personalizados desde la base de datos
para el tema visual del sistema.
*/

$res_cfg_of = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");

$cfg_of = ($res_cfg_of && mysqli_num_rows($res_cfg_of) > 0) ? mysqli_fetch_assoc($res_cfg_of) : [];


/*
---------------------------------------------------------
FUNCIÓN: NORMALIZAR COLOR HEXADECIMAL
---------------------------------------------------------
Valida que el color tenga formato HEX (#FFFFFF)
*/
function normalizar_color_ofertas($valor, $defecto) {
    if (!is_string($valor)) return $defecto;
    $valor = trim($valor);
    if ($valor === '') return $defecto;
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) return $defecto;
    return strtoupper($valor);
}

/*
---------------------------------------------------------
COLORES DEL SISTEMA
---------------------------------------------------------
*/
$of_primary = normalizar_color_ofertas($cfg_of['color_primary'] ?? '#137fec', '#137FEC');
$of_bg_light = normalizar_color_ofertas($cfg_of['color_background_light'] ?? '#f6f7f8', '#F6F7F8');
$of_bg_dark = normalizar_color_ofertas($cfg_of['color_background_dark'] ?? '#101922', '#101922');

?>

<!DOCTYPE html>
<html class="light" lang="es">

<!--
====================================================================
INTERFAZ DE HISTORIAL DE PEDIDOS
====================================================================

El siguiente bloque corresponde al frontend del módulo.

TECNOLOGÍAS UTILIZADAS:
✔ TailwindCSS
✔ Google Fonts (Inter)
✔ Material Icons
✔ Material Symbols

CARACTERÍSTICAS:
✔ Interfaz responsive
✔ Compatible con modo oscuro
✔ Tabla dinámica de pedidos
✔ Modal de detalles de pedido
✔ Modal de confirmación de cancelación
✔ Contador dinámico para cancelar pedidos

====================================================================
-->

<head>

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>

<title>Mis Pedidos - RetailCMS</title>

<!-- TailwindCSS -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

<!-- Fuentes -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>

<!-- Iconos -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>

<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>


<!--
====================================================================
CONFIGURACIÓN DINÁMICA DE TAILWIND
====================================================================
Los colores se cargan desde la base de datos.
-->
<script id="tailwind-config">

tailwind.config = {

    darkMode: "class",

    theme: {
        extend: {

            colors: {
                "primary": $of_primary,
                "background-light": $of_bg_light,
                "background-dark": $of_bg_dark,
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

/*
---------------------------------------------------------
ESTILOS GENERALES
---------------------------------------------------------
*/

body {
    font-family: 'Inter', sans-serif;
}

/*
---------------------------------------------------------
CONFIGURACIÓN DE ICONOS MATERIAL SYMBOLS
---------------------------------------------------------
*/
.material-symbols-outlined {
font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 48;
}

/*
---------------------------------------------------------
ESTILOS DE ESTADOS DE PEDIDOS
---------------------------------------------------------
*/

.status-badge-entregado { background-color: #d1fae5; color: #047857; }
.dark .status-badge-entregado { background-color: rgba(5, 150, 105, 0.3); color: #6ee7b7; }

.status-badge-camino { background-color: #dbeafe; color: #1e40af; }
.dark .status-badge-camino { background-color: rgba(30, 58, 138, 0.3); color: #60a5fa; }

.status-badge-procesando { background-color: #fef3c7; color: #b45309; }
.dark .status-badge-procesando { background-color: rgba(180, 83, 9, 0.3); color: #fbbf24; }

.status-badge-cancelado { background-color: #fee2e2; color: #b91c1c; }
.dark .status-badge-cancelado { background-color: rgba(185, 28, 28, 0.3); color: #fca5a5; }

</style>

<!--
====================================================================
CONTENIDO PRINCIPAL
====================================================================
Tabla que muestra los pedidos del cliente.
Incluye:

✔ ID del pedido
✔ Fecha
✔ Estado
✔ Total
✔ Botón de ver detalles
✔ Botón de cancelar pedido
✔ Contador de cancelación
✔ Sistema de paginación
====================================================================
-->
 