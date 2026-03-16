<?php
require_once __DIR__ . '/mail_config.php';
require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Configura una instancia base de PHPMailer con Gmail SMTP.
 */
function _crearMailer(): PHPMailer {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = MAIL_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = MAIL_USERNAME;
    $mail->Password   = MAIL_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = MAIL_PORT;
    $mail->CharSet    = 'UTF-8';
    $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
    return $mail;
}

/**
 * Envía correo de bienvenida a un candidato recién creado.
 */
function enviarCorreoCandidato(string $email, string $usuario, string $password): bool {
    if (empty($email)) return false;
    try {
        $mail = _crearMailer();
        $mail->addAddress($email, $usuario);
        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido a NeuraL! – Tus accesos';
        $mail->Body    = _templateCandidato($usuario, $email, $password);
        $mail->AltBody = "Hola $usuario,\n\nTus accesos a NeuraL:\nUsuario: $email\nContraseña: $password\nAcceso: " . MAIL_LOGIN_URL;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('NeuraL Mailer [candidato]: ' . $e->getMessage());
        return false;
    }
}

/**
 * Envía correo de bienvenida a un colaborador recién creado.
 */
function enviarCorreoColaborador(string $email, string $usuario, string $password, string $rol): bool {
    if (empty($email)) return false;
    try {
        $mail = _crearMailer();
        $mail->addAddress($email, $usuario);
        $mail->isHTML(true);
        $mail->Subject = '¡Bienvenido al equipo NeuraL! – Tus accesos';
        $mail->Body    = _templateColaborador($usuario, $email, $password, $rol);
        $mail->AltBody = "Hola $usuario,\n\nTus accesos a NeuraL:\nUsuario: $email\nContraseña: $password\nRol: $rol\nAcceso: " . MAIL_LOGIN_URL;
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('NeuraL Mailer [colaborador]: ' . $e->getMessage());
        return false;
    }
}

// ── Plantillas HTML ───────────────────────────────────────────────────────────

function _templateCandidato(string $usuario, string $email, string $password): string {
    $loginUrl = MAIL_LOGIN_URL;
    $appUrl   = MAIL_APP_URL;
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 0">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)">
        <!-- Header -->
        <tr>
          <td style="background:#704a98;padding:32px 40px;text-align:center">
            <h1 style="margin:0;color:#fff;font-size:28px;letter-spacing:2px">NeuraL</span></h1>
            <p style="margin:6px 0 0;color:#e8d8f5;font-size:14px">Gestiona. Conecta. Crece.</p>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:36px 40px">
            <p style="font-size:16px;color:#333;margin:0 0 12px">Hola, <strong>{$usuario}</strong> 👋</p>
            <p style="font-size:14px;color:#555;line-height:1.6;margin:0 0 24px">
              Tu cuenta en <strong>NeuraL</strong> ha sido creada. A continuación encontrarás tus credenciales de acceso:
            </p>
            <!-- Credenciales -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3edf9;border-radius:6px;margin-bottom:24px">
              <tr>
                <td style="padding:20px 24px">
                  <table width="100%" cellpadding="6" cellspacing="0">
                    <tr>
                      <td style="font-size:13px;color:#704a98;font-weight:bold;width:130px">Correo / Usuario</td>
                      <td style="font-size:14px;color:#333">{$email}</td>
                    </tr>
                    <tr>
                      <td style="font-size:13px;color:#704a98;font-weight:bold">Contraseña</td>
                      <td style="font-size:14px;color:#333;font-family:monospace;letter-spacing:1px">{$password}</td>
                    </tr>
                    <!-- <tr>
                      <td style="font-size:13px;color:#704a98;font-weight:bold">Tipo de cuenta</td>
                      <td style="font-size:14px;color:#333">Candidato</td>
                    </tr> -->
                  </table>
                </td>
              </tr>
            </table>
            <!-- <p style="font-size:14px;color:#555;margin:0 0 8px">Desde tu cuenta podrás:</p>
            <ul style="font-size:14px;color:#555;padding-left:20px;line-height:1.8;margin:0 0 28px">
              <li>Revisar y actualizar tu <strong>documentación</strong> requerida</li>
              <li>Ver el progreso de tu proceso de selección</li>
            </ul> -->
            <!-- Botón -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto 28px">
              <tr>
                <td style="background:#976bab;border-radius:6px;text-align:center">
                  <a href="{$loginUrl}" style="display:inline-block;padding:14px 36px;color:#fff;font-size:15px;font-weight:bold;text-decoration:none">
                    Ingresar a NeuraL
                  </a>
                </td>
              </tr>
            </table>
            <p style="font-size:12px;color:#999;margin:0">
              O copia y pega este enlace en tu navegador:<br>
              <a href="{$loginUrl}" style="color:#976bab">{$loginUrl}</a>
            </p>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#f3edf9;padding:18px 40px;text-align:center;border-top:1px solid #e8d8f5">
            <p style="margin:0;font-size:12px;color:#976bab">
              Este correo fue generado automáticamente por <strong>NeuraL</strong>. Por favor no respondas a este mensaje.
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}

