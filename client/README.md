# Client - Funcionalidades del Cliente

Este directorio contiene todas las funcionalidades disponibles para usuarios clientes (rol 3). Incluye perfil, catálogo de productos, carrito de compras, pedidos y más.

## 📄 Archivos Principales

### perfil.php
**Propósito**: Perfil y configuración de cuenta del usuario
**Archivos Requeridos**: core/sesiones.php
**Acceso**: Solo usuario autenticado (cualquier rol)
**Cargado Por**: index1.php mediante fetch()

**Tabs Disponibles**:

#### Tab 1: Información Personal
- Mostrar: Nombre, Email
- Editar: Nombre y Email
- Campo de contraseña oculto
- Validaciones:
  - Nombre: 3-100 caracteres
  - Email: formato válido, no duplicado

**Endpoint**: `POST api/api_actualizar_perfil.php`

#### Tab 2: Seguridad
- Cambiar Contraseña:
  - Contraseña actual (validación con hash)
  - Contraseña nueva (min 6 caracteres)
  - Confirmación de contraseña
- Eliminar Cuenta:
  - Botón con doble confirmación
  - Elimina TODOS los datos asociados

**Endpoints**: 
- `POST api/api_cambiar_contraseña.php`
- `POST api/api_eliminar_cuenta.php`

**UI**:
- Tabs interactivos
- Mensajes de éxito/error
- Recarga automática después de cambios

---

### productos.php
**Propósito**: Catálogo de productos
**Archivos Requeridos**: core/sesiones.php
**Acceso**: Público (incluso sin loguear)

**Funcionalidades**:
- Listar productos disponibles
- Búsqueda por término
- Filtrar por categoría
- Filtrar por precio (rango)
- Ordenar por:
  - Más recientes
  - Menor precio
  - Mayor precio
  - Mejor puntuado

**Información de Producto**:
- Imagen principal
- Nombre
- Precio
- Stock disponible
- Rating/Calificación
- Descripción corta

**Acciones de Producto**:
- Ver detalle completo
- Agregar al carrito
- Agregar a lista de deseos
- Compartir (opcional)

**Visualización**:
- Grid responsive (1-4 columnas según pantalla)
- Lazy loading de imágenes
- Animaciones hover

---

### carrito.php
**Propósito**: Carrito de compras (contenedor lado)
**Archivos Requeridos**: (ninguno requerido, cargado dinámicamente)
**Acceso**: Todos los usuarios

**Funcionalidades**:
- Listar items en carrito
- Cambiar cantidad
- Remover items
- Calcular subtotal, impuestos, total
- Código de descuento (si aplica)
- Botón "Proceder a Checkout"

**Información de Item**:
- Thumbnail de producto
- Nombre
- Precio unitario
- Cantidad
- Subtotal
- Opción remover

**Validaciones**:
- No permitir cantidad <= 0
- No permitir cantidad > stock
- Actualizar total en tiempo real

**Persistencia**:
- Guardado en localStorage (JavaScript)
- O sesión PHP (alternativa)

---

### finalizarcompra.php
**Propósito**: Proceso de pago y creación de pedido
**Archivos Requeridos**: core/sesiones.php, core/conexion.php
**Acceso**: Solo usuario autenticado

**Pasos**:
1. Revisar carrito
2. Ingresar/confirmar dirección de envío
3. Seleccionar método de envío
4. Seleccionar método de pago
5. Confirmar pedicto
6. Procesar pago
7. Crear registro de pedido

**Información Requerida**:
- Dirección (calle, número, ciudad, código postal)
- Teléfono de contacto
- Nota especial (opcional)
- Método de envío
- Método de pago

**Métodos de Envío** (desde configuración):
- Envío estándar
- Envío express
- Recogida en tienda (si aplica)

**Métodos de Pago** (desde configuración):
- Tarjeta de crédito/débito
- PayPal
- Transferencia bancaria
- Efectivo contra entrega

**Validaciones**:
- Carrito no vacío
- Dirección válida
- Stock disponible (re-validar)
- Totalador válido

**Resultado**:
- Crear registro en tabla `pedidos`
- Limpiar carrito
- Enviar email de confirmación
- Redirigir a "Pedido Confirmado"

---

### historialpedidoC.php
**Propósito**: Ver histórico de pedidos del cliente
**Archivos Requeridos**: core/sesiones.php, core/conexion.php
**Acceso**: Solo usuario autenticado (rol 3)

**Funcionalidades**:
- Listar todos los pedidos del usuario
- Filtrar por estado
- Ordenar por fecha
- Ver detalles de pedido
- Descargar recibo/factura
- Rastrear pedido
- Reordenar (quick order)

**Información de Pedido**:
| # Pedido | Fecha | Total | Estado | Acciones |
|----------|-------|-------|--------|----------|
| #1001 | 15/02 | $99.99 | Enviado | Ver/Descargar |

