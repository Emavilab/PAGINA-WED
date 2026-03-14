<?php
/*
====================================================================
API PARA OBTENER LA CONFIGURACIÓN GENERAL DEL SISTEMA
====================================================================

DESCRIPCIÓN:
Este script consulta la configuración principal del sistema
almacenada en la base de datos y devuelve la información
en formato JSON.

La configuración suele incluir datos como:
✔ Nombre del sitio
✔ Información de contacto
✔ Redes sociales
✔ Ajustes generales de la tienda

FUNCIONALIDAD:
1. Incluye el archivo de conexión a la base de datos.
2. Define que la respuesta será en formato JSON.
3. Consulta el registro de configuración principal.
4. Decodifica el campo de redes sociales almacenado en JSON.
5. Devuelve los datos de configuración al cliente.

TABLA UTILIZADA:
- configuracion

CAMPOS IMPORTANTES:
- id_config
- redes_sociales (almacenado como JSON)

RESPUESTA JSON EXITOSA:
{
  "success": true,
  "data": { ...configuración... }
}

RESPUESTA JSON EN CASO DE ERROR:
{
  "success": false,
  "message": "No se encontró configuración"
}

USO EN EL SISTEMA:
Este endpoint suele utilizarse para:
✔ Mostrar información del sitio en el frontend
✔ Cargar redes sociales
✔ Mostrar datos de contacto
✔ Configuración general de la tienda

====================================================================
*/

// Incluir archivo de conexión a la base de datos
require_once __DIR__ . '/../core/conexion.php';

// Definir que la respuesta del servidor será en formato JSON
header('Content-Type: application/json; charset=utf-8');

/*
--------------------------------------------------------------
CONSULTA DE CONFIGURACIÓN
--------------------------------------------------------------
Se obtiene el registro principal de configuración del sistema
identificado con id_config = 1.
*/
$res = mysqli_query($conexion, "SELECT * FROM configuracion WHERE id_config = 1");

/*
--------------------------------------------------------------
VERIFICAR SI EXISTE CONFIGURACIÓN
--------------------------------------------------------------
Si se encuentra el registro se procede a procesar los datos.
*/
if ($res && mysqli_num_rows($res) > 0) {

    // Obtener los datos de configuración en un arreglo asociativo
    $config = mysqli_fetch_assoc($res);
    
    /*
    ----------------------------------------------------------
    DECODIFICAR REDES SOCIALES
    ----------------------------------------------------------
    El campo "redes_sociales" se guarda en la base de datos
    en formato JSON, por lo que se convierte a un arreglo PHP.
    Si está vacío se devuelve un arreglo vacío.
    */
    $config['redes'] = !empty($config['redes_sociales']) ? json_decode($config['redes_sociales'], true) : [];
    
    /*
    ----------------------------------------------------------
    RESPUESTA JSON EXITOSA
    ----------------------------------------------------------
    Se devuelven los datos de configuración.
    */
    echo json_encode([
        'success' => true,
        'data' => $config
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

} else {

    /*
    ----------------------------------------------------------
    RESPUESTA JSON SI NO EXISTE CONFIGURACIÓN
    ----------------------------------------------------------
    */
    echo json_encode([
        'success' => false,
        'message' => 'No se encontró configuración'
    ]);
} 