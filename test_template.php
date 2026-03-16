<?php
// Test de lectura del template
$file_path = __DIR__ . '/sql/template_restaurante.sql';

echo "Verificando archivo: " . $file_path . "<br>";
echo "Existe: " . (file_exists($file_path) ? "SI" : "NO") . "<br>";

if (file_exists($file_path)) {
    $content = file_get_contents($file_path);
    $lines = explode("\n", $content);
    
    echo "Primeras 5 lineas del archivo:<br>";
    echo "<pre>";
    for ($i = 0; $i < min(5, count($lines)); $i++) {
        echo htmlspecialchars($lines[$i]) . "\n";
    }
    echo "</pre>";
    
    // Revisar si hay caracteres corruptos
    if (strpos($content, '??') !== false) {
        echo "<span style='color:red'>ERROR: Se detectaron caracteres corruptos (??)</span><br>";
    } else {
        echo "<span style='color:green'>OK: No se detectaron caracteres corruptos</span><br>";
    }
    
    // Contar numero de tablas CREATE
    $create_count = substr_count($content, 'CREATE TABLE');
    echo "Numero de CREATE TABLE: " . $create_count . "<br>";
}
?>