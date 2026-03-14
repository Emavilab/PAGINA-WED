<?php
/*
==============================================================
API PARA OBTENER DIRECCIONES DEL CLIENTE
==============================================================

DESCRIPCIÓN:
Este script obtiene todas las direcciones registradas por
un cliente autenticado en el sistema. La información incluye
datos de la dirección y detalles del departamento de envío
asociado, como el costo y los días estimados de entrega.

FUNCIONALIDAD:
1. Define que la respuesta del servidor será en formato JSON.
2. Incluye los archivos necesarios para conexión a la base
   de datos y manejo de sesiones.
3. Verifica que el usuario esté autenticado.
4. Obtiene los datos del usuario autenticado.
5. Extrae el ID del cliente.
6. Consulta las direcciones registradas por ese cliente.
7. Relaciona cada dirección con el departamento de envío.
8. Devuelve los resultados en formato JSON.

ARCHIVOS UTILIZADOS:
- conexion.php → Conexión a la base de datos.
- sesiones.php → Manejo de autenticación y sesión del usuario.

TABLAS UTILIZADAS:
- direcciones_cliente
- departamentos_envio

CAMPOS OBTENIDOS:
Direcciones:
- id_direccion
- id_cliente
- direccion
- id_departamento
- otros campos de dirección

Departamento:
- nombre_departamento
- costo_envio
- dias_entrega

RESPUESTA JSON:
{
  "success": true,
  "direcciones": [ ... ]
}

Este endpoint se utiliza generalmente en el proceso de compra
para que el usuario seleccione una dirección de envío guardada.

==============================================================
*/

header('Content-Type: application/json; charset=utf-8');

// Incluir archivos necesarios
require_once '../core/conexion.php';
require_once '../core/sesiones.php';

/*
--------------------------------------------------------------
VERIFICAR AUTENTICACIÓN DEL USUARIO
--------------------------------------------------------------
Se verifica que el usuario haya iniciado sesión. Si no está
autenticado se devuelve una respuesta negativa.
*/
if (!usuarioAutenticado()) {
    echo json_encode(['success' => false]);
    exit;
}

/*
--------------------------------------------------------------
OBTENER DATOS DEL USUARIO AUTENTICADO
--------------------------------------------------------------
Se recupera la información del usuario almacenada en la sesión.
*/
$usuario = obtenerDatosUsuario();

/*
--------------------------------------------------------------
VALIDAR QUE EL USUARIO TENGA ID DE CLIENTE
--------------------------------------------------------------
Si no existe el cliente asociado al usuario se detiene
la ejecución y se devuelve error.
*/
if (!$usuario || !isset($usuario['id_cliente'])) {
    echo json_encode(['success' => false]);
    exit;
}

// Guardar el ID del cliente autenticado
$id_cliente = $usuario['id_cliente'];

/*
--------------------------------------------------------------
CONSULTA SQL
--------------------------------------------------------------
Obtiene todas las direcciones del cliente junto con la
información del departamento de envío asociado.

LEFT JOIN permite obtener la información del departamento
aunque la dirección no tenga un departamento asociado.
*/
$query = "SELECT d.*, dep.nombre_departamento, dep.costo_envio, dep.dias_entrega 
          FROM direcciones_cliente d 
          LEFT JOIN departamentos_envio dep ON d.id_departamento = dep.id_departamento 
          WHERE d.id_cliente = ? 
          ORDER BY d.id_direccion DESC";

/*
--------------------------------------------------------------
PREPARAR Y EJECUTAR CONSULTA
--------------------------------------------------------------
Se utiliza una consulta preparada para mayor seguridad
y evitar inyección SQL.
*/
$stmt = $conexion->prepare($query);
$stmt->bind_param("i", $id_cliente);
$stmt->execute();

// Obtener resultados de la consulta
$result = $stmt->get_result();

// Arreglo donde se almacenarán las direcciones
$direcciones = [];

/*
--------------------------------------------------------------
RECORRER RESULTADOS
--------------------------------------------------------------
Se recorren los registros obtenidos y se agregan
al arreglo de direcciones.
*/
while ($row = $result->fetch_assoc()) {
    $direcciones[] = $row;
}

// Cerrar la consulta preparada
$stmt->close();

/*
--------------------------------------------------------------
RESPUESTA JSON
--------------------------------------------------------------
Se devuelven todas las direcciones del cliente junto
con el indicador de éxito.
*/
echo json_encode([
    'success' => true,
    'direcciones' => $direcciones
]); 