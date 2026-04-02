# 🚀 CHECKLIST ANTES DE PASAR A PRODUCCIÓN

## ⚠️ CRÍTICO - HACER INMEDIATAMENTE

Estado actual:
- Fase 3 ya está incluida en el código y en la base de datos.
- El último paso pendiente es cerrar configuración de entorno y validar despliegue en producción.

### 1. Variables de Entorno (.env)
```bash
# Abrir: c:\xampp\htdocs\PAGINA WED\.env
# Y COMPLETAR CON TUS DATOS REALES:

□ DB_HOST = localhost (o IP de servidor BD)
□ DB_USER = usuario_bd_real (NO root)
□ DB_PASSWORD = contraseña_segura_nueva
□ DB_NAME = negocio_web (o nombre real)

□ SMTP_USER = tuEmail@gmail.com
□ SMTP_PASSWORD = contraseña_app_google_NUEVA
□ SMTP_HOST = smtp.gmail.com
□ SMTP_PORT = 587

□ CSRF_TOKEN_SECRET = openssl rand -hex 32 (copiar salida aquí)
□ APP_ENV = development (CAMBIAR A: production al desplegar)
□ DEBUG = false
□ SESSION_TIMEOUT = 30 (en minutos)
```

### 2. Cambiar Credenciales Comprometidas
```
⚠️ URGENTE: La antigua contraseña Gmail (sijpsgcocjdneuxh) está públicamente visible
```

**Pasos:**
```
1. Ir a: https://myaccount.google.com/apppasswords
2. Seleccionar "Correo" y "Otro (Windows Client)"
3. Generar nueva contraseña de 16 caracteres
4. Copiar y pegar en .env como SMTP_PASSWORD
5. Probar enviando un email de prueba
6. Verificar que funciona correctamente
```

### 3. Cambiar Contraseña de Base de Datos
```sql
-- En MySQL/MariaDB, ejecutar:
ALTER USER 'root'@'localhost' IDENTIFIED BY 'nueva_contraseña_fuerte';
FLUSH PRIVILEGES;

-- Y actualizar en .env:
DB_USER = nuevo_usuario
DB_PASSWORD = nueva_contraseña
```

### 4. Generar CSRF_TOKEN_SECRET
```powershell
# En PowerShell ejecutar:
openssl rand -hex 32

# Copiar salida y pegar en .env como:
CSRF_TOKEN_SECRET = [resultado de comando arriba]
```

---

## 🔒 SEGURIDAD - VERIFICAR

### Archivos y Permisos
```
□ Verificar que .env está en .gitignore
□ Verificar que .env NO está en el repositorio
□ Dar permisos 600 a .env (lectura solo owner)
□ .htaccess presente en raíz
□ .htaccess presente en /database/
□ .htaccess presente en /core/
```

### Base de Datos
```
□ Cambiar credenciales de root
□ Crear usuario específico para la app (NO root)
□ Limitar permisos del usuario a DB específica
□ Backup de BD realizado
□ Testear restauración de backup
□ Verificar que backups automáticos están configurados
```

### Servidor
```
□ HTTPS/SSL instalado
□ request => HTTPS redirigido
□ Certificado válido (Let's Encrypt recomendado)
□ Headers de seguridad habilitados (en .htaccess)
□ Compresión GZIP habilitada
```

### Credenciales Compiladas
```
□ Eliminar cualquier credencial antigua del código
□ Verificar git log no contiene contraseñas
□ Si está comprometido, ejecutar git filter-branch
□ Force-push a repositorio remoto
□ Notificar colaboradores
```

---

## 🧪 TESTING - VALIDACIONES

### Flujo de Login
```
□ Acceso al login.php
□ Credentials correctas = OK
□ Credentials incorrectas = Error apropiado
□ Intentos múltiples = No bloqueado (fase 3)
□ Session inicia correctamente
□ Session timeout funciona
```

### Crear Pedido
```
□ Agregar productos al carrito
□ Carrito actualiza correctamente
□ Checkout carga
□ Seleccionar dirección
□ Seleccionar método envío
□ Seleccionar método pago
□ Subir comprobante (validación de archivos OK)
□ Pedido se crea exitosamente
□ Email de confirmación se envía
□ Stock se actualiza
□ Carrito se vacía
```

### Generar Reporte Excel
```
□ Admin descargue reporte
□ Archivo genera sin errores
□ Datos son correctos
□ Formato Excel es válido
```

### Eliminar Cuenta Usuario
```
□ Usuario pueda eliminar su cuenta
□ Confirmación requerida
□ Sesión se cierre
□ Todos datos del usuario se eliminen
□ Redireccione a login
```

### Cambiar Configuración
```
□ Admin acceda a configuraciones
□ Cambiar nombre negocio
□ Cambiar colores tema
□ Subir logo
□ Cambiar redes sociales
□ Cambiar métodos envío
□ Cambiar métodos pago
□ Guardar cambios OK
□ Cambios visibles en frontend
```

