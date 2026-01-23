<?php
/**
 * Script de utilidad para exportar el esquema de BD actual
 * Ejecutar una vez para generar schema_restaurante.sql
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'fuddo'; // Tu BD actual

$tables = ['mesas', 'productos', 'servicios', 'usuarios'];

echo "<h2>Exportando Esquema...</h2>";
echo "<pre>";

$output = "-- Esquema base para cada restaurante\n";
$output .= "-- Generado automáticamente\n\n";

$conexion = new mysqli($host, $user, $pass, $db);

foreach ($tables as $table) {
    $result = $conexion->query("SHOW CREATE TABLE `$table`");
    $row = $result->fetch_assoc();
    $output .= "\n" . $row['Create Table'] . ";\n\n";
}

// Agregar vistas si existen
$views = ['servicios_total'];
foreach ($views as $view) {
    $result = $conexion->query("SHOW CREATE VIEW `$view`");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $output .= "\n" . str_replace('CREATE VIEW', 'CREATE OR REPLACE VIEW', $row['Create View']) . ";\n\n";
    }
}

echo "</pre>";
file_put_contents('sql/schema_restaurante.sql', $output);
echo "<h3 style='color:green;'>✓ Esquema exportado exitosamente a sql/schema_restaurante.sql</h3>";
echo "<p>Tamaño: " . strlen($output) . " bytes</p>";
echo "<a href='sql/schema_restaurante.sql' target='_blank'>Ver archivo generado</a>";
?>
