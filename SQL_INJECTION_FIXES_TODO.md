# 📋 ARCHIVOS VULNERABLES A LA SQL INJECTION - LISTA DE TAREAS

## ✅ YA PARCHEADOS (FASE 1)

- [x] `admin/admin_bancos.php` - INSERT/UPDATE/DELETE
- [x] `admin/Dashboard.php` - SELECT queries
- [x] `api/api_crear_pedido.php` - DELETE/UPDATE al final

---

## 🔴 CRÍTICOS - NECESITAN PARCHE INMEDIATO

### 1. **admin/admin_compras.php**
**Severidad:** 🔴 CRÍTICA - Operaciones de dinero
**Líneas Problemáticas:** ~150-200
**Vulnerabilidades:**
- Queries sin prepared statements
- Posible SQL injection en filtros de búsqueda
- Operaciones de dinero sin transacciones

**Tipo de Fix:** 
- Reemplazar mysqli_query con prepared statements
- Agregar begin_transaction/commit para operaciones de dinero

**Ejemplo Vulnerable:**
```php
// MALO
$sql = "SELECT * FROM compras WHERE id_compra = $id";
mysqli_query($conexion, $sql);
```

**Ejemplo Corregido:**
```php
// BUENO
$sql = "SELECT * FROM compras WHERE id_compra = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
```

---

### 2. **admin/gestion_productos.php**
**Severidad:** 🔴 CRÍTICA - Datos sensibles de productos
**Vulnerabilidades:**
- Manejo de precios sin validación
- Queries de stock sin prepared statements
- Posible manipulación de ofertas

**Archivos de Upload:** jpg, png, etc - lo mismo que admin_bancos.php

---

### 3. **api/api_carrito.php**
**Severidad:** 🟠 ALTA - Integridad del carrito
**Líneas Problemáticas:** ~80-150
**Estado ACTUAL:** ✅ PARECE USAR prepared statements
**Pendiente:** Revisión de file upload si existe

---

## 🟠 ALTOS - PRÓXIMA PRIORIDAD

### 4. **admin/cambiar_estado.php**
**Severidad:** 🟠 ALTA - Cambios de estado de pedidos
**Vulnerabilidades:**
- Queries sin parametrización
- Posible cambio no autorizado de estados

---

### 5. **admin/configuracion.php**
**Severidad:** 🟠 ALTA - Datos de configuración
**Vulnerabilidades:**
- UPDATE de configuración sin prepared statements
- Posible inyección en campos de configuración

---

### 6. **admin/crear_usuario_admin.php**
**Severidad:** 🟠 ALTA - Creación de usuarios administrativos
**Vulnerabilidades:**
- INSERT de usuarios sin prepared statements
- Validación insuficiente de datos

---

### 7. **admin/editar_usuario_admin.php**
**Severidad:** 🟠 ALTA - Edición de usuarios administrativos
**Vulnerabilidades:**
- UPDATE de usuarios sin prepared statements

---

### 8. **admin/eliminar_usuario_admin.php**
**Severidad:** 🟠 ALTA - Eliminación de usuarios administrativos
**Vulnerabilidades:**
- DELETE sin prepared statements
- Sin verificación de permisos adicionales

---

### 9. **admin/admin_exportar exel.php**
**Severidad:** 🟠 ALTA - Exportación de datos
**Vulnerabilidades:**
- SELECT con concatenación de variables (filtros)
- Posible inyección en parámetros de filtrado

---

### 10. **admin/obtener_usuarios.php**
**Severidad:** 🟠 ALTA - Obtención de usuarios
**Vulnerabilidades:**
- Queries sin parametrización
- Filtros vulnerables

---

## 🟡 MEDIOS - DESPUÉS DE ALTOS

### 11-20. APIs en `/api/` directorio

#### Revisar estos archivos:
- `api_actualizar_perfil.php` - UPDATE de perfil
- `api_cambiar_contraseña.php` - UPDATE de contraseña
- `api_cancelar_pedido.php` - UPDATE de pedidos
- `api_categorias.php` - SELECT/INSERT/UPDATE/DELETE
- `api_crear_direccion.php` - INSERT dirección
- `api_editar_direccion.php` - UPDATE dirección
- `api_eliminar_direccion.php` - DELETE dirección
- `api_lista_deseos.php` - Todas las operaciones
- `api_mensajeria.php` - Todas las operaciones
- `api_productos.php` - SELECT con filtros
- `api_usuario.php` - SELECT usuario
- `registrar_usuario.php` - INSERT usuario

---

## 📋 CHECKLIST PARA CADA ARCHIVO A PARCHEAR

Cuando edites un archivo, sigue este checklist:

