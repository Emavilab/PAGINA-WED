<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .prod-modal-overlay { animation: prodFadeIn 0.2s ease; }
        @keyframes prodFadeIn { from { opacity:0 } to { opacity:1 } }
        .prod-loading { width:40px; height:40px; border:4px solid #e5e7eb; border-top-color:#3b82f6; border-radius:50%; animation:prodSpin .8s linear infinite; margin:40px auto; }
        @keyframes prodSpin { to { transform:rotate(360deg) } }
        .prod-card { transition: all 0.2s ease; }
        .prod-card:hover { box-shadow: 0 4px 16px rgba(59,130,246,0.1); }
    </style>
</head>
<body class="bg-gray-50">
<div class="p-6 max-w-7xl mx-auto" id="prod-container">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                <i class="fas fa-boxes-stacked text-blue-600"></i> Gestión de Productos
            </h1>
            <p class="text-gray-500 text-sm mt-1">Administra el catálogo de productos</p>
        </div>
        <button onclick="window.abrirModalProducto()" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg font-semibold transition shadow-md flex items-center gap-2 text-sm">
            <i class="fas fa-plus"></i> Nuevo Producto
        </button>
    </div>

    <!-- Contadores -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-gray-900" id="prod-count-total">0</p>
            <p class="text-xs text-gray-500 mt-1">Total</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-green-600" id="prod-count-disponible">0</p>
            <p class="text-xs text-gray-500 mt-1">Disponibles</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-red-500" id="prod-count-agotado">0</p>
            <p class="text-xs text-gray-500 mt-1">Agotados</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 text-center">
            <p class="text-2xl font-bold text-amber-500" id="prod-count-oferta">0</p>
            <p class="text-xs text-gray-500 mt-1">En Oferta</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col md:flex-row gap-3 items-center">
        <div class="relative flex-1 w-full">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="prod-busqueda" placeholder="Buscar producto, categoría o marca..."
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none text-sm">
        </div>
        <select id="prod-filtro-estado" onchange="window.cargarProductos()"
            class="px-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none text-sm min-w-[150px]">
            <option value="todos">Todos los estados</option>
            <option value="disponible">Disponibles</option>
            <option value="agotado">Agotados</option>
        </select>
    </div>

    <!-- Tabla de Productos -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider font-semibold border-b border-gray-100">
                        <th class="px-5 py-4">Código</th>
                        <th class="px-5 py-4">Producto</th>
                        <th class="px-5 py-4">Categoría</th>
                        <th class="px-5 py-4">Marca</th>
                        <th class="px-5 py-4">Precio</th>
                        <th class="px-5 py-4">Stock</th>
                        <th class="px-5 py-4">Estado</th>
                        <th class="px-5 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody id="prod-tabla-body" class="divide-y divide-gray-100">
                    <tr><td colspan="7"><div class="prod-loading"></div></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ============ MODAL CREAR/EDITAR ============ -->
<div id="prod-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 prod-modal-overlay">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-gray-100 sticky top-0 bg-white z-10">
            <h2 class="text-xl font-bold text-gray-900" id="prod-modal-titulo">
                <i class="fas fa-plus-circle text-blue-600 mr-2"></i>Nuevo Producto
            </h2>
            <button onclick="window.cerrarModalProducto()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <form id="prod-formulario" onsubmit="window.guardarProducto(event)" class="p-6 space-y-5">
            <input type="hidden" id="prod-id">

            <!-- Código SKU y Nombre -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-barcode text-blue-500 mr-1"></i> Código / SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="prod-codigo" required maxlength="50"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm font-mono uppercase"
                        placeholder="Ej: CAM-BL-001" style="letter-spacing: 0.5px;">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-tag text-blue-500 mr-1"></i> Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="prod-nombre" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                        placeholder="Ej: Laptop Dell XPS 13">
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-align-left text-blue-500 mr-1"></i> Descripción
                </label>
                <textarea id="prod-descripcion" rows="3"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition resize-none text-sm"
                    placeholder="Descripción del producto..."></textarea>
            </div>

            <!-- Categoría y Marca -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-folder text-blue-500 mr-1"></i> Categoría <span class="text-red-500">*</span>
                    </label>
                    <select id="prod-categoria" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                        <option value="">Seleccionar categoría</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-building text-blue-500 mr-1"></i> Marca <span class="text-red-500">*</span>
                    </label>
                    <select id="prod-marca" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition text-sm">
                        <option value="">Seleccionar marca</option>
                    </select>
                </div>
            </div>

            <!-- Precio y Stock -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-dollar-sign text-green-500 mr-1"></i> Precio <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="prod-precio" step="0.01" min="0" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm"
                        placeholder="0.00" oninput="window.calcularPrecioDescuento()">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-percent text-amber-500 mr-1"></i> Descuento (%)
                    </label>
                    <input type="number" id="prod-descuento-pct" step="1" min="0" max="100"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm"
                        placeholder="0" oninput="window.calcularPrecioDescuento()">
                    <p id="prod-precio-descuento-preview" class="text-xs text-green-600 mt-1 hidden"></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-boxes-stacked text-blue-500 mr-1"></i> Stock
                    </label>
                    <input type="number" id="prod-stock" min="0"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm"
                        placeholder="0" value="0">
                </div>
            </div>

            <!-- Estado y Oferta -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-circle-check text-green-500 mr-1"></i> Estado
                    </label>
                    <select id="prod-estado"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        <option value="disponible">Disponible</option>
                        <option value="agotado">Agotado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-percent text-amber-500 mr-1"></i> ¿En oferta?
                    </label>
                    <select id="prod-en-oferta" onchange="window.toggleFechasOferta()"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition text-sm">
                        <option value="0">No</option>
                        <option value="1">Sí</option>
                    </select>
                </div>
            </div>

            <!-- Fechas de Oferta (visible solo cuando en_oferta = 1) -->
            <div id="prod-fechas-oferta" class="hidden grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-plus text-amber-500 mr-1"></i> Inicio de Oferta <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="prod-fecha-inicio-oferta"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-xmark text-amber-500 mr-1"></i> Fin de Oferta <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="prod-fecha-fin-oferta"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition text-sm">
                </div>
            </div>

            <!-- Imágenes -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-images text-blue-500 mr-1"></i> Imágenes del Producto
                </label>
                <!-- Imágenes existentes (al editar) -->
                <div id="prod-imagenes-existentes" class="hidden mb-3">
                    <p class="text-xs text-gray-400 mb-2">Imágenes actuales (clic en <i class="fas fa-times text-red-400"></i> para eliminar):</p>
                    <div id="prod-galeria-existente" class="flex flex-wrap gap-2"></div>
                </div>
                <!-- Input para nuevas imágenes -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-400 transition cursor-pointer relative" id="prod-drop-zone">
                    <input type="file" id="prod-imagenes" accept="image/*" multiple
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="window.previsualizarImagenes(this)">
                    <i class="fas fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Clic o arrastra imágenes aquí</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, GIF, WEBP — Puedes seleccionar varias</p>
                </div>
                <!-- Vista previa de nuevas imágenes -->
                <div id="prod-preview-nuevas" class="flex flex-wrap gap-2 mt-3"></div>
            </div>

            <!-- Botones -->
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="window.cerrarModalProducto()"
                    class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                    Cancelar
                </button>
                <button type="submit" id="prod-btn-guardar"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-save"></i> <span>Guardar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Alerta (error / éxito) -->
<div id="prod-modal-alerta" class="fixed inset-0 bg-black/50 z-[9999] hidden flex items-center justify-center prod-modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
        <div id="prod-alerta-header" class="px-6 py-4 flex items-center gap-3 bg-red-50">
            <i id="prod-alerta-icono" class="fas fa-exclamation-circle text-red-500 text-2xl"></i>
            <h3 id="prod-alerta-titulo" class="text-lg font-bold text-red-700">Atención</h3>
        </div>
        <div class="px-6 pb-2 pt-4">
            <p id="prod-alerta-mensaje" class="text-gray-600 text-sm"></p>
        </div>
        <div class="px-6 pb-6 pt-3">
            <button onclick="window.cerrarModalAlerta()" id="prod-alerta-btn"
                class="w-full py-2.5 rounded-lg font-semibold transition text-sm text-white bg-red-600 hover:bg-red-700">
                Aceptar
            </button>
        </div>
    </div>
</div>

<!-- Modal Confirmación -->
<div id="prod-modal-confirmar" class="fixed inset-0 bg-black/50 z-[9999] hidden flex items-center justify-center prod-modal-overlay">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden">
        <div class="bg-red-50 px-6 py-4 flex items-center gap-3">
            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            <h3 class="text-lg font-bold text-red-700">Confirmar acción</h3>
        </div>
        <div class="px-6 pb-2 pt-4">
            <p id="prod-confirmar-mensaje" class="text-gray-600 text-sm"></p>
        </div>
        <div class="flex gap-3 px-6 pb-6 pt-3">
            <button onclick="window.cerrarModalConfirmar()"
                class="flex-1 py-2.5 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition text-sm">
                Cancelar
            </button>
            <button onclick="window.ejecutarConfirmacion()"
                class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition text-sm">
                Confirmar
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    // ========= CONFIG =========
    var PROD_API = (function() {
        var path = window.location.pathname;
        if (path.includes('/admin/')) return '../api/api_productos.php';
        return 'api/api_productos.php';
    })();

    var prodModoEdicion = false;
    var prodArchivosNuevos = [];

    // ========= CARGAR PRODUCTOS =========
    function cargarProductos() {
        var busqueda = document.getElementById('prod-busqueda') ? document.getElementById('prod-busqueda').value : '';
        var estado = document.getElementById('prod-filtro-estado') ? document.getElementById('prod-filtro-estado').value : 'todos';
        var tbody = document.getElementById('prod-tabla-body');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="8"><div class="prod-loading"></div></td></tr>';

        var url = PROD_API + '?accion=listar';
        if (estado && estado !== 'todos') url += '&estado=' + encodeURIComponent(estado);
        if (busqueda.trim()) url += '&busqueda=' + encodeURIComponent(busqueda.trim());

        fetch(url)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.exito) {
                    renderizarTabla(data.productos);
                    actualizarConteos(data.conteos);
                } else {
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500">' + (data.error || 'Error') + '</td></tr>';
                }
            })
            .catch(function(err) {
                console.error('Error:', err);
                    tbody.innerHTML = '<tr><td colspan="8" class="text-center py-8 text-red-500">Error de conexión</td></tr>';
            });
    }

    // ========= RENDERIZAR TABLA =========
    function renderizarTabla(productos) {
        var tbody = document.getElementById('prod-tabla-body');
        if (!productos || productos.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center py-16">' +
                '<i class="fas fa-box-open text-5xl text-gray-200 mb-4 block"></i>' +
                '<p class="text-gray-500 font-semibold">No hay productos</p>' +
                '<button onclick="window.abrirModalProducto()" class="mt-3 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-semibold transition">' +
                '<i class="fas fa-plus mr-1"></i> Agregar producto</button></td></tr>';
            return;
        }

        var html = '';
        productos.forEach(function(p) {
            var estadoBadge = p.estado === 'disponible'
                ? '<span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-[11px] font-semibold">Disponible</span>'
                : '<span class="bg-red-100 text-red-600 px-2.5 py-1 rounded-full text-[11px] font-semibold">Agotado</span>';

            var ofertaBadge = p.en_oferta == 1
                ? ' <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full text-[10px] font-semibold ml-1">OFERTA</span>' : '';

            var imgSrc = p.imagen_principal ? esc(p.imagen_principal) : '';
            var imgHtml = imgSrc
                ? '<img src="' + imgSrc + '" class="w-10 h-10 rounded-lg object-cover border border-gray-200">'
                : '<div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center"><i class="fas fa-image text-gray-300"></i></div>';

            var precioOriginal = parseFloat(p.precio);
            var precioDesc = p.precio_descuento ? parseFloat(p.precio_descuento) : 0;
            var precioHtml = '<span class="font-bold text-gray-900">$' + precioOriginal.toFixed(2) + '</span>';
            if (precioDesc > 0 && precioDesc < precioOriginal) {
                var pctDesc = Math.round((1 - precioDesc / precioOriginal) * 100);
                precioHtml = '<div class="flex flex-col">' +
                    '<span class="text-gray-400 line-through text-xs">$' + precioOriginal.toFixed(2) + '</span>' +
                    '<span class="font-bold text-green-600">$' + precioDesc.toFixed(2) + '</span>' +
                    '<span class="text-[10px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded-full font-semibold w-fit">-' + pctDesc + '%</span>' +
                    '</div>';
            }

            var stockVal = parseInt(p.stock);
            var stockClass = stockVal <= 0 ? 'text-red-600 font-bold' : (stockVal <= 5 ? 'text-amber-600 font-semibold' : 'text-gray-700');

            html += '<tr class="prod-card hover:bg-gray-50/80 transition">';
            html += '<td class="px-5 py-3.5"><span class="font-mono text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">' + esc(p.codigo || '—') + '</span></td>';
            html += '<td class="px-5 py-3.5"><div class="flex items-center gap-3">' + imgHtml +
                '<div><p class="font-semibold text-gray-900 text-sm">' + esc(p.nombre) + '</p>';
            if (p.descripcion) html += '<p class="text-xs text-gray-400 truncate max-w-[200px]">' + esc(p.descripcion) + '</p>';
            html += '</div></div></td>';
            html += '<td class="px-5 py-3.5 text-sm text-gray-600">' + esc(p.categoria_nombre || '—') + '</td>';
            html += '<td class="px-5 py-3.5 text-sm text-gray-600">' + esc(p.marca_nombre || '—') + '</td>';
            html += '<td class="px-5 py-3.5 text-sm">' + precioHtml + '</td>';
            html += '<td class="px-5 py-3.5 text-sm ' + stockClass + '">' + p.stock + '</td>';
            html += '<td class="px-5 py-3.5">' + estadoBadge + ofertaBadge + '</td>';
            html += '<td class="px-5 py-3.5 text-right"><div class="flex items-center justify-end gap-1">';
            html += '<button onclick="window.editarProducto(' + p.id_producto + ')" title="Editar" class="bg-blue-50 hover:bg-blue-100 text-blue-600 w-8 h-8 rounded-lg text-xs transition flex items-center justify-center"><i class="fas fa-edit"></i></button>';
            html += '<button onclick="window.eliminarProducto(' + p.id_producto + ', \'' + esc(p.nombre).replace(/'/g, "\\'") + '\')" title="Eliminar" class="bg-red-50 hover:bg-red-100 text-red-600 w-8 h-8 rounded-lg text-xs transition flex items-center justify-center"><i class="fas fa-trash"></i></button>';
            html += '</div></td></tr>';
        });
        tbody.innerHTML = html;
    }

    // ========= CONTEOS =========
    function actualizarConteos(c) {
        var el;
        el = document.getElementById('prod-count-total'); if (el) el.textContent = c.total || 0;
        el = document.getElementById('prod-count-disponible'); if (el) el.textContent = c.disponible || 0;
        el = document.getElementById('prod-count-agotado'); if (el) el.textContent = c.agotado || 0;
        el = document.getElementById('prod-count-oferta'); if (el) el.textContent = c.oferta || 0;
    }

    // ========= CARGAR SELECTS =========
    function cargarSelectsCategorias(seleccionarId) {
        var select = document.getElementById('prod-categoria');
        if (!select) return;
        fetch(PROD_API + '?accion=listar_categorias')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.exito) return;
                var cats = data.categorias;
                var padres = [];
                var hijos = {};
                cats.forEach(function(c) {
                    if (!c.id_padre) {
                        padres.push(c);
                    } else {
                        if (!hijos[c.id_padre]) hijos[c.id_padre] = [];
                        hijos[c.id_padre].push(c);
                    }
                });

                var opts = '<option value="">Seleccionar categoría</option>';
                padres.forEach(function(p) {
                    if (hijos[p.id_categoria]) {
                        opts += '<optgroup label="' + esc(p.nombre) + '">';
                        var selPadre = (seleccionarId && parseInt(p.id_categoria) === parseInt(seleccionarId)) ? ' selected' : '';
                        opts += '<option value="' + p.id_categoria + '"' + selPadre + '>' + esc(p.nombre) + ' (General)</option>';
                        hijos[p.id_categoria].forEach(function(h) {
                            var selHijo = (seleccionarId && parseInt(h.id_categoria) === parseInt(seleccionarId)) ? ' selected' : '';
                            opts += '<option value="' + h.id_categoria + '"' + selHijo + '>' + esc(h.nombre) + '</option>';
                        });
                        opts += '</optgroup>';
                    } else {
                        var sel = (seleccionarId && parseInt(p.id_categoria) === parseInt(seleccionarId)) ? ' selected' : '';
                        opts += '<option value="' + p.id_categoria + '"' + sel + '>' + esc(p.nombre) + '</option>';
                    }
                });
                select.innerHTML = opts;
            });
    }

    function cargarSelectsMarcas(seleccionarId) {
        var select = document.getElementById('prod-marca');
        if (!select) return;
        fetch(PROD_API + '?accion=listar_marcas')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.exito) return;
                var opts = '<option value="">Seleccionar marca</option>';
                data.marcas.forEach(function(m) {
                    var sel = (seleccionarId && parseInt(m.id_marca) === parseInt(seleccionarId)) ? ' selected' : '';
                    opts += '<option value="' + m.id_marca + '"' + sel + '>' + esc(m.nombre) + '</option>';
                });
                select.innerHTML = opts;
            });
    }

    // ========= MODAL =========
    function abrirModalProducto() {
        prodModoEdicion = false;
        document.getElementById('prod-id').value = '';
        document.getElementById('prod-codigo').value = '';
        document.getElementById('prod-nombre').value = '';
        document.getElementById('prod-descripcion').value = '';
        document.getElementById('prod-precio').value = '';
        document.getElementById('prod-descuento-pct').value = '';
        document.getElementById('prod-precio-descuento-preview').classList.add('hidden');
        document.getElementById('prod-precio-descuento-preview').textContent = '';
        document.getElementById('prod-stock').value = '0';
        document.getElementById('prod-estado').value = 'disponible';
        document.getElementById('prod-en-oferta').value = '0';
        document.getElementById('prod-fecha-inicio-oferta').value = '';
        document.getElementById('prod-fecha-fin-oferta').value = '';
        document.getElementById('prod-fechas-oferta').classList.add('hidden');
        document.getElementById('prod-imagenes').value = '';
        document.getElementById('prod-imagenes-existentes').classList.add('hidden');
        document.getElementById('prod-galeria-existente').innerHTML = '';
        document.getElementById('prod-preview-nuevas').innerHTML = '';
        prodArchivosNuevos = [];

        document.getElementById('prod-modal-titulo').innerHTML = '<i class="fas fa-plus-circle text-blue-600 mr-2"></i>Nuevo Producto';
        document.getElementById('prod-btn-guardar').querySelector('span').textContent = 'Guardar';

        cargarSelectsCategorias(null);
        cargarSelectsMarcas(null);
        document.getElementById('prod-modal').classList.remove('hidden');
    }

    function editarProducto(id) {
        fetch(PROD_API + '?accion=obtener&id=' + id)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data.exito) {
                    mostrarAlerta(data.error || 'Error al cargar producto', 'error');
                    return;
                }
                var p = data.producto;
                prodModoEdicion = true;
                document.getElementById('prod-id').value = p.id_producto;
                document.getElementById('prod-codigo').value = p.codigo || '';
                document.getElementById('prod-nombre').value = p.nombre;
                document.getElementById('prod-descripcion').value = p.descripcion || '';
                document.getElementById('prod-precio').value = p.precio;
                // Calcular % de descuento a partir de precio y precio_descuento guardados
                if (p.precio_descuento && parseFloat(p.precio_descuento) > 0 && parseFloat(p.precio) > 0) {
                    var pctCalculado = Math.round((1 - parseFloat(p.precio_descuento) / parseFloat(p.precio)) * 100);
                    document.getElementById('prod-descuento-pct').value = pctCalculado;
                } else {
                    document.getElementById('prod-descuento-pct').value = '';
                }
                calcularPrecioDescuento();
                document.getElementById('prod-stock').value = p.stock;
                document.getElementById('prod-estado').value = p.estado;
                document.getElementById('prod-en-oferta').value = p.en_oferta || '0';
                document.getElementById('prod-fecha-inicio-oferta').value = p.fecha_inicio_oferta || '';
                document.getElementById('prod-fecha-fin-oferta').value = p.fecha_fin_oferta || '';
                toggleFechasOferta();
                document.getElementById('prod-imagenes').value = '';
                document.getElementById('prod-preview-nuevas').innerHTML = '';
                prodArchivosNuevos = [];

                // Mostrar imágenes existentes
                var galeriaExistente = document.getElementById('prod-galeria-existente');
                var contenedorExistente = document.getElementById('prod-imagenes-existentes');
                if (p.imagenes && p.imagenes.length > 0) {
                    var galeriaHtml = '';
                    p.imagenes.forEach(function(img) {
                        galeriaHtml += '<div class="relative group" id="prod-img-exist-' + img.id_imagen + '">' +
                            '<img src="' + esc(img.ruta_imagen) + '" class="w-20 h-20 object-cover rounded-lg border border-gray-200">' +
                            '<button type="button" onclick="window.eliminarImagenExistente(' + img.id_imagen + ')" ' +
                            'class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow">' +
                            '<i class="fas fa-times"></i></button></div>';
                    });
                    galeriaExistente.innerHTML = galeriaHtml;
                    contenedorExistente.classList.remove('hidden');
                } else {
                    galeriaExistente.innerHTML = '';
                    contenedorExistente.classList.add('hidden');
                }

                document.getElementById('prod-modal-titulo').innerHTML = '<i class="fas fa-edit text-blue-600 mr-2"></i>Editar Producto';
                document.getElementById('prod-btn-guardar').querySelector('span').textContent = 'Actualizar';

                cargarSelectsCategorias(p.id_categoria);
                cargarSelectsMarcas(p.id_marca);
                document.getElementById('prod-modal').classList.remove('hidden');
            })
            .catch(function(err) {
                console.error(err);
                mostrarAlerta('Error al cargar producto', 'error');
            });
    }

    function cerrarModalProducto() {
        document.getElementById('prod-modal').classList.add('hidden');
    }

    // ========= GUARDAR =========
    function guardarProducto(e) {
        e.preventDefault();

        // --- Validaciones de campos vacíos ---
        var codigo = document.getElementById('prod-codigo').value.trim();
        var nombre = document.getElementById('prod-nombre').value.trim();
        var categoria = document.getElementById('prod-categoria').value;
        var marca = document.getElementById('prod-marca').value;
        var precio = document.getElementById('prod-precio').value;
        var stock = document.getElementById('prod-stock').value;

        if (!codigo) { mostrarAlerta('El código / SKU es obligatorio', 'error', function() { document.getElementById('prod-codigo').focus(); }); return; }
        if (!nombre) { mostrarAlerta('El nombre del producto es obligatorio', 'error', function() { document.getElementById('prod-nombre').focus(); }); return; }
        if (!categoria) { mostrarAlerta('Debe seleccionar una categoría', 'error', function() { document.getElementById('prod-categoria').focus(); }); return; }
        if (!marca) { mostrarAlerta('Debe seleccionar una marca', 'error', function() { document.getElementById('prod-marca').focus(); }); return; }
        if (!precio || parseFloat(precio) <= 0) { mostrarAlerta('El precio debe ser mayor a 0', 'error', function() { document.getElementById('prod-precio').focus(); }); return; }
        if (stock === '' || parseInt(stock) < 0) { mostrarAlerta('El stock debe ser un valor válido', 'error', function() { document.getElementById('prod-stock').focus(); }); return; }

        // --- Validar imágenes: al crear debe tener al menos una ---
        var tieneImagenesExistentes = false;
        if (prodModoEdicion) {
            var galeria = document.getElementById('prod-galeria-existente');
            tieneImagenesExistentes = galeria && galeria.children.length > 0;
        }
        if (!tieneImagenesExistentes && prodArchivosNuevos.length === 0) {
            mostrarAlerta('Debe agregar al menos una imagen del producto', 'error');
            return;
        }

        var formData = new FormData();
        formData.append('accion', prodModoEdicion ? 'editar' : 'crear');

        if (prodModoEdicion) {
            formData.append('id', document.getElementById('prod-id').value);
        }

        formData.append('codigo', codigo.toUpperCase());
        formData.append('nombre', nombre);
        formData.append('descripcion', document.getElementById('prod-descripcion').value.trim());
        formData.append('id_categoria', categoria);
        formData.append('id_marca', marca);
        formData.append('precio', precio);
        // Calcular precio_descuento desde el porcentaje
        var pctVal = parseFloat(document.getElementById('prod-descuento-pct').value) || 0;
        var precioBase = parseFloat(document.getElementById('prod-precio').value) || 0;
        var precioConDesc = pctVal > 0 ? (precioBase - (precioBase * pctVal / 100)).toFixed(2) : '';
        formData.append('precio_descuento', precioConDesc);
        formData.append('stock', document.getElementById('prod-stock').value);
        formData.append('estado', document.getElementById('prod-estado').value);
        var enOfertaVal = document.getElementById('prod-en-oferta').value;
        if (enOfertaVal === '1' && pctVal <= 0) {
            mostrarAlerta('No puede marcar el producto en oferta sin un descuento válido.', 'error');
            return;
        }

        formData.append('en_oferta', enOfertaVal);
        formData.append('fecha_inicio_oferta', document.getElementById('prod-fecha-inicio-oferta').value);
        formData.append('fecha_fin_oferta', document.getElementById('prod-fecha-fin-oferta').value);

        // Agregar todas las imágenes nuevas
        prodArchivosNuevos.forEach(function(file) {
            formData.append('imagenes[]', file);
        });

        fetch(PROD_API, { method: 'POST', body: formData })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.exito) {
                    cerrarModalProducto();
                    cargarProductos();
                } else {
                    mostrarAlerta(data.error || 'Error al guardar', 'error');
                }
            })
            .catch(function(err) {
                console.error(err);
                mostrarAlerta('Error de conexión', 'error');
            });
    }

    // ========= ELIMINAR =========
    function eliminarProducto(id, nombre) {
        mostrarConfirmacion('¿Eliminar "' + nombre + '"? Esta acción no se puede deshacer.', function() {
            var formData = new FormData();
            formData.append('accion', 'eliminar');
            formData.append('id', id);

            fetch(PROD_API, { method: 'POST', body: formData })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.exito) {
                        cargarProductos();
                    } else {
                        mostrarAlerta(data.error || 'Error al eliminar', 'error');
                    }
                })
                .catch(function(err) {
                    console.error(err);
                    mostrarAlerta('Error de conexión', 'error');
                });
        });
    }

    // ========= TOGGLE FECHAS OFERTA =========
    function toggleFechasOferta() {
        var enOferta = document.getElementById('prod-en-oferta').value;
        var container = document.getElementById('prod-fechas-oferta');
        if (!container) return;
        if (enOferta === '1') {
            var pctDesc = parseFloat(document.getElementById('prod-descuento-pct').value) || 0;
            if (pctDesc <= 0) {
                mostrarAlerta('Debe ingresar un porcentaje de descuento válido antes de marcar el producto en oferta.', 'error');
                document.getElementById('prod-en-oferta').value = '0';
                container.classList.add('hidden');
                return;
            }
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
            document.getElementById('prod-fecha-inicio-oferta').value = '';
            document.getElementById('prod-fecha-fin-oferta').value = '';
        }
    }

    // ========= CALCULAR PRECIO DESCUENTO EN TIEMPO REAL =========
    function calcularPrecioDescuento() {
        var precio = parseFloat(document.getElementById('prod-precio').value) || 0;
        var pct = parseFloat(document.getElementById('prod-descuento-pct').value) || 0;
        var preview = document.getElementById('prod-precio-descuento-preview');
        if (pct > 0 && precio > 0) {
            var precioFinal = precio - (precio * pct / 100);
            preview.textContent = 'Precio final: $' + precioFinal.toFixed(2) + ' (ahorro: $' + (precio - precioFinal).toFixed(2) + ')';
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
            preview.textContent = '';
        }
    }

    // ========= PREVISUALIZACIÓN DE IMÁGENES =========
    function previsualizarImagenes(input) {
        var archivos = input.files;
        if (!archivos || archivos.length === 0) return;

        for (var i = 0; i < archivos.length; i++) {
            prodArchivosNuevos.push(archivos[i]);
        }
        renderizarPreviewNuevas();
        // Limpiar input para permitir seleccionar más
        input.value = '';
    }

    function renderizarPreviewNuevas() {
        var container = document.getElementById('prod-preview-nuevas');
        if (!container) return;
        var html = '';
        prodArchivosNuevos.forEach(function(file, idx) {
            var url = URL.createObjectURL(file);
            html += '<div class="relative group" id="prod-new-img-' + idx + '">' +
                '<img src="' + url + '" class="w-20 h-20 object-cover rounded-lg border-2 border-blue-200">' +
                '<button type="button" onclick="window.quitarImagenNueva(' + idx + ')" ' +
                'class="absolute -top-2 -right-2 w-5 h-5 bg-red-500 text-white rounded-full text-[10px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition shadow">' +
                '<i class="fas fa-times"></i></button>' +
                '<span class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-[8px] text-center py-0.5 rounded-b-lg truncate px-1">' + esc(file.name) + '</span></div>';
        });
        container.innerHTML = html;
    }

    function quitarImagenNueva(idx) {
        prodArchivosNuevos.splice(idx, 1);
        renderizarPreviewNuevas();
    }

    function eliminarImagenExistente(idImagen) {
        mostrarConfirmacion('¿Eliminar esta imagen?', function() {
            var formData = new FormData();
            formData.append('accion', 'eliminar_imagen');
            formData.append('id_imagen', idImagen);

            fetch(PROD_API, { method: 'POST', body: formData })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data.exito) {
                        var el = document.getElementById('prod-img-exist-' + idImagen);
                        if (el) el.remove();
                        // Si no quedan imágenes, ocultar sección
                        var galeria = document.getElementById('prod-galeria-existente');
                        if (galeria && galeria.children.length === 0) {
                            document.getElementById('prod-imagenes-existentes').classList.add('hidden');
                        }
                    } else {
                        mostrarAlerta(data.error || 'Error al eliminar imagen', 'error');
                    }
                });
        });
    }

    // ========= UTILIDADES =========
    function esc(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    // ========= MODALES DE ALERTA Y CONFIRMACIÓN =========
    var _callbackConfirmacion = null;
    var _callbackAlertaCerrar = null;

    function mostrarAlerta(mensaje, tipo, callbackCerrar) {
        var modal = document.getElementById('prod-modal-alerta');
        var header = document.getElementById('prod-alerta-header');
        var icono = document.getElementById('prod-alerta-icono');
        var titulo = document.getElementById('prod-alerta-titulo');
        var msg = document.getElementById('prod-alerta-mensaje');
        var btn = document.getElementById('prod-alerta-btn');
        _callbackAlertaCerrar = callbackCerrar || null;

        if (tipo === 'exito') {
            header.className = 'px-6 py-4 flex items-center gap-3 bg-green-50';
            icono.className = 'fas fa-check-circle text-green-500 text-2xl';
            titulo.textContent = 'Éxito';
            titulo.className = 'text-lg font-bold text-green-700';
            btn.className = 'w-full py-2.5 rounded-lg font-semibold transition text-sm text-white bg-green-600 hover:bg-green-700';
        } else {
            header.className = 'px-6 py-4 flex items-center gap-3 bg-red-50';
            icono.className = 'fas fa-exclamation-circle text-red-500 text-2xl';
            titulo.textContent = 'Atención';
            titulo.className = 'text-lg font-bold text-red-700';
            btn.className = 'w-full py-2.5 rounded-lg font-semibold transition text-sm text-white bg-red-600 hover:bg-red-700';
        }

        msg.textContent = mensaje;
        modal.classList.remove('hidden');
    }

    function cerrarModalAlerta() {
        document.getElementById('prod-modal-alerta').classList.add('hidden');
        if (_callbackAlertaCerrar) {
            var cb = _callbackAlertaCerrar;
            _callbackAlertaCerrar = null;
            cb();
        }
    }

    function mostrarConfirmacion(mensaje, callback) {
        _callbackConfirmacion = callback;
        document.getElementById('prod-confirmar-mensaje').textContent = mensaje;
        document.getElementById('prod-modal-confirmar').classList.remove('hidden');
    }

    function cerrarModalConfirmar() {
        _callbackConfirmacion = null;
        document.getElementById('prod-modal-confirmar').classList.add('hidden');
    }

    function ejecutarConfirmacion() {
        document.getElementById('prod-modal-confirmar').classList.add('hidden');
        if (_callbackConfirmacion) {
            var cb = _callbackConfirmacion;
            _callbackConfirmacion = null;
            cb();
        }
    }

    // ========= EXPONER AL GLOBAL =========
    window.cargarProductos = cargarProductos;
    window.abrirModalProducto = abrirModalProducto;
    window.editarProducto = editarProducto;
    window.cerrarModalProducto = cerrarModalProducto;
    window.guardarProducto = guardarProducto;
    window.eliminarProducto = eliminarProducto;
    window.previsualizarImagenes = previsualizarImagenes;
    window.quitarImagenNueva = quitarImagenNueva;
    window.eliminarImagenExistente = eliminarImagenExistente;
    window.toggleFechasOferta = toggleFechasOferta;
    window.calcularPrecioDescuento = calcularPrecioDescuento;
    window.mostrarAlerta = mostrarAlerta;
    window.cerrarModalAlerta = cerrarModalAlerta;
    window.mostrarConfirmacion = mostrarConfirmacion;
    window.cerrarModalConfirmar = cerrarModalConfirmar;
    window.ejecutarConfirmacion = ejecutarConfirmacion;

    // ========= INIT =========
    setTimeout(function() {
        cargarProductos();
        var modal = document.getElementById('prod-modal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) cerrarModalProducto();
            });
        }
        var modalAlerta = document.getElementById('prod-modal-alerta');
        if (modalAlerta) {
            modalAlerta.addEventListener('click', function(e) {
                if (e.target === this) cerrarModalAlerta();
            });
        }
        var modalConfirmar = document.getElementById('prod-modal-confirmar');
        if (modalConfirmar) {
            modalConfirmar.addEventListener('click', function(e) {
                if (e.target === this) cerrarModalConfirmar();
            });
        }
        var searchTimeout;
        var searchInput = document.getElementById('prod-busqueda');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() { cargarProductos(); }, 400);
            });
        }
    }, 100);

})(); // Fin IIFE
</script>
</body>
</html>