**Estados Posibles**:
- Pendiente (esperando confirmación)
- Procesando (preparando envío)
- Enviado (en tránsito)
- Entregado (completado)
- Cancelado

**Detalles de Pedido**:
- Items ordenados con cantidad/precio
- Subtotal, impuestos, total
- Dirección de envío
- Método de envío
- Número de seguimiento (si aplica)

---

### listadedeseo.php
**Propósito**: Lista de deseos / Favoritos del cliente
**Archivos Requeridos**: core/sesiones.php, core/conexion.php
**Acceso**: Solo usuario autenticado

**Funcionalidades**:
- Ver productos agregados a lista
- Agregar producto a carrito directamente
- Remover de la lista
- Compartir lista (link público)
- Marcar como "Comprado"
- Ordenar por fecha agregado

**Información de Producto**:
- Thumbnail
- Nombre
- Precio actual
- Stock disponible
- Agregar al carrito
- Remover de lista

**Características**:
- Notificación si precio baja
- Opción de privacidad (lista pública/privada)
- Compartir en redes sociales

---

### mensajeria.php
**Propósito**: Sistema de mensajería entre cliente y soporte
**Archivos Requeridos**: core/sesiones.php, core/conexion.php
**Acceso**: Solo usuario autenticado

**Funcionalidades**:
- Ver conversaciones
- Enviar mensaje
- Recibir respuesta de soporte
- Buscar mensajes
- Ver adjuntos

**Campos de Mensaje**:
- Asunto
- Mensaje
- Fecha/Hora
- Estado (leído/no leído)
- Adjunto (opcional)

**Validaciones**:
- Asunto no vacío
- Mensaje no vacío (mín 10 caracteres)
- Adjunto valida (si aplica)

---

### categoria.php
**Propósito**: Gestión de categorías de productos
**Archivos Requeridos**: core/sesiones.php, core/conexion.php
**Acceso**: Solo Admin y Vendedor

**Funcionalidades**:
- Listar categorías
- Crear categoría
- Editar categoría
- Eliminar categoría
- Ver productos en categoría

**Información de Categoría**:
- Nombre
- Descripción
- Imagen/Icono
- Cantidad de productos
- Estado (activo/inactivo)

**Validaciones**:
- Nombre único
- Nombre no vacío

---

### clientes.php
**Propósito**: Gestión de perfil de cliente
**Archivos Requeridos**: core/sesiones.php, core/conexion.php
**Acceso**: Solo Admin y Vendedor (para editar cliente)
**Acceso Cliente**: Puede ver solo sus datos

**Información de Cliente**:
- Nombre
- Email
- Dirección principal
- Teléfono
- Fecha de registr
- Último pedido
- Total gastado

**Dirección Secundaria**:
- Permitir múltiples direcciones
- Guardar direcciones favoritas
- Editar/eliminar direcciones

**Historial del Cliente**:
- Pedidos realizados
- Valor total gastado
- Cliente desde (fecha)
- Valor promedio de compra

---

## 🛒 Carrito de Compras

### Estructura de Datos
```javascript
// Ejemplo de item en carrito
{
  id_producto: 5,
  nombre: "Producto X",
  precio: 29.99,
  cantidad: 2,
  imagen: "img/producto5.jpg",
  subtotal: 59.98
}
```

### Operaciones
- `agregarAlCarrito(id_producto, cantidad)`
- `removerDelCarrito(id_producto)`
- `actualizarCantidad(id_producto, nuevaCantidad)`
- `vaciarCarrito()`
- `obtenerCarrito()` - retorna array de items
- `calcularTotal()` - retorna suma total

---

## 📊 Base de Datos

### Tabla `pedidos`
```sql
CREATE TABLE pedidos (
  id_pedido INT PRIMARY KEY AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
  total DECIMAL(10,2) NOT NULL,
  estado ENUM('Pendiente','Procesando','Enviado','Entregado','Cancelado'),
  direccion_envio VARCHAR(255),
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);
```

### Tabla `lista_deseos`
```sql
CREATE TABLE lista_deseos (
  id_deseo INT PRIMARY KEY AUTO_INCREMENT,
  id_usuario INT NOT NULL,
  id_producto INT NOT NULL,
  fecha_agregado DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
  UNIQUE KEY (id_usuario, id_producto)
);
```

---

## 🆘 Troubleshooting

**Problema**: Carrito se vacía al recargar
- Verificar localStorage habilitado en navegador
- Verificar sesión PHP activa
- Ver consola para errores JavaScript

**Problema**: No puedo finalizar compra
- Verificar usuario autenticado
- Verificar carrito no vacío
- Verificar stock disponible
- Revisar errores en api/

**Problema**: Cambio de precio no se refleja
- Actualizar caché navegador (Ctrl+F5)
- Verificar producto en BD tiene precio actualizado

---

Última actualización: 15 de febrero de 2026
