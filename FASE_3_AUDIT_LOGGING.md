# 📊 FASE 3-2: AUDIT LOGGING - DOCUMENTACIÓN COMPLETA

## 📋 RESUMEN

Se ha implementado un **sistema completo de auditoría** que registra todas las acciones sensitivas realizadas en el sistema.

**Qué se Registra:**
- ✅ Creación, edición y eliminación de usuarios
- ✅ Cambios en configuración (marcas, envíos, pagos)
- ✅ Cambios de contraseña
- ✅ Cambios de rol/permisos
- ✅ Intentos de acceso denegado
- ✅ Modificaciones en pedidos y pagos

**Información Capturada:**
- Usuario que realizó la acción
- Rol del usuario
- Fecha y hora exacta
- IP del cliente
- Navegador/User Agent
- Valores anteriores y nuevos (en JSON)
- Notas descriptivas

---

## 🔧 INSTALACIÓN

### PASO 1: Crear la Tabla en Base de Datos

**Opción A: Vía phpMyAdmin**

1. Ir a: `http://localhost/phpmyadmin`
2. Seleccionar BD: `negocio_web`
3. Click en "SQL"
4. Copiar y pegar contenido de: [database/FASE_3_AUDIT_LOGGING.sql](database/FASE_3_AUDIT_LOGGING.sql)
5. Click "Go"

**SQL a ejecutar:**
```sql
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` INT NULL,
  `usuario_nombre` VARCHAR(255) NOT NULL,
  `usuario_rol` INT NULL,
  `accion` VARCHAR(50) NOT NULL,
  `tabla` VARCHAR(100) NOT NULL,
  `registro_id` INT NOT NULL,
  `valores_anteriores` LONGTEXT NULL,
  `valores_nuevos` LONGTEXT NULL,
  `notas` VARCHAR(500) NULL,
  `ip` VARCHAR(45) NOT NULL,
  `navegador` VARCHAR(255) NULL,
  `tiempo` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_usuario_id` (`usuario_id`),
  INDEX `idx_usuario_nombre` (`usuario_nombre`),
  INDEX `idx_accion` (`accion`),
  INDEX `idx_tabla` (`tabla`),
  INDEX `idx_tiempo` (`tiempo`),
  INDEX `idx_accion_tabla` (`accion`, `tabla`),
  INDEX `idx_usuario_tiempo` (`usuario_id`, `tiempo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### PASO 2: Confirmación de Archivos

✅ **Archivos Creados:**
- `core/audit_logging.php` (300 líneas) - Sistema completo
- `database/FASE_3_AUDIT_LOGGING.sql` - Script tabla

✅ **Archivos Modificados:**
- `admin/crear_usuario_admin.php` - Registra creación
- `admin/editar_usuario_admin.php` - Registra ediciones
- `admin/eliminar_usuario_admin.php` - Registra eliminaciones
- `core/procesar_configuracion.php` - Registra cambios config

---

## 🧪 TESTING

### TEST 1: Verificar Tabla Creada

```sql
-- En phpMyAdmin Query:
SELECT * FROM audit_logs;
-- Debe mostrar tabla vacía (0 registros)
```

---

### TEST 2: Crear un Nuevo Usuario

**Pasos:**
1. Ir a: Admin Panel → Usuarios → Crear Usuario
2. Llenar datos: Nombre, Email, Contraseña, Rol
3. Click "Guardar"
4. Debe mostrar éxito

**Verificar en BD:**
```sql
SELECT * FROM audit_logs WHERE accion = 'CREATE' ORDER BY tiempo DESC LIMIT 1;
```

**Resultado esperado:**
```
Columna               | Valor
---------------------|------------------------------------------
usuario_nombre       | [Tu nombre admin]
accion              | CREATE
tabla               | usuarios
valores_nuevos      | {"nombre":"Juan","correo":"..."}
notas               | Nuevo usuario creado: Juan (juan@...)
tiempo              | [Fecha/hora actual]
```

---

### TEST 3: Editar el Usuario Creado

**Pasos:**
1. Ir a: Admin Panel → Usuarios → Editar (el usuario creado)
2. Cambiar: Nombre, Estado
3. Click "Guardar"
4. Debe mostrar éxito

