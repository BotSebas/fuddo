<?php
// Detecta automáticamente si estás en localhost o en un dominio
$BASE_URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$BASE_URL .= "://" . $_SERVER['HTTP_HOST'];

// Si tu proyecto está en una subcarpeta (por ejemplo localhost/miapp), inclúyelo así:
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
// Si estamos en una subcarpeta del proyecto (mesas/, productos/, usuarios/, restaurantes/, permisos/, cocina/, reportes/), subir un nivel
if (strpos($scriptPath, '/mesas') !== false || 
    strpos($scriptPath, '/comandas') !== false ||
    strpos($scriptPath, '/productos') !== false || 
    strpos($scriptPath, '/usuarios') !== false ||
    strpos($scriptPath, '/restaurantes') !== false ||
    strpos($scriptPath, '/permisos') !== false ||
    strpos($scriptPath, '/cocina') !== false ||
    strpos($scriptPath, '/reportes') !== false) {
    $BASE_URL .= dirname($scriptPath) ;
} else {
    $BASE_URL .= $scriptPath ;
}

// Asegurar que siempre termine con un solo slash
$BASE_URL = rtrim($BASE_URL, '/') . '/';
?>