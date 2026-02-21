<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .cat-modal-overlay { animation: catFadeIn 0.2s ease; }
        @keyframes catFadeIn { from { opacity: 0; } to { opacity: 1; } }
        .cat-loading { width: 40px; height: 40px; border: 4px solid #e5e7eb; border-top-color: #7c3aed; border-radius: 50%; animation: catSpin 0.8s linear infinite; margin: 40px auto; }
        @keyframes catSpin { to { transform: rotate(360deg); } }
        .cat-card { transition: all 0.2s ease; border: 1px solid #e5e7eb; }
        .cat-card:hover { border-color: #c4b5fd; box-shadow: 0 4px 12px rgba(124,58,237,0.08); }
        .cat-sub-card { transition: all 0.2s ease; border: 1px solid #f3f4f6; }
        .cat-sub-card:hover { border-color: #ddd6fe; background: #faf5ff; }
        .cat-badge-principal { background: linear-gradient(135deg, #7c3aed, #6d28d9); color: white; }
        .cat-badge-sub { background: #ede9fe; color: #6d28d9; }
        .cat-toggle { transition: transform 0.2s ease; }
        .cat-toggle.open { transform: rotate(90deg); }
    </style>
</head>
<body class="bg-gray-50">
    <div class="p-6 max-w-7xl mx-auto" id="cat-container">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-sitemap text-purple-600"></i> Categorías y Subcategorías
                </h1>
                <p class="text-gray-500 text-sm mt-1">Organiza tus productos en categorías principales y subcategorías</p>
            </div>
            <div class="flex gap-2">
                <button onclick="abrirModalCategoria(null)" class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-lg font-semibold transition shadow-md flex items-center gap-2 text-sm">
                    <i class="fas fa-folder-plus"></i> Nueva Categoría
                </button>
            </div>
        </div>

        <!-- Contadores -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <p class="text-2xl font-bold text-gray-900" id="cat-count-total">0</p>
                <p class="text-xs text-gray-500 mt-1">Total</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <p class="text-2xl font-bold text-purple-600" id="cat-count-principales">0</p>
                <p class="text-xs text-gray-500 mt-1">Principales</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <p class="text-2xl font-bold text-indigo-600" id="cat-count-subcategorias">0</p>
                <p class="text-xs text-gray-500 mt-1">Subcategorías</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <p class="text-2xl font-bold text-green-600" id="cat-count-activo">0</p>
                <p class="text-xs text-gray-500 mt-1">Activas</p>
            </div>
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
                <p class="text-2xl font-bold text-gray-400" id="cat-count-inactivo">0</p>
                <p class="text-xs text-gray-500 mt-1">Inactivas</p>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-3 items-center">
            <div class="relative flex-1 w-full">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="cat-busqueda" placeholder="Buscar categoría..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none text-sm">
            </div>
            <select id="cat-filtro-estado" onchange="cargarCategorias()"
                class="px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none text-sm min-w-[140px]">
                <option value="todos">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="inactivo">Inactivos</option>
            </select>
        </div>

        <!-- Árbol de categorías -->
        <div id="cat-arbol-container" class="space-y-3">
            <div class="cat-loading"></div>
        </div>
    </div>

    <!-- ============ MODAL ============ -->
    <div id="cat-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 cat-modal-overlay">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900" id="cat-modal-titulo">
                    <i class="fas fa-folder-plus text-purple-600 mr-2"></i>Nueva Categoría
                </h2>
                <button onclick="cerrarModalCategoria()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <form id="cat-formulario" onsubmit="guardarCategoria(event)" class="p-6 space-y-5">
                <input type="hidden" id="cat-id">

                <!-- Tipo / Categoría Padre -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-sitemap text-purple-500 mr-1"></i> Tipo
                    </label>
                    <select id="cat-id-padre" onchange="onCambioPadre()"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm">
                        <option value="">Categoría Principal</option>
                    </select>
                    <p class="text-xs text-gray-400 mt-1" id="cat-tipo-info">Se creará como categoría principal (nivel raíz)</p>
                </div>

                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag text-purple-500 mr-1"></i> Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="cat-nombre" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                        placeholder="Ej: Electrónica">
                </div>

                <!-- Ícono -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-icons text-purple-500 mr-1"></i> Ícono
                    </label>
                    <div class="flex items-center gap-3 mb-2">
                        <div id="cat-icono-preview" class="w-12 h-12 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 text-gray-400 text-xl flex-shrink-0">
                            <i class="fas fa-image"></i>
                        </div>
                        <input type="text" id="cat-icono"
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm"
                            placeholder="Ej: fa-laptop" oninput="previsualizarIcono(this.value)">
                    </div>
                    <p class="text-xs text-gray-400 mb-2">Selecciona un ícono o escribe la clase manualmente</p>
                    <div class="grid grid-cols-8 gap-1.5 max-h-36 overflow-y-auto p-2 bg-gray-50 rounded-lg border border-gray-200" id="cat-iconos-grid"></div>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left text-purple-500 mr-1"></i> Descripción
                    </label>
                    <textarea id="cat-descripcion" rows="2"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition resize-none text-sm"
                        placeholder="Descripción de la categoría..."></textarea>
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-green-500 mr-1"></i> Estado
                    </label>
                    <select id="cat-estado"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition text-sm">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="cerrarModalCategoria()"
                        class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                        Cancelar
                    </button>
                    <button type="submit" id="cat-btn-guardar"
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2 text-sm">
                        <i class="fas fa-save"></i> <span>Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    (function() {
    // ============ CONFIGURACIÓN ============
    var CAT_API_URL = (function() {
        var path = window.location.pathname;
        if (path.includes('/admin/')) return '../api/api_categorias.php';
        return 'api/api_categorias.php';
    })();

    var catModoEdicion = false;

    // ============ ÍCONOS POPULARES ============
    var iconosPopulares = [
        { clase: 'fa-laptop', nombre: 'Laptop' }, { clase: 'fa-mobile-screen', nombre: 'Móvil' },
        { clase: 'fa-tv', nombre: 'TV' }, { clase: 'fa-headphones', nombre: 'Audio' },
        { clase: 'fa-camera', nombre: 'Cámara' }, { clase: 'fa-gamepad', nombre: 'Juegos' },
        { clase: 'fa-keyboard', nombre: 'Teclado' }, { clase: 'fa-microchip', nombre: 'Chip' },
        { clase: 'fa-shirt', nombre: 'Ropa' }, { clase: 'fa-shoe-prints', nombre: 'Zapatos' },
        { clase: 'fa-gem', nombre: 'Joyería' }, { clase: 'fa-glasses', nombre: 'Gafas' },
        { clase: 'fa-bag-shopping', nombre: 'Bolsa' }, { clase: 'fa-ring', nombre: 'Anillo' },
        { clase: 'fa-utensils', nombre: 'Comida' }, { clase: 'fa-burger', nombre: 'Burger' },
        { clase: 'fa-pizza-slice', nombre: 'Pizza' }, { clase: 'fa-mug-hot', nombre: 'Café' },
        { clase: 'fa-ice-cream', nombre: 'Helado' }, { clase: 'fa-wine-bottle', nombre: 'Vino' },
        { clase: 'fa-apple-whole', nombre: 'Fruta' }, { clase: 'fa-fish', nombre: 'Pescado' },
        { clase: 'fa-cheese', nombre: 'Lácteos' }, { clase: 'fa-cow', nombre: 'Vaca' },
        { clase: 'fa-egg', nombre: 'Huevo' }, { clase: 'fa-wheat-awn', nombre: 'Granos' },
        { clase: 'fa-carrot', nombre: 'Verduras' }, { clase: 'fa-drumstick-bite', nombre: 'Pollo' },
        { clase: 'fa-bread-slice', nombre: 'Pan' }, { clase: 'fa-cookie', nombre: 'Galleta' },
        { clase: 'fa-house', nombre: 'Hogar' }, { clase: 'fa-couch', nombre: 'Muebles' },
        { clase: 'fa-bed', nombre: 'Cama' }, { clase: 'fa-lightbulb', nombre: 'Luz' },
        { clase: 'fa-fan', nombre: 'Ventilador' }, { clase: 'fa-blender', nombre: 'Licuadora' },
        { clase: 'fa-car', nombre: 'Auto' }, { clase: 'fa-bicycle', nombre: 'Bicicleta' },
        { clase: 'fa-motorcycle', nombre: 'Moto' }, { clase: 'fa-truck', nombre: 'Camión' },
        { clase: 'fa-wrench', nombre: 'Herram.' }, { clase: 'fa-screwdriver-wrench', nombre: 'Tools' },
        { clase: 'fa-book', nombre: 'Libro' }, { clase: 'fa-graduation-cap', nombre: 'Educación' },
        { clase: 'fa-palette', nombre: 'Arte' }, { clase: 'fa-music', nombre: 'Música' },
        { clase: 'fa-futbol', nombre: 'Fútbol' }, { clase: 'fa-dumbbell', nombre: 'Gym' },
        { clase: 'fa-heart-pulse', nombre: 'Salud' }, { clase: 'fa-pills', nombre: 'Medicina' },
        { clase: 'fa-baby', nombre: 'Bebé' }, { clase: 'fa-paw', nombre: 'Mascotas' },
        { clase: 'fa-dog', nombre: 'Perro' }, { clase: 'fa-cat', nombre: 'Gato' },
        { clase: 'fa-seedling', nombre: 'Jardín' }, { clase: 'fa-spa', nombre: 'Spa' },
        { clase: 'fa-gift', nombre: 'Regalo' }, { clase: 'fa-star', nombre: 'Estrella' },
        { clase: 'fa-bolt', nombre: 'Eléctrico' }, { clase: 'fa-fire', nombre: 'Fuego' },
        { clase: 'fa-tag', nombre: 'Etiqueta' }, { clase: 'fa-bottle-water', nombre: 'Agua' }
    ];

    // ============ CARGAR CATEGORÍAS ============
    function cargarCategorias() {
        var busqueda = document.getElementById('cat-busqueda') ? document.getElementById('cat-busqueda').value : '';
        var estado = document.getElementById('cat-filtro-estado') ? document.getElementById('cat-filtro-estado').value : 'todos';
        var container = document.getElementById('cat-arbol-container');
        if (!container) return;
        container.innerHTML = '<div class="cat-loading"></div>';

        var url = CAT_API_URL + '?accion=listar';
        if (estado && estado !== 'todos') url += '&estado=' + encodeURIComponent(estado);
        if (busqueda.trim()) url += '&busqueda=' + encodeURIComponent(busqueda.trim());

        fetch(url)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.exito) {
                    renderizarArbol(data.arbol);
                    actualizarConteosCat(data.conteos);
                } else {
                    container.innerHTML = '<div class="text-center py-8 text-red-500">' + (data.error || 'Error') + '</div>';
                }
            })
            .catch(function(err) {
                console.error('Error:', err);
                container.innerHTML = '<div class="text-center py-8 text-red-500">Error de conexión</div>';
            });
    }

    // ============ RENDERIZAR ÁRBOL ============
    function renderizarArbol(arbol) {
        var container = document.getElementById('cat-arbol-container');
        if (!arbol || arbol.length === 0) {
            container.innerHTML = '<div class="text-center py-16 bg-white rounded-xl border border-gray-100">' +
                '<i class="fas fa-sitemap text-5xl text-gray-200 mb-4 block"></i>' +
                '<p class="text-gray-500 font-semibold text-lg">No hay categorías</p>' +
                '<p class="text-gray-400 text-sm mt-1">Crea una categoría principal para empezar</p>' +
                '<button onclick="abrirModalCategoria(null)" class="mt-4 bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg text-sm font-semibold transition">' +
                '<i class="fas fa-plus mr-1"></i> Crear primera categoría</button></div>';
            return;
        }

        var html = '';
        arbol.forEach(function(cat) {
            var estadoBadge = cat.estado === 'activo'
                ? '<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px] font-semibold">Activo</span>'
                : '<span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full text-[11px] font-semibold">Inactivo</span>';

            var iconoHtml = cat.icono
                ? '<i class="fas ' + esc(cat.icono) + ' text-xl text-purple-600"></i>'
                : '<i class="fas fa-folder text-xl text-purple-300"></i>';

            var tieneSubs = cat.subcategorias && cat.subcategorias.length > 0;
            var toggleBtn = tieneSubs
                ? '<button onclick="toggleSubcategorias(' + cat.id_categoria + ', event)" class="cat-toggle open text-gray-400 hover:text-purple-600 transition p-1" id="cat-toggle-' + cat.id_categoria + '"><i class="fas fa-chevron-right"></i></button>'
                : '<span class="w-6"></span>';

            html += '<div class="cat-card bg-white rounded-xl overflow-hidden">';
            // Cabecera
            html += '<div class="flex items-center gap-3 p-4">';
            html += toggleBtn;
            html += '<div class="w-11 h-11 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">' + iconoHtml + '</div>';
            html += '<div class="flex-1 min-w-0">';
            html += '<div class="flex items-center gap-2 flex-wrap">';
            html += '<h3 class="font-bold text-gray-900">' + esc(cat.nombre) + '</h3>';
            html += '<span class="cat-badge-principal text-[10px] px-2 py-0.5 rounded-full font-semibold">PRINCIPAL</span>';
            html += estadoBadge;
            html += '</div>';
            html += '<p class="text-xs text-gray-400 mt-0.5">';
            if (cat.descripcion) html += esc(cat.descripcion) + ' &middot; ';
            html += '<span class="text-indigo-500 font-medium">' + (cat.total_hijos || 0) + ' sub</span>';
            html += ' &middot; <span class="text-gray-500">' + (cat.total_productos || 0) + ' productos</span>';
            html += '</p></div>';
            // Acciones
            html += '<div class="flex items-center gap-1 flex-shrink-0">';
            html += '<button onclick="abrirModalSubcategoria(' + cat.id_categoria + ')" title="Agregar subcategoría" class="bg-indigo-50 hover:bg-indigo-100 text-indigo-600 w-8 h-8 rounded-lg text-xs transition flex items-center justify-center"><i class="fas fa-plus"></i></button>';
            html += '<button onclick="editarCategoria(' + cat.id_categoria + ')" title="Editar" class="bg-blue-50 hover:bg-blue-100 text-blue-600 w-8 h-8 rounded-lg text-xs transition flex items-center justify-center"><i class="fas fa-edit"></i></button>';
            html += '<button onclick="eliminarCategoria(' + cat.id_categoria + ', \'' + esc(cat.nombre).replace(/\x27/g, "\\\x27") + '\')" title="Eliminar" class="bg-red-50 hover:bg-red-100 text-red-600 w-8 h-8 rounded-lg text-xs transition flex items-center justify-center"><i class="fas fa-trash"></i></button>';
            html += '</div></div>';

            // Subcategorías
            if (tieneSubs) {
                html += '<div class="border-t border-gray-100" id="cat-subs-' + cat.id_categoria + '">';
                html += '<div class="pl-12 pr-4 py-2 space-y-1.5">';
                cat.subcategorias.forEach(function(sub) {
                    var subEstado = sub.estado === 'activo'
                        ? '<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[10px] font-semibold">Activo</span>'
                        : '<span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full text-[10px] font-semibold">Inactivo</span>';
                    var subIcono = sub.icono
                        ? '<i class="fas ' + esc(sub.icono) + ' text-sm text-indigo-500"></i>'
                        : '<i class="fas fa-tag text-sm text-gray-300"></i>';

                    html += '<div class="cat-sub-card flex items-center gap-3 p-3 rounded-lg bg-gray-50/50">';
                    html += '<div class="w-8 h-8 rounded-md bg-indigo-50 flex items-center justify-center flex-shrink-0">' + subIcono + '</div>';
                    html += '<div class="flex-1 min-w-0">';
                    html += '<div class="flex items-center gap-2">';
                    html += '<span class="font-semibold text-sm text-gray-800">' + esc(sub.nombre) + '</span>';
                    html += '<span class="cat-badge-sub text-[10px] px-1.5 py-0.5 rounded font-medium">SUB</span>';
                    html += subEstado;
                    html += '</div>';
                    if (sub.descripcion) html += '<p class="text-xs text-gray-400 truncate">' + esc(sub.descripcion) + '</p>';
                    html += '</div>';
                    html += '<div class="flex items-center gap-1 flex-shrink-0">';
                    html += '<span class="text-[11px] text-gray-400 mr-1">' + (sub.total_productos || 0) + ' prod.</span>';
                    html += '<button onclick="editarCategoria(' + sub.id_categoria + ')" title="Editar" class="bg-blue-50 hover:bg-blue-100 text-blue-600 w-7 h-7 rounded text-xs transition flex items-center justify-center"><i class="fas fa-edit"></i></button>';
                    html += '<button onclick="eliminarCategoria(' + sub.id_categoria + ', \'' + esc(sub.nombre).replace(/\x27/g, "\\\x27") + '\')" title="Eliminar" class="bg-red-50 hover:bg-red-100 text-red-600 w-7 h-7 rounded text-xs transition flex items-center justify-center"><i class="fas fa-trash"></i></button>';
                    html += '</div></div>';
                });
                html += '</div></div>';
            }
            html += '</div>';
        });
        container.innerHTML = html;
    }

    // ============ TOGGLE SUBCATEGORÍAS ============
    function toggleSubcategorias(id, e) {
        e.stopPropagation();
        var subsDiv = document.getElementById('cat-subs-' + id);
        var toggleBtn = document.getElementById('cat-toggle-' + id);
        if (subsDiv && toggleBtn) {
            if (subsDiv.style.display === 'none') {
                subsDiv.style.display = '';
                toggleBtn.classList.add('open');
            } else {
                subsDiv.style.display = 'none';
                toggleBtn.classList.remove('open');
            }
        }
    }

    // ============ CONTEOS ============
    function actualizarConteosCat(c) {
        var el;
        el = document.getElementById('cat-count-total'); if (el) el.textContent = c.total || 0;
        el = document.getElementById('cat-count-principales'); if (el) el.textContent = c.principales || 0;
        el = document.getElementById('cat-count-subcategorias'); if (el) el.textContent = c.subcategorias || 0;
        el = document.getElementById('cat-count-activo'); if (el) el.textContent = c.activo || 0;
        el = document.getElementById('cat-count-inactivo'); if (el) el.textContent = c.inactivo || 0;
    }

    // ============ CARGAR PADRES EN SELECT ============
    function cargarPadresEnSelect(excluirId, seleccionarId) {
        var select = document.getElementById('cat-id-padre');
        if (!select) return;
        var url = CAT_API_URL + '?accion=listar_padres';
        if (excluirId) url += '&excluir=' + excluirId;

        fetch(url)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.exito) {
                    var opts = '<option value="">── Categoría Principal ──</option>';
                    data.padres.forEach(function(p) {
                        var selText = (seleccionarId && parseInt(p.id_categoria) === parseInt(seleccionarId)) ? ' selected' : '';
                        opts += '<option value="' + p.id_categoria + '"' + selText + '>' + String.fromCharCode(8627) + ' ' + esc(p.nombre) + '</option>';
                    });
                    select.innerHTML = opts;
                    onCambioPadre();
                }
            });
    }

    function onCambioPadre() {
        var select = document.getElementById('cat-id-padre');
        var info = document.getElementById('cat-tipo-info');
        if (!select || !info) return;
        if (select.value) {
            info.textContent = 'Se creará como subcategoría dentro de la categoría seleccionada';
        } else {
            info.textContent = 'Se creará como categoría principal (nivel raíz)';
        }
    }

    // ============ MODAL: ABRIR ============
    function abrirModalCategoria(idPadre) {
        catModoEdicion = false;
        document.getElementById('cat-id').value = '';
        document.getElementById('cat-nombre').value = '';
        document.getElementById('cat-icono').value = '';
        document.getElementById('cat-descripcion').value = '';
        document.getElementById('cat-estado').value = 'activo';

        if (idPadre) {
            document.getElementById('cat-modal-titulo').innerHTML = '<i class="fas fa-folder-plus text-indigo-600 mr-2"></i>Nueva Subcategoría';
        } else {
            document.getElementById('cat-modal-titulo').innerHTML = '<i class="fas fa-folder-plus text-purple-600 mr-2"></i>Nueva Categoría';
        }
        document.getElementById('cat-btn-guardar').querySelector('span').textContent = 'Guardar';

        cargarPadresEnSelect(null, idPadre);
        renderizarGridIconos();
        previsualizarIcono('');
        document.getElementById('cat-modal').classList.remove('hidden');
    }

    function abrirModalSubcategoria(idPadre) {
        abrirModalCategoria(idPadre);
    }

    function editarCategoria(id) {
        fetch(CAT_API_URL + '?accion=obtener&id=' + id)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.exito) {
                    if (typeof CustomModal !== 'undefined') CustomModal.show('error', 'Error', data.error);
                    return;
                }
                var cat = data.categoria;
                catModoEdicion = true;
                document.getElementById('cat-id').value = cat.id_categoria;
                document.getElementById('cat-nombre').value = cat.nombre;
                document.getElementById('cat-icono').value = cat.icono || '';
                document.getElementById('cat-descripcion').value = cat.descripcion || '';
                document.getElementById('cat-estado').value = cat.estado;

                var titulo = cat.id_padre
                    ? '<i class="fas fa-edit text-indigo-600 mr-2"></i>Editar Subcategoría'
                    : '<i class="fas fa-edit text-blue-600 mr-2"></i>Editar Categoría';
                document.getElementById('cat-modal-titulo').innerHTML = titulo;
                document.getElementById('cat-btn-guardar').querySelector('span').textContent = 'Actualizar';

                cargarPadresEnSelect(cat.id_categoria, cat.id_padre);
                renderizarGridIconos();
                previsualizarIcono(cat.icono || '');
                document.getElementById('cat-modal').classList.remove('hidden');
            })
            .catch(function(err) {
                console.error(err);
                if (typeof CustomModal !== 'undefined') CustomModal.show('error', 'Error', 'No se pudo cargar');
            });
    }

    function cerrarModalCategoria() {
        document.getElementById('cat-modal').classList.add('hidden');
    }

    // ============ GUARDAR ============
    function guardarCategoria(e) {
        e.preventDefault();
        var id = document.getElementById('cat-id').value;
        var nombre = document.getElementById('cat-nombre').value.trim();
        var icono = document.getElementById('cat-icono').value.trim();
        var descripcion = document.getElementById('cat-descripcion').value.trim();
        var estado = document.getElementById('cat-estado').value;
        var id_padre = document.getElementById('cat-id-padre').value;

        if (!nombre) {
            if (typeof CustomModal !== 'undefined') CustomModal.show('warning', 'Campo requerido', 'El nombre es obligatorio');
            return;
        }

        var formData = new FormData();
        formData.append('accion', catModoEdicion ? 'editar' : 'crear');
        if (catModoEdicion) formData.append('id', id);
        formData.append('nombre', nombre);
        formData.append('icono', icono);
        formData.append('descripcion', descripcion);
        formData.append('estado', estado);
        if (id_padre) formData.append('id_padre', id_padre);

        fetch(CAT_API_URL, { method: 'POST', body: formData })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.exito) {
                    cerrarModalCategoria();
                    if (typeof CustomModal !== 'undefined') {
                        CustomModal.show('success', 'Éxito', data.mensaje, function() { cargarCategorias(); });
                    } else {
                        cargarCategorias();
                    }
                } else {
                    if (typeof CustomModal !== 'undefined') CustomModal.show('error', 'Error', data.error);
                }
            })
            .catch(function(err) {
                console.error(err);
                if (typeof CustomModal !== 'undefined') CustomModal.show('error', 'Error', 'Error de conexión');
            });
    }

    // ============ ELIMINAR ============
    function eliminarCategoria(id, nombre) {
        var confirmar = function() {
            var formData = new FormData();
            formData.append('accion', 'eliminar');
            formData.append('id', id);

            fetch(CAT_API_URL, { method: 'POST', body: formData })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.exito) {
                        if (typeof CustomModal !== 'undefined') {
                            CustomModal.show('success', 'Eliminada', data.mensaje, function() { cargarCategorias(); });
                        } else {
                            cargarCategorias();
                        }
                    } else {
                        if (typeof CustomModal !== 'undefined') CustomModal.show('error', 'Error', data.error);
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    if (typeof CustomModal !== 'undefined') CustomModal.show('error', 'Error', 'Error de conexión');
                });
        };

        if (typeof CustomModal !== 'undefined') {
            CustomModal.show('confirm', 'Eliminar categoría', '¿Eliminar "' + nombre + '"? Si es principal, sus subcategorías también se eliminarán.', function(confirmed) {
                if (confirmed) confirmar();
            });
        } else {
            confirmar();
        }
    }

    // ============ ÍCONOS ============
    function renderizarGridIconos() {
        var grid = document.getElementById('cat-iconos-grid');
        if (!grid) return;
        var html = '';
        iconosPopulares.forEach(function(icon) {
            html += '<button type="button" onclick="seleccionarIcono(\'' + icon.clase + '\')" ' +
                'class="cat-icono-btn flex flex-col items-center justify-center p-1.5 rounded-lg hover:bg-purple-100 hover:text-purple-600 transition cursor-pointer border border-transparent hover:border-purple-300" ' +
                'title="' + icon.nombre + '" data-icono="' + icon.clase + '">' +
                '<i class="fas ' + icon.clase + ' text-sm"></i>' +
                '<span class="text-[8px] text-gray-400 mt-0.5 truncate w-full text-center">' + icon.nombre + '</span></button>';
        });
        grid.innerHTML = html;
    }

    function seleccionarIcono(clase) {
        document.getElementById('cat-icono').value = clase;
        previsualizarIcono(clase);
    }

    function previsualizarIcono(valor) {
        var preview = document.getElementById('cat-icono-preview');
        if (!preview) return;
        valor = (valor || '').trim();
        if (valor) {
            preview.innerHTML = '<i class="fas ' + esc(valor) + ' text-2xl text-purple-600"></i>';
            preview.classList.remove('border-dashed', 'border-gray-300', 'text-gray-400');
            preview.classList.add('border-solid', 'border-purple-400', 'bg-purple-50');
        } else {
            preview.innerHTML = '<i class="fas fa-image"></i>';
            preview.classList.add('border-dashed', 'border-gray-300', 'text-gray-400');
            preview.classList.remove('border-solid', 'border-purple-400', 'bg-purple-50');
        }
        document.querySelectorAll('.cat-icono-btn').forEach(function(btn) {
            if (btn.getAttribute('data-icono') === valor) {
                btn.classList.add('bg-purple-100', 'text-purple-600', 'border-purple-400');
            } else {
                btn.classList.remove('bg-purple-100', 'text-purple-600', 'border-purple-400');
            }
        });
    }

    // ============ UTILIDADES ============
    function esc(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // ============ EXPONER FUNCIONES GLOBALES ============
    window.cargarCategorias = cargarCategorias;
    window.abrirModalCategoria = abrirModalCategoria;
    window.abrirModalSubcategoria = abrirModalSubcategoria;
    window.editarCategoria = editarCategoria;
    window.cerrarModalCategoria = cerrarModalCategoria;
    window.guardarCategoria = guardarCategoria;
    window.eliminarCategoria = eliminarCategoria;
    window.seleccionarIcono = seleccionarIcono;
    window.previsualizarIcono = previsualizarIcono;
    window.toggleSubcategorias = toggleSubcategorias;
    window.onCambioPadre = onCambioPadre;

    // ============ INICIALIZACIÓN ============
    setTimeout(function() {
        cargarCategorias();
        var modal = document.getElementById('cat-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) cerrarModalCategoria();
            });
        }
        var catSearchTimeout;
        var searchInput = document.getElementById('cat-busqueda');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(catSearchTimeout);
                catSearchTimeout = setTimeout(function() { cargarCategorias(); }, 400);
            });
        }
    }, 100);

    })(); // Fin IIFE
    </script>
</body>
</html>
