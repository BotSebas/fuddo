<?php
$file_path = 'sql/template_restaurante.sql';
$content = file_get_contents($file_path);

if ($content === false) {
    echo 'ERROR: No se pudo leer el archivo' . "\n";
    exit(1);
}

if (strpos($content, '??') !== false) {
    echo 'ERROR: Se detectaron caracteres corruptos (??)' . "\n";
    exit(1);
}

$table_count = substr_count($content, 'CREATE TABLE');
echo 'OK: Archivo leido correctamente' . "\n";
echo 'Tablas encontradas: ' . $table_count . "\n";
echo 'Primeras 200 caracteres:' . "\n";
echo substr($content, 0, 200) . "\n";
?>
