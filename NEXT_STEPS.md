# 🎬 PRÓXIMOS PASOS - GUÍA RÁPIDA

## ✋ ANTES DE EMPEZAR CON PRODUCCIÓN

### PASO 1: Configurar Variables de Entorno (5 minutos)

**Archivo:** `c:\xampp\htdocs\PAGINA WED\.env`

```bash
# BASE DE DATOS
DB_HOST=localhost
DB_USER=pagina_web_user
DB_PASSWORD=contraseña_muy_segura_aqui
DB_NAME=negocio_web

# GMAIL SMTP (Envío de emails)
SMTP_USER=tu_email@gmail.com
SMTP_PASSWORD=generado_en_paso_2
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_FROM_NAME=Tu Negocio

# SEGURIDAD
CSRF_TOKEN_SECRET=genera_con_comando_paso_3
APP_ENV=development
DEBUG=false
SESSION_TIMEOUT=30
```

**⚠️ Importante:** Este archivo NO debe être commiteado a git (ya está en `.gitignore`)

---

### PASO 2: Generar Nueva Contraseña Gmail (3 minutos)

**La Contraseña Actual es Comprometida:**
```
❌ NUNCA usar: sijpsgcocjdneuxh
✅ Generar nueva ANTES de producción
```

**Procedimiento:**

1. Ir a: https://myaccount.google.com/apppasswords
2. Iniciar sesión si es necesario
3. Seleccionar:
   - App: **Correo (Mail)**
   - Dispositivo: **Otro (Windows Client)**
4. Google generará contraseña de 16 caracteres
5. Copiar contraseña (sin espacios)
6. Pegar en `.env` como `SMTP_PASSWORD=`
7. Guardar `.env`
8. Probar enviando email de prueba

**Test rápido:**
```php
// Crear archivo test_email.php en raíz temporalmente
<?php
require_once 'api/api_usuario.php'; // Contiene envío

// Enviar test
$envio = enviarEmail('tu_email@test.com', 'Test', 'Testing email');
if ($envio) echo "✅ Email enviado OK";
else echo "❌ Falló envío";
?>
```

---

### PASO 3: Generar CSRF_TOKEN_SECRET (2 minutos)

**En PowerShell (como Admin):**

```powershell
# Ejecutar este comando:
openssl rand -hex 32

# Output será algo como:
# a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z

# Copiar ese valor y pegarlo en .env:
CSRF_TOKEN_SECRET=a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z
```

**Si no tienes OpenSSL instalado:**
```powershell
# Instalador (si tienes Chocolatey):
choco install openssl

# O descarguedesde: https://slproweb.com/products/Win32OpenSSL.html
```

**Alternativa sin OpenSSL:**
```php
// En PHP (una sola vez):
<?php
echo bin2hex(random_bytes(32));  // Copiar salida
?>
```

---

### PASO 4: Cambiar Contraseña de Base de Datos (10 minutos)

**En phpMyAdmin:**

1. Acceder a: `http://localhost/phpmyadmin`
2. Login como root
3. Click en "User accounts" (Arriba)
4. Click en "Edit privileges" para "root@localhost"
5. Tab "Change password"
6. Seleccionar "Use the text field: "
7. Ingresar nueva contraseña (ej: `P@ssw0rd_Super_Segura_2024`)
8. Click "Go"
9. Actualizar en `.env` como `DB_PASSWORD=`

**O vía SQL:**
```sql
-- En phpMyAdmin Query window:
ALTER USER 'root'@'localhost' IDENTIFIED BY 'P@ssw0rd_Super_Segura_2024';
FLUSH PRIVILEGES;
```

**Alternativamente - Crear usuario específico:**
```sql
-- Es MEJOR prácitca que usar root:
CREATE USER 'pagina_web_user'@'localhost' IDENTIFIED BY 'P@ssw0rd_Segura';
GRANT ALL PRIVILEGES ON negocio_web.* TO 'pagina_web_user'@'localhost';
FLUSH PRIVILEGES;

-- Luego en .env:
DB_USER=pagina_web_user
DB_PASSWORD=P@ssw0rd_Segura
```

