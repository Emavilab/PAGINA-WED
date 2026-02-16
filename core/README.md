# Core - Núcleo de la Aplicación

Este directorio contiene los archivos fundamentales para el funcionamiento de la aplicación: autenticación, sesiones y base de datos.

## 📄 Archivos

### conexion.php
**Propósito**: Establecer y mantener conexión con MySQL
- **Variables Globales**: `$conexion` (objeto mysqli)
- **Configuración**: servidor, usuario, contraseña, base de datos
- **Charset**: UTF-8 MB4 para soporte completo de caracteres
- **Error Handling**: Valida conexión y muestra error si falla

**Dependencias**: Ninguna
**Usado Por**: sesiones.php, todos los archivos que acceden BD

```php
// Ejemplo de uso
require_once 'conexion.php';
$resultado = $conexion->query("SELECT * FROM usuarios");
```

---

### sesiones.php
**Propósito**: Gestión centralizada de autenticación y sesiones
- **Archivos Requeridos**: conexion.php
- **Funciones Principales**:
  - `usuarioAutenticado()` - Verifica si usuario está logeado
  - `obtenerDatosUsuario()` - Retorna datos del usuario actual
  - `registrarSesion($id_usuario)` - Inicia sesión para usuario
  - `validarCredenciales($email, $password)` - Autentica usuario
  - `crearUsuario($nombre, $email, $password, $id_rol)` - Registra nuevo usuario
  - `cerrarSesion()` - Destruye sesión

**Variables de Sesión Almacenadas**:
- `$_SESSION['id_usuario']` - ID del usuario
- `$_SESSION['nombre']` - Nombre completo
- `$_SESSION['correo']` - Email del usuario
- `$_SESSION['id_rol']` - Rol (1=admin, 2=vendedor, 3=cliente)
- `$_SESSION['nombre_rol']` - Nombre del rol

**Hash de Contraseña**: PASSWORD_BCRYPT

**Ejemplo de Implementación**:
```php
// En cualquier página
require_once 'core/sesiones.php';

if (!usuarioAutenticado()) {
    header("Location: pages/login.php");
    exit();
}

$usuario = obtenerDatosUsuario();
echo "Bienvenido " . $usuario['nombre'];
```

---

### configuracion.php
**Propósito**: Panel de configuración del sistema (solo Admin)
- **Archivos Requeridos**: sesiones.php
- **Funcionalidades**:
  - Gestión de Marcas (CRUD)
  - Configuración de Métodos de Envío
  - Configuración de Métodos de Pago
  - Configuración General (nombre, email, teléfono)
- **Validación**: Verifica rol Admin antes de acceso
- **Interfaz UI**: Tailwind CSS con tabs y modales

**Acceso**: Solo para usuarios con rol 1 (Admin)

---

### cerrar_sesion.php
**Propósito**: Terminar sesión del usuario y limpiar datos
- **Archivos Requeridos**: sesiones.php
- **Acciones**:
  1. Llama `cerrarSesion()` de sesiones.php
  2. Destruye la sesión
  3. Redirige a index1.php

**Uso**: Llamado desde botón "Cerrar Sesión" en header

```php
// Desde JavaScript/formulario
fetch('core/cerrar_sesion.php');
```

---

## 🔄 Flujo de Autenticación

```
Usuario Abre index1.php
        ↓
Verifica usuarioAutenticado()
        ↓
    ¿Autenticado?
    /          \
   SÍ           NO
  /              \
Carga datos    Muestra login/registro
de usuario        ↓
   ↓           Usuario submite formulario
Verifica rol      ↓
   /|\          Valida credenciales
  / | \           ↓
AD VD CL       ¿Válido?
|  |  |         /      \
|  |  |        SÍ       NO
↓  ↓  ↓        |        └→ Muestra error
```

---

## 📊 Tabla de Roles

| ID | Nombre | Acceso |
|----|--------|--------|
| 1 | Admin | Dashboard completo + Configuración |
| 2 | Vendedor | Dashboard (solo Pedidos) |
| 3 | Cliente | index1.php + Perfil + Carrito |

---

## ⚠️ Notas de Seguridad

- Contraseñas SIEMPRE hasheadas con PASSWORD_BCRYPT
- Validación de sesión en CADA página protegida
- Prepared statements para todas las queries
- No guardar contraseña en sesión
- Timeout automático de sesión (recomendado: 30 minutos)

---

## 🆘 Troubleshooting

**Problema**: "Error de conexión a base de datos"
- Verificar credenciales en `conexion.php`
- Confirmar MySQL está ejecutándose
- Verificar base de datos "negocio_web" existe

**Problema**: Sesión no se mantiene entre páginas
- Verificar `session_start()` se ejecuta ANTES de headers
- Check cookies habilitadas en navegador
- Verificar `/tmp` escribible (Linux) o carpeta temp (Windows)

**Problema**: Usuario puede acceder a páginas de admin
- Verificar función `usuarioAutenticado()` en página
- Confirmarch roles asignados correctamente en BD

---

Última actualización: 15 de febrero de 2026
