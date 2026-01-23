<?php
session_start();

header('Content-Type: application/json');

// Verificar que sea super-admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar variables de sesión relacionadas con el restaurante
    unset($_SESSION['id_restaurante']);
    unset($_SESSION['nombre_bd']);
    unset($_SESSION['nombre_restaurante']);
    unset($_SESSION['modo_soporte']);

    echo json_encode([
        'success' => true, 
        'message' => 'Has salido del modo soporte'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
