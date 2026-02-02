<?php
/**
 * Conexión dinámica con prefijos de tabla para Cloudways
 * AHORA: Una sola BD, tablas con prefijo fuddo_{identificador}_
 */

// Detectar si estamos en Cloudways (producción) o local
$is_cloudways = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'phpstack-1316371-6163825.cloudwaysapps.com') !== false);

if ($is_cloudways) {
    // Configuración Cloudways (producción)
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_USER')) define('DB_USER', 'fwedexhvyx');
    if (!defined('DB_PASS')) define('DB_PASS', 'r6yS5sVU4e');
    if (!defined('DB_NAME')) define('DB_NAME', 'mgacgdnjkg');
} else {
    // Entorno Local (XAMPP)
    // Probar si existe BD mgacgdnjkg (simulación Cloudways local)
    $test_local_cloud = @new mysqli('localhost', 'root', '', 'mgacgdnjkg');
    if (!$test_local_cloud->connect_error) {
        // BD mgacgdnjkg existe - usar modo Cloudways con credenciales root
        if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
        if (!defined('DB_USER')) define('DB_USER', 'root');
        if (!defined('DB_PASS')) define('DB_PASS', '');
        if (!defined('DB_NAME')) define('DB_NAME', 'mgacgdnjkg');
        $is_cloudways = true; // Activar modo prefijos
        $test_local_cloud->close();
    } else {
        // BD mgacgdnjkg no existe - usar modo legacy multi-database
        if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
        if (!defined('DB_USER')) define('DB_USER', 'root');
        if (!defined('DB_PASS')) define('DB_PASS', '');
        // DB_NAME se definirá dinámicamente por restaurante
    }
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
        header("Location: login.php");
        exit();
    }
}

// Crear conexión
$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión a base de datos: " . $conexion->connect_error);
}

// Configurar charset y zona horaria
$conexion->set_charset("utf8mb4");
$conexion->query("SET time_zone = '-05:00'"); // Colombia

// Definir nombres de tabla con prefijo
define('TBL_MESAS', $TABLE_PREFIX . 'mesas');
define('TBL_PRODUCTOS', $TABLE_PREFIX . 'productos');
define('TBL_SERVICIOS', $TABLE_PREFIX . 'servicios');
define('TBL_SERVICIOS_TOTAL', $TABLE_PREFIX . 'servicios_total');
?>