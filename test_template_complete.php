<?php
// Test completo de la lectura y procesamiento del template
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sql_template = file_get_contents('sql/template_restaurante.sql');

if ($sql_template === false) {
    echo 'ERROR: No se pudo leer el template SQL' . "\n";
    exit(1);
}

echo '✓ Archivo leido correctamente' . "\n";

// Validar que no hay caracteres corruptos
if (strpos($sql_template, '??') !== false) {
    echo 'ERROR: Se detectaron caracteres corruptos' . "\n";
    exit(1);
}

echo '✓ No hay caracteres corruptos' . "\n";

// Test de reemplazo de {PREFIX}
$table_prefix = 'fuddo_test_';
$sql_schema = str_replace('{PREFIX}', $table_prefix, $sql_template);

if (strpos($sql_schema, $table_prefix . 'mesas') === false) {
    echo 'ERROR: El reemplazo de {PREFIX} no funciono' . "\n";
    exit(1);
}

echo '✓ Reemplazo de {PREFIX} funciona correctamente' . "\n";

// Contar tablas creadas
$table_count = substr_count($sql_schema, 'CREATE TABLE');
echo "✓ Numero de tablas encontradas: " . $table_count . "\n";

// Validar sintaxis SQL basica
$tables = preg_match_all('/CREATE TABLE IF NOT EXISTS `' . preg_quote($table_prefix) . '(\w+)`/i', $sql_schema, $matches);
if ($tables > 0) {
    echo "✓ Tablas que se crearan: " . implode(', ', $matches[1]) . "\n";
}

echo "\n✓✓✓ TEST COMPLETADO EXITOSAMENTE ✓✓✓\n";
echo "El template esta listo para usarse en procesar.php\n";
?>
