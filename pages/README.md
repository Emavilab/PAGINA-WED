# Pages - Páginas Públicas

Este directorio contiene páginas públicas de la aplicación que se cargan dinámicamente en el contenido principal de index1.php. No contienen estructura HTML completa, solo el contenido específico.

## 📄 Archivos

### login.php
**Propósito**: Formulario de inicio de sesión
**Cargado Por**: Desde index1.php mediante `loadLogin()`
**Acceso**: Público (usuarios no autenticados)
**Método**: POST asincrónico

**Formulario (HTML)**:
- Email (input email)
- Contraseña (input password)
- Botón "Inicia Sesión"
- Link "¿No tienes cuenta? Regístrate"
- Opción "Recuérda mi contraseña" (opcional)

**Validaciones Cliente**:
- Email no vacío
- Formato email válido
- Contraseña no vacía

**ID del Formulario**: `form-login-modal`

**Procesamiento (JavaScript)**:
```javascript
function setupLoginForm() {
  document.getElementById('form-login-modal')
    .addEventListener('submit', async (e) => {
      e.preventDefault();
      // Enviar a api/validar_login.php
      // Mostrar errores o redirigir
    });
}
```

**Endpoint Used**: `POST api/validar_login.php`

**Respuesta Exitosa**:
- Redirige a Dashboard.php (admin/vendedor) o index1.php (cliente)
- Mensaje de bienvenida opcional

**Respuesta Erro**:
- Muestra mensaje de error rojo
- Borra campo de contraseña
- Foco en campo email

**Estilos Tailwind**:
- Tema claro/oscuro
- Inputs con placeholder descriptivos
- Botón primario con hover effect
- Typography responsive

---

### crear_cuenta.php
**Propósito**: Formulario de registro/crear cuenta
**Cargado Por**: Desde index1.php mediante `loadRegistrarse()`
**Acceso**: Público (usuarios no autenticados)
**Método**: POST asincrónico
**Rol Asignado**: Siempre rol 3 (Cliente)

**Formulario (HTML)**:
- Nombre completo (input text)
- Email (input email)
- Contraseña (input password)
- Confirmar contraseña (input password)
- Aceptar términos (checkbox requerido)
- Botón "Crear Cuenta"
- Link "¿Ya tienes cuenta? Inicia Sesión"

**Validaciones Cliente**:
- Nombre: no vacío, 3+ caracteres
- Email: formato válido
- Contraseña: 6+ caracteres
- Confirmación: coincide con contraseña
- Términos: debe aceptar

**ID del Formulario**: `form-registro-modal`

**Procesamiento (JavaScript)**:
```javascript
function setupRegistroForm() {
  document.getElementById('form-registro-modal')
    .addEventListener('submit', async (e) => {
      e.preventDefault();
      // Validar passwords coincidan
      // Enviar POST a api/registrar_usuario.php
      // Mostrar errores o redirigir a login
    });
}
```

**Endpoint Used**: `POST api/registrar_usuario.php`

**Validaciones Servidor**:
- Email no duplicado
- Nombre válido (3-100 chars)
- Contraseña segura (6+ chars, hashed BCRYPT)
- SQL injection prevention

**Respuesta Exitosa**:
- Muestra mensaje "Cuenta creada correctamente"
- Automáticamente carga login
- Usuario debe iniciar sesión

**Respuesta Error**:
- Lista todos los errores encontrados
- Highlight en rojo campos con error
- Mantiene datos ingresados (excepto contraseña)

---

### contactanos.php
**Propósito**: Página de contacto con formulario
**Cargado Por**: Desde index1.php mediante `loadContacto()`
**Acceso**: Público
**Método**: POST o email directo

**Secciones**:
#### Información de Contacto
- Dirección física
- Teléfono
- Email de soporte
- Horario de atención
- Ubicación en mapa (Google Maps embed)

#### Formulario de Contacto
- Nombre (required)
- Email (required, email format)
- Asunto/Tema (dropdown)
- Mensaje (textarea)
- Adjunto (opcional)
- Botón "Enviar Mensaje"

**Validaciones Cliente**:
- Nombre no vacío
- Email válido
- Asunto seleccionado
- Mensaje 10+ caracteres

**Procesamiento**:
- Envía email a administrador
- Retorna confirmación al usuario
- Mensaje de `gracias por contactarnos`

