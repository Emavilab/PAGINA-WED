<?php
/*
=====================================================================
API PARA OBTENER DATOS DEL USUARIO AUTENTICADO
=====================================================================

DESCRIPCIÓN:
Este script funciona como un endpoint que permite verificar si un
usuario tiene una sesión activa en el sistema y, en caso afirmativo,
devuelve sus datos en formato JSON.

FUNCIONALIDAD:
1. Incluye el sistema de sesiones del proyecto.
2. Verifica si el usuario está autenticado.
3. Si no está autenticado, devuelve una respuesta indicando que no
   existe sesión activa.
4. Si está autenticado, obtiene los datos del usuario.
5. Devuelve la información del usuario en formato JSON.

ARCHIVO UTILIZADO:
- sesiones.php → Contiene funciones para manejar autenticación,
  sesión del usuario y obtención de datos del mismo.

DATOS DEVUELTOS:
- id_usuario
- nombre
- correo
- id_rol
- nombre_rol

RESPUESTA CUANDO NO HAY SESIÓN:
{
  "autenticado": false,
  "usuario": null
}

RESPUESTA CUANDO EL USUARIO ESTÁ AUTENTICADO:
{
  "autenticado": true,
  "usuario": {
      "id_usuario": 1,
      "nombre": "Juan",
      "correo": "correo@ejemplo.com",
      "id_rol": 1,
      "nombre_rol": "Administrador"
  }
}

Este endpoint suele utilizarse en el frontend para:
- Verificar si el usuario tiene sesión activa
- Mostrar datos del usuario en el sistema
- Controlar accesos o permisos según el rol

=====================================================================
*/

/*
-------------------------------------------------------------
INCLUIR SISTEMA DE SESIONES
-------------------------------------------------------------
Se carga el archivo encargado de manejar la autenticación
y las funciones relacionadas con la sesión del usuario.
*/
require_once '../core/sesiones.php';

/*
-------------------------------------------------------------
DEFINIR TIPO DE RESPUESTA
-------------------------------------------------------------
Se indica que la respuesta del servidor será en formato JSON.
*/
header('Content-Type: application/json');

/*
-------------------------------------------------------------
VERIFICAR SI EL USUARIO ESTÁ AUTENTICADO
-------------------------------------------------------------
Si no existe una sesión activa, se devuelve una respuesta
indicando que el usuario no está autenticado.
*/
if (!usuarioAutenticado()) {
    echo json_encode([
        'autenticado' => false,
        'usuario' => null
    ]);
    exit();
}

/*
-------------------------------------------------------------
OBTENER DATOS DEL USUARIO
-------------------------------------------------------------
Se obtiene la información del usuario autenticado mediante
la función obtenerDatosUsuario().
*/
$usuario = obtenerDatosUsuario();

/*
-------------------------------------------------------------
RESPUESTA CON DATOS DEL USUARIO
-------------------------------------------------------------
Se devuelven los datos básicos almacenados en la sesión,
junto con el nombre del rol obtenido del sistema.
*/
echo json_encode([
    'autenticado' => true,
    'usuario' => [
        'id_usuario' => $_SESSION['id_usuario'] ?? null,
        'nombre' => $_SESSION['nombre'] ?? null,
        'correo' => $_SESSION['correo'] ?? null,
        'id_rol' => $_SESSION['id_rol'] ?? null,
        'nombre_rol' => $usuario['nombre_rol'] ?? null
    ]
]);

?> 