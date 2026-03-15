<?php
/*
========================================================
MODULO: LISTA DE DESEOS (WISHLIST)
========================================================

Este módulo permite a los usuarios visualizar y gestionar
los productos que han guardado como favoritos.

FUNCIONALIDADES PRINCIPALES:
✔ Ver productos guardados en la lista de deseos
✔ Eliminar productos de la lista
✔ Agregar productos al carrito
✔ Compartir la lista de deseos
✔ Mostrar estado si el usuario no ha iniciado sesión

DEPENDENCIAS:
- sesiones.php → controla la autenticación de usuarios
- conexion.php → conexión a la base de datos
- api_lista_deseos.php → API para listar y eliminar productos
- api_carrito.php → API para agregar productos al carrito

AUTOR: Sistema RetailCMS
========================================================
*/

require_once __DIR__ . '/../core/sesiones.php';

/*
--------------------------------------------------------
Verifica si el usuario tiene una sesión activa
--------------------------------------------------------
Devuelve TRUE si el usuario está autenticado
Devuelve FALSE si el usuario no ha iniciado sesión
*/
$usuarioAutenticado = usuarioAutenticado();
?>

<?php
/*
--------------------------------------------------------
CONEXIÓN A BASE DE DATOS
--------------------------------------------------------
Se incluye el archivo que contiene la conexión MySQL
usada en todo el sistema.
*/
require_once '../core/conexion.php';

/*
--------------------------------------------------------
CARGAR CONFIGURACIÓN DE COLORES DEL SISTEMA
--------------------------------------------------------
La tabla "configuracion" almacena los colores globales
que usa la interfaz del sistema.
*/
$res_cfg_of = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");

/*
Si existe configuración se guarda en $cfg_of
de lo contrario se usa un arreglo vacío
*/
$cfg_of = ($res_cfg_of && mysqli_num_rows($res_cfg_of) > 0) ? mysqli_fetch_assoc($res_cfg_of) : [];

/*
--------------------------------------------------------
FUNCIÓN: NORMALIZAR COLORES HEXADECIMALES
--------------------------------------------------------
Valida que el color tenga el formato HEX correcto (#FFFFFF).
Si el valor no es válido, devuelve un color por defecto.
*/
function normalizar_color_ofertas($valor, $defecto) {
    if (!is_string($valor)) return $defecto;
    $valor = trim($valor);
    if ($valor === '') return $defecto;
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) return $defecto;
    return strtoupper($valor);
}

/*
--------------------------------------------------------
COLORES DEL SISTEMA
--------------------------------------------------------
Se obtienen de la base de datos o se asignan valores
por defecto si no existen.
*/
$of_primary = normalizar_color_ofertas($cfg_of['color_primary'] ?? '#137fec', '#137FEC');
$of_bg_light = normalizar_color_ofertas($cfg_of['color_background_light'] ?? '#f6f7f8', '#F6F7F8');
$of_bg_dark = normalizar_color_ofertas($cfg_of['color_background_dark'] ?? '#101922', '#101922');
?>

<!DOCTYPE html>
<html class="light" lang="es">
<head>

<!-- =====================================================
CONFIGURACIÓN GENERAL DEL DOCUMENTO HTML
===================================================== -->

<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>

<title>Lista de Deseos - RetailCMS</title>

<!-- =====================================================
LIBRERÍAS Y ESTILOS
===================================================== -->

<!-- TailwindCSS para estilos -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

<!-- Fuente Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>

<!-- Iconos Material -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>

<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

<!-- =====================================================
CONFIGURACIÓN DE COLORES PARA TAILWIND
===================================================== -->

<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "<?php echo $of_primary; ?>",
                        "background-light": "<?php echo $of_bg_light; ?>",
                        "background-dark": "<?php echo $of_bg_dark; ?>"
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

<!-- =====================================================
ESTILOS PERSONALIZADOS
===================================================== -->

<style type="text/tailwindcss">

        /*
        Iconos con relleno activo
        */
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;
        }

        /*
        Corazón activo (producto en favoritos)
        */
        .heart-active {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 48;
            color: #ef4444;
        }

</style>

<!-- =====================================================
CONTENEDOR PRINCIPAL DE LA LISTA DE DESEOS
===================================================== -->

