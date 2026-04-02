<?php

/*
=========================================================
API: OBTENER INFORMACIÓN DE UN CLIENTE
=========================================================

DESCRIPCIÓN:
Este script permite obtener la información básica de un
cliente específico a partir de su ID de usuario.

El sistema consulta dos tablas relacionadas:

✔ clientes
✔ usuarios

La relación entre ambas tablas se realiza mediante
el campo id_usuario.

DATOS QUE RETORNA:
- id_usuario
- nombre del cliente
- estado del cliente
- correo del usuario

La respuesta se devuelve en formato JSON para ser
consumida por aplicaciones frontend (JavaScript,
formularios dinámicos, panel administrativo, etc.).

EJEMPLO DE RESPUESTA:
{
  "id_usuario": 5,
  "nombre": "Juan Pérez",
  "estado": "activo",
  "correo": "juan@email.com"
}

REQUISITOS:
- Conexión activa a la base de datos
- Archivo: ../core/conexion.php
- Parámetro GET requerido:
  id = ID del usuario/cliente

AUTOR: Sistema de Gestión
=========================================================
*/

/*
---------------------------------------------------------
INCLUIR CONEXIÓN A BASE DE DATOS
---------------------------------------------------------
Se carga el archivo que contiene la conexión
a la base de datos mediante mysqli.
*/
require_once '../core/conexion.php';

/*
---------------------------------------------------------
CONFIGURAR RESPUESTA COMO JSON
---------------------------------------------------------
Se indica al navegador que la respuesta del
servidor será en formato JSON.
*/
header('Content-Type: application/json');

/*
---------------------------------------------------------
OBTENER ID DEL CLIENTE DESDE LA URL
---------------------------------------------------------
El ID se recibe mediante el método GET.
Ejemplo:
api_cliente.php?id=5
*/
$id = $_GET['id'];

/*
---------------------------------------------------------
CONSULTA PREPARADA A LA BASE DE DATOS
---------------------------------------------------------
Se utiliza una consulta preparada para mejorar la
seguridad y evitar ataques de inyección SQL.

Se realiza un INNER JOIN entre:
- tabla clientes
- tabla usuarios
*/
$stmt = $conexion->prepare("
    SELECT 
        c.id_cliente,
        u.id_usuario,
        c.nombre,
        c.estado,
        u.correo
    FROM clientes c
    INNER JOIN usuarios u ON c.id_usuario = u.id_usuario
    WHERE c.id_cliente = ? OR u.id_usuario = ?
");

/*
---------------------------------------------------------
ASOCIAR PARÁMETROS A LA CONSULTA
---------------------------------------------------------
*/
$stmt->bind_param("ii", $id, $id);

/*
---------------------------------------------------------
EJECUTAR CONSULTA
---------------------------------------------------------
*/
$stmt->execute();

/*
---------------------------------------------------------
OBTENER RESULTADO DE LA CONSULTA
---------------------------------------------------------
*/
$resultado = $stmt->get_result();

/*
---------------------------------------------------------
DEVOLVER DATOS EN FORMATO JSON
---------------------------------------------------------
Se envía el primer registro encontrado como
respuesta al cliente.
*/
echo json_encode($resultado->fetch_assoc());