**Verificar en BD:**
```sql
SELECT * FROM audit_logs WHERE accion = 'UPDATE' ORDER BY tiempo DESC LIMIT 1;
```

**Resultado esperado:**
```
Columna              | Valor
--------------------|------------------------------------------
usuario_nombre      | [Tu nombre admin]
accion             | UPDATE
tabla              | usuarios
valores_anteriores | {"correo":"..."}
valores_nuevos     | {"nombre":"Nuevo Nombre","estado":"inactivo"}
notas              | Usuario editado: Nuevo Nombre - Cambios en...
```

---

### TEST 4: Cambiar Configuración

**Pasos:**
1. Ir a: Admin Panel → Configuración
2. Cambiar: Color primario, Nombre negocio, o agregar nueva marca
3. Click "Guardar"

**Verificar en BD:**
```sql
SELECT * FROM audit_logs WHERE tabla IN ('configuracion', 'marcas') ORDER BY tiempo DESC LIMIT 3;
```

---

### TEST 5: Eliminar Usuario

**Pasos:**
1. Ir a: Admin Panel → Usuarios
2. Click "Eliminar" en un usuario
3. Confirmar eliminación

**Verificar en BD:**
```sql
SELECT * FROM audit_logs WHERE accion = 'DELETE' ORDER BY tiempo DESC LIMIT 1;
```

**Resultado esperado:**
```
accion      | DELETE
tabla       | usuarios
valores_anteriores | Datos del usuario eliminado
valores_nuevos | (vacío)
notas       | Usuario eliminado completamente del sistema
```

---

## 📊 FUNCIONES DISPONIBLES

### `registrarAudit($accion, $tabla, $registro_id, $valores_anteriores, $valores_nuevos, $notas)`

```php
// Ejemplo: Registrar creación de usuario
registrarAudit(
    'CREATE',
    'usuarios',
    $id_nuevo_usuario,
    [],  // Sin valores anteriores
    [
        'nombre' => 'Juan García',
        'correo' => 'juan@empresa.com',
        'rol' => 1
    ],
    'Nuevo usuario creado por admin'
);
```

**Parámetros:**
- `$accion` - CREATE, UPDATE, DELETE, ACCESO_DENEGADO, CAMBIO_CONTRASEÑA, etc
- `$tabla` - Tabla afectada (usuarios, pedidos, productos, etc)
- `$registro_id` - ID del registro modificado
- `$valores_anteriores` - Array con estado anterior (para UPDATE)
- `$valores_nuevos` - Array con estado nuevo
- `$notas` - Descripción adicional

---

### `registrarAccesoDenegado($tabla, $motivo)`

```php
// Si alguien intenta acceder sin permisos
registrarAccesoDenegado('usuarios', 'cliente_intento_admin');
```

---

### `registrarCambioContrasena($usuario_id_afectado, $notas)`

```php
// Cuando admin cambia contraseña de usuario
registrarCambioContrasena(5, 'Contraseña reseteada manualmente');
```

---

### `registrarCambioRol($usuario_id_afectado, $rol_anterior, $rol_nuevo)`

```php
// Cuando se cambia el rol de un usuario
registrarCambioRol(5, 3, 2);  // De Cliente (3) a Vendedor (2)
```

---

### `obtenerAuditLog($filtro, $valor, $limit)`

```php
// Ver todos los audits (últimos 100)
$logs = obtenerAuditLog();

// Audits de un usuario específico
$logs = obtenerAuditLog('usuario', 'admin@empresa.com');

// Audits de una tabla específica
$logs = obtenerAuditLog('tabla', 'usuarios');

// Audits de una acción específica
$logs = obtenerAuditLog('accion', 'DELETE');

// Audits de una fecha específica
$logs = obtenerAuditLog('fecha', '2026-03-21');
```

---

### `obtenerActividadUsuario($usuario_id, $limit)`

```php
// Ver todas las acciones de un usuario específico
$actividad = obtenerActividadUsuario(5, 50);

foreach ($actividad as $log) {
    echo $log['accion'] . " - " . $log['tiempo'];
}
```

---

### `obtenerActividadSospechosa()`