<div class="max-w-7xl mx-auto px-4 py-8">

    <!-- =====================================================
    ENCABEZADO
    ===================================================== -->

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                Lista de Deseos
            </h1>

            <!-- Cantidad de productos en la lista -->
            <p id="wishlist-count" class="text-slate-500 mt-1">
                Cargando...
            </p>
        </div>

        <!-- Botón para compartir la lista -->
        <button id="shareBtn"
            class="flex items-center gap-2 text-sm font-semibold text-primary hover:bg-primary/5 px-4 py-2 rounded-lg transition-colors">

            <span class="material-symbols-outlined text-lg">share</span>
            Compartir Lista

        </button>
    </div>

    <!-- =====================================================
    GRID DE PRODUCTOS FAVORITOS
    ===================================================== -->

    <div id="wishlist-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Los productos se renderizan dinámicamente con JS -->
    </div>

    <!-- =====================================================
    MENSAJE SI LA LISTA ESTÁ VACÍA
    ===================================================== -->

    <div id="wishlist-empty"
        class="hidden mt-12 text-center py-20 bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">

        <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">
            favorite_border
        </span>

        <h2 class="text-xl font-bold mb-2">
            Tu lista está vacía
        </h2>

        <p class="text-slate-500 mb-8">
            ¡Explora nuestra tienda y guarda tus productos favoritos!
        </p>

        <!-- Botón para volver a la tienda -->
        <button
        onclick="if (typeof loadProductos === 'function') { loadProductos(); } else { window.location.href='../index.php'; }"
        class="px-8 py-3 bg-primary text-white rounded-xl font-bold">

        Empezar a comprar

        </button>

    </div>

    <!-- =====================================================
    MENSAJE SI EL USUARIO NO HA INICIADO SESIÓN
    ===================================================== -->

    <div id="wishlist-need-login"
        class="hidden mt-12 text-center py-20 bg-white dark:bg-slate-900 rounded-3xl border border-dashed border-slate-300 dark:border-slate-700">

        <span class="material-symbols-outlined text-6xl text-slate-300 mb-4">
            person_off
        </span>

        <h2 class="text-xl font-bold mb-2">
            Inicia sesión para ver tu lista
        </h2>

        <p class="text-slate-500 mb-8">
            La lista de deseos es una función para usuarios registrados.
        </p>

        <!-- Botón para iniciar sesión -->
        <button
        onclick="if (typeof loadLogin === 'function') { loadLogin(); } else { window.location.href='../pages/login.php'; }"
        class="px-8 py-3 bg-primary text-white rounded-xl font-bold">

        Iniciar Sesión

        </button>

    </div>

</div>

<!-- =====================================================
SCRIPT PRINCIPAL DE LA LISTA DE DESEOS
===================================================== -->

<script>

/*
--------------------------------------------------------
Variable global que indica si el usuario está autenticado
--------------------------------------------------------
*/
window._usuarioAutenticado = <?php echo $usuarioAutenticado ? 'true' : 'false'; ?>;

/*
--------------------------------------------------------
FUNCIÓN: Mostrar notificación tipo toast
--------------------------------------------------------
*/
function showToast(msg) {

    let t = document.getElementById('globalToast');

    if (!t) {
        t = document.createElement('div');
        t.id = 'globalToast';

        t.className = 'fixed bottom-6 right-6 z-[200] bg-green-600 text-white px-5 py-3 rounded-xl shadow-2xl flex items-center gap-2 text-sm font-semibold transition-opacity opacity-0';

        document.body.appendChild(t);
    }

    t.textContent = msg;
    t.style.opacity = '1';

    setTimeout(() => {
        t.style.opacity = '0';
    }, 2200);

}