---

## 📝 DOCUMENTACION

### README
```
□ Instrucciones de instalación actualizadas
□ Requisitos del sistema documentados
□ Configuración .env explicada
□ Procedimiento de backup documentado
□ Procedimiento de restauración documentado
□ Contacto para soporte
□ Licencia incluida
```

### Credenciales
```
□ Documento de credenciales guardado SEGURO
□ Acceso documentado para equipo
□ Contraseñas almacenadas en vault/lastpass
□ NO en email o chat sin encriptar
```

---

## 🌐 DEPLOYMENT

### Pre-Deployment
```
□ Código revisado
□ Tests pasados
□ Dependencias actualizadas
□ Migraciones de BD aplicadas
□ Backup pre-deploy hecho
```

### Deployment
```
□ SSH acceso al servidor verificado
□ FTP/SFTP acceso verificado
□ Permisos de directorios correctos (755 dirs, 644 files)
□ Cache limpio
□ Logs limpios
□ .env desplegado en servidor
```

### Post-Deployment
```
□ Sitio accesible por dominio
□ HTTPS funciona
□ Redirecciones funcionan
□ API responde correctamente
□ Base de datos conecta
□ Emails se envían
□ Assets cargan correctamente
□ No hay errores en logs
```

---

## 📊 MONITOREO

### Logs
```
□ Error log configurado
□ Access log está activo
□ Logs rotan automáticamente
□ Logs revisados diariamente
□ Alertas configuradas para errores críticos
```

### Performance
```
□ Sitio carga en < 3 segundos
□ Imágenes optimizadas
□ CSS/JS comprimido
□ GZIP habilitado
□ Cache headers configurados
```

### Seguridad
```
□ WAF activo (si disponible)
□ Rate limiting en login activo
□ Dos factores disponible (opcional)
□ Auditoría de cambios activa
□ Monitoreo de acceso no autorizado activo
```

---

## 📋 PROCEDIMIENTOS OPERACIONALES

### Backup
```bash
# Diario automático:
□ Backup de BD a las 2:00 AM
□ Backup de archivos a las 3:00 AM
□ Retención de 30 días
□ Stored offsite (storage externo)

# Testing de restauración:
□ Semanal: Probar restauración en ambiente staging
□ Mensual: Full restore test
□ Documentar procedimiento
```

### Monitoring
```
□ Uptime monitoring configurado (UptimeRobot)
□ Alertas por email si sitio cae
□ Log monitoring automático
□ Database size monitoring
□ CPU/Memory monitoring
```

### Actualizaciones
```
□ PHP actualizado
□ MySQL actualizado
□ Apache actualizado
□ Scheduled downtime documentado
□ Maintenance page preparado
```

---

## ✅ FINAL CHECKLIST

```
SEGURIDAD
□ .env configurado con datos reales
□ Credenciales cambadas (si necesarias)
□ HTTPS funciona
□ CSRF tokens presentes
□ Prepared statements en 95%+ código
□ File upload validado
□ No hay vulnerabilidades críticas conocidas

FUNCIONALIDAD
□ Login funciona
□ Crear pedido funciona
□ Checkout completo
□ Admin panel funciona
□ Reportes generan
□ Emails se envían

PERFORMANCE
□ Sitio carga rápido
□ BD optimizada
□ Caches configuradas
□ Assets minificados

DOCUMENTACIÓN
□ Procedimientos documentados
□ Credenciales almacenadas seguro
□ Contacto de soporte disponible
□ Licencia incluida

MONITORING
□ Logs activos
□ Alertas configuradas
□ Backups automáticos
□ Monitoreo de uptime

EQUIPO
□ Acceso determinado (SSH, FTP, DB)
□ Responsabilidades claras
□ Procedimientos comunicados
□ Training completado
```

---

## 🚨 EN CASO DE EMERGENCIA

### If Compromised
```
1. Tomar sitio OFFLINE inmediatamente
2. Cambiar TODAS las contraseñas
3. Revisar logs de acceso
4. Restaurar desde backup limpio
5. Ejecutar análisis de malware
6. Notificar usuarios si datos expuestos
7. Reporte post-mortem
```

### If Database Down
```
1. Activar modo mantenimiento
2. Restaurar desde backup más reciente
3. Verificar integridad de datos
4. Comunicar usuarios
5. Incrementar monitoring
```

---

## 📞 ESCALATION CONTACTS

```
Admin: [Tu correo]
DBA: [Contacto BD]
Security: [Contacto seguridad]
Hosting Provider: [Contacto soporte hosting]
Emergency: [Número telecomunicación]
```

---

**IMPORTANTE:** No continuar a producción sin completar TODOS los checkboxes de la sección "CRÍTICO".

**Estado:** [ ] LISTO PARA PRODUCCIÓN

**Fecha de Review:** _______________

**Responsable:** _______________

**Aprobación:** _______________
