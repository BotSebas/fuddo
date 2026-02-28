<?php
// Detecta automáticamente si estás en localhost o en un dominio
$BASE_URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$BASE_URL .= "://" . $_SERVER['HTTP_HOST'];

// Detectar si estamos en producción (dominios específicos)
$is_production = (
    strpos($_SERVER['HTTP_HOST'], 'fuddo.co') !== false ||
    strpos($_SERVER['HTTP_HOST'], 'phpstack-1316371-6163825.cloudwaysapps.com') !== false
);

// En producción, el proyecto está en la raíz del dominio
if ($is_production) {
    $BASE_URL .= '/';
} else {
    // En local, el proyecto está en una subcarpeta
    $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
    
    // Si estamos en una subcarpeta del proyecto (mesas/, productos/, etc.), subir un nivel
    if (strpos($scriptPath, '/mesas') !== false || 
        strpos($scriptPath, '/comandas') !== false ||
        strpos($scriptPath, '/productos') !== false || 
        strpos($scriptPath, '/usuarios') !== false ||
        strpos($scriptPath, '/restaurantes') !== false ||
        strpos($scriptPath, '/permisos') !== false ||
        strpos($scriptPath, '/cocina') !== false ||
        strpos($scriptPath, '/menu-digital') !== false ||
        strpos($scriptPath, '/reportes') !== false ||
        strpos($scriptPath, '/materias_primas') !== false ||
        strpos($scriptPath, '/recetas') !== false) {
        $BASE_URL .= dirname($scriptPath);
    } else {
        $BASE_URL .= $scriptPath;
    }
    
    // Asegurar que siempre termine con un solo slash
    $BASE_URL = rtrim($BASE_URL, '/') . '/';
}
?>