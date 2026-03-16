<?php
// Detecta el idioma del navegador (por defecto: español)
$idioma_navegador = '';

// Obtener idioma del navegador de forma segura
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $idioma_navegador = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}

// Soporta solo 'es' o 'en'
switch ($idioma_navegador) {
    case 'en':
        $idioma = 'en';
        break;
    default:
        $idioma = 'es';
        break;
}

// Construir ruta del archivo de idioma
$ruta_idioma = __DIR__ . "/{$idioma}.php";

// Verificar que el archivo existe antes de incluirlo
if (file_exists($ruta_idioma)) {
    include_once $ruta_idioma;
} else {
    // Fallback a español si el archivo no existe
    $idioma = 'es';
    $ruta_idioma = __DIR__ . "/es.php";
    if (file_exists($ruta_idioma)) {
        include_once $ruta_idioma;
    }
}
?>