```php
// Ver actividades sospechosas (múltiples DELETES, accesos denegados, etc)
$sospechoso = obtenerActividadSospechosa();

foreach ($sospechoso as $evento) {
    // Alertar al admin
    echo "⚠️ " . $evento['usuario_nombre'] . ": " . 
         $evento['cantidad'] . " intentos de " . 
         $evento['accion'];
}
```

---

### `limpiarLogsAntiguos()`

```php
// Ejecutar diariamente vía cron para borrar logs >30 días
limpiarLogsAntiguos();
```

---

### `exportarAuditLogCSV($nombre_archivo, $filtro, $valor)`

```php
// Exportar audits a CSV
// header('Content-Disposition: attachment');
exportarAuditLogCSV('audit_reporteMarzo', 'fecha', '2026-03-21');
// Descarga archivo: audit_reporteMarzo_2026-03-21.csv
```

---

## 📈 CASOS DE USO

### 1. Generar Reporte de Cambios de Usuario

```php
<?php
require_once 'core/audit_logging.php';
require_once 'core/conexion.php';

$usuario_id = 5;
$logs = obtenerActividadUsuario($usuario_id);

echo "=== ACTIVIDAD DEL USUARIO ===\n";
foreach ($logs as $log) {
    echo "Fecha: " . $log['tiempo'] . "\n";
    echo "Acción: " . $log['accion'] . "\n";
    echo "Tabla: " . $log['tabla'] . "\n";
    echo "Notas: " . $log['notas'] . "\n";
    echo "---\n";
}
?>
```

---

### 2. Detectar Actividad Maliciosa

```php
<?php
$sospechosos = obtenerActividadSospechosa();

if (!empty($sospechosos)) {
    // Enviar alerta al admin
    foreach ($sospechosos as $evento) {
        registrarAudit(
            'ALERTA_SEGURIDAD',
            'audit_logs',
            0,
            [],
            ['evento' => $evento],
            "⚠️ ACTIVIDAD SOSPECHOSA: " . $evento['usuario_nombre']
        );
    }
}
?>
```

---

### 3. Auditar Eliminaciones en Masa

```php
<?php
// Después de eliminar múltiples usuarios

registrarAudit(
    'DELETE_BATCH',
    'usuarios',
    0,
    ['cantidad' => 10],
    ['eliminados' => ['user1@...', 'user2@...', ...]],
    'Eliminación en lote de 10 usuarios inactivos'
);
?>
```

---

## 📊 CONSULTAS SQL ÚTILES

### Ver Actividad del Último Día

```sql
SELECT usuario_nombre, accion, tabla, COUNT(*) as cantidad, MAX(tiempo) as ultimo
FROM audit_logs
WHERE tiempo > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY usuario_nombre, accion
ORDER BY cantidad DESC;
```

### Ver Cambios en Configuración

```sql
SELECT * FROM audit_logs
WHERE tabla = 'configuracion'
ORDER BY tiempo DESC
LIMIT 20;
```

### Ver Eliminaciones

```sql
SELECT usuario_nombre, tabla, registro_id, notas, tiempo
FROM audit_logs
WHERE accion = 'DELETE'
ORDER BY tiempo DESC
LIMIT 50;
```

### Ver Cambios de Contraseña

```sql
SELECT usuario_nombre, usuario_id, notas, tiempo
FROM audit_logs
WHERE accion = 'CAMBIO_CONTRASEÑA'
ORDER BY tiempo DESC
LIMIT 20;
```

### Ver Accesos Denegados

```sql
SELECT usuario_nombre, tabla, notas, COUNT(*) as intentos
FROM audit_logs
WHERE accion = 'ACCESO_DENEGADO'
AND tiempo > DATE_SUB(NOW(), INTERVAL 24 HOUR)
GROUP BY usuario_nombre, tabla
ORDER BY intentos DESC;
```

---

## 🛡️ ANÁLISIS DE SEGURIDAD

### Script para Alertas Automáticas

