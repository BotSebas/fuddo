<?php
/**
 * Conexión a la base de datos MAESTRA
 * AHORA: Misma BD para local y Cloudways, solo cambian las credenciales
 */

// Detectar si estamos en Cloudways (producción) o local
$is_cloudways = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'phpstack-1316371-6163825.cloudwaysapps.com') !== false);

if ($is_cloudways) {
    // Configuración Cloudways (producción)
    if (!defined('DB_MASTER_HOST')) define('DB_MASTER_HOST', 'localhost');
    if (!defined('DB_MASTER_USER')) define('DB_MASTER_USER', 'fwedexhvyx');
    if (!defined('DB_MASTER_PASS')) define('DB_MASTER_PASS', 'r6yS5sVU4e');
    if (!defined('DB_MASTER_NAME')) define('DB_MASTER_NAME', 'fwedexhvyx');
} else {
    // Entorno Local (XAMPP)
    // Probar si existe BD fwedexhvyx (simulación Cloudways local)
    $test_local_cloud = @new mysqli('localhost', 'root', '', 'fwedexhvyx');
    if (!$test_local_cloud->connect_error) {
        // BD fwedexhvyx existe - usar modo Cloudways con credenciales root
        if (!defined('DB_MASTER_HOST')) define('DB_MASTER_HOST', 'localhost');
        if (!defined('DB_MASTER_USER')) define('DB_MASTER_USER', 'root');
        if (!defined('DB_MASTER_PASS')) define('DB_MASTER_PASS', '');
        if (!defined('DB_MASTER_NAME')) define('DB_MASTER_NAME', 'fwedexhvyx');
        $is_cloudways = true; // Activar modo prefijos
        $test_local_cloud->close();
    } else {
        // BD fwedexhvyx no existe - usar modo legacy multi-database
        if (!defined('DB_MASTER_HOST')) define('DB_MASTER_HOST', 'localhost');
        if (!defined('DB_MASTER_USER')) define('DB_MASTER_USER', 'root');
        if (!defined('DB_MASTER_PASS')) define('DB_MASTER_PASS', '');
        if (!defined('DB_MASTER_NAME')) define('DB_MASTER_NAME', 'fuddo_master');
    }
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