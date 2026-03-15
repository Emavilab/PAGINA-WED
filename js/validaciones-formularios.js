/**
 * =========================================================
 * VALIDACIONES DE FORMULARIOS - SISTEMA REUTILIZABLE
 * =========================================================
 *
 * Proporciona validaciones centralizadas para formularios normales,
 * modales dinámicos y formularios cargados por AJAX.
 *
 * Uso: agregar la clase "form-validar-usuario" al <form> para
 * activar las validaciones automáticamente por delegación de eventos.
 *
 * Reglas:
 * - Contraseña: vacío permitido (edición); si tiene valor: mínimo 8 caracteres,
 *   al menos una mayúscula y al menos un número.
 * - Email: formato válido.
 * - Nombre: mínimo 3 caracteres, no vacío.
 * =========================================================
 */

(function () {
    'use strict';

    /* =====================================================
       VALIDACIÓN DE CONTRASEÑA
       - Permite campo vacío (para formularios de edición).
       - Si tiene valor: mínimo 8 caracteres, una mayúscula, un número.
       ===================================================== */
    function validarPassword(password) {
        const valor = typeof password === 'string' ? password.trim() : '';
        if (valor.length === 0) {
            return { valido: true, mensaje: '' };
        }
        if (valor.length < 8) {
            return { valido: false, mensaje: 'La contraseña debe tener al menos 8 caracteres.' };
        }
        if (!/[A-Z]/.test(valor)) {
            return { valido: false, mensaje: 'La contraseña debe contener al menos una letra mayúscula.' };
        }
        if (!/[0-9]/.test(valor)) {
            return { valido: false, mensaje: 'La contraseña debe contener al menos un número.' };
        }
        return { valido: true, mensaje: '' };
    }

    /* =====================================================
       VALIDACIÓN DE EMAIL
       ===================================================== */
    function validarEmail(email) {
        const valor = typeof email === 'string' ? email.trim() : '';
        if (valor.length === 0) {
            return { valido: false, mensaje: 'El correo electrónico es requerido.' };
        }
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test(valor)) {
            return { valido: false, mensaje: 'El correo electrónico no es válido.' };
        }
        return { valido: true, mensaje: '' };
    }

    /* =====================================================
       VALIDACIÓN DE NOMBRE (usuario/cliente)
       Mínimo 3 caracteres, no vacío.
       ===================================================== */
    function validarNombre(nombre) {
        const valor = typeof nombre === 'string' ? nombre.trim() : '';
        if (valor.length === 0) {
            return { valido: false, mensaje: 'El nombre es requerido.' };
        }
        if (valor.length < 3) {
            return { valido: false, mensaje: 'El nombre debe tener al menos 3 caracteres.' };
        }
        return { valido: true, mensaje: '' };
    }

    /* =====================================================
       VALIDAR FORMULARIO TIPO USUARIO
       Campos esperados: nombre, correo, y opcionalmente
       contraseña o password (permite vacío en edición).
       Retorna: { valido: boolean, errores: Array<{ campo, mensaje }> }
       ===================================================== */
    function validarFormularioUsuario(form) {
        if (!form || !form.nodeName || form.nodeName !== 'FORM') {
            return { valido: false, errores: [{ campo: '', mensaje: 'Formulario no válido.' }] };
        }

        const errores = [];
        const getVal = (name) => {
            const el = form.querySelector('[name="' + name + '"]');
            return el ? (el.value || '').trim() : '';
        };

        // Nombre
        const nombre = getVal('nombre');
        const resNombre = validarNombre(nombre);
        if (!resNombre.valido) {
            errores.push({ campo: 'nombre', mensaje: resNombre.mensaje });
        }

        // Correo
        const correo = getVal('correo');
        const resCorreo = validarEmail(correo);
        if (!resCorreo.valido) {
            errores.push({ campo: 'correo', mensaje: resCorreo.mensaje });
        }

        // Contraseña: buscar por "contraseña" o "password"
        // En edición (existe id o id_usuario con valor) puede estar vacía; en creación es requerida.
        const inputPassword = form.querySelector('[name="contraseña"]') || form.querySelector('[name="password"]');
        if (inputPassword) {
            const idEdit = (getVal('id') || getVal('id_usuario') || '').trim();
            const esEdicion = idEdit.length > 0;
            const password = (inputPassword.value || '').trim();
            if (password.length === 0 && !esEdicion) {
                errores.push({ campo: inputPassword.getAttribute('name'), mensaje: 'La contraseña es requerida.' });
            } else {
                const resPassword = validarPassword(password);
                if (!resPassword.valido) {
                    errores.push({ campo: inputPassword.getAttribute('name'), mensaje: resPassword.mensaje });
                }
            }
        }

        return {
            valido: errores.length === 0,
            errores: errores
        };
    }

    /* =====================================================
       MOSTRAR ERRORES EN EL FORMULARIO
       Busca elementos con id "error-<campo>" o "mensaje-error-form"
       dentro del form. Si no hay, usa alert().
       ===================================================== */
    function mostrarErroresEnFormulario(form, errores) {
        const mensajes = errores.map(function (e) { return e.mensaje; });
        const texto = mensajes.join('\n');

        // Limpiar errores previos por campo
        form.querySelectorAll('[id^="error-"]').forEach(function (el) {
            el.textContent = '';
            el.classList.add('hidden');
        });

        // Mostrar error por campo si existe el contenedor
        errores.forEach(function (err) {
            const idCampo = err.campo === 'contraseña' ? 'contraseña' : err.campo;
            const posibleId = 'error-' + idCampo;
            let errorEl = form.querySelector('#' + posibleId);
            if (!errorEl && idCampo === 'password') {
                errorEl = form.querySelector('#error-password');
            }
            if (errorEl) {
                errorEl.textContent = err.mensaje;
                errorEl.classList.remove('hidden');
            }
        });

        // Contenedor de error general
        const contenedorGeneral = form.querySelector('#mensaje-error-form');
        if (contenedorGeneral) {
            contenedorGeneral.innerHTML = '<strong>Por favor corrige los siguientes errores:</strong><ul class="mt-2">' +
                errores.map(function (e) { return '<li>• ' + e.mensaje + '</li>'; }).join('') + '</ul>';
            contenedorGeneral.classList.remove('hidden');
        } else if (mensajes.length > 0) {
            alert(texto);
        }
    }

    /* =====================================================
       DELEGACIÓN DE EVENTOS: SUBMIT
       Usa fase de captura para ejecutarse antes que otros handlers.
       Cualquier <form> con clase "form-validar-usuario" se valida
       antes de enviar (incluye modales dinámicos y contenido AJAX).
       ===================================================== */
    document.addEventListener('submit', function (e) {
        const form = e.target && e.target.tagName === 'FORM' ? e.target : (e.target.form || (e.target.closest && e.target.closest('form')));
        if (!form || form.tagName !== 'FORM') return;
        if (!form.classList.contains('form-validar-usuario')) return;

        const resultado = validarFormularioUsuario(form);
        if (!resultado.valido) {
            e.preventDefault();
            e.stopImmediatePropagation();
            mostrarErroresEnFormulario(form, resultado.errores);
            return;
        }
        // Si es válido, no hacer nada; el resto de handlers puede enviar el formulario o hacer fetch
    }, true);

    // Exponer funciones para uso directo si se necesita validar sin submit
    window.ValidacionesFormularios = {
        validarPassword: validarPassword,
        validarEmail: validarEmail,
        validarNombre: validarNombre,
        validarFormularioUsuario: validarFormularioUsuario
    };
})();
