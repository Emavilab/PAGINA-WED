# 📋 GUÍA DE SEGURIDAD - CAMBIOS FASE 1

## ✅ CAMBIOS COMPLETADOS

### 1. **Credenciales Movidas a .env** ✅
- Creado archivo `.env` (NO incluido en git)
- Creado archivo `.env.example` (incluido en git)
- Actualizado `core/conexion.php` para leer del .env
- Actualizado `core/smtp_config.php` para leer del .env

### 2. **.htaccess Protecciones** ✅
- Creado `.htaccess` raíz - bloquea .env, archivos sensibles
- Creado `.htaccess` en `/database/` - bloquea SQL
- Creado `.htaccess` en `/core/` - bloquea configuración
- Agregados headers de seguridad (X-Frame-Options, etc)
- Compresión GZIP habilitada
- Cache configurado

### 3. **Sistema CSRF Tokens** ✅
- Creado `core/csrf.php` con funciones para:
  - Generar tokens
  - Validar tokens
  - Regenerar después de login
  - Middleware para APIs
  - Helpers para formularios

### 4. **SQL Injection Fixes** ✅
- ✅ `admin/admin_bancos.php` - INSERT/UPDATE/DELETE ahora con prepared statements
- ✅ `admin/Dashboard.php` - Queries de estadísticas ahora seguras
- 🔄 **PENDIENTE:** Revisar otros archivos de admin/

### 5. **.gitignore Configurado** ✅
- Archivo `.env` nunca será cometido
- Archivos de backup y cache ignorados
- Directorios IDE excluidos

---

## 🚀 PRÓXIMOS PASOS INMEDIATOS

### ANTES DE USAR EN PRODUCCIÓN:

1. **Editar `.env` con tus datos:**
   ```bash
   # En c:\xampp\htdocs\PAGINA WED\.env
   DB_HOST=localhost
   DB_USER=tu_usuario
   DB_PASSWORD=tu_contraseña
   
   SMTP_USER=tuEmail@gmail.com
   SMTP_PASSWORD=tu_contraseña_app_google
   
   CSRF_TOKEN_SECRET=generar_con_openssl
   APP_ENV=production  # En producción
   DEBUG=false
   ```

2. **Generar CSRF_TOKEN_SECRET** (ejecutar en terminal):
   ```powershell
   # Windows PowerShell
   openssl rand -hex 32
   # Copiar resultado y pegar en .env como CSRF_TOKEN_SECRET
   ```

3. **Cambiar contraseña Gmail:**
   - La contraseña anterior está comprometida
   - Generar nueva en: https://myaccount.google.com/apppasswords
   - Agregar a `.env`

4. **Limpiar histórico de Git:**
   ```bash
   # Remover credenciales del historial
   git filter-branch --force --index-filter \
     'git rm --cached --ignore-unmatch core/smtp_config.php' \
     --prune-empty --tag-name-filter cat -- --all
   ```

---

## 📝 CÓMO USAR LAS NUEVAS FUNCIONES

### Protección CSRF en Formularios HTML:

```php
<?php require_once 'core/csrf.php'; ?>

<form method="POST" action="api/api_crear_pedido.php">
    <!-- Incluir token CSRF -->
    <?php echo campoTokenCSRF(); ?>
    
    <!-- Resto del formulario -->
</form>
```

### Protección CSRF en APIs (JSON):

```php
<?php
require_once 'core/sesiones.php'; // Ya incluye csrf.php

// Al inicio de la API - valida automáticamente
validarCSRFMiddleware('csrf_token');

// Resto del código de la API
// El token es validado automáticamente
?>
```

### Validación Manual en APIs:

```php
<?php
require_once 'core/csrf.php';

// Obtener token y validar
if (!validarTokenCSRF($_POST['csrf_token'] ?? '')) {
    echo json_encode(['exito' => false, 'mensaje' => 'Token CSRF inválido']);
    exit();
}

// Procesar solicitud
?>
```

### Obtener Token para AJAX:

```php
<?php
require_once 'core/csrf.php';

// En respuesta JSON, devolver token renovado
echo json_encode([
    'exito' => true,
    'datos' => $datos,
    ...tokenCSRFJSON()  // Agrega csrf_token a la respuesta
]);
?>
```

### JavaScript AJAX con CSRF:

```javascript
// Enviar token en headers
fetch('api/api_crear_pedido.php', {
    method: 'POST',
    headers: {
        'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({
        id_producto: 1,
        cantidad: 2
    })
})
.then(r => r.json())
.then(data => {
    // Actualizar token si viene en respuesta
    if (data.csrf_token) {
        document.querySelector('input[name="csrf_token"]').value = data.csrf_token;
    }
    console.log(data);
});
```

