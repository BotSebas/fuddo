<?php
// ─────────────────────────────────────────────────────────────────────────────
// Configuración de correo – Gmail SMTP
// Rellena con tus datos antes de usar el sistema
// ─────────────────────────────────────────────────────────────────────────────

define('MAIL_HOST',       'smtp.gmail.com');
define('MAIL_PORT',       587);
define('MAIL_USERNAME',   'neural.foy@gmail.com');
define('MAIL_PASSWORD',   'ulmziqlxqgrdvabm');
define('MAIL_FROM_EMAIL', 'neural.foy@gmail.com');
define('MAIL_FROM_NAME',  'NeuraL');

// URL base de la aplicación local (visible en el correo)
define('MAIL_APP_URL',    'http://localhost/neural/');
define('MAIL_LOGIN_URL',  'http://localhost/neural/login.php');

// URL base de la aplicación Produccion (visible en el correo)
// define('MAIL_APP_URL',    'http://foygroup.co/neural/');
// define('MAIL_LOGIN_URL',  'http://foygroup.co/neural/login.php');
