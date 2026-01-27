<?php
/**
 * Conexión con prefijos de tabla para Cloudways
 * AHORA: Una sola BD, tablas con prefijo fuddo_{identificador}_
 */

// Detectar entorno por dominio
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
if (
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false
) {
    // Localhost (desarrollo)
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');
    if (!defined('DB_NAME')) define('DB_NAME', 'mgacgdnjkg');
} else if (
    strpos($host, 'fuddo.co') !== false ||
    strpos($host, 'phpstack-1316371-6163825.cloudwaysapps.com') !== false
) {
    // Producción (Cloudways o dominio principal)
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_USER')) define('DB_USER', 'fwedexhvyx');
    if (!defined('DB_PASS')) define('DB_PASS', 'r6yS5sVU4e');
    if (!defined('DB_NAME')) define('DB_NAME', 'mgacgdnjkg');
} else {
    // Fallback: usar local por defecto
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');
    if (!defined('DB_NAME')) define('DB_NAME', 'mgacgdnjkg');
}

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definir prefijo de tabla según el restaurante
$TABLE_PREFIX = '';

if (isset($_SESSION['identificador']) && !empty($_SESSION['identificador'])) {
    // Usuario tiene restaurante asignado: usar prefijo
    $TABLE_PREFIX = 'fuddo_' . $_SESSION['identificador'] . '_';
} elseif (isset($_SESSION['rol']) && $_SESSION['rol'] === 'super-admin') {
    // Super-admin sin restaurante: sin prefijo (tablas master)
    $TABLE_PREFIX = '';
} else {
    // Sin sesión válida: redirigir a login
    if (basename($_SERVER['PHP_SELF']) != 'login.php' && 
        basename($_SERVER['PHP_SELF']) != 'validar.php' &&
        basename($_SERVER['PHP_SELF']) != 'forgot-password.php' &&
        basename($_SERVER['PHP_SELF']) != 'generar_password.php') {
        include_once __DIR__ . '/url.php';
        header("Location: " . $BASE_URL . "login.php");
        exit();
    }
}

// Crear conexión solo si no existe
if (!isset($conexion)) {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conexion->connect_error) {
        die("Error de conexión a base de datos: " . $conexion->connect_error);
    }

    // Configurar charset y zona horaria
    $conexion->set_charset("utf8mb4");
    $conexion->query("SET time_zone = '-05:00'"); // Colombia
}

// Definir nombres de tabla con prefijo (solo si no están definidas)
if (!defined('TBL_MESAS')) define('TBL_MESAS', $TABLE_PREFIX . 'mesas');
if (!defined('TBL_PRODUCTOS')) define('TBL_PRODUCTOS', $TABLE_PREFIX . 'productos');
if (!defined('TBL_SERVICIOS')) define('TBL_SERVICIOS', $TABLE_PREFIX . 'servicios');
if (!defined('TBL_SERVICIOS_TOTAL')) define('TBL_SERVICIOS_TOTAL', $TABLE_PREFIX . 'servicios_total');
?>