function _templateColaborador(string $usuario, string $email, string $password, string $rol): string {
    $loginUrl  = MAIL_LOGIN_URL;
    $rolLabel  = ucfirst($rol);
    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f4f6f9;font-family:Arial,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f9;padding:30px 0">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.1)">
        <!-- Header -->
        <tr>
          <td style="background:#704a98;padding:32px 40px;text-align:center">
            <h1 style="margin:0;color:#fff;font-size:28px;letter-spacing:2px">NeuraL</span></h1>
            <p style="margin:6px 0 0;color:#e8d8f5;font-size:14px">Plataforma de FOY Group</p>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:36px 40px">
            <p style="font-size:16px;color:#333;margin:0 0 12px">Hola, <strong>{$usuario}</strong> 👋</p>
            <p style="font-size:14px;color:#555;line-height:1.6;margin:0 0 24px">
              Has sido registrado como colaborador en <strong>NeuraL</strong>. A continuación tus credenciales de acceso:
            </p>
            <!-- Credenciales -->
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3edf9;border-radius:6px;margin-bottom:24px">
              <tr>
                <td style="padding:20px 24px">
                  <table width="100%" cellpadding="6" cellspacing="0">
                    <tr>
                      <td style="font-size:13px;color:#704a98;font-weight:bold;width:130px">Correo / Usuario</td>
                      <td style="font-size:14px;color:#333">{$email}</td>
                    </tr>
                    <tr>
                      <td style="font-size:13px;color:#704a98;font-weight:bold">Contraseña</td>
                      <td style="font-size:14px;color:#333;font-family:monospace;letter-spacing:1px">{$password}</td>
                    </tr>
                    <!-- <tr>
                      <td style="font-size:13px;color:#704a98;font-weight:bold">Rol asignado</td>
                      <td style="font-size:14px;color:#333">{$rolLabel}</td>
                    </tr> 
                    <tr>
                      <td style="font-size:13px;color:#704a98;font-weight:bold">Tipo de cuenta</td>
                      <td style="font-size:14px;color:#333">Colaborador</td>
                    </tr> -->
                  </table>
                </td>
              </tr>
            </table>
            <!-- <p style="font-size:14px;color:#555;margin:0 0 8px">Desde tu cuenta podrás gestionar:</p>
            <ul style="font-size:14px;color:#555;padding-left:20px;line-height:1.8;margin:0 0 28px">
              <li>Candidatos y su documentación</li>
              <li>Reportes y vacantes según tu rol</li>
              <li>Comunicación interna del equipo</li>
            </ul> -->
            <!-- Botón -->
            <table cellpadding="0" cellspacing="0" style="margin:0 auto 28px">
              <tr>
                <td style="background:#976bab;border-radius:6px;text-align:center">
                  <a href="{$loginUrl}" style="display:inline-block;padding:14px 36px;color:#fff;font-size:15px;font-weight:bold;text-decoration:none">
                    Ingresar a NeuraL
                  </a>
                </td>
              </tr>
            </table>
            <p style="font-size:12px;color:#999;margin:0">
              O copia y pega este enlace en tu navegador:<br>
              <a href="{$loginUrl}" style="color:#976bab">{$loginUrl}</a>
            </p>
          </td>
        </tr>
        <!-- Footer -->
        <tr>
          <td style="background:#f3edf9;padding:18px 40px;text-align:center;border-top:1px solid #e8d8f5">
            <p style="margin:0;font-size:12px;color:#976bab">
              Este correo fue generado automáticamente por <strong>NeuraL</strong>. Por favor no respondas a este mensaje.
            </p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;
}
