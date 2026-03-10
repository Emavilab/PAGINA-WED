/**
 * Sistema de Advertencia de Sesión por Expirar
 * Muestra un modal con aviso antes de que expire la sesión
 */

console.log('✅ Script de advertencia de sesión cargado');

(function() {
    'use strict';

    // Tiempo de sesión en segundos
    const TIEMPO_SESION = 120; // 2 minutos
    
    let lastActivity = Date.now();
    let sessionTimer = null;
    let modalVisible = false;

    /**
     * Mostrar modal de advertencia
     */
    function mostrarAdvertencia() {
        console.log('⏰ Mostrando advertencia de sesión');
        modalVisible = true;
        
        const modal = document.getElementById('modal-advertencia-sesion');
        if (modal) {
            modal.style.display = 'flex';
            iniciarCuentaRegresiva();
        } else {
            console.error('❌ Modal no encontrado');
        }
    }

    /**
     * Ocultar modal de advertencia
     */
    function ocultarAdvertencia() {
        modalVisible = false;
        const modal = document.getElementById('modal-advertencia-sesion');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    /**
     * Iniciar cuenta regresiva de expiración
     */
    function iniciarCuentaRegresiva() {
        let segundosRestantes = 30;
        const countdownElement = document.getElementById('countdown-sesion');

        const intervalo = setInterval(() => {
            if (countdownElement) {
                countdownElement.textContent = segundosRestantes;
            }

            if (segundosRestantes <= 0) {
                clearInterval(intervalo);
                console.log('⏳ Sesión expirada por inactividad');
                // Redirigir a endpoint que destruye la sesión
                window.location.href = '../core/destruir_sesion.php';
            }

            segundosRestantes--;
        }, 1000);
    }

    /**
     * Reiniciar el temporizador de sesión
     */
    function reiniciarTimer() {
        if (sessionTimer) {
            clearTimeout(sessionTimer);
        }

        // Mostrar advertencia en 90 segundos (120 - 30)
        sessionTimer = setTimeout(() => {
            mostrarAdvertencia();
            
            // Expirar en otros 30 segundos si no hay actividad
            setTimeout(() => {
                if (modalVisible) {
                    window.location.href = '../core/destruir_sesion.php';
                }
            }, 30000);
        }, (TIEMPO_SESION - 30) * 1000);

        ocultarAdvertencia();
        lastActivity = Date.now();
    }

    /**
     * Detectar actividad del usuario
     */
    function detectarActividad() {
        const ahora = Date.now();
        // Solo actualizar si han pasado más de 10 segundos
        if (ahora - lastActivity > 10000) {
            console.log('👆 Actividad detectada, reseteando timer');
            reiniciarTimer();
        }
    }

    /**
     * Renovar sesión (botón "Seguir Activo")
     */
    window.renovarSesion = function() {
        console.log('✅ Sesión renovada por usuario');
        
        // Actualizar en servidor
        fetch('../core/actualizar_actividad.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ timestamp: Date.now() }),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            reiniciarTimer();
        })
        .catch(err => {
            console.error('Error:', err);
            reiniciarTimer();
        });
    };

    /**
     * Inicializar sistema
     */
    function inicializar() {
        console.log('🔧 Inicializando sistema de advertencia de sesión');

        // Escuchar eventos de actividad
        const eventos = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click', 'submit'];
        eventos.forEach(evento => {
            document.addEventListener(evento, detectarActividad, true);
        });

        // Iniciar timer
        reiniciarTimer();
    }

    // Ejecutar cuando DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', inicializar);
    } else {
        inicializar();
    }
})();
