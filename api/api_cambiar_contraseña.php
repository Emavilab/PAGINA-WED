<?php
/*
========================================================
MODULO: CAMBIO DE CONTRASEÑA DE USUARIO
========================================================
Este archivo funciona como una API que permite a un
usuario autenticado cambiar su contraseña dentro del
sistema.

FUNCIONALIDADES:
✔ Verificar que el usuario esté autenticado
✔ Validar que la solicitud sea mediante método POST
✔ Validar datos enviados desde el formulario
✔ Verificar que la contraseña actual sea correcta
✔ Encriptar la nueva contraseña
✔ Actualizar la contraseña en la base de datos
✔ Retornar respuesta en formato JSON

TABLA UTILIZADA:
- usuarios

RESPUESTA DEL SERVIDOR:

Éxito:
{
  "exito": true,
  "mensaje": "Contraseña actualizada correctamente"
}

Error:
{
  "exito": false,
  "mensaje": "Descripción del error"
}

USO:
Este archivo es llamado generalmente mediante
peticiones AJAX desde el formulario de cambio
de contraseña del usuario.

AUTOR: Sistema de Gestión de Usuarios
========================================================
*/

/**
 * API para cambiar contraseña del usuario
 */

require_once '../core/sesiones.php'; // Archivo que gestiona las sesiones del sistema
require_once '../core/csrf.php';

validarCSRFMiddleware();

// Establecer que la respuesta será en formato JSON
header('Content-Type: application/json');

/*
========================================================
VERIFICAR AUTENTICACIÓN DEL USUARIO
========================================================
Se valida que exista una sesión activa del usuario.
Si no está autenticado se devuelve error 401.
*/
if (!usuarioAutenticado()) {
    http_response_code(401);

    echo json_encode([
        'exito' => false,
        'mensaje' => 'No autorizado'
    ]);

    exit();
}

/*
========================================================
VERIFICAR MÉTODO DE PETICIÓN
========================================================
Solo se permite el método POST para mayor seguridad.
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);

    echo json_encode([
        'exito' => false,
        'mensaje' => 'Método no permitido'
    ]);

    exit();
}

// Acceder a la conexión global de la base de datos
global $conexion;


/*
========================================================
OBTENER DATOS DEL FORMULARIO
========================================================
Se obtienen los valores enviados desde el formulario
de cambio de contraseña.
*/
$id_usuario = $_SESSION['id_usuario'];

$contraseña_actual = isset($_POST['contraseña_actual']) ? $_POST['contraseña_actual'] : '';
$contraseña_nueva = isset($_POST['contraseña_nueva']) ? $_POST['contraseña_nueva'] : '';
$confirmar_contraseña = isset($_POST['confirmar_contraseña']) ? $_POST['confirmar_contraseña'] : '';


/*
========================================================
VALIDACIONES DE LOS DATOS
========================================================
Se valida que los campos requeridos estén completos
y que la nueva contraseña cumpla con los requisitos
de seguridad establecidos.
*/
$errores = [];

// Validar contraseña actual
if (empty($contraseña_actual)) {
    $errores[] = 'La contraseña actual es requerida';
}

// Validar nueva contraseña
if (empty($contraseña_nueva)) {

    $errores[] = 'La nueva contraseña es requerida';

} elseif (strlen($contraseña_nueva) < 6) {

    $errores[] = 'La nueva contraseña debe tener al menos 6 caracteres';

} elseif (strlen($contraseña_nueva) > 50) {

    $errores[] = 'La contraseña es demasiado larga';
}

// Validar confirmación de contraseña
if ($contraseña_nueva !== $confirmar_contraseña) {
    $errores[] = 'Las contraseñas no coinciden';
}


/*
========================================================
SI EXISTEN ERRORES DE VALIDACIÓN
========================================================
Se devuelve una respuesta con los errores encontrados
para que el usuario pueda corregirlos.
*/
if (!empty($errores)) {

    echo json_encode([
        'exito' => false,
        'mensaje' => 'Por favor corrige los siguientes errores:',
        'errores' => $errores
    ]);

    exit();
}


/*
========================================================
OBTENER CONTRASEÑA ACTUAL DEL USUARIO
========================================================
Se consulta la base de datos para obtener la contraseña
encriptada almacenada del usuario.
*/
$query = "SELECT contraseña FROM usuarios WHERE id_usuario = ?";

$stmt = $conexion->prepare($query);

$stmt->bind_param("i", $id_usuario);

$stmt->execute();

$result = $stmt->get_result();

$usuario = $result->fetch_assoc();

$stmt->close();


/*
========================================================
VERIFICAR CONTRASEÑA ACTUAL
========================================================
Se compara la contraseña ingresada por el usuario con
la contraseña almacenada en la base de datos utilizando
password_verify().
*/
if (!password_verify($contraseña_actual, $usuario['contraseña'])) {

    echo json_encode([
        'exito' => false,
        'mensaje' => 'La contraseña actual es incorrecta'
    ]);

    exit();
}


/*
========================================================
ENCRIPTAR NUEVA CONTRASEÑA
========================================================
Se utiliza password_hash con el algoritmo BCRYPT para
almacenar la contraseña de forma segura.
*/
$contraseña_encriptada = password_hash($contraseña_nueva, PASSWORD_BCRYPT);


/*
========================================================
ACTUALIZAR CONTRASEÑA EN LA BASE DE DATOS
========================================================
Se guarda la nueva contraseña encriptada para el usuario.
*/
$query = "UPDATE usuarios SET contraseña = ? WHERE id_usuario = ?";

$stmt = $conexion->prepare($query);

$stmt->bind_param("si", $contraseña_encriptada, $id_usuario);


/*
========================================================
RESPUESTA DEL SISTEMA
========================================================
Se envía una respuesta indicando si la actualización
de la contraseña fue exitosa o si ocurrió algún error.
*/
if ($stmt->execute()) {

    echo json_encode([
        'exito' => true,
        'mensaje' => 'Contraseña actualizada correctamente'
    ]);

} else {

    echo json_encode([
        'exito' => false,
        'mensaje' => 'Error al actualizar la contraseña'
    ]);
}

// Cerrar la consulta preparada
$stmt->close();

?> 