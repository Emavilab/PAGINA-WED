<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mensajes - Panel Administrativo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        * {
            scroll-behavior: smooth;
        }

        /* Estilos solo para el contenedor de mensajería */
        #mensajeria-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .message-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-left: 4px solid #3b82f6;
        }

        .message-card.nuevo {
            border-left-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2 0%, #fde2e2 100%);
        }

        .message-card.leido {
            border-left-color: #f59e0b;
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
        }

        .message-card.respondido {
            border-left-color: #10b981;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }

        .message-card.cerrado {
            border-left-color: #6b7280;
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }

        .message-card:hover {
            transform: translateX(8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .badge {
            display: inline-block;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .badge-nuevo {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-leido {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-respondido {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-cerrado {
            background: #f3f4f6;
            color: #4b5563;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 50;
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal.active {
            display: flex;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content {
            animation: slideUp 0.3s ease-in-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 0.5rem;
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        .status-indicator.nuevo {
            background: #ef4444;
        }

        .status-indicator.leido {
            background: #f59e0b;
        }

        .status-indicator.respondido {
            background: #10b981;
        }

        .status-indicator.cerrado {
            background: #6b7280;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .input-focus {
            transition: all 0.2s ease;
        }

        .input-focus:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            transition: all 0.3s ease;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            border-bottom: 2px solid #e5e7eb;
        }

        .tab {
            padding: 1rem;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
            color: #6b7280;
            font-weight: 500;
        }

        .tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }

        .tab:hover {
            color: #3b82f6;
        }

        .search-input {
            position: relative;
        }

        .search-input svg {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            pointer-events: none;
        }

        .counter {
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            background: #f3f4f6;
            color: #374151;
        }
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
                    <span class="counter"><span id="total-mensajes">8</span> mensajes</span>
                </div>
            </div>
        </div>

        <!-- Contenedor principal -->
        <div class="max-w-7xl mx-auto">
            <!-- Filtros y búsqueda -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 search-input">
                        <input 
                            type="text" 
                            id="search-input"
                            placeholder="Buscar por nombre, correo o asunto..."
                            class="w-full px-4 py-2 pl-4 border border-gray-300 rounded-lg input-focus"
                        >
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button class="btn-primary text-white px-6 py-2 rounded-lg font-semibold">
                        Exportar
                    </button>
                </div>

                <!-- Tabs de filtro -->
                <div class="tabs mt-6">
                    <div class="tab active" data-filter="todos">
                        Todos <span class="text-xs ml-1 opacity-75">(8)</span>
                    </div>
                    <div class="tab" data-filter="nuevo">
                        <span class="status-indicator nuevo"></span>Nuevos <span class="text-xs ml-1 opacity-75">(3)</span>
                    </div>
                    <div class="tab" data-filter="leido">
                        <span class="status-indicator leido"></span>Leídos <span class="text-xs ml-1 opacity-75">(2)</span>
                    </div>
                    <div class="tab" data-filter="respondido">
                        <span class="status-indicator respondido"></span>Respondidos <span class="text-xs ml-1 opacity-75">(2)</span>
                    </div>
                    <div class="tab" data-filter="cerrado">
                        <span class="status-indicator cerrado"></span>Cerrados <span class="text-xs ml-1 opacity-75">(1)</span>
                    </div>
                </div>
            </div>

            <!-- Lista de mensajes -->
            <div class="space-y-4" id="mensajes-container">
                <!-- Mensaje 1 - Nuevo -->
                <div class="message-card nuevo p-6 rounded-lg cursor-pointer" data-estado="nuevo" data-id="1">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">Juan Pérez</h3>
                                <span class="badge badge-nuevo">Nuevo</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">juan.perez@email.com</p>
                            <h4 class="font-semibold text-gray-900 mb-2">Duda sobre envío a domicilio</h4>
                            <p class="text-gray-700 line-clamp-2">¿Cuál es el costo del envío a San Pedro Sula? ¿Cuánto tiempo tarda?</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-gray-500">Hace 2 horas</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button class="btn-primary text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 open-modal" data-id="1">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Responder
                        </button>
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold">Marcar como leído</button>
                    </div>
                </div>

                <!-- Mensaje 2 - Nuevo -->
                <div class="message-card nuevo p-6 rounded-lg cursor-pointer" data-estado="nuevo" data-id="2">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">María López</h3>
                                <span class="badge badge-nuevo">Nuevo</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">maria.lopez@email.com</p>
                            <h4 class="font-semibold text-gray-900 mb-2">Problema con pedido #1234</h4>
                            <p class="text-gray-700 line-clamp-2">Recibí el pedido pero uno de los productos viene dañado. ¿Qué hago?</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-gray-500">Hace 5 horas</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button class="btn-primary text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 open-modal" data-id="2">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Responder
                        </button>
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold">Marcar como leído</button>
                    </div>
                </div>

                <!-- Mensaje 3 - Leído -->
                <div class="message-card leido p-6 rounded-lg cursor-pointer" data-estado="leido" data-id="3">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">Carlos Gómez</h3>
                                <span class="badge badge-leido">Leído</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">carlos.gomez@email.com</p>
                            <h4 class="font-semibold text-gray-900 mb-2">Consulta sobre métodos de pago</h4>
                            <p class="text-gray-700 line-clamp-2">¿Aceptan transferencia bancaria? ¿Tienen algún descuento por pago en efectivo?</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-gray-500">Hace 8 horas</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button class="btn-primary text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 open-modal" data-id="3">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Responder
                        </button>
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold">Ver respuesta</button>
                    </div>
                </div>

                <!-- Mensaje 4 - Respondido -->
                <div class="message-card respondido p-6 rounded-lg cursor-pointer" data-estado="respondido" data-id="4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">Ana Rodríguez</h3>
                                <span class="badge badge-respondido">Respondido</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">ana.rodriguez@email.com</p>
                            <h4 class="font-semibold text-gray-900 mb-2">¿Tienen sucursal física?</h4>
                            <p class="text-gray-700 line-clamp-2">Me gustaría saber si puedo ir a ver los productos en persona.</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-gray-500">Ayer</p>
                            <p class="text-xs text-green-600 font-semibold mt-1">Respondido hace 4h</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 open-modal" data-id="4">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver detalle
                        </button>
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold">Cerrar ticket</button>
                    </div>
                </div>

                <!-- Mensaje 5 - Nuevo -->
                <div class="message-card nuevo p-6 rounded-lg cursor-pointer" data-estado="nuevo" data-id="5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">Roberto Sánchez</h3>
                                <span class="badge badge-nuevo">Nuevo</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">roberto.sanchez@email.com</p>
                            <h4 class="font-semibold text-gray-900 mb-2">Descuento para compras al por mayor</h4>
                            <p class="text-gray-700 line-clamp-2">Soy distribuidor, ¿tienen programa de descuentos para compras en volumen?</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-gray-500">Hace 12 horas</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button class="btn-primary text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 open-modal" data-id="5">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Responder
                        </button>
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold">Marcar como leído</button>
                    </div>
                </div>

                <!-- Mensaje 6 - Respondido -->
                <div class="message-card respondido p-6 rounded-lg cursor-pointer" data-estado="respondido" data-id="6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">Laura Martínez</h3>
                                <span class="badge badge-respondido">Respondido</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">laura.martinez@email.com</p>
                            <h4 class="font-semibold text-gray-900 mb-2">Cambio de producto</h4>
                            <p class="text-gray-700 line-clamp-2">Deseo cambiar el color del producto que compré. ¿Es posible?</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-gray-500">Hace 2 días</p>
                            <p class="text-xs text-green-600 font-semibold mt-1">Respondido hace 1d</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 open-modal" data-id="6">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver detalle
                        </button>
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold">Cerrar ticket</button>
                    </div>
                </div>

                <!-- Mensaje 7 - Leído -->
                <div class="message-card leido p-6 rounded-lg cursor-pointer" data-estado="leido" data-id="7">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">David Torres</h3>
                                <span class="badge badge-leido">Leído</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">david.torres@email.com</p>
                            <h4 class="font-semibold text-gray-900 mb-2">Garantía de productos</h4>
                            <p class="text-gray-700 line-clamp-2">¿Qué garantía tienen los productos? ¿Cuál es el tiempo de cobertura?</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-gray-500">Hace 3 días</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button class="btn-primary text-white px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 open-modal" data-id="7">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Responder
                        </button>
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold">Ver respuesta</button>
                    </div>
                </div>

                <!-- Mensaje 8 - Cerrado -->
                <div class="message-card cerrado p-6 rounded-lg cursor-pointer" data-estado="cerrado" data-id="8">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-gray-900">Sofía García</h3>
                                <span class="badge badge-cerrado">Cerrado</span>
                            </div>
                            <p class="text-sm text-gray-600 mb-2">sofia.garcia@email.com</p>
                            <h4 class="font-semibold text-gray-900 mb-2">Consulta de disponibilidad</h4>
                            <p class="text-gray-700 line-clamp-2">¿Tienen en stock el producto XYZ en color azul?</p>
                        </div>
                        <div class="text-right ml-4">
                            <p class="text-xs text-gray-500">Hace 5 días</p>
                            <p class="text-xs text-gray-600 font-semibold mt-1">Cerrado hace 2d</p>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-4 pt-4 border-t border-gray-200">
                        <button class="btn-secondary px-4 py-2 rounded-lg text-sm font-semibold flex items-center gap-2 open-modal" data-id="8">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Ver detalle
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para responder/ver mensaje -->
    <div class="modal" id="messageModal">
        <div class="modal-content bg-white rounded-lg shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto m-auto">
            <!-- Header del modal -->
            <div class="sticky top-0 bg-white border-b border-gray-200 p-6 flex items-center justify-between">
                <h2 class="text-2xl font-bold text-gray-900">Mensaje de Contacto</h2>
                <button class="close-modal text-gray-500 hover:text-gray-700 text-2xl" onclick="closeModal()">&times;</button>
            </div>

            <!-- Contenido del modal -->
            <div class="p-6">
                <!-- Información del mensaje -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Nombre</p>
                            <p class="text-lg font-semibold text-gray-900" id="modal-nombre">Juan Pérez</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Correo</p>
                            <p class="text-lg font-semibold text-gray-900" id="modal-correo">juan.perez@email.com</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Teléfono</p>
                            <p class="text-lg font-semibold text-gray-900" id="modal-telefono">+504 9999-9999</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 uppercase tracking-wide">Fecha</p>
                            <p class="text-lg font-semibold text-gray-900" id="modal-fecha">Hoy, 14:30</p>
                        </div>
                    </div>
                </div>

                <!-- Mensaje original -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Asunto</h3>
                    <p class="text-gray-700 font-semibold mb-4" id="modal-asunto">Duda sobre envío a domicilio</p>
                    
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Mensaje</h4>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <p class="text-gray-700 leading-relaxed" id="modal-mensaje">
                            ¿Cuál es el costo del envío a San Pedro Sula? ¿Cuánto tiempo tarda la entrega?
                        </p>
                    </div>
                </div>

                <!-- Respuesta (si existe) -->
                <div class="mb-8" id="modal-respuesta-container" style="display: none;">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Tu Respuesta</h4>
                    <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                        <p class="text-gray-700 leading-relaxed" id="modal-respuesta-text">
                            Gracias por contactarnos. El envío a San Pedro Sula tiene un costo de Lps. 150 y tarda 3-5 días hábiles.
                        </p>
                    </div>
                </div>

                <!-- Formulario de respuesta (si no está respondido o cerrado) -->
                <div id="modal-form-container">
                    <h4 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Tu Respuesta</h4>
                    <textarea 
                        id="respuesta-text"
                        placeholder="Escribe tu respuesta aquí..."
                        rows="6"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg input-focus resize-none"
                    ></textarea>
                    <p class="text-xs text-gray-500 mt-2">Se enviará un correo al cliente con tu respuesta</p>
                </div>

                <!-- Acciones -->
                <div class="flex flex-col md:flex-row gap-3 mt-8 pt-6 border-t border-gray-200">
                    <button onclick="marcarComoLeido()" class="btn-secondary px-6 py-3 rounded-lg font-semibold flex-1">
                        Marcar como Leído
                    </button>
                    <button onclick="guardarRespuesta()" class="btn-primary text-white px-6 py-3 rounded-lg font-semibold flex-1">
                        Guardar Respuesta
                    </button>
                    <button onclick="cerrarTicket()" class="px-6 py-3 rounded-lg font-semibold bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 flex-1">
                        Cerrar Ticket
                    </button>
                    <button onclick="closeModal()" class="px-6 py-3 rounded-lg font-semibold bg-gray-100 text-gray-700 flex-1">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Datos de ejemplo
        const mensajes = [
            {
                id: 1,
                nombre: "Juan Pérez",
                correo: "juan.perez@email.com",
                telefono: "+504 9999-9999",
                asunto: "Duda sobre envío a domicilio",
                mensaje: "¿Cuál es el costo del envío a San Pedro Sula? ¿Cuánto tiempo tarda?",
                estado: "nuevo",
                fecha: "Hoy, 14:30",
                respuesta: null
            },
            {
                id: 2,
                nombre: "María López",
                correo: "maria.lopez@email.com",
                telefono: "+504 8888-8888",
                asunto: "Problema con pedido #1234",
                mensaje: "Recibí el pedido pero uno de los productos viene dañado. ¿Qué hago?",
                estado: "nuevo",
                fecha: "Ayer, 10:15",
                respuesta: null
            },
            {
                id: 3,
                nombre: "Carlos Gómez",
                correo: "carlos.gomez@email.com",
                telefono: "+504 7777-7777",
                asunto: "Consulta sobre métodos de pago",
                mensaje: "¿Aceptan transferencia bancaria? ¿Tienen algún descuento por pago en efectivo?",
                estado: "leido",
                fecha: "Hace 2 días",
                respuesta: null
            },
            {
                id: 4,
                nombre: "Ana Rodríguez",
                correo: "ana.rodriguez@email.com",
                telefono: "+504 6666-6666",
                asunto: "¿Tienen sucursal física?",
                mensaje: "Me gustaría saber si puedo ir a ver los productos en persona.",
                estado: "respondido",
                fecha: "Hace 3 días",
                respuesta: "Sí, tenemos una sucursal en la Avenida Principal. Abierto de lunes a viernes de 9am a 6pm."
            }
        ];

        function openModal(id) {
            const mensaje = mensajes.find(m => m.id == id) || {
                id: id,
                nombre: "Cliente",
                correo: "cliente@email.com",
                telefono: "+504 0000-0000",
                asunto: "Asunto",
                mensaje: "Contenido del mensaje",
                estado: "nuevo",
                fecha: "Hoy",
                respuesta: null
            };

            document.getElementById('modal-nombre').textContent = mensaje.nombre;
            document.getElementById('modal-correo').textContent = mensaje.correo;
            document.getElementById('modal-telefono').textContent = mensaje.telefono;
            document.getElementById('modal-fecha').textContent = mensaje.fecha;
            document.getElementById('modal-asunto').textContent = mensaje.asunto;
            document.getElementById('modal-mensaje').textContent = mensaje.mensaje;

            const respuestaContainer = document.getElementById('modal-respuesta-container');
            const formContainer = document.getElementById('modal-form-container');

            if (mensaje.respuesta) {
                document.getElementById('modal-respuesta-text').textContent = mensaje.respuesta;
                respuestaContainer.style.display = 'block';
                formContainer.style.display = 'none';
            } else {
                respuestaContainer.style.display = 'none';
                formContainer.style.display = 'block';
                document.getElementById('respuesta-text').value = '';
            }

            document.getElementById('messageModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('messageModal').classList.remove('active');
        }

        function filterMensajes(filter) {
            const cards = document.querySelectorAll('.message-card');
            cards.forEach(card => {
                if (filter === 'todos' || card.getAttribute('data-estado') === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function marcarComoLeido() {
            CustomModal.show('success', 'Éxito', 'Mensaje marcado como leído', () => {
                closeModal();
            });
        }

        function guardarRespuesta() {
            const respuesta = document.getElementById('respuesta-text').value;
            if (respuesta.trim()) {
                CustomModal.show('success', 'Éxito', 'Respuesta guardada y correo enviado al cliente', () => {
                    closeModal();
                });
            } else {
                CustomModal.show('warning', 'Campo vacío', 'Por favor escribe una respuesta');
            }
        }

        function cerrarTicket() {
            CustomModal.show('confirm', 'Confirmar', '¿Estás seguro de que deseas cerrar este ticket?', (confirmed) => {
                if (confirmed) {
                    CustomModal.show('success', 'Éxito', 'Ticket cerrado', () => {
                        closeModal();
                    });
                }
            });
        }

        // Función para inicializar los event listeners
        function initMensajeriaFunctions() {
            // Abrir modal
            document.querySelectorAll('.open-modal').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    openModal(id);
                });
            });

            // Cerrar modal al hacer clic fuera
            const messageModal = document.getElementById('messageModal');
            if (messageModal) {
                messageModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeModal();
                    }
                });
            }

            // Filtros
            document.querySelectorAll('.tab').forEach(tab => {
                tab.addEventListener('click', function() {
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    
                    const filter = this.getAttribute('data-filter');
                    filterMensajes(filter);
                });
            });

            // Búsqueda
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.addEventListener('keyup', function(e) {
                    const search = e.target.value.toLowerCase();
                    const cards = document.querySelectorAll('.message-card');
                    
                    cards.forEach(card => {
                        const nombre = card.querySelector('h3').textContent.toLowerCase();
                        const correo = card.querySelector('.text-sm').textContent.toLowerCase();
                        const asunto = card.querySelector('h4').textContent.toLowerCase();
                        
                        if (nombre.includes(search) || correo.includes(search) || asunto.includes(search)) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            }
        }

        // Ejecutar al cargar el contenido
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initMensajeriaFunctions);
        } else {
            initMensajeriaFunctions();
        }
    </script>
</body>
</html>