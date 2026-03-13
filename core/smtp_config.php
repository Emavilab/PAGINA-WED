<?php
/**
 * Configuración SMTP para envío de correos
 * Usa PHPMailer con Gmail SMTP
 * 
 * IMPORTANTE: Debes generar una "Contraseña de aplicación" en tu cuenta Google:
 * 1. Ve a https://myaccount.google.com/security
 * 2. Activa la verificación en dos pasos si no la tienes
 * 3. Ve a "Contraseñas de aplicaciones" (o busca "App passwords")
 * 4. Genera una nueva contraseña para "Correo" y "Otro (nombre personalizado)"
 * 5. Copia la contraseña de 16 caracteres y pégala abajo en SMTP_PASSWORD
 */

// ====== CONFIGURACIÓN SMTP ======
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'wedpaginawed@gmail.com');
define('SMTP_PASSWORD', 'sijpsgcocjdneuxh');  // Contraseña de aplicación de Google
define('SMTP_FROM_EMAIL', 'wedpaginawed@gmail.com');
define('SMTP_FROM_NAME', 'ControlPlus - Soporte');

/**
 * Enviar correo usando PHPMailer + Gmail SMTP
 * 
 * @param string $destinatario Correo del destinatario
 * @param string $asunto Asunto del correo
 * @param string $cuerpoHtml Cuerpo del correo en HTML
 * @param string $cuerpoTexto Cuerpo alternativo en texto plano (opcional)
 * @return array ['exito' => bool, 'mensaje' => string]
 */
function enviarCorreo($destinatario, $asunto, $cuerpoHtml, $cuerpoTexto = '') {
    // Verificar que la contraseña SMTP esté configurada
    if (empty(SMTP_PASSWORD)) {
        return [
            'exito' => false, 
            'mensaje' => 'La contraseña SMTP no está configurada. Revisa core/smtp_config.php'
        ];
    }

    // Cargar PHPMailer
    require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        // Remitente
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        
        // Destinatario
        $mail->addAddress($destinatario);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $cuerpoHtml;
        $mail->AltBody = $cuerpoTexto ?: strip_tags($cuerpoHtml);

        $mail->send();
        
        return ['exito' => true, 'mensaje' => 'Correo enviado correctamente'];

    } catch (PHPMailer\PHPMailer\Exception $e) {
        return [
            'exito' => false, 
            'mensaje' => 'Error al enviar correo: ' . $mail->ErrorInfo
        ];
    }
}

/**
 * Genera una plantilla HTML bonita para el correo de respuesta
 * 
 * @param string $nombreCliente Nombre del cliente
 * @param string $asuntoOriginal Asunto del mensaje original
 * @param string $mensajeOriginal Mensaje original del cliente
 * @param string $respuesta Respuesta del administrador
 * @return string HTML del correo
 */
function plantillaRespuestaContacto($nombreCliente, $asuntoOriginal, $mensajeOriginal, $respuesta) {
    $respuestaHtml = nl2br(htmlspecialchars($respuesta));
    $mensajeHtml = nl2br(htmlspecialchars($mensajeOriginal));
    $nombreHtml = htmlspecialchars($nombreCliente);
    $asuntoHtml = htmlspecialchars($asuntoOriginal);

    return '
    <!DOCTYPE html>
    <html lang="es">
    <head><meta charset="UTF-8"></head>
    <body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:40px 0;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
                        <!-- Header -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #137fec, #0d66c2); padding:30px 40px; text-align:center;">
                                <h1 style="color:#ffffff; margin:0; font-size:24px; font-weight:700;">ControlPlus</h1>
                                <p style="color:rgba(255,255,255,0.85); margin:8px 0 0; font-size:14px;">Respuesta a tu consulta</p>
                            </td>
                        </tr>
                        <!-- Body -->
                        <tr>
                            <td style="padding:40px;">
                                <p style="color:#333; font-size:16px; margin:0 0 20px;">
                                    Hola <strong>' . $nombreHtml . '</strong>,
                                </p>
                                <p style="color:#555; font-size:15px; line-height:1.6; margin:0 0 25px;">
                                    Hemos revisado tu mensaje y aquí te dejamos nuestra respuesta:
                                </p>

                                <!-- Respuesta -->
                                <div style="background-color:#f0f9ff; border-left:4px solid #137fec; padding:20px; border-radius:0 8px 8px 0; margin:0 0 25px;">
                                    <p style="color:#137fec; font-size:12px; font-weight:700; text-transform:uppercase; margin:0 0 8px;">Nuestra respuesta</p>
                                    <p style="color:#333; font-size:15px; line-height:1.6; margin:0;">' . $respuestaHtml . '</p>
                                </div>

                                <!-- Mensaje original -->
                                <div style="background-color:#f9fafb; border:1px solid #e5e7eb; padding:20px; border-radius:8px; margin:0 0 25px;">
                                    <p style="color:#9ca3af; font-size:12px; font-weight:700; text-transform:uppercase; margin:0 0 4px;">Tu mensaje original</p>
                                    <p style="color:#6b7280; font-size:13px; font-weight:600; margin:0 0 8px;">Asunto: ' . $asuntoHtml . '</p>
                                    <p style="color:#6b7280; font-size:13px; line-height:1.5; margin:0;">' . $mensajeHtml . '</p>
                                </div>

                                <p style="color:#555; font-size:14px; line-height:1.6; margin:0;">
                                    Si tienes más preguntas, no dudes en responder a este correo o contactarnos nuevamente desde nuestro sitio web.
                                </p>
                            </td>
                        </tr>
                        <!-- Footer -->
                        <tr>
                            <td style="background-color:#f9fafb; padding:25px 40px; text-align:center; border-top:1px solid #e5e7eb;">
                                <p style="color:#9ca3af; font-size:12px; margin:0;">
                                    &copy; ' . date('Y') . ' ControlPlus. Todos los derechos reservados.
                                </p>
                                <p style="color:#9ca3af; font-size:12px; margin:5px 0 0;">
                                    Este correo fue enviado en respuesta a tu consulta de contacto.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';
}

