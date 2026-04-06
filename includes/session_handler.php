<?php
// Configurar inicio de sesión seguro
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Tiempo de inactividad permitido (en segundos): 30 minutos
define('SESSION_TIMEOUT', 30 * 60);

// Validar si existe sesión activa de usuario
if (!isset($_SESSION['user_id'])) {
    // No hay sesión, redirigir a login
    
    // Construir URL completa dinámicamente
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . $host;
    
    // Detectar si es localhost (desarrollo) o dominio (producción)
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        // Desarrollo local: localhost/fuddo/login.php
        $redirect_url = $baseUrl . '/fuddo/login.php?sesion=1';
    } else {
        // Producción (fuddo.co): fuddo.co/login.php
        $redirect_url = $baseUrl . '/login.php?sesion=1';
    }
    
    header('Location: ' . $redirect_url);
    exit();
}

// Validar tiempo de inactividad
$last_activity = $_SESSION['last_activity'] ?? time();
$current_time = time();
$inactive_time = $current_time - $last_activity;

if ($inactive_time > SESSION_TIMEOUT) {
    // Sesión expirada por inactividad
    session_destroy();
    $_SESSION = [];
    
    // Construir URL completa dinámicamente
    $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . $host;
    
    // Detectar si es localhost (desarrollo) o dominio (producción)
    if (strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) {
        // Desarrollo local: localhost/fuddo/login.php
        $redirect_url = $baseUrl . '/fuddo/login.php?expired=1';
    } else {
        // Producción (fuddo.co): fuddo.co/login.php
        $redirect_url = $baseUrl . '/login.php?expired=1';
    }
    
    header('Location: ' . $redirect_url);
    exit();
}

// Actualizar timestamp de última actividad
$_SESSION['last_activity'] = $current_time;

// Regenerar ID de sesión cada 5 minutos para seguridad
if (!isset($_SESSION['last_regen']) || (time() - $_SESSION['last_regen']) > 5 * 60) {
    session_regenerate_id(true);
    $_SESSION['last_regen'] = time();
}

?>
