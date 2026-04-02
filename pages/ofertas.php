<?php
require_once '../core/conexion.php';

// Cargar configuración general de colores
$res_cfg_of = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");
$cfg_of = ($res_cfg_of && mysqli_num_rows($res_cfg_of) > 0) ? mysqli_fetch_assoc($res_cfg_of) : [];

function normalizar_color_ofertas($valor, $defecto) {
    if (!is_string($valor)) return $defecto;
    $valor = trim($valor);
    if ($valor === '') return $defecto;
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $valor)) return $defecto;
    return strtoupper($valor);
}

$of_primary = normalizar_color_ofertas($cfg_of['color_primary'] ?? '#137fec', '#137FEC');
$of_bg_light = normalizar_color_ofertas($cfg_of['color_background_light'] ?? '#f6f7f8', '#F6F7F8');
$of_bg_dark = normalizar_color_ofertas($cfg_of['color_background_dark'] ?? '#101922', '#101922');
?>
<!DOCTYPE html>
<html lang="es"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Ofertas y Promociones Especiales | Retail CMS</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "<?php echo $of_primary; ?>",
                        "accent": "#ef4444",
                        "background-light": "<?php echo $of_bg_light; ?>",
                        "background-dark": "<?php echo $of_bg_dark; ?>",
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
        body { font-family: 'Inter', sans-serif; }
        .promotion-gradient { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); }
        .product-card:hover .product-actions { opacity: 1; transform: translateY(0); }
    </style>
<main class="max-w-7xl mx-auto px-4 py-8">

<!-- Banner -->
<section class="promotion-gradient rounded-2xl overflow-hidden relative mb-12 shadow-2xl border border-slate-700">
<div class="absolute inset-0 opacity-20 pointer-events-none">
<div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-primary via-transparent to-transparent"></div>
</div>
<div class="relative z-10 p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8">
<div class="text-center md:text-left">
<span class="bg-accent text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest mb-4 inline-block">Flash Sale</span>
<h1 class="text-4xl md:text-6xl font-black text-white mb-4 leading-tight">
                    Venta Flash <br/><span class="text-primary">Hasta -70%</span>
</h1>
<p class="text-slate-300 text-lg mb-6 max-w-md">
                    Solo por tiempo limitado. Descuentos exclusivos en todos los departamentos. ¡No dejes que se escapen!
                </p>
</div>
<div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 md:p-8 rounded-2xl text-white text-center">
<p class="text-xs uppercase font-semibold tracking-tighter mb-4 opacity-80">La oferta termina en:</p>
<div class="flex gap-4 items-start">
<div><div class="text-3xl md:text-4xl font-black tabular-nums" id="of-hours">00</div><div class="text-[10px] uppercase opacity-70">Horas</div></div>
<div class="text-3xl md:text-4xl font-black opacity-50">:</div>
<div><div class="text-3xl md:text-4xl font-black tabular-nums" id="of-mins">00</div><div class="text-[10px] uppercase opacity-70">Minutos</div></div>
<div class="text-3xl md:text-4xl font-black opacity-50">:</div>
<div><div class="text-3xl md:text-4xl font-black tabular-nums" id="of-secs">00</div><div class="text-[10px] uppercase opacity-70">Segundos</div></div>
</div>
</div>
</div>
</section>

<div class="flex flex-col lg:flex-row gap-8">

<!-- Sidebar filtros -->
<aside class="w-full lg:w-64 space-y-8">
<div>
<h3 class="font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-xl">filter_list</span> Filtros
</h3>

<!-- Filtro Categoría -->
<div class="space-y-4 mb-8">
<p class="text-sm font-bold text-slate-700 dark:text-slate-300">Categoría</p>
<div class="space-y-2" id="of-filtro-categorias">
    <p class="text-xs text-slate-400">Cargando...</p>
</div>
</div>