/**
 * Plantilla HTML para notificación de cambio de estado de pedido
 *
 * @param string $nombreCliente Nombre del cliente
 * @param int $id_pedido Número de pedido
 * @param string $estado Estado actual del pedido (confirmado, enviado, entregado, cancelado)
 * @param string $mensajeInformativo Mensaje según el estado
 * @return string HTML del correo
 */
function plantillaCorreoEstadoPedido($nombreCliente, $id_pedido, $estado, $mensajeInformativo) {
    $nombreHtml = htmlspecialchars($nombreCliente);
    $idPedidoHtml = (int) $id_pedido;
    $estadoHtml = htmlspecialchars(ucfirst($estado));
    $mensajeHtml = nl2br(htmlspecialchars($mensajeInformativo));

    return '
    <!DOCTYPE html>
    <html lang="es">
    <head><meta charset="UTF-8"></head>
    <body style="margin:0; padding:0; background-color:#f4f6f8; font-family: Arial, Helvetica, sans-serif;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f8; padding:40px 0;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.07);">
                        <tr>
                            <td style="background: linear-gradient(135deg, #137fec, #0d66c2); padding:30px 40px; text-align:center;">
                                <h1 style="color:#ffffff; margin:0; font-size:24px; font-weight:700;">ControlPlus</h1>
                                <p style="color:rgba(255,255,255,0.85); margin:8px 0 0; font-size:14px;">Actualización de tu pedido</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:40px;">
                                <p style="color:#333; font-size:16px; margin:0 0 20px;">
                                    Hola <strong>' . $nombreHtml . '</strong>,
                                </p>
                                <p style="color:#555; font-size:15px; line-height:1.6; margin:0 0 20px;">
                                    ' . $mensajeHtml . '
                                </p>
                                <div style="background-color:#f0f9ff; border-left:4px solid #137fec; padding:20px; border-radius:0 8px 8px 0; margin:0 0 25px;">
                                    <p style="color:#137fec; font-size:12px; font-weight:700; text-transform:uppercase; margin:0 0 4px;">Pedido</p>
                                    <p style="color:#333; font-size:18px; font-weight:700; margin:0;">#' . $idPedidoHtml . ' &ndash; ' . $estadoHtml . '</p>
                                </div>
                                <p style="color:#555; font-size:14px; line-height:1.6; margin:0;">
                                    Si tienes dudas, contáctanos desde nuestro sitio web.
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <td style="background-color:#f9fafb; padding:25px 40px; text-align:center; border-top:1px solid #e5e7eb;">
                                <p style="color:#9ca3af; font-size:12px; margin:0;">
                                    &copy; ' . date('Y') . ' ControlPlus. Todos los derechos reservados.
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';
}

/**
 * Notifica por correo al cliente un cambio de estado de pedido.
 * Reutilizable desde admin (cambiar_estado.php) y desde cliente (api_cancelar_pedido.php).
 *
 * @param int $id_pedido ID del pedido
 * @param string $estado_nuevo Estado actual del pedido (confirmado, enviado, entregado, cancelado)
 * @param string $nombre_cliente Nombre del cliente
 * @param string $correo_cliente Correo del cliente
 */
function notificarCambioEstadoPedido($id_pedido, $estado_nuevo, $nombre_cliente, $correo_cliente) {
    $estadosNotificables = ['confirmado', 'enviado', 'entregado', 'cancelado'];
    if (!in_array($estado_nuevo, $estadosNotificables)) {
        return;
    }
    if (empty($correo_cliente) || !filter_var($correo_cliente, FILTER_VALIDATE_EMAIL)) {
        return;
    }

    $mensajes = [
        'confirmado' => "Tu pedido #{$id_pedido} ha sido confirmado y pronto será preparado.",
        'enviado'    => "Tu pedido #{$id_pedido} ha sido enviado y está en camino.",
        'entregado'  => "Tu pedido #{$id_pedido} ha sido entregado. Gracias por tu compra.",
        'cancelado'  => "Tu pedido #{$id_pedido} ha sido cancelado correctamente. Si tienes alguna duda puedes contactarnos respondiendo a este correo. Gracias."
    ];
    $mensajeInformativo = $mensajes[$estado_nuevo];
    $saludo = "Hola " . ($nombre_cliente !== '' ? $nombre_cliente : 'cliente') . ", ";
    $mensajeCompleto = $saludo . $mensajeInformativo;

    $asunto = "Actualización de tu pedido #{$id_pedido}";
    $cuerpoHtml = plantillaCorreoEstadoPedido($nombre_cliente ?: 'Cliente', $id_pedido, $estado_nuevo, $mensajeCompleto);

    enviarCorreo($correo_cliente, $asunto, $cuerpoHtml);
}
