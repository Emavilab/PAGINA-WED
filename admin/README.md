# Admin - Panel Administrativo

Panel de administración para gestión del sistema, usuarios, pedidos y configuración. Solo accesible por usuarios con rol 1 (Admin) o rol 2 (Vendedor con acceso limitado).

## 📄 Archivos Principales

### Dashboard.php
**Propósito**: Página principal del panel administrativo
**Archivos Requeridos**: core/sesiones.php
**Acceso**: Solo Admin (rol 1) y Vendedor (rol 2)
**Redirección**: Si usuario no es admin/vendedor → index1.php

**Características**:
- Menú lateral dinámico según rol
- Navegación con carga asincrónica de páginas
- Display de información del usuario (nombre, rol)
- Logout disponible

**Menú Admin (rol 1)**:
- Dashboard (página actual)
- Usuarios (gestión)
- Pedidos (administración)
- Configuración (sistema)

**Menú Vendedor (rol 2)**:
- Dashboard (página actual)
- Pedidos (solo lectura/actualización)

**Estructura de Página**:
```
┌─ Header (Usuario info, Logout) ─────────┐
├─┬────────────────────────────────────┐───┤
│ │ Sidebar con navegación             │   │
│ │ (dinamicasegun rol)                │   │
│ │                                    │ M │
│ │ - Dashboard                        │ A │
│ │ - Usuarios (si admin)              │ I │
│ │ - Pedidos                          │ N │
│ │ - Configuración (si admin)         │   │
│ │                                    │ C │
│ │ - Logout                           │ O │
│ │                                    │ N │
└─┴────────────────────────────────────┘───┤
│ Contenido Principal (cargado dinámicamente)│
│ (se actualiza con fetch)                   │
└────────────────────────────────────────────┘
└─ Footer ────────────────────────────────────┘
```

---

### usuarios.php
**Propósito**: Gestión completa de usuarios
**Archivos Requeridos**: core/sesiones.php, core/conexion.php
**Acceso**: Solo Admin (rol 1)

**Funcionalidades**:
- Listar usuarios (con paginación y filtrado)
- Crear nuevo usuario
- Editar usuario existente
- Eliminar usuario
- Cambiar rol de usuario
- Cambiar estado (activo/inactivo)

**Tabla de Usuarios**:
| ID | Nombre | Email | Rol | Estado | Acciones |
|----|--------|-------|-----|--------|----------|
| 1  | Admin  | admin@... | Admin | Activo | Editar/Eliminar |

**Filtros Disponibles**:
- Por nombre
- Por email
- Por rol
- Por estado
- Búsqueda combinada

**Paginación**:
- 10 usuarios por página
- Navegación: Anterior/Siguiente/Ir a página

** 插件动态**加载:
```javascript
fetch(`admin/obtener_usuarios.php?${params}`)
  .then(response => response.json())
  .then(data => { /* actualizar tabla */ })
```

---

### crear_usuario_admin.php
**Propósito**: Crear nuevo usuario desde panel admin
**Archivos Requeridos**: core/conexion.php
**Método**: POST
**Acceso**: Solo Admin

**Formula Requerido**:
- Nombre (3-100 caracteres)
- Email (válido, no duplicado)
- Contraseña (6+ caracteres, auto-generada o manual)
- Rol (asignable: Admin, Vendedor, Cliente)

**Validaciones**:
- Email único en sistema
- Nombre no vacío
- Contraseña encriptada con PASSWORD_BCRYPT

**Respuesta API**:
```json
{
  "exito": true,
  "mensaje": "Usuario creado exitosamente",
  "nuevoUsuario": { "id": 5, "nombre": "..." }
}
```

---

### editar_usuario_admin.php
**Propósito**: Modificar datos de usuario existente
**Archivos Requeridos**: core/conexion.php
**Método**: POST
**Acceso**: Solo Admin

**Campos Editables**:
- Nombre
- Email (valida no duplicado)
- Rol (cambio de permisos)
- Estado (activo/inactivo)
- Contraseña (opcional)

**Validaciones**:
- ID usuario existente
- Email no duplicado (excepto usuario actual)
- Nombre válido
- Rol existe en tabla `roles`

---

### eliminar_usuario_admin.php
**Propósito**: Eliminar usuario y asociados datos
**Archivos Requeridos**: core/conexion.php
**Método**: POST
**Acceso**: Solo Admin

