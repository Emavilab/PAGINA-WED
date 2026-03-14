/**
 * =====================================================
 * SISTEMA DE ADVERTENCIA DE SESIÓN - v3 FINAL
 * =====================================================
 *
 * Este script detecta la inactividad del usuario y muestra
 * un modal de advertencia antes de cerrar automáticamente
 * la sesión. Permite renovar la sesión desde el modal.
 *
 * FUNCIONALIDADES:
 * - Control del tiempo de sesión y advertencia
 * - Modal de alerta con cuenta regresiva
 * - Renovación de sesión vía AJAX
 * - Detección de actividad del usuario (mouse, teclado, scroll)
 */

(function() {
    'use strict';

    // =====================================================
    // CONFIGURACIÓN
    // =====================================================
    var TIEMPO_SESION = 120;      // Tiempo total de sesión en segundos
    var TIEMPO_ADVERTENCIA = 30;  // Tiempo antes de expirar para mostrar advertencia
    var timerPrincipal = null;     // Timer principal que controla la sesión
    var intervaloCuenta = null;    // Intervalo para cuenta regresiva del modal
    var estaModalAbierto = false;  // Estado del modal
    var ultimaActividad = Date.now(); // Timestamp de última actividad

    // =====================================================
    // FUNCIONES DEL MODAL
    // =====================================================

    // Abrir modal de advertencia de sesión
    function abrirModal() {
        estaModalAbierto = true;
        var m = document.getElementById('modal-advertencia-sesion');
        if (m) m.style.display = 'flex';
    }

    // Cerrar modal de advertencia de sesión
    function cerrarModal() {
        estaModalAbierto = false;
        var m = document.getElementById('modal-advertencia-sesion');
        if (m) m.style.display = 'none';
    }

    // =====================================================
    // CONTROL DE TIMERS
    // =====================================================

    // Detener todos los timers activos
    function pararTodo() {
        if (timerPrincipal !== null) {
            clearTimeout(timerPrincipal);
            timerPrincipal = null;
        }
        if (intervaloCuenta !== null) {
            clearInterval(intervaloCuenta);
            intervaloCuenta = null;
        }
    }

    // Iniciar cuenta regresiva dentro del modal
    function empezarCuentaRegresiva() {
        var seg = TIEMPO_ADVERTENCIA; // Segundos restantes
        var el = document.getElementById('countdown-sesion');
        if (el) el.textContent = seg;

        intervaloCuenta = setInterval(function() {
            if (!estaModalAbierto) {
                clearInterval(intervaloCuenta);
                intervaloCuenta = null;
                return;
            }
            seg--;
            if (el) el.textContent = seg;

            // Cuando llega a cero, cerrar sesión automáticamente
            if (seg <= 0) {
                clearInterval(intervaloCuenta);
                intervaloCuenta = null;
                window.location.href = '../core/destruir_sesion.php';
            }
        }, 1000);
    }

    // Iniciar el temporizador principal de sesión
    function iniciarTemporizador() {
        pararTodo();       // Detener timers anteriores
        cerrarModal();     // Asegurar que el modal esté cerrado
        ultimaActividad = Date.now();

        timerPrincipal = setTimeout(function() {
            timerPrincipal = null;
            abrirModal();         // Mostrar advertencia
            empezarCuentaRegresiva(); // Iniciar cuenta regresiva
        }, (TIEMPO_SESION - TIEMPO_ADVERTENCIA) * 1000);
    }

    // =====================================================
    // BOTÓN "SEGUIR ACTIVO"
    // =====================================================

    window.renovarSesion = function() {
        pararTodo();   // Detener timers
        cerrarModal(); // Cerrar modal

        // Avisar al servidor que la sesión sigue activa
        try {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../core/actualizar_actividad.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(JSON.stringify({ timestamp: Date.now() }));
        } catch(e) {}

        // Reiniciar temporizador desde cero
        iniciarTemporizador();
    };

    // =====================================================
    // DETECCIÓN DE ACTIVIDAD DEL USUARIO
    // =====================================================

    function onActividad() {
        if (estaModalAbierto) return; // No reiniciar si modal está abierto
        var ahora = Date.now();

        // Solo reiniciar si han pasado más de 10 segundos desde la última actividad
        if (ahora - ultimaActividad > 10000) {
            ultimaActividad = ahora;
            pararTodo();

            // Reiniciar timer principal
            timerPrincipal = setTimeout(function() {
                timerPrincipal = null;
                abrirModal();
                empezarCuentaRegresiva();
            }, (TIEMPO_SESION - TIEMPO_ADVERTENCIA) * 1000);

            // Avisar al servidor
            try {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '../core/actualizar_actividad.php', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.send(JSON.stringify({ timestamp: ahora }));
            } catch(e) {}
        }
    }

    // =====================================================
    // INICIO - DETECCIÓN DE EVENTOS
    // =====================================================

    document.addEventListener('mousedown', onActividad, true); // Movimiento de mouse
    document.addEventListener('keydown', onActividad, true);   // Teclado
    document.addEventListener('scroll', onActividad, true);    // Scroll

    iniciarTemporizador(); // Iniciar el temporizador al cargar la página

})(); 