---

## 🔒 CAMBIOS DE SEGURIDAD DETALLADOS

### admin_bancos.php
- ❌ ANTES: `INSERT INTO bancos VALUES ('$nombre', ...)`
- ✅ AHORA: `INSERT INTO bancos VALUES (?, ?, ?, ?)` con bind_param

- ✅ Validación de entrada:
  - nombre: 2-100 caracteres
  - numero_cuenta: 5-50 caracteres
  - Validación de MIME type para logo
  - Validación de extensión de archivo

- ✅ Upload seguro:
  - Validación MIME type
  - Límite de tamaño (2MB)
  - Nombre único generado (no usar nombre del usuario)
  - Verificación de directorio

### Dashboard.php
- ❌ ANTES: `WHERE DATE(fecha_pedido) = '$hoy'`
- ✅ AHORA: `WHERE DATE(fecha_pedido) = ?` con prepared statement

---

## 🚨 AÚN POR HACER (FASE 2+)

### Archivos que aún necesitan SQL Injection fixes:
- [ ] `admin/admin_compras.php`
- [ ] `admin/admin_exportar exel.php`
- [ ] `admin/cambiar_estado.php`
- [ ] `admin/configuracion.php`
- [ ] `admin/crear_usuario_admin.php`
- [ ] `admin/editar_usuario_admin.php`
- [ ] `admin/eliminar_usuario_admin.php`
- [ ] `admin/gestion_productos.php`
- [ ] Múltiples archivos en `/api/`

### Otros problemas a resolver (FASE 2):
- [ ] Rate limiting en login
- [ ] Validación de file upload en todas las APIs
- [ ] Sistema de logging completo
- [ ] Headers de seguridad completos
- [ ] Validación de sesión en timeout
- [ ] Protección de password reset
- [ ] HTTPS enforcement
- [ ] CSP headers

---

## 📊 CHECKLIST DE SEGURIDAD PRODUCCIÓN

- [ ] .env configurado con valores reales
- [ ] .env NO está en git
- [ ] Contraseña Gmail regenerada
- [ ] CSRF_TOKEN_SECRET generado
- [ ] DEBUG=false en .env (producción)
- [ ] APP_ENV=production en .env
- [ ] .htaccess desplegado en servidor
- [ ] Archivos "preparados" testeados
- [ ] Backup de BD hecho
- [ ] Logs configurados
- [ ] SSL/HTTPS habilitado
- [ ] Credenciales antiguas removidas de git

---

## 🧪 TESTING RÁPIDO

### Probar que .env se lee correctamente:
```php
<?php
require_once 'core/env_loader.php';
echo "DB Host: " . getEnv('DB_HOST') . "<br>";
echo "DB User: " . getEnv('DB_USER') . "<br>";
// No mostrar password en la web
?>
```

### Probar CSRF token:
```php
<?php
session_start();
require_once 'core/csrf.php';

$token = obtenerTokenCSRF();
echo "Token: " . $token . "<br>";
echo "Válido: " . (validarTokenCSRF($token) ? 'Sí' : 'No') . "<br>";
?>
```

### Verificar que prepared statements funcionan:
- Probar crear, editar, y eliminar bancos
- Verificar que no hay cambios en funcionalidad
- Revisar logs para errores de prepared statements

---

## ⚠️ NOTAS IMPORTANTES

1. **Repositorio Git:**
   - Si las credenciales estaban antes en git, necesitas limpiar el historio
   - Los colaboradores deben hacer un nuevo clone
   - Las credenciales viejas están comprometidas

2. **Producción:**
   - Cambiar TODAS las credencials (DB, Gmail, etc)
   - Clave CSRF_TOKEN_SECRET debe ser aleatoria y secreta
   - .env debe estar en gitignore
   - DEBUG debe ser false
   - APP_ENV debe ser production

3. **CSRF Tokens:**
   - Se regeneran automáticamente después de login
   - Cada sesión tiene su propio token
   - Los tokens expiran cuando la sesión termina
   - Los formularios HTML necesitan `<?php echo campoTokenCSRF(); ?>`
   - Las APIs AJAX pueden enviar en header `X-CSRF-Token`

4. **File Uploads:**
   - Se valida MIME type (no solo extensión)
   - Se genera nombre único
   - Se valida tamaño máximo
   - Se valida que directorio existe

---

Última actualización: 21 de marzo de 2026