**ID Requerido**:
```json
{
  "id_usuario": 5
}
```

**Validaciones**:
- Usuario no es admin actual (prevenir auto-eliminación)
- Usuario existe en BD

**Cascada de Eliminación**:
- Pedidos del usuario
- Wishlist del usuario  
- Registro en tabla `clientes`
- Registro en tabla `usuarios`

**Nota**: Usa transacciones para seguridad

---

### obtener_usuarios.php
**Propósito**: API para obtener lista de usuarios (paginada/filtrada)
**Método**: GET
**Parámetros Requeridos**:
- `page` (pagination número)
- `search` (búsqueda opcional)
- `filter` (rol/estado opcional)

**Ejemplo de URL**:
```
admin/obtener_usuarios.php?page=1&search=juan&filter=rol:3
```

**Respuesta**:
```json
{
  "exito": true,
  "usuarios": [
    {
      "id_usuario": 5,
      "nombre": "Juan Pérez",
      "correo": "juan@ejemplo.com",
      "nombre_rol": "Cliente",
      "estado": "Activo"
    }
  ],
  "total": 45,
  "pagina": 1,
  "porPagina": 10
}
```

---

### pedidosadmin.php
**Propósito**: Gestión de órdenes del sistema
**Archivos Requeridos**: core/sesiones.php
**Acceso**: Admin y Vendedor

**Funcionalidades**:
- Ver todos los pedidos (Admin) o sus pedidos (Vendedor)
- Filtrar por estado (Pendiente, Procesando, Enviado, Entregado)
- Ver detalles del pedido
- Actualizar estado del pedido
- Ver información del cliente
- Generar reportes

**Campos Mostrados**:
| # | Cliente | Monto | Estado | Fecha | Acciones |
|---|---------|-------|--------|-------|----------|
| 001 | Juan P. | $99.99 | Pendiente | 15/02 | Ver/Editar |

---

## 🔐 Control de Acceso

### Matriz de Permisos

| Funcionalidad | Admin (1) | Vendedor (2) | Cliente (3) |
|---|---|---|---|
| Dashboard | ✅ | ✅ (limitado) | ❌ |
| Gestión Usuarios | ✅ | ❌ | ❌ |
| Ver Pedidos | ✅ | ✅ | ✅ (propios) |
| Configuración Sistema | ✅ | ❌ | ❌ |
| Gestión Categorías | ✅ | ✅ | ❌ |
| Gestión Productos | ✅ | ✅ | ❌ |

### Implementación

```php
// Al inicio de cada archivo admin
require_once '../core/sesiones.php';

// Verificar Admin
if ($_SESSION['id_rol'] != 1) {
    header("Location: ../index1.php");
    exit();
}
```

---

## 🎨 Interfaz UI

### Biblioteca: Tailwind CSS
- Tema claro/oscuro
- Componentes reutilizables
- Formularios validados
- Modal diálogos
- Notificaciones de éxito/error

### Validación
- **Cliente**: HTML5 + JavaScript
- **Servidor**: PHP validación

---

## 📊 Base de Datos

### Tabla `usuarios`
```sql
CREATE TABLE usuarios (
  id_usuario INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(100) UNIQUE NOT NULL,
  contraseña VARCHAR(255) NOT NULL,
  id_rol INT NOT NULL,
  estado ENUM('Activo','Inactivo') DEFAULT 'Activo',
  FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);
```

### Tabla `roles`
```sql
CREATE TABLE roles (
  id_rol INT PRIMARY KEY,
  nombre_rol VARCHAR(50) NOT NULL
);
-- 1=Admin, 2=Vendedor, 3=Cliente
```

---

## 🆘 Troubleshooting

**Problema**: No puedo acceder al Dashboard
- Verificar rol en tabla `usuarios`
- Verificar sesión activa (login)
- Verificar redirección en Dashboard.php

**Problema**: Los usuarios no se cargan en tabla
- Verificar conexión a BD
- Verificar tabla `obtener_usuarios.php` retorna JSON válido
- Ver consola navegador (F12) para errores JavaScript

**Problema**: No puedo eliminar usuario
- Verificar transacción BD en `eliminer_usuario_admin.php`
- Verificar Foreign keys en tabla `pedidos`

---

Última actualización: 15 de febrero de 2026
