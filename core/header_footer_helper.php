<?php
/**
 * Funciones auxiliares para Header y Footer
 * Importa este archivo en tus archivos principales (index.php, categorias.php, etc.)
 * 
 * Uso:
 *   <?php include 'core/header_footer_helper.php'; ?>
 *   <?php mostrar_header(); ?>
 *   ... contenido de la página ...
 *   <?php mostrar_footer(); ?>
 */

require_once __DIR__ . '/conexion.php';

/**
 * Obtiene el header desde la base de datos
 * @return string HTML del header
 */
function obtener_header() {
    global $conexion;
    
    $query = "SELECT contenido, estado FROM header_footer WHERE tipo = 'header' AND estado = 'activo'";
    $resultado = $conexion->query($query);
    
    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        return $fila['contenido'];
    }
    
    return ''; // Retorna vacío si no hay header
}

/**
 * Obtiene el footer desde la base de datos
 * @return string HTML del footer
 */
function obtener_footer() {
    global $conexion;
    
    $query = "SELECT contenido, estado FROM header_footer WHERE tipo = 'footer' AND estado = 'activo'";
    $resultado = $conexion->query($query);
    
    if ($resultado && $resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        return $fila['contenido'];
    }
    
    return ''; // Retorna vacío si no hay footer
}

/**
 * Muestra el header directamente
 */
function mostrar_header() {
    echo obtener_header();
}

/**
 * Muestra el footer directamente
 */
function mostrar_footer() {
    echo obtener_footer();
}

/**
 * Caché de header y footer en memoria durante la misma solicitud
 */
$_cache_header = null;
$_cache_footer = null;

/**
 * Obtiene el header con caché
 * @return string HTML del header
 */
function obtener_header_cache() {
    global $_cache_header;
    
    if ($_cache_header === null) {
        $_cache_header = obtener_header();
    }
    
    return $_cache_header;
}

/**
 * Obtiene el footer con caché
 * @return string HTML del footer
 */
function obtener_footer_cache() {
    global $_cache_footer;
    
    if ($_cache_footer === null) {
        $_cache_footer = obtener_footer();
    }
    
    return $_cache_footer;
}

/**
 * Muestra el header con caché
 */
function mostrar_header_cache() {
    echo obtener_header_cache();
}

/**
 * Muestra el footer con caché
 */
function mostrar_footer_cache() {
    echo obtener_footer_cache();
}
