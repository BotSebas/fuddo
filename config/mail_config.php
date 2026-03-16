<?php
/**
 * Configuración de Email - FUDDO
 * 
 * Este archivo contiene la configuración para PHPMailer
 * Reemplaza los valores con tu configuración
 */

// Configuración SMTP Gmail (recomendado para desarrollo/pruebas)
define('MAIL_DRIVER', 'smtp');
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'tu_email@gmail.com'); // Tu email Gmail
define('MAIL_PASSWORD', 'tu_app_password'); // Contraseña de aplicación (NO contraseña de Gmail)
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'noreply@fuddo.com');
define('MAIL_FROM_NAME', 'FUDDO - Sistema de Gestión');

/**
 * INSTRUCCIONES PARA CONFIGURAR GMAIL:
 * 
 * 1. Habilitar Autenticación en 2 Pasos:
 *    - Ve a https://myaccount.google.com/
 *    - Seguridad → Verificación en 2 Pasos
 *    
 * 2. Generar Contraseña de Aplicación:
 *    - https://myaccount.google.com/apppasswords
 *    - Selecciona "Correo" y "Windows"
 *    - Google generará una contraseña de 16 caracteres
 *    - Usa esta contraseña en MAIL_PASSWORD (sin espacios)
 *
 * ALTERNATIVAS:
 * 
 * Outlook/Hotmail:
 * - MAIL_HOST: smtp-mail.outlook.com
 * - MAIL_PORT: 587
 * - MAIL_ENCRYPTION: tls
 * 
 * SendGrid:
 * - MAIL_HOST: smtp.sendgrid.net
 * - MAIL_PORT: 587
 * - MAIL_USERNAME: apikey
 * - MAIL_PASSWORD: SG.xxxxxxxxxxxxxxx (clave API)
 */
?>
