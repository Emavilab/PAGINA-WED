/**
 * Script de Mantenimiento de Sesión
 * Actualiza la actividad de la sesión cuando el usuario interactúa con la página
 */

(function() {
    'use strict';

    // Tiempo entre actualizaciones de actividad (en milisegundos)
    // Se actualiza cada 30 segundos mientras haya actividad
    const INTERVALO_ACTIVIDAD = 30000; // 30 segundos

    // Lista de eventos que indican actividad del usuario
    const eventos = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];

    // Variable para controlar si ya se actualizó recientemente
    let ultimaActualizacion = 0;
    let tiemeroActualizacion = null;

    /**
     * Actualizar la actividad de la sesión en el servidor
     */
    function actualizarActividad() {
        const ahora = Date.now();

        // Solo actualizar si han pasado más de 30 segundos
        if (ahora - ultimaActualizacion < INTERVALO_ACTIVIDAD) {
            return;
        }

        ultimaActualizacion = ahora;

        // Hacer una solicitud silenciosa al servidor para actualizar la sesión
        fetch('../core/actualizar_actividad.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                timestamp: ahora 
            })
        }).catch(function(error) {
            // No mostrar error, esto es silencioso
            console.log('Actividad actualizada');
        });
    }

    /**
     * Registrar eventos de actividad del usuario
     */
    function registrarActividad() {
        actualizarActividad();
    }

    /**
     * Inicializar los listeners de eventos
     */
    function inicializar() {
        // Agregar listeners a todos los eventos de actividad
        eventos.forEach(function(evento) {
            document.addEventListener(evento, registrarActividad, true);
        });

        console.log('✓ Sistema de mantenimiento de sesión iniciado');
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
})();