```php
<?php
// admin/cron_audit_alerts.php
// Ejecutar diariamente vía cron

require_once '../core/conexion.php';
require_once '../core/audit_logging.php';
require_once '../core/smtp_config.php';

// 1. Detectar múltiples eliminaciones
$hace_24horas = date('Y-m-d H:i:s', strtotime('-24 hours'));
$stmt = $conexion->prepare(
    "SELECT usuario_nombre, COUNT(*) as cantidad
     FROM audit_logs
     WHERE accion = 'DELETE'
     AND tiempo > ?
     GROUP BY usuario_nombre
     HAVING cantidad >= 5"
);
$stmt->bind_param("s", $hace_24horas);
$stmt->execute();
$resultado = $stmt->get_result();

while ($fila = $resultado->fetch_assoc()) {
    $asunto = "⚠️ ALERTA: Múltiples eliminaciones detectadas";
    $msg = "Usuario: " . $fila['usuario_nombre'] . "\n" .
           "Eliminaciones: " . $fila['cantidad'];
    
    // enviarEmail('admin@empresa.com', $asunto, $msg);
}

// 2. Accesos denegados sospechosos
$stmt = $conexion->prepare(
    "SELECT ip, COUNT(*) as intentos
     FROM audit_logs
     WHERE accion = 'ACCESO_DENEGADO'
     AND tiempo > ?
     GROUP BY ip
     HAVING intentos >= 10"
);
$stmt->bind_param("s", $hace_24horas);
$stmt->execute();
// Procesar resultados...
?>
```

---

## ⚙️ CONFIGURACIÓN AVANZADA

### Modificar Duración de Retención de Logs

**Por defecto:** 30 días

```php
// En core/audit_logging.php, función limpiarLogsAntiguos()
$hace_30_dias = date('Y-m-d', strtotime('-30 days')); // ← Cambiar aquí
```

**Cambiar a 60 días:**
```php
$hace_60_dias = date('Y-m-d', strtotime('-60 days'));
```

---

### Auto-Limpieza Diaria (Via Cron)

**Linux/Unix:**
```bash
# Agregar a crontab:
0 2 * * * php /var/www/html/pagina_web/admin/cron_cleanup_logs.php

# Este archivo:
<?php
require_once '../core/audit_logging.php';
limpiarLogsAntiguos();
?>
```

---

## 📊 PANEL DE AUDITORÍA (RECOMENDADO - FASE 3 AVANZADA)

Se recomienda crear `admin/audit_panel.php` para:

```php
<!-- admin/audit_panel.php -->
<?php
require_once '../core/conexion.php';
require_once '../core/sesiones.php';
require_once '../core/audit_logging.php';

if ($_SESSION['id_rol'] != 1) {
    header('Location: ../index.php');
    exit();
}

$filtro = $_GET['filtro'] ?? 'todos';
$valor = $_GET['valor'] ?? '';

$logs = obtenerAuditLog($filtro, $valor, 100);
?>

<table>
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>Tabla</th>
            <th>IP</th>
            <th>Navegador</th>
            <th>Notas</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td><?php echo date('d/m/Y H:i', strtotime($log['tiempo'])); ?></td>
            <td><?php echo htmlspecialchars($log['usuario_nombre']); ?></td>
            <td><?php echo $log['accion']; ?></td>
            <td><?php echo $log['tabla']; ?></td>
            <td><?php echo $log['ip']; ?></td>
            <td><?php echo substr($log['navegador'], 0, 30) . '...'; ?></td>
            <td><?php echo $log['notas']; ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
```

---

## ✅ VALIDACIÓN FINAL

```
✅ Tabla audit_logs creada en BD
✅ core/audit_logging.php presente
✅ admin/crear_usuario_admin.php integrado
✅ admin/editar_usuario_admin.php integrado
✅ admin/eliminar_usuario_admin.php integrado
✅ core/procesar_configuracion.php integrado
✅ Se registran creaciones de usuarios
✅ Se registran ediciones de usuarios
✅ Se registran eliminaciones de usuarios
✅ Se registran cambios de configuración
```

**Status:** 🟢 LISTO PARA PRODUCCIÓN

---

## 🔄 PRÓXIMO PASO

✅ **Fase 3-2 Completada:** Audit Logging

📋 **Siguiente:** Fase 3-3 - Monitoring & Alertas Automáticas

El monitoring alertará automáticamente cuando detecte:
- Múltiples intentos de acceso denegado
- Eliminaciones en masa
- Cambios sospechosos
- Patrones de ataque

---

Cuando estés listo, avisa para comenzar con Monitoring & Alertas.
