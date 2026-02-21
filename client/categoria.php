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
        .cat-modal-content { animation: catSlideUp 0.3s ease; }
        @keyframes catFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes catSlideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .cat-loading { border: 3px solid #e5e7eb; border-top: 3px solid #7c3aed; border-radius: 50%; width: 36px; height: 36px; animation: catSpin 0.7s linear infinite; margin: 2rem auto; }
        @keyframes catSpin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <main class="max-w-7xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Categorías</h1>
                <p class="text-gray-500 mt-1">Administra las categorías de tus productos</p>
            </div>
            <button onclick="abrirModalCategoria()" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition shadow-md flex items-center gap-2">
                <i class="fas fa-plus"></i> Nueva Categoría
            </button>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 relative">
                    <input type="text" id="cat-busqueda" placeholder="Buscar categorías..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                <select id="cat-filtro-estado" onchange="cargarCategorias()"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                    <option value="todos">Todos los estados</option>
                    <option value="activo">Activos</option>
                    <option value="inactivo">Inactivos</option>
                </select>
            </div>
        </div>

        <!-- Contadores -->
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center shadow-sm">
                <p class="text-2xl font-bold text-gray-900" id="cat-count-total">0</p>
                <p class="text-sm text-gray-500">Total</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center shadow-sm">
                <p class="text-2xl font-bold text-green-600" id="cat-count-activo">0</p>
                <p class="text-sm text-gray-500">Activas</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4 text-center shadow-sm">
                <p class="text-2xl font-bold text-gray-400" id="cat-count-inactivo">0</p>
                <p class="text-sm text-gray-500">Inactivas</p>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-purple-800 text-white p-4">
                <h2 class="text-lg font-bold flex items-center gap-2">
                    <i class="fas fa-list"></i> Lista de Categorías
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ícono</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="cat-tabla-body">
                        <tr><td colspan="6"><div class="cat-loading"></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Crear/Editar Categoría -->
    <div id="cat-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 cat-modal-overlay">
        <div class="cat-modal-content bg-white rounded-xl shadow-2xl max-w-lg w-full mx-4">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900" id="cat-modal-titulo">
                    <i class="fas fa-folder-plus text-purple-600 mr-2"></i>Nueva Categoría
                </h2>
                <button onclick="cerrarModalCategoria()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <form id="cat-formulario" onsubmit="guardarCategoria(event)" class="p-6 space-y-5">
                <input type="hidden" id="cat-id" value="">

                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-heading text-purple-500 mr-1"></i> Nombre de la Categoría *
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
                            class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition"
                            placeholder="Ej: fa-laptop" oninput="previsualizarIcono(this.value)">
                    </div>
                    <p class="text-xs text-gray-400 mb-2">Selecciona un ícono o escribe la clase manualmente</p>
                    <!-- Grid de íconos populares -->
                    <div class="grid grid-cols-8 gap-1.5 max-h-40 overflow-y-auto p-2 bg-gray-50 rounded-lg border border-gray-200" id="cat-iconos-grid">
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-align-left text-purple-500 mr-1"></i> Descripción
                    </label>
                    <textarea id="cat-descripcion" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition resize-none"
                        placeholder="Descripción de la categoría..."></textarea>
                </div>

                <!-- Estado -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-green-500 mr-1"></i> Estado
                    </label>
                    <select id="cat-estado"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition">
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>

                <!-- Botones -->
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="cerrarModalCategoria()"
                        class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                        Cancelar
                    </button>
                    <button type="submit" id="cat-btn-guardar"
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2">
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
        if (path.includes('/admin/')) {
            return '../api/api_categorias.php';
        }
        return 'api/api_categorias.php';
    })();

    var catModoEdicion = false;

    // ============ CARGAR CATEGORÍAS ============
    function cargarCategorias() {
        var busqueda = document.getElementById('cat-busqueda') ? document.getElementById('cat-busqueda').value : '';
        var estado = document.getElementById('cat-filtro-estado') ? document.getElementById('cat-filtro-estado').value : 'todos';
        var tbody = document.getElementById('cat-tabla-body');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="6"><div class="cat-loading"></div></td></tr>';

        var url = CAT_API_URL + '?accion=listar';
        if (estado && estado !== 'todos') url += '&estado=' + encodeURIComponent(estado);
        if (busqueda.trim()) url += '&busqueda=' + encodeURIComponent(busqueda.trim());

        fetch(url)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.exito) {
                    renderizarTablaCategorias(data.categorias);
                    actualizarConteosCat(data.conteos);
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-red-500">' + (data.error || 'Error al cargar') + '</td></tr>';
                }
            })
            .catch(function(err) {
                console.error('Error:', err);
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-red-500">Error de conexión</td></tr>';
            });
    }

    // ============ RENDERIZAR TABLA ============
    function renderizarTablaCategorias(categorias) {
        var tbody = document.getElementById('cat-tabla-body');

        if (categorias.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-12">' +
                '<i class="fas fa-folder-open text-4xl text-gray-300 mb-3 block"></i>' +
                '<p class="text-gray-500 font-semibold">No hay categorías</p>' +
                '<p class="text-gray-400 text-sm">Crea una nueva categoría para empezar</p>' +
                '</td></tr>';
            return;
        }

        var html = '';
        categorias.forEach(function(cat) {
            var estadoBadge = cat.estado === 'activo'
                ? '<span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Activo</span>'
                : '<span class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-semibold">Inactivo</span>';

            var iconoHtml = cat.icono
                ? '<i class="fas ' + escapeHtmlCat(cat.icono) + ' text-lg text-purple-600"></i> <span class="text-xs text-gray-400 ml-1">' + escapeHtmlCat(cat.icono) + '</span>'
                : '<span class="text-gray-400 text-sm">Sin ícono</span>';

            html += '<tr class="border-b border-gray-100 hover:bg-gray-50 transition">' +
                '<td class="px-6 py-4 text-sm text-gray-500 font-mono">' + cat.id_categoria + '</td>' +
                '<td class="px-6 py-4 font-semibold text-gray-900">' + escapeHtmlCat(cat.nombre) + '</td>' +
                '<td class="px-6 py-4">' + iconoHtml + '</td>' +
                '<td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">' + escapeHtmlCat(cat.descripcion || '') + '</td>' +
                '<td class="px-6 py-4">' + estadoBadge + '</td>' +
                '<td class="px-6 py-4 text-center">' +
                    '<div class="flex items-center justify-center gap-2">' +
                        '<button onclick="editarCategoria(' + cat.id_categoria + ')" title="Editar" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded-lg text-sm transition"><i class="fas fa-edit"></i></button>' +
                        '<button onclick="eliminarCategoria(' + cat.id_categoria + ', \'' + escapeHtmlCat(cat.nombre).replace(/'/g, "\\'") + '\')" title="Eliminar" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-sm transition"><i class="fas fa-trash"></i></button>' +
                    '</div>' +
                '</td>' +
            '</tr>';
        });

        tbody.innerHTML = html;
    }

    // ============ CONTEOS ============
    function actualizarConteosCat(conteos) {
        var el;
        el = document.getElementById('cat-count-total'); if (el) el.textContent = conteos.total || 0;
        el = document.getElementById('cat-count-activo'); if (el) el.textContent = conteos.activo || 0;
        el = document.getElementById('cat-count-inactivo'); if (el) el.textContent = conteos.inactivo || 0;
    }

    // ============ MODAL ============
    function abrirModalCategoria() {
        catModoEdicion = false;
        document.getElementById('cat-id').value = '';
        document.getElementById('cat-nombre').value = '';
        document.getElementById('cat-icono').value = '';
        document.getElementById('cat-descripcion').value = '';
        document.getElementById('cat-estado').value = 'activo';
        document.getElementById('cat-modal-titulo').innerHTML = '<i class="fas fa-folder-plus text-purple-600 mr-2"></i>Nueva Categoría';
        document.getElementById('cat-btn-guardar').querySelector('span').textContent = 'Guardar';
        renderizarGridIconos();
        previsualizarIcono('');
        document.getElementById('cat-modal').classList.remove('hidden');
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
                document.getElementById('cat-modal-titulo').innerHTML = '<i class="fas fa-edit text-blue-600 mr-2"></i>Editar Categoría';
                document.getElementById('cat-btn-guardar').querySelector('span').textContent = 'Actualizar';
                renderizarGridIconos();
                previsualizarIcono(cat.icono || '');
                document.getElementById('cat-modal').classList.remove('hidden');
            })
            .catch(function(err) {
                console.error(err);
                if (typeof CustomModal !== 'undefined') CustomModal.show('error', 'Error', 'No se pudo cargar la categoría');
            });
    }

    function cerrarModalCategoria() {
        document.getElementById('cat-modal').classList.add('hidden');
    }

    // ============ GUARDAR (CREAR/EDITAR) ============
    function guardarCategoria(e) {
        e.preventDefault();

        var id = document.getElementById('cat-id').value;
        var nombre = document.getElementById('cat-nombre').value.trim();
        var icono = document.getElementById('cat-icono').value.trim();
        var descripcion = document.getElementById('cat-descripcion').value.trim();
        var estado = document.getElementById('cat-estado').value;

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
            CustomModal.show('confirm', 'Eliminar categoría', '¿Estás seguro de eliminar la categoría "' + nombre + '"?', function(confirmed) {
                if (confirmed) confirmar();
            });
        } else {
            confirmar();
        }
    }

    // ============ UTILIDADES ============
    function escapeHtmlCat(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // ============ SELECTOR DE ÍCONOS ============
    var iconosPopulares = [
        { clase: 'fa-laptop', nombre: 'Laptop' },
        { clase: 'fa-mobile-screen', nombre: 'Móvil' },
        { clase: 'fa-tv', nombre: 'TV' },
        { clase: 'fa-headphones', nombre: 'Audifonos' },
        { clase: 'fa-camera', nombre: 'Cámara' },
        { clase: 'fa-gamepad', nombre: 'Juegos' },
        { clase: 'fa-keyboard', nombre: 'Teclado' },
        { clase: 'fa-microchip', nombre: 'Chip' },
        { clase: 'fa-shirt', nombre: 'Ropa' },
        { clase: 'fa-shoe-prints', nombre: 'Zapatos' },
        { clase: 'fa-gem', nombre: 'Joyería' },
        { clase: 'fa-glasses', nombre: 'Gafas' },
        { clase: 'fa-hat-cowboy', nombre: 'Sombrero' },
        { clase: 'fa-bag-shopping', nombre: 'Bolsa' },
        { clase: 'fa-ring', nombre: 'Anillo' },
        { clase: 'fa-vest', nombre: 'Chaleco' },
        { clase: 'fa-utensils', nombre: 'Comida' },
        { clase: 'fa-burger', nombre: 'Burger' },
        { clase: 'fa-pizza-slice', nombre: 'Pizza' },
        { clase: 'fa-mug-hot', nombre: 'Café' },
        { clase: 'fa-ice-cream', nombre: 'Helado' },
        { clase: 'fa-wine-bottle', nombre: 'Vino' },
        { clase: 'fa-apple-whole', nombre: 'Fruta' },
        { clase: 'fa-fish', nombre: 'Pescado' },
        { clase: 'fa-cheese', nombre: 'Lácteos' },
        { clase: 'fa-cow', nombre: 'Vaca' },
        { clase: 'fa-egg', nombre: 'Huevo' },
        { clase: 'fa-wheat-awn', nombre: 'Granos' },
        { clase: 'fa-carrot', nombre: 'Verduras' },
        { clase: 'fa-lemon', nombre: 'Cítricos' },
        { clase: 'fa-drumstick-bite', nombre: 'Pollo' },
        { clase: 'fa-bread-slice', nombre: 'Pan' },
        { clase: 'fa-jar', nombre: 'Conservas' },
        { clase: 'fa-bottle-water', nombre: 'Agua' },
        { clase: 'fa-candy-cane', nombre: 'Dulces' },
        { clase: 'fa-cookie', nombre: 'Galleta' },
        { clase: 'fa-house', nombre: 'Hogar' },
        { clase: 'fa-couch', nombre: 'Muebles' },
        { clase: 'fa-bed', nombre: 'Cama' },
        { clase: 'fa-bath', nombre: 'Baño' },
        { clase: 'fa-lightbulb', nombre: 'Luz' },
        { clase: 'fa-plug', nombre: 'Enchufe' },
        { clase: 'fa-fan', nombre: 'Ventilador' },
        { clase: 'fa-blender', nombre: 'Licuadora' },
        { clase: 'fa-car', nombre: 'Auto' },
        { clase: 'fa-bicycle', nombre: 'Bicicleta' },
        { clase: 'fa-motorcycle', nombre: 'Moto' },
        { clase: 'fa-truck', nombre: 'Camión' },
        { clase: 'fa-plane', nombre: 'Avión' },
        { clase: 'fa-gas-pump', nombre: 'Gasolina' },
        { clase: 'fa-wrench', nombre: 'Herram.' },
        { clase: 'fa-screwdriver-wrench', nombre: 'Tools' },
        { clase: 'fa-book', nombre: 'Libro' },
        { clase: 'fa-graduation-cap', nombre: 'Educación' },
        { clase: 'fa-pen', nombre: 'Escritura' },
        { clase: 'fa-palette', nombre: 'Arte' },
        { clase: 'fa-music', nombre: 'Música' },
        { clase: 'fa-guitar', nombre: 'Guitarra' },
        { clase: 'fa-futbol', nombre: 'Fútbol' },
        { clase: 'fa-basketball', nombre: 'Basket' },
        { clase: 'fa-dumbbell', nombre: 'Gym' },
        { clase: 'fa-person-running', nombre: 'Deporte' },
        { clase: 'fa-heart-pulse', nombre: 'Salud' },
        { clase: 'fa-pills', nombre: 'Medicina' },
        { clase: 'fa-baby', nombre: 'Bebé' },
        { clase: 'fa-paw', nombre: 'Mascotas' },
        { clase: 'fa-dog', nombre: 'Perro' },
        { clase: 'fa-cat', nombre: 'Gato' },
        { clase: 'fa-seedling', nombre: 'Jardín' },
        { clase: 'fa-tree', nombre: 'Árbol' },
        { clase: 'fa-spa', nombre: 'Spa' },
        { clase: 'fa-gift', nombre: 'Regalo' },
        { clase: 'fa-star', nombre: 'Estrella' },
        { clase: 'fa-bolt', nombre: 'Eléctrico' },
        { clase: 'fa-fire', nombre: 'Fuego' },
        { clase: 'fa-tag', nombre: 'Etiqueta' }
    ];

    function renderizarGridIconos() {
        var grid = document.getElementById('cat-iconos-grid');
        if (!grid) return;
        var html = '';
        iconosPopulares.forEach(function(icon) {
            html += '<button type="button" onclick="seleccionarIcono(\'' + icon.clase + '\')" ' +
                'class="cat-icono-btn flex flex-col items-center justify-center p-2 rounded-lg hover:bg-purple-100 hover:text-purple-600 transition cursor-pointer border border-transparent hover:border-purple-300" ' +
                'title="' + icon.nombre + '" data-icono="' + icon.clase + '">' +
                '<i class="fas ' + icon.clase + ' text-base"></i>' +
                '<span class="text-[9px] text-gray-400 mt-0.5 truncate w-full text-center">' + icon.nombre + '</span>' +
                '</button>';
        });
        grid.innerHTML = html;
    }

    function seleccionarIcono(clase) {
        document.getElementById('cat-icono').value = clase;
        previsualizarIcono(clase);
        // Marcar visualmente el seleccionado
        document.querySelectorAll('.cat-icono-btn').forEach(function(btn) {
            if (btn.getAttribute('data-icono') === clase) {
                btn.classList.add('bg-purple-100', 'text-purple-600', 'border-purple-400');
            } else {
                btn.classList.remove('bg-purple-100', 'text-purple-600', 'border-purple-400');
            }
        });
    }

    function previsualizarIcono(valor) {
        var preview = document.getElementById('cat-icono-preview');
        if (!preview) return;
        valor = (valor || '').trim();
        if (valor) {
            preview.innerHTML = '<i class="fas ' + escapeHtmlCat(valor) + ' text-2xl text-purple-600"></i>';
            preview.classList.remove('border-dashed', 'border-gray-300', 'text-gray-400');
            preview.classList.add('border-solid', 'border-purple-400', 'bg-purple-50');
        } else {
            preview.innerHTML = '<i class="fas fa-image"></i>';
            preview.classList.add('border-dashed', 'border-gray-300', 'text-gray-400');
            preview.classList.remove('border-solid', 'border-purple-400', 'bg-purple-50');
        }
        // Resaltar en el grid si coincide
        document.querySelectorAll('.cat-icono-btn').forEach(function(btn) {
            if (btn.getAttribute('data-icono') === valor) {
                btn.classList.add('bg-purple-100', 'text-purple-600', 'border-purple-400');
            } else {
                btn.classList.remove('bg-purple-100', 'text-purple-600', 'border-purple-400');
            }
        });
    }

    // ============ EXPONER FUNCIONES GLOBALES (para onclick en HTML) ============
    window.cargarCategorias = cargarCategorias;
    window.abrirModalCategoria = abrirModalCategoria;
    window.editarCategoria = editarCategoria;
    window.cerrarModalCategoria = cerrarModalCategoria;
    window.guardarCategoria = guardarCategoria;
    window.eliminarCategoria = eliminarCategoria;
    window.seleccionarIcono = seleccionarIcono;
    window.previsualizarIcono = previsualizarIcono;

    // ============ INICIALIZACIÓN ============
    // setTimeout asegura que el DOM inyectado por Dashboard esté listo
    setTimeout(function() {
        cargarCategorias();

        // Cerrar modal al hacer clic fuera
        var modal = document.getElementById('cat-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) cerrarModalCategoria();
            });
        }

        // Búsqueda con debounce
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
