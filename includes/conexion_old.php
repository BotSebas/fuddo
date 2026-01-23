<?php
// Configuración de la base de datos
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_NAME')) define('DB_NAME', 'fuddo_herenciaargentina');

// Crear conexión solo si no existe
if (!isset($conexion)) {
    $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verificar conexión
    if ($conexion->connect_error) {
        echo "<div style='color: red; font-weight: bold; padding: 20px; background-color: #ffe6e6; border: 2px solid red; border-radius: 5px;'>
                ❌ Error de conexión: " . $conexion->connect_error . "
              </div>";
        die();
    }

    // Establecer charset UTF-8
    $conexion->set_charset("utf8mb4");
}

// Mostrar mensaje de éxito
// echo "<div style='color: green; font-weight: bold; padding: 20px; background-color: #e6ffe6; border: 2px solid green; border-radius: 5px;'>
//         ✔ Conexión exitosa a la base de datos: <strong>" . DB_NAME . "</strong>
//       </div>";
?>