**Temas de Contacto**:
- Orden/Entrega
- Producto defectuoso
- Reembolso
- Otro

---

## 🔄 Ciclo de Carga Dinámica

### HTML → JavaScript → Rendering

```
index1.php cargado
     ↓
Usuario clickea botón (ej: "Iniciar Sesión")
     ↓
JavaScript llama loadLogin()
     ↓
fetch('pages/login.php')
     ↓
HTML recibido, inyectado en #mainContent
     ↓
Scripts extraídos y ejecutados
     ↓
setupLoginForm() se ejecuta
     ↓
Event listeners attach a form
     ↓
Usuario interactúa con formulario
     ↓
Submit → POST a api/
     ↓
JSON respuesta → JavaScript procesa
     ↓
Redirige o muestra error
```

### Código de Ejemplo (index1.php)

```javascript
function loadLogin() {
    fetch('pages/login.php')
        .then(response => response.text())
        .then(data => {
            // Parsear HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = data;
            const bodyContent = tempDiv.querySelector('body')?.innerHTML || data;
            
            // Inyectar HTML
            document.getElementById('mainContent').innerHTML = bodyContent;
            
            // Extraer y ejecutar scripts
            const scriptRegex = /<script[^>]*>([\s\S]*?)<\/script>/g;
            let scriptMatch;
            while ((scriptMatch = scriptRegex.exec(data)) !== null) {
                const script = document.createElement('script');
                script.textContent = scriptMatch[1];
                document.body.appendChild(script);
            }
            
            window.scrollTo(0, 0);
        })
        .catch(error => console.error('Error:', error));
}
```

---

## 🎨 Estructura HTML de Pages

### Estructura Base (no repetida en cada archivo)
```html
<!-- NO incluir en pages/*.php -->
<!DOCTYPE html>
<html>
<head>...</head>
<body>
```

### Incluir Solo en pages/*.php
```html
<main class="container">
    <!-- Contenido específico -->
</main>

<script>
    // JavaScript específico
    function setupForm() { ... }
    
    // Ejecutar setup si necesario
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupForm);
    } else {
        setupForm();
    }
</script>
```

---

## 🔒 Consideraciones de Seguridad

### Lo Que SÍ Hacer
- ✅ Validar email format en formularios
- ✅ Contraseña mínimo 6 caracteres
- ✅ Mostrar/ocultar contraseña toggle
- ✅ Mostrar validación real-time

### Lo Que NO Hacer
- ❌ NO enviar contraseña en console.log
- ❌ NO guardar contraseña en localStorage
- ❌ NO hacer queries SQL desde JavaScript
- ❌ NO confiar solo en validación cliente

---

## 📊 Flujo de Datos

### Login Flow

```
Usuario ingresa credenciales
         ↓
Cliente valida (no vacío, email format)
         ↓
POST a api/validar_login.php
         ↓
Servidor valida BD
         ↓
¿Credenciales válidas?
    /           \
   SÍ            NO
  /               \
Server crea    Server retorna
sesión         error JSON
  ↓               ↓
Retorna        JavaScript
redirect       muestra error
  ↓
JS redirige
a Dashboard
o index1.php
```

### Registration Flow

```
Usuario completa form
         ↓
Cliente valida (nombre, email, password match)
         ↓
POST a api/registrar_usuario.php
         ↓
Servidor valida email no duplicado
         ↓
Server crea usuario (rol 3)
         ↓
Hash password BCRYPT
         ↓
Insertar en BD
         ↓
¿Éxito?
  /       \
SÍ       NO
|         |
|    JSON error
|    JS muestra
|    
JSON success
JS muestra "Cuenta creada"
Carga automático login.php
```

---

## 🆘 Troubleshooting

**Problema**: Formulario no se envía
- Verificar `form-login-modal` existe en HTML
- Ver consola (F12) para errores JavaScript
- Verificar `setupLoginForm()` se ejecutó
- Verificar endpoint api/ correcto

**Problema**: Errores de validación no aparecen
- Verificar elemento `#mensaje-error` existe
- Verificar clase `hidden` se quita con `.remove('hidden')`
- Ver console para excepciones

**Problema**: Script de formulario no ejecuta
- Verificar `<script>` tags en HTML
- Verificar `DOMContentLoaded` event listener
- Verificar scope de funciones (global vs local)

---

Última actualización: 15 de febrero de 2026
