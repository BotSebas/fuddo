<?php
// Detecta el idioma del navegador (por defecto: español)
$idioma_navegador = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

// Soporta solo 'es' o 'en'
switch ($idioma_navegador) {
    case 'en':
        $idioma = 'en';
        break;
    default:
        $idioma = 'es';
        break;
}

// Carga el archivo de idioma correspondiente
include_once __DIR__ . "/$idioma.php";
?>