---

### PASO 5: Verificar Archivos de Seguridad (5 minutos)

**Checklist rápido:**
```bash
# En explorador de archivos o terminal:

# ✅ Debe existir:
□ c:\xampp\htdocs\PAGINA WED\.env (Con tus datos)
□ c:\xampp\htdocs\PAGINA WED\.env.example (Template)
□ c:\xampp\htdocs\PAGINA WED\.htaccess
□ c:\xampp\htdocs\PAGINA WED\database\.htaccess
□ c:\xampp\htdocs\PAGINA WED\core\.htaccess
□ c:\xampp\htdocs\PAGINA WED\core\env_loader.php
□ c:\xampp\htdocs\PAGINA WED\core\csrf.php

# ❌ Con datos sensibles (revisar):
□ Ningún archivo con credenciales hardcoded
□ .env NO está en git (verificar git status)
```

**comando para verificar .htaccess:**
```powershell
# PowerShell:
Get-Item -Path "c:\xampp\htdocs\PAGINA WED" -Force | Select-Object -Property Name
Get-Item -Path "c:\xampp\htdocs\PAGINA WED\database" -Force
Get-Item -Path "c:\xampp\htdocs\PAGINA WED\core" -Force
```

---

## 🧪 TESTING - VALIDAR QUE TODO FUNCIONA

### TEST 1: Login (5 minutos)

```
1. Ir a: http://localhost/PAGINA\ WED/pages/login.php
2. Ingresar credenciales correctas
3. Verificar que entra al dashboard
4. Revisar que sesión está activa
5. Cerrar navegador y volver a abrir
6. Verificar que sesión expiró (correcto)
```

**Esperado:**
- ✅ Login exitoso con credenciales válidas
- ✅ Redireccción a dashboard
- ✅ Sesión persiste en ventanas nuevas (30 min default)
- ✅ Cierre de navegador = sesión muere

---

### TEST 2: Crear Pedido (10 minutos)

```
1. Navegar como cliente
2. Agregar producto al carrito
3. Ir a checkout
4. Seleccionar dirección (o crear nueva)
5. Seleccionar método envío
6. Seleccionar método pago
7. Subir comprobante (TRY: imagen JPEG, luego intentar .exe)
8. Crear pedido
```

**Esperado:**
- ✅ Carrito agrega correctamente
- ✅ Checkout flujo completo
- ✅ .exe rechazado (validación MIME)
- ✅ JPEG aceptado
- ✅ Pedido se crea
- ✅ Email de confirmación se envía
- ✅ Stock se actualiza

---

### TEST 3: CSRF Protection (5 minutos)

**Verificar que CSRF tokens están presentes:**

```
1. Abrir DevTools (F12)
2. Ir a Network tab
3. Ir a cualquier formulario (Login, cambiar contraseña, etc)
4. Inspeccionar HTML
5. Buscar campo: name="csrf_token"
```

**Esperado:**
- ✅ Token presente en formulario
- ✅ Token valor diferente cada recarga
- ✅ Cada sesión tiene patrón diferente

**Test de protección:**
```javascript
// En DevTools Console, intenta hacer request sin token:
fetch('/PAGINA WED/api/api_cambiar_contraseña.php', {
    method: 'POST',
    body: JSON.stringify({password_nueva: 'hack'})
})
// Esperado: Error o rechazo
```

---

### TEST 4: SQL Injection - Verificar Prepared Statements (3 minutos)

**Intento de inyección SQL (Debe fallar):**

```
1. En login: username = admin' OR '1'='1
2. En busqueda: 123'; DROP TABLE usuarios; --
3. Verificar que NO ejecuta
```

---

## ✅ ESTADO ACTUAL DEL PROYECTO

