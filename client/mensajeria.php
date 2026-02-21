<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mensajes - Panel Administrativo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { scroll-behavior: smooth; }

        #mensajeria-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .message-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid #3b82f6;
        }
        .message-card.nuevo { border-left-color: #ef4444; background: linear-gradient(135deg, #fef2f2 0%, #fde2e2 100%); }
        .message-card.leido { border-left-color: #f59e0b; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); }
        .message-card.respondido { border-left-color: #10b981; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); }
        .message-card.cerrado { border-left-color: #6b7280; background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%); }
        .message-card:hover { transform: translateX(8px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }

        .badge { display: inline-block; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; }
        .badge-nuevo { background: #fee2e2; color: #991b1b; }
        .badge-leido { background: #fef3c7; color: #92400e; }
        .badge-respondido { background: #dcfce7; color: #15803d; }
        .badge-cerrado { background: #f3f4f6; color: #4b5563; }

        .modal-msg { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 50; animation: fadeInMsg 0.3s ease-in-out; }
        .modal-msg.active { display: flex; }

        @keyframes fadeInMsg { from { opacity: 0; } to { opacity: 1; } }
        .modal-content-msg { animation: slideUpMsg 0.3s ease-in-out; }
        @keyframes slideUpMsg { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .status-indicator { width: 12px; height: 12px; border-radius: 50%; display: inline-block; margin-right: 0.5rem; animation: pulseMsg 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        .status-indicator.nuevo { background: #ef4444; }
        .status-indicator.leido { background: #f59e0b; }
        .status-indicator.respondido { background: #10b981; }
        .status-indicator.cerrado { background: #6b7280; }
        @keyframes pulseMsg { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

        .input-focus-msg { transition: all 0.2s ease; }
        .input-focus-msg:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }

        .btn-primary-msg { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); transition: all 0.3s ease; }
        .btn-primary-msg:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4); }
        .btn-secondary-msg { background: #f3f4f6; color: #374151; transition: all 0.3s ease; }
        .btn-secondary-msg:hover { background: #e5e7eb; }

        .tabs-msg { display: flex; gap: 1rem; border-bottom: 2px solid #e5e7eb; flex-wrap: wrap; }
        .tab-msg { padding: 1rem; cursor: pointer; border-bottom: 3px solid transparent; transition: all 0.3s ease; color: #6b7280; font-weight: 500; }
        .tab-msg.active { color: #3b82f6; border-bottom-color: #3b82f6; }
        .tab-msg:hover { color: #3b82f6; }

        .search-input-wrapper { position: relative; }
        .search-input-wrapper svg { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; pointer-events: none; }

        .counter-msg { font-size: 0.875rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 0.5rem; background: #f3f4f6; color: #374151; }

        .loading-spinner-msg { border: 3px solid #e5e7eb; border-top: 3px solid #3b82f6; border-radius: 50%; width: 40px; height: 40px; animation: spinMsg 0.8s linear infinite; margin: 2rem auto; }
        @keyframes spinMsg { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="min-h-screen p-6" id="mensajeria-container">
        <!-- Header -->
        <div class="max-w-7xl mx-auto mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Mensajes de Contacto</h1>
                    <p class="text-gray-600">Gestiona y responde los mensajes de tus clientes</p>
                </div>
                <div class="flex gap-3">
                    <span class="counter-msg"><span id="total-mensajes">0</span> mensajes</span>
                </div>
            </div>
        </div>

        <!-- Contenedor principal -->
        <div class="max-w-7xl mx-auto">
            <!-- Filtros y búsqueda -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 search-input-wrapper">
                        <input 
                            type="text" 
                            id="search-input-msg"
                            placeholder="Buscar por nombre, correo o asunto..."
                            class="w-full px-4 py-2 pl-4 border border-gray-300 rounded-lg input-focus-msg"
                        >
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button onclick="cargarMensajes()" class="btn-primary-msg text-white px-6 py-2 rounded-lg font-semibold">
                        Actualizar
                    </button>
                </div>

                <!-- Tabs de filtro -->
                <div class="tabs-msg mt-6">
                    <div class="tab-msg active" data-filter="todos">
                        Todos <span class="text-xs ml-1 opacity-75" id="conteo-todos">(0)</span>
                    </div>
                    <div class="tab-msg" data-filter="nuevo">
                        <span class="status-indicator nuevo"></span>Nuevos <span class="text-xs ml-1 opacity-75" id="conteo-nuevo">(0)</span>
                    </div>
                    <div class="tab-msg" data-filter="leido">
                        <span class="status-indicator leido"></span>Leídos <span class="text-xs ml-1 opacity-75" id="conteo-leido">(0)</span>
                    </div>
                    <div class="tab-msg" data-filter="respondido">
                        <span class="status-indicator respondido"></span>Respondidos <span class="text-xs ml-1 opacity-75" id="conteo-respondido">(0)</span>
                    </div>
                    <div class="tab-msg" data-filter="cerrado">
                        <span class="status-indicator cerrado"></span>Cerrados <span class="text-xs ml-1 opacity-75" id="conteo-cerrado">(0)</span>
                    </div>
                </div>
            </div>

            <!-- Lista de mensajes (se llena dinámicamente) -->
            <div class="space-y-4" id="mensajes-container">
                <div class="loading-spinner-msg"></div>
            </div>
        </div>
    </div>

    <!-- Modal para responder/ver mensaje -->
    <div class="modal-msg" id="messageModalMsg">
        <div class="modal-content-msg bg-white rounded-lg shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto m-auto">
            <!-- Header del modal -->
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between z-10">
                <h2 class="text-2xl font-bold text-gray-900">Mensaje de Contacto</h2>
                <button class="text-gray-500 hover:text-gray-700 text-2xl" onclick="cerrarModalMsg()">&times;</button>
            </div>

            <!-- Contenido del modal -->
            <div class="p-6">
                <!-- ID oculto -->
                <input type="hidden" id="modal-id-msg">
                <input type="hidden" id="modal-estado-msg">

                <!-- Información del mensaje -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Nombre</p>
                            <p class="text-lg font-semibold text-gray-900" id="modal-nombre-msg"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Correo</p>
                            <p class="text-lg font-semibold text-gray-900 break-all" id="modal-correo-msg"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Teléfono</p>
                            <p class="text-lg font-semibold text-gray-900" id="modal-telefono-msg"></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Fecha</p>
                            <p class="text-lg font-semibold text-gray-900" id="modal-fecha-msg"></p>
                        </div>
                    </div>
                </div>

                <!-- Mensaje original -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Asunto</h3>
                    <p class="text-gray-700 font-semibold mb-4" id="modal-asunto-msg"></p>
                    
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Mensaje</h4>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap" id="modal-mensaje-msg"></p>
                    </div>
                </div>

                <!-- Respuesta existente (si ya fue respondido) -->
                <div class="mb-8 hidden" id="modal-respuesta-container-msg">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Respuesta enviada</h4>
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <p class="text-gray-700 leading-relaxed whitespace-pre-wrap" id="modal-respuesta-text-msg"></p>
                    </div>
                    <p class="text-xs text-gray-500 mt-2" id="modal-fecha-respuesta-msg"></p>
                </div>

                <!-- Formulario de respuesta (si no está respondido o cerrado) -->
                <div id="modal-form-container-msg" class="hidden">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Tu Respuesta</h4>
                    <textarea 
                        id="respuesta-text-msg"
                        placeholder="Escribe tu respuesta aquí..."
                        rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg input-focus-msg resize-none"
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-2">La respuesta quedará registrada en el sistema</p>
                </div>

                <!-- Acciones -->
                <div class="flex flex-col md:flex-row gap-3 mt-8 pt-6 border-t border-gray-200" id="modal-acciones-msg">
                </div>
            </div>
        </div>
    </div>

    <script>
    // ============ CONFIGURACIÓN ============
    // Detectar la ruta base relativa para la API
    const MSG_API_URL = (function() {
        const path = window.location.pathname;
        if (path.includes('/admin/')) {
            return '../api/api_mensajeria.php';
        }
        return 'api/api_mensajeria.php';
    })();

    let filtroActualMsg = 'todos';
    let mensajesCargadosMsg = [];

    // ============ CARGAR MENSAJES ============
    function cargarMensajes() {
        const busqueda = document.getElementById('search-input-msg')?.value || '';
        const contenedor = document.getElementById('mensajes-container');
        contenedor.innerHTML = '<div class="loading-spinner-msg"></div>';

        let url = MSG_API_URL + '?accion=listar';
        if (filtroActualMsg && filtroActualMsg !== 'todos') {
            url += '&estado=' + encodeURIComponent(filtroActualMsg);
        }
        if (busqueda.trim()) {
            url += '&busqueda=' + encodeURIComponent(busqueda.trim());
        }

        fetch(url)
            .then(res => res.json())
            .then(data => {
                if (data.exito) {
                    mensajesCargadosMsg = data.mensajes;
                    renderizarMensajes(data.mensajes);
                    actualizarConteos(data.conteos);
                } else {
                    contenedor.innerHTML = '<div class="text-center text-red-500 py-8"><p>Error al cargar mensajes: ' + (data.error || 'desconocido') + '</p></div>';
                }
            })
            .catch(err => {
                console.error('Error cargando mensajes:', err);
                contenedor.innerHTML = '<div class="text-center text-red-500 py-8"><p>Error de conexión con el servidor</p></div>';
            });
    }

    // ============ RENDERIZAR MENSAJES ============
    function renderizarMensajes(mensajes) {
        const contenedor = document.getElementById('mensajes-container');

        if (mensajes.length === 0) {
            contenedor.innerHTML = `
                <div class="text-center py-16">
                    <svg class="mx-auto mb-4" width="64" height="64" fill="none" stroke="#9ca3af" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg font-semibold">No hay mensajes</p>
                    <p class="text-gray-400 text-sm mt-1">Los mensajes enviados desde el formulario de contacto aparecerán aquí</p>
                </div>`;
            return;
        }

        let html = '';
        mensajes.forEach(msg => {
            const fecha = formatearFechaMsg(msg.fecha_mensaje);
            const badgeClass = 'badge-' + msg.estado;
            const estadoLabel = { 'nuevo': 'Nuevo', 'leido': 'Leído', 'respondido': 'Respondido', 'cerrado': 'Cerrado' };

            let botonesHtml = '';
            if (msg.estado === 'nuevo') {
                botonesHtml = `
                    <button class="btn-primary-msg text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2" onclick="event.stopPropagation(); abrirModalMsg(${msg.id_mensaje})">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Responder
                    </button>
                    <button class="btn-secondary-msg px-4 py-2 rounded-lg text-sm font-semibold" onclick="event.stopPropagation(); marcarComoLeidoMsg(${msg.id_mensaje})">Marcar como leído</button>
                    <button class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100" onclick="event.stopPropagation(); eliminarMensajeMsg(${msg.id_mensaje})">Eliminar</button>`;
            } else if (msg.estado === 'leido') {
                botonesHtml = `
                    <button class="btn-primary-msg text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2" onclick="event.stopPropagation(); abrirModalMsg(${msg.id_mensaje})">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Responder
                    </button>
                    <button class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100" onclick="event.stopPropagation(); eliminarMensajeMsg(${msg.id_mensaje})">Eliminar</button>`;
            } else if (msg.estado === 'respondido') {
                botonesHtml = `
                    <button class="btn-secondary-msg px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2" onclick="event.stopPropagation(); abrirModalMsg(${msg.id_mensaje})">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Ver detalle
                    </button>
                    <button class="btn-secondary-msg px-4 py-2 rounded-lg text-sm font-semibold" onclick="event.stopPropagation(); cerrarTicketMsg(${msg.id_mensaje})">Cerrar ticket</button>
                    <button class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100" onclick="event.stopPropagation(); eliminarMensajeMsg(${msg.id_mensaje})">Eliminar</button>`;
            } else if (msg.estado === 'cerrado') {
                botonesHtml = `
                    <button class="btn-secondary-msg px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2" onclick="event.stopPropagation(); abrirModalMsg(${msg.id_mensaje})">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Ver detalle
                    </button>
                    <button class="px-4 py-2 rounded-lg text-sm font-semibold bg-red-50 text-red-600 border border-red-200 hover:bg-red-100" onclick="event.stopPropagation(); eliminarMensajeMsg(${msg.id_mensaje})">Eliminar</button>`;
            }

            let respuestaInfo = '';
            if (msg.estado === 'respondido' && msg.fecha_respuesta) {
                respuestaInfo = '<p class="text-xs text-green-600 font-semibold mt-1">Respondido ' + formatearFechaMsg(msg.fecha_respuesta) + '</p>';
            }
            if (msg.estado === 'cerrado') {
                respuestaInfo = '<p class="text-xs text-gray-500 font-semibold mt-1">Cerrado</p>';
            }

            html += `
            <div class="message-card ${msg.estado} p-6 rounded-lg cursor-pointer" data-estado="${msg.estado}" data-id="${msg.id_mensaje}" onclick="abrirModalMsg(${msg.id_mensaje})">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <h3 class="text-lg font-bold text-gray-900">${escapeHtmlMsg(msg.nombre)}</h3>
                            <span class="badge ${badgeClass}">${estadoLabel[msg.estado] || msg.estado}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-2">${escapeHtmlMsg(msg.correo)}</p>
                        <h4 class="font-semibold text-gray-900 mb-2">${escapeHtmlMsg(msg.asunto)}</h4>
                        <p class="text-gray-700 line-clamp-2">${escapeHtmlMsg(msg.mensaje)}</p>
                    </div>
                    <div class="text-right ml-4 flex-shrink-0">
                        <p class="text-xs text-gray-500">${fecha}</p>
                        ${respuestaInfo}
                    </div>
                </div>
                <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200 flex-wrap">
                    ${botonesHtml}
                </div>
            </div>`;
        });

        contenedor.innerHTML = html;
    }

    // ============ ACTUALIZAR CONTEOS ============
    function actualizarConteos(conteos) {
        document.getElementById('total-mensajes').textContent = conteos.todos || 0;
        document.getElementById('conteo-todos').textContent = '(' + (conteos.todos || 0) + ')';
        document.getElementById('conteo-nuevo').textContent = '(' + (conteos.nuevo || 0) + ')';
        document.getElementById('conteo-leido').textContent = '(' + (conteos.leido || 0) + ')';
        document.getElementById('conteo-respondido').textContent = '(' + (conteos.respondido || 0) + ')';
        document.getElementById('conteo-cerrado').textContent = '(' + (conteos.cerrado || 0) + ')';
    }

    // ============ ABRIR MODAL ============
    function abrirModalMsg(id) {
        fetch(MSG_API_URL + '?accion=obtener&id=' + id)
            .then(res => res.json())
            .then(data => {
                if (!data.exito) {
                    if (typeof CustomModal !== 'undefined') {
                        CustomModal.show('error', 'Error', data.error || 'No se pudo cargar el mensaje');
                    }
                    return;
                }
                const msg = data.mensaje;
                document.getElementById('modal-id-msg').value = msg.id_mensaje;
                document.getElementById('modal-estado-msg').value = msg.estado;
                document.getElementById('modal-nombre-msg').textContent = msg.nombre;
                document.getElementById('modal-correo-msg').textContent = msg.correo;
                document.getElementById('modal-telefono-msg').textContent = msg.telefono || 'No proporcionado';
                document.getElementById('modal-fecha-msg').textContent = formatearFechaCompletaMsg(msg.fecha_mensaje);
                document.getElementById('modal-asunto-msg').textContent = msg.asunto;
                document.getElementById('modal-mensaje-msg').textContent = msg.mensaje;

                const respuestaContainer = document.getElementById('modal-respuesta-container-msg');
                const formContainer = document.getElementById('modal-form-container-msg');
                const accionesDiv = document.getElementById('modal-acciones-msg');

                // Mostrar respuesta existente si hay
                if (msg.respuesta) {
                    document.getElementById('modal-respuesta-text-msg').textContent = msg.respuesta;
                    document.getElementById('modal-fecha-respuesta-msg').textContent = msg.fecha_respuesta ? 'Respondido el ' + formatearFechaCompletaMsg(msg.fecha_respuesta) : '';
                    respuestaContainer.classList.remove('hidden');
                } else {
                    respuestaContainer.classList.add('hidden');
                }

                // Mostrar formulario solo si NO está respondido ni cerrado
                if (msg.estado === 'nuevo' || msg.estado === 'leido') {
                    formContainer.classList.remove('hidden');
                    document.getElementById('respuesta-text-msg').value = '';
                } else {
                    formContainer.classList.add('hidden');
                }

                // Botones de acción según estado
                let botonesModal = '';
                if (msg.estado === 'nuevo') {
                    botonesModal = `
                        <button onclick="accionMarcarLeidoMsg()" class="btn-secondary-msg px-6 py-3 rounded-lg font-semibold flex-1">Marcar como Leído</button>
                        <button onclick="accionGuardarRespuestaMsg()" class="btn-primary-msg text-white px-6 py-3 rounded-lg font-semibold flex-1">Guardar Respuesta</button>
                        <button onclick="accionCerrarTicketMsg()" class="px-6 py-3 rounded-lg font-semibold bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 flex-1">Cerrar Ticket</button>
                        <button onclick="cerrarModalMsg()" class="px-6 py-3 rounded-lg font-semibold bg-gray-100 text-gray-700 flex-1">Cerrar</button>`;
                } else if (msg.estado === 'leido') {
                    botonesModal = `
                        <button onclick="accionGuardarRespuestaMsg()" class="btn-primary-msg text-white px-6 py-3 rounded-lg font-semibold flex-1">Guardar Respuesta</button>
                        <button onclick="accionCerrarTicketMsg()" class="px-6 py-3 rounded-lg font-semibold bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 flex-1">Cerrar Ticket</button>
                        <button onclick="cerrarModalMsg()" class="px-6 py-3 rounded-lg font-semibold bg-gray-100 text-gray-700 flex-1">Cerrar</button>`;
                } else if (msg.estado === 'respondido') {
                    botonesModal = `
                        <button onclick="accionCerrarTicketMsg()" class="px-6 py-3 rounded-lg font-semibold bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 flex-1">Cerrar Ticket</button>
                        <button onclick="cerrarModalMsg()" class="px-6 py-3 rounded-lg font-semibold bg-gray-100 text-gray-700 flex-1">Cerrar</button>`;
                } else {
                    botonesModal = `<button onclick="cerrarModalMsg()" class="px-6 py-3 rounded-lg font-semibold bg-gray-100 text-gray-700 flex-1">Cerrar</button>`;
                }
                accionesDiv.innerHTML = botonesModal;

                document.getElementById('messageModalMsg').classList.add('active');

                // Auto-marcar como leído si es nuevo (silencioso)
                if (msg.estado === 'nuevo') {
                    ejecutarAccionMsg('marcar_leido', { id: msg.id_mensaje }, false);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                if (typeof CustomModal !== 'undefined') {
                    CustomModal.show('error', 'Error', 'Error de conexión al cargar el mensaje');
                }
            });
    }

    function cerrarModalMsg() {
        document.getElementById('messageModalMsg').classList.remove('active');
    }

    // ============ ACCIONES DESDE EL MODAL ============
    function accionMarcarLeidoMsg() {
        const id = document.getElementById('modal-id-msg').value;
        ejecutarAccionMsg('marcar_leido', { id: id }, true, 'Mensaje marcado como leído');
    }

    function accionGuardarRespuestaMsg() {
        const id = document.getElementById('modal-id-msg').value;
        const respuesta = document.getElementById('respuesta-text-msg').value.trim();
        if (!respuesta) {
            if (typeof CustomModal !== 'undefined') {
                CustomModal.show('warning', 'Campo vacío', 'Por favor escribe una respuesta antes de guardar');
            }
            return;
        }
        ejecutarAccionMsg('responder', { id: id, respuesta: respuesta }, true, 'Respuesta guardada correctamente');
    }

    function accionCerrarTicketMsg() {
        if (typeof CustomModal !== 'undefined') {
            CustomModal.show('confirm', 'Confirmar', '¿Estás seguro de que deseas cerrar este ticket?', (confirmed) => {
                if (confirmed) {
                    const id = document.getElementById('modal-id-msg').value;
                    ejecutarAccionMsg('cerrar', { id: id }, true, 'Ticket cerrado correctamente');
                }
            });
        } else {
            const id = document.getElementById('modal-id-msg').value;
            ejecutarAccionMsg('cerrar', { id: id }, true, 'Ticket cerrado correctamente');
        }
    }

    // ============ ACCIONES DESDE LISTADO ============
    function marcarComoLeidoMsg(id) {
        ejecutarAccionMsg('marcar_leido', { id: id }, true, 'Mensaje marcado como leído');
    }

    function cerrarTicketMsg(id) {
        if (typeof CustomModal !== 'undefined') {
            CustomModal.show('confirm', 'Confirmar', '¿Estás seguro de que deseas cerrar este ticket?', (confirmed) => {
                if (confirmed) {
                    ejecutarAccionMsg('cerrar', { id: id }, true, 'Ticket cerrado correctamente');
                }
            });
        } else {
            ejecutarAccionMsg('cerrar', { id: id }, true, 'Ticket cerrado correctamente');
        }
    }

    function eliminarMensajeMsg(id) {
        if (typeof CustomModal !== 'undefined') {
            CustomModal.show('confirm', 'Eliminar mensaje', '¿Estás seguro de que deseas eliminar este mensaje? Esta acción no se puede deshacer.', (confirmed) => {
                if (confirmed) {
                    ejecutarAccionMsg('eliminar', { id: id }, true, 'Mensaje eliminado correctamente');
                }
            });
        } else {
            ejecutarAccionMsg('eliminar', { id: id }, true, 'Mensaje eliminado correctamente');
        }
    }

    // ============ EJECUTAR ACCIÓN EN API ============
    function ejecutarAccionMsg(accion, datos, recargar, mensajeExito) {
        const formData = new FormData();
        formData.append('accion', accion);
        for (const key in datos) {
            formData.append(key, datos[key]);
        }

        fetch(MSG_API_URL, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.exito) {
                if (recargar && mensajeExito) {
                    cerrarModalMsg();
                    if (typeof CustomModal !== 'undefined') {
                        CustomModal.show('success', 'Éxito', mensajeExito, () => {
                            cargarMensajes();
                        });
                    } else {
                        cargarMensajes();
                    }
                }
            } else {
                if (typeof CustomModal !== 'undefined') {
                    CustomModal.show('error', 'Error', data.error || 'Ocurrió un error');
                } else {
                    console.error('Error:', data.error);
                }
            }
        })
        .catch(err => {
            console.error('Error:', err);
            if (typeof CustomModal !== 'undefined') {
                CustomModal.show('error', 'Error', 'Error de conexión con el servidor');
            }
        });
    }

    // ============ UTILIDADES ============
    function escapeHtmlMsg(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function formatearFechaMsg(fechaStr) {
        if (!fechaStr) return '';
        const fecha = new Date(fechaStr);
        const ahora = new Date();
        const diff = ahora - fecha;
        const minutos = Math.floor(diff / 60000);
        const horas = Math.floor(diff / 3600000);
        const dias = Math.floor(diff / 86400000);

        if (minutos < 1) return 'Justo ahora';
        if (minutos < 60) return 'Hace ' + minutos + ' min';
        if (horas < 24) return 'Hace ' + horas + 'h';
        if (dias === 1) return 'Ayer';
        if (dias < 7) return 'Hace ' + dias + ' días';
        return fecha.toLocaleDateString('es-HN', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function formatearFechaCompletaMsg(fechaStr) {
        if (!fechaStr) return '';
        const fecha = new Date(fechaStr);
        return fecha.toLocaleDateString('es-HN', { 
            day: '2-digit', month: 'long', year: 'numeric', 
            hour: '2-digit', minute: '2-digit' 
        });
    }

    // ============ INICIALIZACIÓN ============
    function initMensajeriaFunctions() {
        // Cargar mensajes al inicio
        cargarMensajes();

        // Cerrar modal al hacer clic fuera
        const modal = document.getElementById('messageModalMsg');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) cerrarModalMsg();
            });
        }

        // Filtros (tabs)
        document.querySelectorAll('#mensajeria-container .tab-msg').forEach(tab => {
            tab.addEventListener('click', function() {
                document.querySelectorAll('#mensajeria-container .tab-msg').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                filtroActualMsg = this.getAttribute('data-filter');
                cargarMensajes();
            });
        });

        // Búsqueda con debounce
        let timeoutBusquedaMsg;
        const searchInput = document.getElementById('search-input-msg');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                clearTimeout(timeoutBusquedaMsg);
                timeoutBusquedaMsg = setTimeout(() => {
                    cargarMensajes();
                }, 400);
            });
        }
    }

    // Ejecutar al cargar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMensajeriaFunctions);
    } else {
        initMensajeriaFunctions();
    }
    </script>
</body>
</html>
