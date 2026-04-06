<?php
/**
 * Módulo de Envío de Correos con PHPMailer Nativo
 * Archivo: includes/enviar_correo.php
 * 
 * Usa PHPMailer directamente desde includes/phpmailer/
 * Sin Composer ni dependencias externas
 */

// Cargar PHPMailer natively
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';
require_once __DIR__ . '/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Envía un correo usando PHPMailer
 */
function enviarCorreo($destinatario, $asunto, $cuerpoHTML, $nombreDestino = '', $adjuntos = []) {
    try {
        $mail = new PHPMailer(true);

        // CONFIGURACIÓN SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fuddocol@gmail.com';
        $mail->Password = 'dfwx cwhc yudp etal';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // CONFIGURACIÓN DEL CORREO
        $mail->setFrom('fuddocol@gmail.com', 'FUDDO Sistema');
        
        if (!empty($nombreDestino)) {
            $mail->addAddress($destinatario, $nombreDestino);
        } else {
            $mail->addAddress($destinatario);
        }

        $mail->addReplyTo('fuddocol@gmail.com', 'Soporte FUDDO');

        // CONTENIDO
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $cuerpoHTML;
        $mail->AltBody = strip_tags($cuerpoHTML);

        // ADJUNTOS
        if (!empty($adjuntos) && is_array($adjuntos)) {
            foreach ($adjuntos as $ruta) {
                if (file_exists($ruta)) {
                    $mail->addAttachment($ruta);
                }
            }
        }

        // ENVIAR
        if ($mail->send()) {
            error_log("[CORREO ENVIADO] A: $destinatario | Asunto: $asunto");
            return [
                'success' => true,
                'message' => 'Correo enviado exitosamente',
                'error' => null
            ];
        } else {
            error_log("[CORREO ERROR] No se pudo enviar: " . $mail->ErrorInfo);
            return [
                'success' => false,
                'message' => 'No se pudo enviar el correo',
                'error' => $mail->ErrorInfo
            ];
        }

    } catch (Exception $e) {
        error_log("[CORREO EXCEPCIÓN] " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error en envío de correo',
            'error' => $e->getMessage()
        ];
    } catch (Throwable $e) {
        error_log("[CORREO FATAL] " . $e->getMessage());
        return [
            'success' => false,
            'message' => 'Error fatal en envío de correo',
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Genera HTML profesional para correos
 */
function generarHTMLCorreo($titulo, $contenido, $boton_texto = '', $boton_url = '') {
    $botonHTML = '';
    if (!empty($boton_texto) && !empty($boton_url)) {
        $botonHTML = '<p style="margin-top: 30px; text-align: center;">
                        <a href="' . htmlspecialchars($boton_url) . '" style="background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600;">
                            ' . htmlspecialchars($boton_texto) . '
                        </a>
                    </p>';
    }

    return "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f5f5f5; }
            .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); overflow: hidden; }
            .header { background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); padding: 40px 20px; text-align: center; color: white; }
            .header h1 { margin: 0; font-size: 24px; font-weight: 700; }
            .content { padding: 30px; text-align: center; color: #333; line-height: 1.6; }
            .footer { background: #f9f9f9; padding: 20px; text-align: center; border-top: 1px solid #e0e0e0; color: #999; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'><h1>FUDDO</h1></div>
            <div class='content'>
                <h2 style='color: #27ae60; margin-bottom: 20px;'>$titulo</h2>
                <p>$contenido</p>
                $botonHTML
            </div>
            <div class='footer'>
                <p style='margin: 0;'>© 2026 FUDDO - Sistema Profesional de Gestión de Restaurantes</p>
                <p style='margin: 5px 0 0 0;'><a href='https://fuddo.co' style='color: #27ae60; text-decoration: none;'>www.fuddo.co</a></p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Genera HTML de bienvenida para nuevos registros en el demo
 * Ahora RECIBE y MUESTRA la contraseña generada
 */
function generarHTMLBienvenida($nombreNegocio, $usuario, $email, $password, $linkActivacion = '') {
    // Construir URL de activación dinámica si no se proporciona
    if (empty($linkActivacion)) {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $linkActivacion = $scheme . $host . '/registro.php';
    }
    return "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f5f5f5; }
            .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); overflow: hidden; }
            .header { background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); padding: 40px 20px; text-align: center; color: white; }
            .header h1 { margin: 0; font-size: 28px; font-weight: 700; }
            .content { padding: 30px; color: #333; line-height: 1.8; }
            .section-title { color: #27ae60; font-size: 18px; font-weight: 700; margin-top: 25px; margin-bottom: 15px; border-bottom: 2px solid #e8f5e9; padding-bottom: 10px; }
            .credentials-box { background: #f0f8f5; border-left: 4px solid #27ae60; padding: 15px; margin: 20px 0; border-radius: 4px; }
            .cred-item { margin: 10px 0; font-size: 14px; }
            .cred-label { font-weight: 600; color: #1e8449; }
            .cred-value { background: white; padding: 8px 12px; border-radius: 4px; word-break: break-all; font-family: 'Courier New', monospace; }
            .button-container { text-align: center; margin: 30px 0; }
            .activation-btn { background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); color: #fff; padding: 14px 40px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600; transition: transform 0.3s; }
            .activation-btn:hover { transform: translateY(-2px); color: #fff; }
            .steps { background: #f9f9f9; padding: 15px; border-radius: 6px; margin: 20px 0; }
            .step { margin: 10px 0; padding: 10px; border-left: 3px solid #27ae60; padding-left: 15px; }
            .step-number { color: #27ae60; font-weight: 700; }
            .footer { background: #f9f9f9; padding: 20px; text-align: center; border-top: 1px solid #e0e0e0; color: #999; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>¡BIENVENIDO A FUDDO!</h1>
            </div>
            <div class='content'>
                <p style='font-size: 16px; margin-bottom: 20px;'>Hola <strong>$nombreNegocio</strong></p>
                
                <h2 style='color: #27ae60; font-size: 20px; margin-bottom: 15px;'>¡Bienvenido a nuestra familia!</h2>
                <p style='font-size: 15px; color: #555;'>Nuestro objetivo es crecer juntos. Estamos emocionados de tenerte como parte de la comunidad FUDDO.</p>
                
                <div class='section-title'>📋 Estos son los pasos que debes seguir para iniciar</div>
                
                <div class='steps'>
                    <div class='step'><span class='step-number'>1.</span> Revisa tus <strong>datos de acceso</strong> abajo</div>
                    <div class='step'><span class='step-number'>2.</span> Haz clic en el botón <strong>\"Activar Mi Cuenta\"</strong></div>
                    <div class='step'><span class='step-number'>3.</span> ¡Comienza a usar FUDDO!</div>
                </div>

                <div class='section-title'>🔐 Tus datos de Acceso son:</div>
                
                <div class='credentials-box'>
                    <div class='cred-item'>
                        <div class='cred-label'>👤 USUARIO (Correo completo):</div>
                        <div class='cred-value'>$usuario</div>
                    </div>
                    <div class='cred-item'>
                        <div class='cred-label'>🔑 CONTRASEÑA:</div>
                        <div class='cred-value'>$password</div>
                    </div>
                </div>

                <p style='background: #fff3cd; border-left: 4px solid #ff9800; padding: 12px; border-radius: 4px; font-size: 13px; color: #666;'>
                    <strong>⚠️ Importante:</strong> Guarda tus credenciales en un lugar seguro. Esta es la única vez que verás tu contraseña.
                </p>

                <div class='section-title'>✅ Activa tu suscripción</div>
                <p style='color: #666; margin-bottom: 20px;'>Haz clic en el botón de abajo para activar tu cuenta y comenzar tu prueba gratuita de 7 días.</p>
                
                <div class='button-container'>
                    <a href='$linkActivacion' class='activation-btn'>🎉 Activar Mi Cuenta</a>
                </div>

                <p style='font-size: 12px; color: #999; margin-top: 20px; text-align: center;'>
                    Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                    <code style='background: #f0f0f0; padding: 5px 10px; border-radius: 3px; display: inline-block; margin-top: 5px; word-break: break-all; font-size: 11px;'>$linkActivacion</code>
                </p>

                <p style='background: #e8f5e9; border-left: 4px solid #27ae60; padding: 15px; border-radius: 4px; margin-top: 20px; font-size: 13px; color: #1e8449;'>
                    <strong>💡 Tip:</strong> Una vez actives tu cuenta, tendrás acceso completo a FUDDO durante 7 días sin costo. ¡Sin tarjeta de crédito requerida!
                </p>
            </div>
            
            <div class='footer'>
                <p style='margin: 0;'>¿Preguntas? No dudes en contactarnos en <a href='mailto:fuddocol@gmail.com' style='color: #27ae60; text-decoration: none;'>fuddocol@gmail.com</a></p>
                <p style='margin: 10px 0 0 0;'>© 2026 FUDDO - Sistema Profesional de Gestión de Restaurantes</p>
                <p style='margin: 5px 0 0 0;'><a href='https://fuddo.co' style='color: #27ae60; text-decoration: none;'>www.fuddo.co</a></p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Genera HTML de bienvenida para usuarios creados por un administrador
 */
function generarHTMLBienvenidaUsuario($nombre, $usuario, $email, $password, $rol = '', $loginUrl = '') {
    // Construir URL de login dinámica si no se proporciona
    if (empty($loginUrl)) {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $loginUrl = $scheme . $host . '/fuddo/login.php';
    }
    $rolTexto = !empty($rol) ? '<div class="cred-item"><div class="cred-label">👔 ROL ASIGNADO:</div><div class="cred-value">' . ucfirst(str_replace('-', ' ', $rol)) . '</div></div>' : '';
    
    return "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; background-color: #f5f5f5; }
            .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); overflow: hidden; }
            .header { background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); padding: 40px 20px; text-align: center; color: white; }
            .header h1 { margin: 0; font-size: 28px; font-weight: 700; }
            .content { padding: 30px; color: #333; line-height: 1.8; }
            .section-title { color: #27ae60; font-size: 18px; font-weight: 700; margin-top: 25px; margin-bottom: 15px; border-bottom: 2px solid #e8f5e9; padding-bottom: 10px; }
            .credentials-box { background: #f0f8f5; border-left: 4px solid #27ae60; padding: 15px; margin: 20px 0; border-radius: 4px; }
            .cred-item { margin: 12px 0; font-size: 14px; }
            .cred-label { font-weight: 600; color: #1e8449; margin-bottom: 5px; }
            .cred-value { background: white; padding: 8px 12px; border-radius: 4px; word-break: break-all; font-family: 'Courier New', monospace; font-size: 13px; }
            .button-container { text-align: center; margin: 30px 0; }
            .login-btn { background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); color: #fff !important; padding: 14px 40px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: 600; transition: transform 0.3s; }
            .login-btn:hover { transform: translateY(-2px); color: #fff !important; }
            .important-box { background: #fff3cd; border-left: 4px solid #ff9800; padding: 15px; border-radius: 4px; margin: 20px 0; font-size: 13px; color: #666; }
            .tip-box { background: #e8f5e9; border-left: 4px solid #27ae60; padding: 15px; border-radius: 4px; margin: 20px 0; font-size: 13px; color: #1e8449; }
            .footer { background: #f9f9f9; padding: 20px; text-align: center; border-top: 1px solid #e0e0e0; color: #999; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h1>🎉 ¡BIENVENIDO A FUDDO!</h1>
            </div>
            <div class='content'>
                <p style='font-size: 16px; margin-bottom: 20px;'>Hola <strong>$nombre</strong>,</p>
                
                <p style='font-size: 15px; color: #555; margin-bottom: 20px;'>Tu cuenta ha sido creada en el sistema FUDDO. A continuación encontrarás tus datos de acceso para que puedas comenzar a trabajar inmediatamente.</p>
                
                <div class='section-title'>🔐 Tus datos de Acceso</div>
                
                <div class='credentials-box'>
                    <div class='cred-item'>
                        <div class='cred-label'>👤 USUARIO:</div>
                        <div class='cred-value'>$usuario</div>
                    </div>
                    <div class='cred-item'>
                        <div class='cred-label'>🔑 CONTRASEÑA:</div>
                        <div class='cred-value'>$password</div>
                    </div>
                    $rolTexto
                </div>

                <div class='important-box'>
                    <strong>⚠️ Importante:</strong> Guarda tus credenciales en un lugar seguro.
                </div>

                <div class='section-title'>🚀 Acceso al Sistema</div>
                
                <div class='button-container'>
                    <a href='$loginUrl' class='login-btn'>Ir a FUDDO</a>
                </div>

                <div class='tip-box'>
                    <strong>💡 Tip:</strong> Nos recomienda que cambies tu contraseña en tu primer acceso por razones de seguridad.
                </div>

                <p style='font-size: 13px; color: #666; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e0e0e0;'>
                    Si no fuiste tú quien solicitó esta cuenta, por favor contacta al administrador.
                </p>
            </div>
            
            <div class='footer'>
                <p style='margin: 0;'>¿Preguntas? No dudes en contactarnos en <a href='mailto:fuddocol@gmail.com' style='color: #27ae60; text-decoration: none;'>fuddocol@gmail.com</a></p>
                <p style='margin: 10px 0 0 0;'>© 2026 FUDDO - Sistema Profesional de Gestión de Restaurantes</p>
                <p style='margin: 5px 0 0 0;'><a href='https://fuddo.co' style='color: #27ae60; text-decoration: none;'>www.fuddo.co</a></p>
            </div>
        </div>
    </body>
    </html>
    ";
}
?>