- Rate limiting: implementado
- Audit logging: implementado
- Monitoring alerts: implementado
- CSRF: implementado en formularios y endpoints mutables
- Migración de cascada de clientes: aplicada
- Pendiente real: configuración final de producción, HTTPS, backups y pruebas end-to-end

**Esperado:**
- ✅ Queries fallidas correctamente (NULL result)
- ✅ No muestra error SQL interno
- ✅ No modifica base de datos

---

### TEST 5: File Upload - Validación (5 minutos)

**En admin, subir imagen de banco/marca:**

```
1. Intenta subir .txt → ❌ Rechazado (MIME)
2. Intenta subir .exe → ❌ Rechazado (Extension)
3. Intenta subir >2MB → ❌ Rechazado (Size)
4. Subir JPG válido → ✅ Aceptado
```

**Esperado:**
- ✅ Rechaza MIDI inválidos
- ✅ Rechaza extensiones peligrosas
- ✅ Rechaza archivos muy grandes
- ✅ Acepta imágenes válidas
- ✅ Nombres únicos (timestamp + random)

---

## 🚀 DEPLOYMENT A PRODUCCIÓN

### Checklist Pre-Deployment

```
SEGURIDAD
□ .env configurado con credenciales reales
□ CSRF_TOKEN_SECRET generado
□ Contraseña Gmail renovada
□ Contraseña BD renovada
□ .env NO en git (git status clean)

TESTING
□ Login funciona
□ Crear pedido funciona
□ CSRF tokens presentes
□ Subida de archivos validados
□ SQL injection blocked
□ Emails se envían

DOCUMENTACIÓN
□ RESUMEN_EJECUTIVO.md leído
□ PRODUCCION_CHECKLIST.md completado
□ Credenciales en vault/manager seguro

SERVIDOR
□ HTTPS configurado
□ DNS apuntando correctamente
□ Firewall abierto puertos necesarios
□ Backup pre-deploy realizado
```

---

### Pasos de Deployment

**Opción 1: FTP/SFTP (Más común)**

```powershell
# 1. Copiar archivos (excepto .git, node_modules, etc)
# 2. Subir .env al servidor (NUNCA en deploy.sh)
# 3. Dar permisos: chmod 755 directorios, 644 archivos
# 4. Probar: curl https://tudominio.com
```

**Opción 2: Git + SSH (Recomendado)**

```bash
# En servidor:
cd /var/www/html
git clone https://github.com/tuusername/PAGINA-WED.git
cd PAGINA-WED

# Crear .env:
nano .env
# Pegar contenido (copiar de local)

# Permisos:
chmod 755 .
find . -type f -name ".htaccess" -exec chmod 644 {} \;
chmod 600 .env

# Test:
curl -I https://tudominio.com
```

---

### Post-Deployment Verification

```bash
# 1. Conectar al servidor via SSH:
ssh user@tudominio.com

# 2. Verificar archivos críticos:
ls -la /var/www/html/pagina_wed/.env    # Debe existir
ls -la /var/www/html/pagina_wed/.htaccess
ls -la /var/www/html/pagina_wed/database/.htaccess

# 3. Verificar permisos:
stat /var/www/html/pagina_wed/.env  # Debe ser 600

# 4. Test DB connection:
php -r "require 'conexion.php'; echo 'DB OK';"

# 5. Test email:
php -r "require 'core/smtp_config.php'; echo 'SMTP OK';"

# 6. Verificar HTTPS:
curl -I https://tudominio.com  # HTTP 200
```

---

## 📞 TROUBLESHOOTING

### Problema: "Cannot find .env file"

**Solución:**
```php
// core/conexion.php debe tener:
require_once __DIR__ . '/env_loader.php';

// O desde otros directorio:
require_once dirname(__FILE__) . '/core/env_loader.php';
```

---

### Problema: "SMTP Connection Failed"