<!-- Filtro Rango de Precio -->
<div class="space-y-4 mb-8">
<p class="text-sm font-bold text-slate-700 dark:text-slate-300">Rango de Precio</p>
<div class="flex items-center gap-2">
    <input id="of-precio-min" type="number" min="0" placeholder="Min" class="w-full text-sm border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1.5 focus:ring-primary focus:border-primary dark:bg-slate-800"/>
    <span class="text-slate-400">-</span>
    <input id="of-precio-max" type="number" min="0" placeholder="Max" class="w-full text-sm border border-slate-200 dark:border-slate-700 rounded-lg px-2 py-1.5 focus:ring-primary focus:border-primary dark:bg-slate-800"/>
</div>
<button onclick="ofAplicarFiltros()" class="w-full text-xs bg-primary text-white py-1.5 rounded-lg font-medium hover:bg-primary/90 transition-colors mt-1">Aplicar precio</button>
</div>

<!-- Filtro Descuento -->
<div class="space-y-4">
<p class="text-sm font-bold text-slate-700 dark:text-slate-300">Descuento</p>
<div class="space-y-2">
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="text-primary focus:ring-primary border-slate-300" name="of-discount" type="radio" value="0" checked onchange="ofAplicarFiltros()"/> Todos
</label>
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="text-primary focus:ring-primary border-slate-300" name="of-discount" type="radio" value="10" onchange="ofAplicarFiltros()"/> -10% o más
</label>
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="text-primary focus:ring-primary border-slate-300" name="of-discount" type="radio" value="30" onchange="ofAplicarFiltros()"/> -30% o más
</label>
<label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 cursor-pointer hover:text-primary">
<input class="text-primary focus:ring-primary border-slate-300" name="of-discount" type="radio" value="50" onchange="ofAplicarFiltros()"/> -50% o más
</label>
</div>
</div>
</div>
<button onclick="ofLimpiarFiltros()" class="w-full py-2 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-lg text-sm font-medium hover:bg-slate-200 transition-colors">
    Limpiar Filtros
</button>
</aside>

<!-- Productos -->
<div class="flex-1">
<div class="flex items-center justify-between mb-6">
<p class="text-sm text-slate-500" id="of-count-text">Cargando ofertas...</p>
<select id="of-ordenar" onchange="ofAplicarFiltros()" class="bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-sm rounded-lg focus:ring-primary">
<option value="relevancia">Ordenar por: Relevancia</option>
<option value="precio-asc">Precio: Menor a Mayor</option>
<option value="precio-desc">Precio: Mayor a Menor</option>
<option value="descuento">Mayor Descuento</option>
</select>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6" id="of-productos-grid">
    <div class="col-span-full flex justify-center py-16">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent"></div>
    </div>
</div>
</div>

</div>
</main>

<script>
// --- Datos ---
let ofTodosProductos = [];
let ofProductosFiltrados = [];

// --- Cargar productos en oferta ---
function ofCargarProductos() {
    fetch('/PAGINA%20WED/api/obtener_productos.php')
        .then(r => r.json())
        .then(data => {
            // Solo productos en oferta
            ofTodosProductos = data.filter(p => p.en_oferta == 1 && p.precio_descuento);
            ofGenerarFiltroCategorias();
            ofAplicarFiltros();
        })
        .catch(err => {
            console.error('Error:', err);
            document.getElementById('of-productos-grid').innerHTML = '<div class="col-span-full text-center py-16 text-red-500"><span class="material-symbols-outlined text-5xl">error</span><p class="mt-2">Error al cargar ofertas</p></div>';
        });
}

