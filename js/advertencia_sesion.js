/**
 * Sistema de Advertencia de Sesión - v3 FINAL
 */
(function() {
    'use strict';

    var TIEMPO_SESION = 120;
    var TIEMPO_ADVERTENCIA = 30;
    var timerPrincipal = null;
    var intervaloCuenta = null;
    var estaModalAbierto = false;
    var ultimaActividad = Date.now();

    // ========== FUNCIONES DEL MODAL ==========

    function abrirModal() {
        estaModalAbierto = true;
        var m = document.getElementById('modal-advertencia-sesion');
        if (m) m.style.display = 'flex';
    }

    function cerrarModal() {
        estaModalAbierto = false;
        var m = document.getElementById('modal-advertencia-sesion');
        if (m) m.style.display = 'none';
    }

    // ========== CONTROL DE TIMERS ==========

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

    function empezarCuentaRegresiva() {
        var seg = TIEMPO_ADVERTENCIA;
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
            if (seg <= 0) {
                clearInterval(intervaloCuenta);
                intervaloCuenta = null;
                // EXPIRÓ - cerrar sesión
                window.location.href = '../core/destruir_sesion.php';
            }
        }, 1000);
    }

    function iniciarTemporizador() {
        pararTodo();
        cerrarModal();
        ultimaActividad = Date.now();

        timerPrincipal = setTimeout(function() {
            timerPrincipal = null;
            abrirModal();
            empezarCuentaRegresiva();
        }, (TIEMPO_SESION - TIEMPO_ADVERTENCIA) * 1000);
    }

    // ========== BOTÓN SEGUIR ACTIVO ==========

    window.renovarSesion = function() {
        // Parar absolutamente todo
        pararTodo();
        cerrarModal();

        // Avisar al servidor
        try {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../core/actualizar_actividad.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.send(JSON.stringify({ timestamp: Date.now() }));
        } catch(e) {}

        // Reiniciar desde cero
        iniciarTemporizador();
    };

    // ========== DETECCIÓN DE ACTIVIDAD ==========

    function onActividad() {
        if (estaModalAbierto) return;
        var ahora = Date.now();
        if (ahora - ultimaActividad > 10000) {
            ultimaActividad = ahora;
            // Resetear timer
            pararTodo();
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

    // ========== INICIO ==========

    document.addEventListener('mousedown', onActividad, true);
    document.addEventListener('keydown', onActividad, true);
    document.addEventListener('scroll', onActividad, true);

    iniciarTemporizador();
})();
