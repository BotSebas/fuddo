<?php
/**
 * Conexión a la base de datos MAESTRA
 * AHORA: Misma BD para local y Cloudways, solo cambian las credenciales
 */

// Detectar entorno por dominio
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
if (
    strpos($host, 'localhost') !== false ||
    strpos($host, '127.0.0.1') !== false
) {
    // Localhost (desarrollo)
    if (!defined('DB_MASTER_HOST')) define('DB_MASTER_HOST', 'localhost');
    if (!defined('DB_MASTER_USER')) define('DB_MASTER_USER', 'root');
    if (!defined('DB_MASTER_PASS')) define('DB_MASTER_PASS', '');
    if (!defined('DB_MASTER_NAME')) define('DB_MASTER_NAME', 'mgacgdnjkg');
} else if (
    strpos($host, 'fuddo.co') !== false ||
    strpos($host, 'phpstack-1316371-6163825.cloudwaysapps.com') !== false
) {
    // Producción (Cloudways o dominio principal)
    if (!defined('DB_MASTER_HOST')) define('DB_MASTER_HOST', 'localhost');
    if (!defined('DB_MASTER_USER')) define('DB_MASTER_USER', 'fwedexhvyx');
    if (!defined('DB_MASTER_PASS')) define('DB_MASTER_PASS', 'r6yS5sVU4e');
    if (!defined('DB_MASTER_NAME')) define('DB_MASTER_NAME', 'mgacgdnjkg');
} else {
    // Fallback: usar local por defecto
    if (!defined('DB_MASTER_HOST')) define('DB_MASTER_HOST', 'localhost');
    if (!defined('DB_MASTER_USER')) define('DB_MASTER_USER', 'root');
    if (!defined('DB_MASTER_PASS')) define('DB_MASTER_PASS', '');
    if (!defined('DB_MASTER_NAME')) define('DB_MASTER_NAME', 'mgacgdnjkg');
}

// Crear conexión a BD maestra
$conexion_master = new mysqli(DB_MASTER_HOST, DB_MASTER_USER, DB_MASTER_PASS, DB_MASTER_NAME);

// Verificar conexión
if ($conexion_master->connect_error) {
    die("Error de conexión a BD maestra: " . $conexion_master->connect_error);
}

// Configurar charset
$conexion_master->set_charset("utf8mb4");
?>