// --- Generar checkboxes de categorías dinámicamente ---
function ofGenerarFiltroCategorias() {
    const categorias = [...new Set(ofTodosProductos.map(p => p.categoria_nombre).filter(Boolean))];
    const container = document.getElementById('of-filtro-categorias');
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

// --- Aplicar filtros y ordenar ---
function ofAplicarFiltros() {
    // Categorías seleccionadas
    const catChecks = document.querySelectorAll('.of-cat-check:checked');
    const catsSeleccionadas = Array.from(catChecks).map(c => c.value);

    // Rango de precio (sobre precio con descuento)
    const precioMin = parseFloat(document.getElementById('of-precio-min').value) || 0;
    const precioMax = parseFloat(document.getElementById('of-precio-max').value) || Infinity;

    // Descuento mínimo
    const descMin = parseInt(document.querySelector('input[name="of-discount"]:checked').value) || 0;

    // Filtrar
    ofProductosFiltrados = ofTodosProductos.filter(p => {
        const precioDesc = parseFloat(p.precio_descuento);
        const precioOrig = parseFloat(p.precio);
        const pctDesc = Math.round(((precioOrig - precioDesc) / precioOrig) * 100);
        const catOk = catsSeleccionadas.length === 0 || catsSeleccionadas.includes(p.categoria_nombre);
        const precioOk = precioDesc >= precioMin && precioDesc <= precioMax;
        const descOk = pctDesc >= descMin;
        return catOk && precioOk && descOk;
    });

    // Ordenar
    const orden = document.getElementById('of-ordenar').value;
    if (orden === 'precio-asc') {
        ofProductosFiltrados.sort((a, b) => parseFloat(a.precio_descuento) - parseFloat(b.precio_descuento));
    } else if (orden === 'precio-desc') {
        ofProductosFiltrados.sort((a, b) => parseFloat(b.precio_descuento) - parseFloat(a.precio_descuento));
    } else if (orden === 'descuento') {
        ofProductosFiltrados.sort((a, b) => {
            const dA = ((parseFloat(a.precio) - parseFloat(a.precio_descuento)) / parseFloat(a.precio)) * 100;
            const dB = ((parseFloat(b.precio) - parseFloat(b.precio_descuento)) / parseFloat(b.precio)) * 100;
            return dB - dA;
        });
    }

    ofRenderProductos();
}

// --- Renderizar productos ---
function ofRenderProductos() {
    const grid = document.getElementById('of-productos-grid');
    document.getElementById('of-count-text').textContent = 'Mostrando ' + ofProductosFiltrados.length + ' de ' + ofTodosProductos.length + ' ofertas disponibles';

    if (ofProductosFiltrados.length === 0) {
        grid.innerHTML = '<div class="col-span-full text-center py-16"><span class="material-symbols-outlined text-6xl text-slate-300">search_off</span><p class="text-slate-500 mt-4 text-lg">No se encontraron ofertas con estos filtros.</p></div>';
        return;
    }

    let html = '';
    ofProductosFiltrados.forEach((prod, idx) => {
        const precioOrig = parseFloat(prod.precio).toFixed(2);
        const precioDesc = parseFloat(prod.precio_descuento).toFixed(2);
        const pctDesc = Math.round(((parseFloat(prod.precio) - parseFloat(prod.precio_descuento)) / parseFloat(prod.precio)) * 100);
        const imgSrc = prod.imagen_principal || 'https://via.placeholder.com/300x300?text=Sin+Imagen';
        const catNombre = prod.categoria_nombre || 'Sin categoría';

        html += '<div class="product-card group bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-md transition-shadow relative">' +
            '<div class="relative aspect-square overflow-hidden bg-slate-100">' +
                '<img alt="' + prod.nombre + '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="' + imgSrc + '" onerror="this.src=\'https://via.placeholder.com/300x300?text=Sin+Imagen\'"/>' +
                '<span class="absolute top-3 left-3 bg-accent text-white px-2 py-1 rounded-lg text-xs font-bold">-' + pctDesc + '%</span>' +
                '<div class="product-actions absolute inset-0 bg-black/5 flex items-center justify-center gap-3 opacity-0 translate-y-4 transition-all duration-300">' +
                    '<button onclick="toggleWishlist(this,' + prod.id_producto + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-accent shadow-lg transition-colors" title="Lista de deseos"><span class="material-symbols-outlined">favorite</span></button>' +
                    '<button onclick="ofVistaPrevia(' + idx + ')" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-700 hover:text-primary shadow-lg transition-colors" title="Vista previa"><span class="material-symbols-outlined">visibility</span></button>' +
                '</div>' +
            '</div>' +
            '<div class="p-4">' +
                '<p class="text-xs text-slate-500 uppercase tracking-widest mb-1">' + catNombre + '</p>' +
                '<h4 class="font-bold text-slate-900 dark:text-white mb-2 line-clamp-2">' + prod.nombre + '</h4>' +
                '<div class="flex items-center gap-2 mb-4">' +
                    '<span class="text-accent font-bold text-lg">L ' + precioDesc + '</span>' +
                    '<span class="text-slate-400 line-through text-sm">L ' + precioOrig + '</span>' +
                '</div>' +
                '<button class="w-full bg-slate-900 dark:bg-primary text-white py-2 rounded-lg font-medium text-sm hover:opacity-90 transition-opacity">Añadir al carrito</button>' +
            '</div>' +
        '</div>';
    });

    grid.innerHTML = html;
}

// --- Limpiar filtros ---
function ofLimpiarFiltros() {
    document.querySelectorAll('.of-cat-check').forEach(c => c.checked = true);
    document.getElementById('of-precio-min').value = '';
    document.getElementById('of-precio-max').value = '';
    document.querySelector('input[name="of-discount"][value="0"]').checked = true;
    document.getElementById('of-ordenar').value = 'relevancia';
    ofAplicarFiltros();
}

// --- Vista previa ---
let ofPrevImgIndex = 0;
let ofPrevImagenes = [];

function ofVistaPrevia(idx) {
    const prod = ofProductosFiltrados[idx];
    if (!prod) return;

    const precioOrig = parseFloat(prod.precio).toFixed(2);
    const precioDesc = parseFloat(prod.precio_descuento).toFixed(2);
    const pctDesc = Math.round(((parseFloat(prod.precio) - parseFloat(prod.precio_descuento)) / parseFloat(prod.precio)) * 100);

    // Imágenes
    ofPrevImagenes = (prod.imagenes && prod.imagenes.length > 0) ? prod.imagenes : (prod.imagen_principal ? [prod.imagen_principal] : ['https://via.placeholder.com/400x400?text=Sin+Imagen']);
    ofPrevImgIndex = 0;

    // Crear modal si no existe
    let modal = document.getElementById('ofModalPrevia');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'ofModalPrevia';
        document.body.appendChild(modal);
    }

    modal.innerHTML =
        '<div class="fixed inset-0 z-[9999] flex items-center justify-center">' +
            '<div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="ofCerrarPrevia()"></div>' +
            '<div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto z-10">' +
                '<button onclick="ofCerrarPrevia()" class="absolute top-4 right-4 z-10 w-10 h-10 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-600 hover:text-red-500 hover:bg-red-50 transition-colors"><span class="material-symbols-outlined">close</span></button>' +
                '<div class="flex flex-col md:flex-row">' +
                    '<div class="md:w-1/2 p-6">' +
                        '<div class="relative aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 mb-4">' +
                            '<img id="ofPrevImg" src="' + ofPrevImagenes[0] + '" class="w-full h-full object-cover"/>' +
                            (ofPrevImagenes.length > 1 ? '<button onclick="ofCambiarImg(-1)" class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md"><span class="material-symbols-outlined text-sm">arrow_back_ios_new</span></button><button onclick="ofCambiarImg(1)" class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 bg-white/80 hover:bg-white rounded-full flex items-center justify-center shadow-md"><span class="material-symbols-outlined text-sm">arrow_forward_ios</span></button>' : '') +
                        '</div>' +
                        (ofPrevImagenes.length > 1 ? '<div id="ofPrevMins" class="flex gap-2 overflow-x-auto pb-2">' + ofPrevImagenes.map(function(img, i) { return '<button onclick="ofSelImg(' + i + ')" class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 ' + (i === 0 ? 'border-primary' : 'border-transparent hover:border-slate-300') + ' transition-colors"><img src="' + img + '" class="w-full h-full object-cover"/></button>'; }).join('') + '</div>' : '') +
                    '</div>' +
                    '<div class="md:w-1/2 p-6 md:pl-2 flex flex-col justify-center">' +
                        '<span class="text-xs text-primary font-semibold uppercase tracking-wider mb-2">' + (prod.categoria_nombre || 'Sin categoría') + '</span>' +
                        '<h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">' + prod.nombre + '</h2>' +
                        (prod.marca_nombre ? '<p class="text-sm text-slate-400 mb-3">Marca: ' + prod.marca_nombre + '</p>' : '') +
                        '<p class="text-sm text-slate-600 dark:text-slate-400 mb-6 leading-relaxed">' + (prod.descripcion || 'Sin descripción disponible.') + '</p>' +
                        '<div class="flex items-center gap-3 mb-4">' +
                            '<span class="text-3xl font-bold text-accent">L ' + precioDesc + '</span>' +
                            '<span class="text-lg text-slate-400 line-through">L ' + precioOrig + '</span>' +
                            '<span class="bg-accent text-white text-xs font-bold px-2 py-1 rounded">-' + pctDesc + '%</span>' +
                        '</div>' +
                        '<div class="flex items-center gap-2 mb-6">' +
                            (prod.stock > 0 ? '<span class="material-symbols-outlined text-lg text-green-500">check_circle</span><span class="text-sm font-medium text-green-600">En stock (' + prod.stock + ' disponibles)</span>' : '<span class="material-symbols-outlined text-lg text-red-500">cancel</span><span class="text-sm font-medium text-red-500">Agotado</span>') +
                        '</div>' +
                        '<button class="w-full bg-primary hover:bg-primary/90 text-white py-3 rounded-lg flex items-center justify-center gap-2 font-bold transition-colors"><span class="material-symbols-outlined">shopping_cart</span> Agregar al Carrito</button>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';

    document.body.style.overflow = 'hidden';
}

