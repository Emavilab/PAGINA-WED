<?php
/*
========================================================
MODULO: API DE GESTIÓN DE CATEGORÍAS
========================================================
Este archivo funciona como una API para administrar
las categorías del sistema de tienda.

FUNCIONALIDADES PRINCIPALES:
✔ Listar categorías
✔ Obtener una categoría específica
✔ Listar categorías principales (padres)
✔ Crear nuevas categorías o subcategorías
✔ Editar categorías existentes
✔ Eliminar categorías

CARACTERÍSTICAS:
✔ Soporte de jerarquía (categoría principal y subcategorías)
✔ Validación de duplicados
✔ Validación de permisos por rol
✔ Prevención de eliminación si existen productos asociados
✔ Respuestas en formato JSON

TABLAS UTILIZADAS:
- categorias
- productos

ROLES PERMITIDOS:
1 = Administrador
2 = Gestor / Empleado autorizado

RESPUESTA DEL SERVIDOR:
{
  "exito": true | false,
  "mensaje": "...",
  "error": "..."
}

AUTOR: Sistema de Gestión de Tienda
========================================================
*/

// Definir respuesta en formato JSON
header('Content-Type: application/json; charset=utf-8');

// Incluir manejo de sesiones y conexión a base de datos
require_once '../core/sesiones.php';
require_once '../core/conexion.php';
require_once '../core/csrf.php';

validarCSRFMiddleware();


/*
========================================================
VERIFICAR AUTENTICACIÓN Y PERMISOS
========================================================
Solo usuarios autenticados con rol administrador
o gestor pueden acceder a esta API.
*/
if (!usuarioAutenticado() || ($_SESSION['id_rol'] != 1 && $_SESSION['id_rol'] != 2)) {

    echo json_encode([
        'exito' => false,
        'error' => 'No autorizado'
    ]);

    exit();
}


/*
========================================================
VERIFICAR CONEXIÓN A BASE DE DATOS
========================================================
Se valida que la conexión a la base de datos esté
disponible antes de ejecutar cualquier operación.
*/
if (!isset($conexion)) {

    echo json_encode([
        'exito' => false,
        'error' => 'Conexión a BD no disponible'
    ]);

    exit();
}


// Obtener método HTTP de la solicitud
$metodo = $_SERVER['REQUEST_METHOD'];