**Verificar:**
```
1. SMTP_USER correcto en .env
2. SMTP_PASSWORD es App Password (NO contraseña regular)
3. SMTP_HOST=smtp.gmail.com
4. SMTP_PORT=587
5. Sesión permite acceso "App password" (mirar email de Google)
```

**Test rápido:**
```php
<?php
require_once 'core/env_loader.php';
echo "SMTP_USER: " . getEnv('SMTP_USER') . "\n";
echo "SMTP_HOST: " . getEnv('SMTP_HOST') . "\n";
echo "SMTP_PORT: " . getEnv('SMTP_PORT') . "\n";
?>
```

---

### Problema: "Access Denied" a /database/ o /core/

**Esperado - No es error!**

Este es el comportamiento correcto. Los archivos están protegidos por `.htaccess`:

```
✅ Correcto: GET /database/negocio_web.sql → 403 Forbidden
✅ Correcto: GET /core/conexion.php → 403 Forbidden
✅ Correcto: GET /index.php → 200 OK (incluye los archivos internamente)
```

---

### Problema: "CSRF token mismatch"

**Causa:** Token no se envió o expiró

**Soluciones:**
```
1. Asegura que formulario tiene: <?php echo campoTokenCSRF(); ?>
2. Verifica que sesión está activa
3. Regenera sesión: session_regenerate_id(true);
4. Limpiar cookies del navegador
5. Deshabilitar adblocker (puede bloquear requests)
```

---

### Problema: "File upload always fails"

**Checklist:**
```
□ MIME type es válido (image/jpeg, image/png, etc)
□ Extensión es .jpg, .jpeg, .png, .gif
□ Tamaño < 2MB
□ Permiso de escritura en /img/[carpeta]/
□ Carpeta existe (se crea automáticamente)
```

**Debug:**
```php
// Agregar temporalmente en api que procesa upload:
error_log("FILE: " . print_r($_FILES, true));
error_log("MIME: " . $_FILES['file']['type']);
error_log("SIZE: " . $_FILES['file']['size']);
// Ver error_log en /logs/ o Apache logs
```

---

## 🎓 DOCUMETANCION IMPORTANTE A LEER

**Orden de lectura recomendado:**
1. ✅ ESTE DOCUMENTO (Próximos Pasos)
2. 📋 PRODUCCION_CHECKLIST.md (Antes de ir a prod)
3. 📊 RESUMEN_EJECUTIVO.md (Entender cambios)
4. 🔒 SECURITY_PHASE_2_CLOSURE.md (Detalles técnicos)

---

## ✅ FINAL CHECKLIST

```
ANTES DE PRODUCCIÓN:

CONFIGURACIÓN:
□ .env creado con datos reales
□ DB_PASSWORD actualizado
□ SMTP_PASSWORD generado (App Password)
□ CSRF_TOKEN_SECRET generado
□ APP_ENV = production
□ DEBUG = false

TESTING:
□ Login funciona
□ Crear pedido funciona
□ Emails se envían
□ Archivos se suben correctamente
□ CSRF tokens presentes
□ Base de datos conecta

SEGURIDAD:
□ .env NO está en git
□ .htaccess en su lugar
□ Credenciales en vault seguro
□ Contraseña Gmail creada (no reutilizar)

DOCUMENTACIÓN:
□ Este archivo leído
□ Checklists completados
□ Links de soporte guardado
□ Team notificado

DEPLOYMENT:
□ Backup pre-deployment
□ Archivos subidos a servidor
□ Permisos configurados
□ .env deploado (no en git)
□ Test post-deployment pasado
```

---

**Cuando termines todo esto, PAGINA-WED estará ✅ LISTO PARA PRODUCCIÓN**

---

**¿Necesitas ayuda?**

1. Lee el error completo
2. Busca en TROUBLESHOOTING arriba
3. Lee SECURITY_PHASE_2_CLOSURE.md para detalles técnicos
4. Asegúrate que .env está en la ubicación correcta

**¡Good luck! 🚀**
