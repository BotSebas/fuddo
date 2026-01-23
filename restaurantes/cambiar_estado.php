<?php
session_start();
include '../includes/conexion_master.php';

header('Content-Type: application/json');

// Verificar permisos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = intval($_POST['id']);
    
    // Obtener estado actual
    $sql = "SELECT estado FROM restaurantes WHERE id = $id";
    $resultado = $conexion_master->query($sql);
    
    if ($resultado->num_rows > 0) {
        $row = $resultado->fetch_assoc();
        $nuevo_estado = ($row['estado'] === 'activo') ? 'inactivo' : 'activo';
        
        // Actualizar estado
        $sql_update = "UPDATE restaurantes SET estado = '$nuevo_estado' WHERE id = $id";
        
        if ($conexion_master->query($sql_update)) {
            echo json_encode([
                'success' => true,
                'message' => 'Estado cambiado a ' . $nuevo_estado,
                'nuevo_estado' => $nuevo_estado
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al cambiar el estado'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Restaurante no encontrado'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>