/*
########################################################
######################## GET ############################
########################################################
Operaciones de lectura de información.
*/
if ($metodo === 'GET') {

    // Acción solicitada (listar por defecto)
    $accion = $_GET['accion'] ?? 'listar';


    /*
    ====================================================
    LISTAR TODAS LAS CATEGORÍAS
    ====================================================
    Permite filtrar por estado o por búsqueda.
    También devuelve conteos y estructura jerárquica.
    */
    if ($accion === 'listar') {

        $busqueda = $_GET['busqueda'] ?? '';
        $estado = $_GET['estado'] ?? '';
        $estadosPermitidos = ['', 'todos', 'activo', 'inactivo'];

        if (!in_array($estado, $estadosPermitidos, true)) {
            echo json_encode([
                'exito' => false,
                'error' => 'Estado de filtro inválido'
            ]);
            exit();
        }

        // Consulta principal de categorías
        $sql = "SELECT c.*, p.nombre AS nombre_padre,
                (SELECT COUNT(*) FROM categorias h WHERE h.id_padre = c.id_categoria) AS total_hijos,
                (SELECT COUNT(*) FROM productos pr WHERE pr.id_categoria = c.id_categoria) AS total_productos
                FROM categorias c
                LEFT JOIN categorias p ON c.id_padre = p.id_categoria
                WHERE 1=1";

        $params = [];
        $types = '';

        // Filtro por estado
        if (!empty($estado) && $estado !== 'todos') {
            $sql .= " AND c.estado = ?";
            $params[] = $estado;
            $types .= 's';
        }

        // Filtro por búsqueda
        if (!empty($busqueda)) {
            $sql .= " AND (c.nombre LIKE ? OR c.descripcion LIKE ?)";
            $busquedaLike = "%$busqueda%";
            $params[] = $busquedaLike;
            $params[] = $busquedaLike;
            $types .= 'ss';
        }

        // Orden jerárquico
        $sql .= " ORDER BY COALESCE(c.id_padre, c.id_categoria), c.id_padre IS NOT NULL, c.nombre ASC";

        $stmt = $conexion->prepare($sql);

        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();

        // Guardar categorías en arreglo
        $categorias = [];

        while ($fila = $resultado->fetch_assoc()) {

            $fila['total_hijos'] = (int)$fila['total_hijos'];
            $fila['total_productos'] = (int)$fila['total_productos'];

            $categorias[] = $fila;
        }

        // Construir árbol jerárquico
        $arbol = construirArbol($categorias);


        /*
        ====================================================
        OBTENER CONTEOS GENERALES
        ====================================================
        Total de categorías, principales, subcategorías
        y estado activo/inactivo.
        */
        $conteos = ['total' => 0, 'principales' => 0, 'subcategorias' => 0, 'activo' => 0, 'inactivo' => 0];

        $resConteo = $conexion->query("SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN id_padre IS NULL THEN 1 ELSE 0 END) AS principales,
            SUM(CASE WHEN id_padre IS NOT NULL THEN 1 ELSE 0 END) AS subcategorias,
            SUM(CASE WHEN estado = 'activo' THEN 1 ELSE 0 END) AS activo,
            SUM(CASE WHEN estado = 'inactivo' THEN 1 ELSE 0 END) AS inactivo
            FROM categorias");

        if ($fila = $resConteo->fetch_assoc()) {

            $conteos = [
                'total' => (int)$fila['total'],
                'principales' => (int)$fila['principales'],
                'subcategorias' => (int)$fila['subcategorias'],
                'activo' => (int)$fila['activo'],
                'inactivo' => (int)$fila['inactivo']
            ];
        }

        // Respuesta final
        echo json_encode([
            'exito' => true,
            'categorias' => $categorias,
            'arbol' => $arbol,
            'conteos' => $conteos
        ]);

        exit();
    }


    /*
    ====================================================
    OBTENER UNA CATEGORÍA ESPECÍFICA
    ====================================================
    Devuelve los datos completos de una categoría.
    */
    if ($accion === 'obtener') {

        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {

            echo json_encode([
                'exito' => false,
                'error' => 'ID inválido'
            ]);

            exit();
        }

        $stmt = $conexion->prepare("SELECT c.*, p.nombre AS nombre_padre 
            FROM categorias c 
            LEFT JOIN categorias p ON c.id_padre = p.id_categoria 
            WHERE c.id_categoria = ?");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $categoria = $stmt->get_result()->fetch_assoc();

        if ($categoria) {

            echo json_encode([
                'exito' => true,
                'categoria' => $categoria
            ]);

        } else {

            echo json_encode([
                'exito' => false,
                'error' => 'Categoría no encontrada'
            ]);
        }

        exit();
    }


    /*
    ====================================================
    LISTAR CATEGORÍAS PADRE
    ====================================================
    Se usa normalmente para llenar un SELECT
    cuando se crea una subcategoría.
    */
    if ($accion === 'listar_padres') {

        $excluir = (int)($_GET['excluir'] ?? 0);

        if ($excluir > 0) {
            $stmtPadres = $conexion->prepare(
                "SELECT id_categoria, nombre, icono
                 FROM categorias
                 WHERE id_padre IS NULL
                 AND estado = 'activo'
                 AND id_categoria != ?
                 ORDER BY nombre ASC"
            );
            $stmtPadres->bind_param("i", $excluir);
        } else {
            $stmtPadres = $conexion->prepare(
                "SELECT id_categoria, nombre, icono
                 FROM categorias
                 WHERE id_padre IS NULL
                 AND estado = 'activo'
                 ORDER BY nombre ASC"
            );
        }

        $stmtPadres->execute();
        $resultado = $stmtPadres->get_result();

        $padres = [];

        while ($fila = $resultado->fetch_assoc()) {
            $padres[] = $fila;
        }

        echo json_encode([
            'exito' => true,
            'padres' => $padres
        ]);

        exit();
    }
}


/*
########################################################
######################## POST ###########################
########################################################
Operaciones de escritura: crear, editar o eliminar.
*/
if ($metodo === 'POST') {

    $accion = $_POST['accion'] ?? '';

    /*
    ====================================================
    CREAR CATEGORÍA
    ====================================================
    Permite crear categorías principales o subcategorías.
    */
    if ($accion === 'crear') {

        $nombre = trim($_POST['nombre'] ?? '');
        $icono = trim($_POST['icono'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado = $_POST['estado'] ?? 'activo';
        if (!in_array($estado, ['activo', 'inactivo'], true)) {
            echo json_encode([
                'exito' => false,
                'error' => 'Estado inválido'
            ]);
            exit();
        }

        $id_padre = !empty($_POST['id_padre']) ? (int)$_POST['id_padre'] : null;

        $tasa_impuesto = isset($_POST['tasa_impuesto']) && $_POST['tasa_impuesto'] !== ''
            ? (float)$_POST['tasa_impuesto']
            : null;

        // Validación de nombre
        if (empty($nombre)) {

            echo json_encode([
                'exito' => false,
                'error' => 'El nombre es obligatorio'
            ]);

            exit();
        }

        /*
        ------------------------------------------------
        VALIDAR NOMBRE DUPLICADO
        ------------------------------------------------
        No se permite repetir el mismo nombre dentro
        del mismo nivel jerárquico.
        */
        if ($id_padre) {

            $stmtCheck = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ? AND id_padre = ?");
            $stmtCheck->bind_param("si", $nombre, $id_padre);

        } else {

            $stmtCheck = $conexion->prepare("SELECT id_categoria FROM categorias WHERE nombre = ? AND id_padre IS NULL");
            $stmtCheck->bind_param("s", $nombre);
        }

        $stmtCheck->execute();

        if ($stmtCheck->get_result()->num_rows > 0) {

            echo json_encode([
                'exito' => false,
                'error' => 'Ya existe una categoría con ese nombre en este nivel'
            ]);

            exit();
        }

        /*
        ------------------------------------------------
        INSERTAR NUEVA CATEGORÍA
        ------------------------------------------------
        */
        if ($id_padre) {
            $stmt = $conexion->prepare("INSERT INTO categorias (nombre, id_padre, icono, descripcion, estado, tasa_impuesto) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sisssd", $nombre, $id_padre, $icono, $descripcion, $estado, $tasa_impuesto);
        } else {
            $stmt = $conexion->prepare("INSERT INTO categorias (nombre, id_padre, icono, descripcion, estado, tasa_impuesto) VALUES (?, NULL, ?, ?, ?, ?)");
            $stmt->bind_param("sssd", $nombre, $icono, $descripcion, $estado, $tasa_impuesto);
        }

        if ($stmt->execute()) {

            $tipo = $id_padre ? 'Subcategoría' : 'Categoría principal';

            echo json_encode([
                'exito' => true,
                'mensaje' => "$tipo creada correctamente",
                'id' => $stmt->insert_id
            ]);

        } else {

            echo json_encode([
                'exito' => false,
                'error' => 'Error al crear: ' . $stmt->error
            ]);
        }

        exit();
    }

    /*
    ====================================================
    ELIMINAR CATEGORÍA
    ====================================================
    Antes de eliminar se valida que no tenga productos.
    */
    if ($accion === 'eliminar') {

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {

            echo json_encode([
                'exito' => false,
                'error' => 'ID inválido'
            ]);

            exit();
        }

        $stmtProd = $conexion->prepare("SELECT COUNT(*) AS total FROM productos WHERE id_categoria = ?");
        $stmtProd->bind_param("i", $id);
        $stmtProd->execute();
        $totalProd = (int)($stmtProd->get_result()->fetch_assoc()['total'] ?? 0);

        if ($totalProd > 0) {
            echo json_encode([
                'exito' => false,
                'error' => 'No se puede eliminar: existen productos asociados a esta categoría'
            ]);
            exit();
        }

        $stmtHijos = $conexion->prepare("SELECT COUNT(*) AS total FROM categorias WHERE id_padre = ?");
        $stmtHijos->bind_param("i", $id);
        $stmtHijos->execute();
        $totalHijos = (int)($stmtHijos->get_result()->fetch_assoc()['total'] ?? 0);

        if ($totalHijos > 0) {
            echo json_encode([
                'exito' => false,
                'error' => 'No se puede eliminar: existen subcategorías asociadas'
            ]);
            exit();
        }

        $stmt = $conexion->prepare("DELETE FROM categorias WHERE id_categoria = ?");

        $stmt->bind_param("i", $id);

        if ($stmt->execute() && $stmt->affected_rows > 0) {

            echo json_encode([
                'exito' => true,
                'mensaje' => 'Categoría eliminada correctamente'
            ]);

        } else {

            echo json_encode([
                'exito' => false,
                'error' => 'No se pudo eliminar'
            ]);
        }

        exit();
    }

    echo json_encode([
        'exito' => false,
        'error' => 'Acción no reconocida'
    ]);

    exit();
}


/*
========================================================
FUNCIÓN: CONSTRUIR ÁRBOL DE CATEGORÍAS
========================================================
Convierte la lista de categorías en una estructura
jerárquica donde cada categoría padre contiene sus
subcategorías.
*/
function construirArbol($categorias) {

    $arbol = [];
    $hijos = [];

    foreach ($categorias as $cat) {

        if ($cat['id_padre'] === null) {

            $cat['subcategorias'] = [];
            $arbol[$cat['id_categoria']] = $cat;

        } else {

            $hijos[] = $cat;
        }
    }

    foreach ($hijos as $hijo) {

        if (isset($arbol[$hijo['id_padre']])) {

            $arbol[$hijo['id_padre']]['subcategorias'][] = $hijo;
        }
    }

    return array_values($arbol);
} 