function ofCerrarPrevia() {
    const m = document.getElementById('ofModalPrevia');
    if (m) m.innerHTML = '';
    document.body.style.overflow = '';
}

function ofCambiarImg(dir) {
    ofPrevImgIndex = (ofPrevImgIndex + dir + ofPrevImagenes.length) % ofPrevImagenes.length;
    document.getElementById('ofPrevImg').src = ofPrevImagenes[ofPrevImgIndex];
    ofActMins();
}

function ofSelImg(i) {
    ofPrevImgIndex = i;
    document.getElementById('ofPrevImg').src = ofPrevImagenes[i];
    ofActMins();
}

function ofActMins() {
    const btns = document.querySelectorAll('#ofPrevMins button');
    btns.forEach(function(btn, i) {
        btn.className = 'flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden border-2 transition-colors ' + (i === ofPrevImgIndex ? 'border-primary' : 'border-transparent hover:border-slate-300');
    });
}

// --- Countdown timer ---
function ofIniciarTimer() {
    const ahora = new Date();
    const fin = new Date(ahora);
    fin.setHours(23, 59, 59, 0);

    function tick() {
        const diff = Math.max(0, fin - new Date());
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        document.getElementById('of-hours').textContent = String(h).padStart(2, '0');
        document.getElementById('of-mins').textContent = String(m).padStart(2, '0');
        document.getElementById('of-secs').textContent = String(s).padStart(2, '0');
    }
    tick();
    setInterval(tick, 1000);
}

// --- Init ---
ofCargarProductos();
ofIniciarTimer();
</script>
</body></html>