- [ ] Identificar todas las queries (SELECT, INSERT, UPDATE, DELETE)
- [ ] Marcar queries que usan `mysqli_query` directamente
- [ ] Verificar si concatenan variables: `WHERE id = $id` ❌
- [ ] Reemplazar con prepared statement:
  ```php
  $stmt = $conexion->prepare("SELECT * FROM tabla WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $result = $stmt->get_result();
  ```
- [ ] Validar/sanitizar entrada si es necesario
- [ ] Agregar manejo de errores
- [ ] Probar que la funcionalidad sigue igual
- [ ] Revisar logs para errores de prepared statements

---

## 🔄 TIPO DE CAMBIOS POR TIPO DE QUERY

### SELECT Básico
```php
// ANTES
$resultado = mysqli_query($conexion, "SELECT * FROM usuarios WHERE id = $id");

// DESPUÉS
$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$resultado = $stmt->get_result();
var_dump($resultado->fetch_assoc());
```

### INSERT
```php
// ANTES
mysqli_query($conexion, "INSERT INTO usuarios (nombre, email) VALUES ('$nombre', '$email')");

// DESPUÉS
$stmt = $conexion->prepare("INSERT INTO usuarios (nombre, email) VALUES (?, ?)");
$stmt->bind_param("ss", $nombre, $email);
$stmt->execute();
$id = $conexion->insert_id;
$stmt->close();
```

### UPDATE
```php
// ANTES
mysqli_query($conexion, "UPDATE usuarios SET nombre='$nuevo' WHERE id=$id");

// DESPUÉS
$stmt = $conexion->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?");
$stmt->bind_param("si", $nuevo, $id);
$stmt->execute();
$stmt->close();
```

### DELETE
```php
// ANTES
mysqli_query($conexion, "DELETE FROM usuarios WHERE id=$id");

// DESPUÉS
$stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
```

---

## 📊 TABLA DE BIND_PARAM TIPOS

| Tipo | Letra | Ejemplo |
|------|-------|---------|
| Integer | `i` | `$id = 5` |
| Float/Decimal | `d` | `$precio = 19.99` |
| String | `s` | `$nombre = "Juan"` |
| Blob | `b` | `$binario = file_get_contents()` |

**Ejemplo con múltiples tipos:**
```php
$stmt->bind_param("isi", $nombre, $id, $precio);
// $nombre = string, $id = integer, $precio = float
```

---

## ⚠️ ERRORES COMUNES A EVITAR

```php
// ❌ MAL - Olvidar cerrar statement
$stmt = $conexion->prepare($sql);
$stmt->bind_param(...);
$stmt->execute();
// FALTA: $stmt->close();

// ❌ MAL - No verificar prepare()
$stmt = $conexion->prepare($sql); // Podría retornar false
$stmt->bind_param(...); // Error si prepare falló

// ✅ BIEN
$stmt = $conexion->prepare($sql);
if (!$stmt) {
    error_log("Error: " . $conexion->error);
    exit();
}
$stmt->bind_param(...);

// ❌ MAL - Pasar variable sin referenciar
$stmt->bind_param("i", $variable); // Incorrecto si variable cambia luego

// ✅ BIEN si variable no cambia durante execute
$stmt->bind_param("i", $id); // $id no debería cambiar

// ❌ MAL - Reutilizar statement para misma query múltiples veces
foreach ($ids as $id) {
    $stmt->execute(); // No vincula el nuevo $id cada vez
}

// ✅ BIEN
foreach ($ids as $id) {
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
```

---

## 🧪 PRUEBAS POR ARCHIVO

### Después de cada parche:
1. **Probar funcionalidad normal** - Crear, editar, eliminar registros
2. **Prueba de inyección SQL** - Intentar inyectar: `1' OR '1'='1`
   - Debe rechazar o trata como string literal
3. **Revisar logs** - No debe haber errores de prepared statements
4. **Comparar resultados** - Datos antes y después deben ser iguales

---

## 📈 PROGRESO ESPERADO

- **Día 1:** Críticos (admin_bancos ✅, admin_compras, gestion_productos)
- **Día 2-3:** Altos (cambiar_estado, configuracion, crear/editar/eliminar usuarios)
- **Semana 1:** APIs básicas (carrito, direcciones, productos)
- **Semana 2:** APIs restantes

---

## 📞 REFERENCIA RÁPIDA

**mysqli_prepare Documentation:**
https://www.php.net/manual/en/mysqli.quickstart.prepared-statements.php

**SQL Injection Prevention:**
https://owasp.org/www-community/attacks/SQL_Injection

**OWASP Top 10:**
https://owasp.org/www-project-top-ten/

---

Última actualización: 21 de marzo de 2026
Fase 1 completada - Credenciales .env, .htaccess, CSRF, queries iniciales.
