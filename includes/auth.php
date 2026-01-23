<?php
// Archivo para proteger páginas que requieren autenticación
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: " . dirname($_SERVER['PHP_SELF']) . "/login.php?sesion=1");
    exit();
}

// Verificar que el restaurante siga activo (solo para usuarios no super-admin)
if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'super-admin' && isset($_SESSION['id_restaurante'])) {
    include_once __DIR__ . '/conexion_master.php';
    
    $stmt = $conexion_master->prepare("SELECT estado FROM restaurantes WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['id_restaurante']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $restaurante = $resultado->fetch_assoc();
        
        if ($restaurante['estado'] !== 'activo') {
            // Cerrar sesión y redirigir con mensaje
            session_destroy();
            header("Location: " . dirname($_SERVER['PHP_SELF']) . "/login.php?error=restaurante_inactivo");
            exit();
        }
    }
    
    $stmt->close();
}
?>