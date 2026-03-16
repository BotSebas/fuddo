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

// Nota: NO usamos die() aquí - el error será manejado por procesar_registro.php
// Esto permite que procesar_registro.php devuelva JSON con el error

// Verificar conexión y registrar error si es necesario
if ($conexion_master && !$conexion_master->connect_error) {
    // Conexión exitosa - configurar charset
    $conexion_master->set_charset("utf8mb4");
} else {
    // Si hay error, lo manejará procesar_registro.php
    // Solo registrar para debugging
    error_log("Error de conexión a BD maestra (será manejado por procesar_registro.php): " . ($conexion_master ? $conexion_master->connect_error : "mysqli no inicializado"));
}
?>