/*
--------------------------------------------------------
FUNCIÓN: Cargar lista de deseos desde la API
--------------------------------------------------------
*/
function fetchWishlist() {

    const grid = document.getElementById('wishlist-grid');
    const empty = document.getElementById('wishlist-empty');
    const needLogin = document.getElementById('wishlist-need-login');
    const countEl = document.getElementById('wishlist-count');

    grid.innerHTML = '';
    empty.classList.add('hidden');
    needLogin.classList.add('hidden');

    /*
    Si el usuario no está autenticado,
    mostrar mensaje para iniciar sesión
    */
    if (!window._usuarioAutenticado) {
        needLogin.classList.remove('hidden');
        countEl.textContent = '';
        return;
    }

    countEl.textContent = 'Cargando...';

    /*
    Petición a la API para obtener la lista
    */
    fetch('api/api_lista_deseos.php?accion=listar', { credentials: 'same-origin' })

        .then(response => response.text())

        .then(text => {

            let data;

            /*
            Intentar convertir la respuesta a JSON
            */
            try {

                data = JSON.parse(text);

            } catch (e) {

                console.error('api_lista_deseos respuesta no JSON:', text);

                grid.innerHTML =
                '<div class="col-span-full text-center py-6 text-slate-500">Respuesta inválida del servidor.</div>';

                countEl.textContent = 'Error';

                return;
            }

            /*
            Validar respuesta
            */
            if (!data || !data.exito) {

                grid.innerHTML =
                '<div class="col-span-full text-center py-12 text-slate-500">No se pudo cargar la lista.</div>';

                countEl.textContent = '';

                return;
            }

            /*
            Obtener productos de la lista
            */
            const items = data.items || [];

            countEl.textContent =
                items.length + (items.length === 1 ? ' artículo' : ' artículos');

            /*
            Si no hay productos
            */
            if (items.length === 0) {
                empty.classList.remove('hidden');
                return;
            }

            /*
            Renderizar cada producto
            */
            items.forEach(it => {

                const img =
                it.imagen ? it.imagen : 'https://via.placeholder.com/400x400?text=Sin+Imagen';

                const precioOriginal =
                parseFloat(it.precio || 0).toFixed(2);

                const enOferta =
                it.en_oferta == 1 && it.precio_descuento;

                const precioFinal =
                enOferta ? parseFloat(it.precio_descuento).toFixed(2) : precioOriginal;

                const card = document.createElement('div');

                card.className =
                'bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden group hover:shadow-xl transition-shadow';
                    card.innerHTML = `
                    <div class="relative aspect-square overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img alt="${escapeHtml(it.nombre)}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" src="${img}" onerror="this.src='https://via.placeholder.com/400x400?text=Error'"/>
                        <button onclick="removeFromWishlist(this, ${it.id_producto})" class="absolute top-3 right-3 w-8 h-8 bg-white/90 dark:bg-slate-900/90 rounded-full flex items-center justify-center text-primary shadow-sm">
                            <span class="material-symbols-outlined heart-active text-xl">favorite</span>
                        </button>
                    </div>
                    <div class="p-5">
                        <p class="text-[10px] font-bold text-primary uppercase tracking-wider mb-1">-</p>
                        <h3 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-1">${escapeHtml(it.nombre)}</h3>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-xl font-black text-slate-900 dark:text-white">${precioFinal} €</span>
                            ${enOferta ? '<span class="text-sm text-slate-400 line-through">' + precioOriginal + ' €</span>' : ''}
                        </div>
                        <div class="space-y-2">
                            <button onclick="agregarAlCarritoDesdeWishlist(this, ${it.id_producto})" class="w-full py-2.5 bg-primary hover:bg-primary/90 text-white rounded-lg font-bold text-sm transition-colors flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined text-lg">shopping_cart</span> Agregar al Carrito
                            </button>
                            <button onclick="removeFromWishlist(this, ${it.id_producto})" class="w-full py-2 text-slate-400 hover:text-red-500 text-xs font-medium transition-colors flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-sm">delete_outline</span> Eliminar
                            </button>
                        </div>
                    </div>`;
                grid.appendChild(card);

            });

        })

        .catch(err => {

            console.error('fetch lista deseos error:', err);
             document.getElementById('wishlist-grid').innerHTML = '<div class="col-span-full text-center py-12 text-red-500">Error de conexión: ' + escapeHtml((err && err.message) ? err.message : String(err)) + '</div>';
            countEl.textContent = 'Error de conexión';
        });

}

/*
--------------------------------------------------------
Eliminar producto de la lista de deseos
--------------------------------------------------------
*/
function removeFromWishlist(btn, idProducto) {

    try { btn.disabled = true; } catch(e) {}

    const fd = new FormData();

    fd.append('accion', 'eliminar');
    fd.append('id_producto', idProducto);

    fetch('api/api_lista_deseos.php', { method: 'POST', body: fd })

        .then(r => r.json())

        .then(data => {

            if (data.exito) {

                showToast('Producto eliminado de la lista');

                fetchWishlist();

            } else {

                alert(data.error || 'No se pudo eliminar');

            }

        })

        .catch(() => alert('Error de conexión'))

        .finally(() => { try { btn.disabled = false; } catch(e) {} });

}

/*
--------------------------------------------------------
Agregar producto al carrito desde la lista
--------------------------------------------------------
*/
function agregarAlCarritoDesdeWishlist(btn, idProducto) {

    try { btn.disabled = true; } catch(e) {}

    const fd = new FormData();

    fd.append('accion', 'agregar');
    fd.append('id_producto', idProducto);
    fd.append('cantidad', 1);

    fetch('api/api_carrito.php', { method: 'POST', body: fd })

        .then(r => r.json())

        .then(data => {

            if (data.exito) {

                showToast('Producto agregado al carrito');

            } else {

                alert(data.error || 'Error al agregar al carrito');

            }

        })

        .catch(() => alert('Error de conexión'))

        .finally(() => { try { btn.disabled = false; } catch(e) {} });

}

/*
--------------------------------------------------------
Escapar HTML para evitar XSS
--------------------------------------------------------
*/
function escapeHtml(str) {

    if (!str) return '';

    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\"/g, '&quot;')
        .replace(/'/g, '&#39;');

}

/*
--------------------------------------------------------
Evento al cargar la página
--------------------------------------------------------
*/
document.addEventListener('DOMContentLoaded', function() {

    fetchWishlist();

    /*
    Botón para compartir la lista
    */
    const share = document.getElementById('shareBtn');

    if (share)

    share.addEventListener('click', function() {

        try {

            navigator.clipboard.writeText(window.location.href);

            showToast('Enlace copiado al portapapeles');

        } catch(e) {

            alert('No se pudo copiar');

        }

    });

});
</script>

</body>
</html> 