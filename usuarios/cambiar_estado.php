<?php
session_start();
include '../includes/conexion_master.php';

header('Content-Type: application/json');

// Verificar permisos
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos']);
    exit();
}

try {
    $id = $_POST['id'] ?? null;
    
    if (empty($id)) {
        throw new Exception('ID no proporcionado');
    }
    
    // Verificar que no sea el propio usuario
    if ($id == $_SESSION['user_id']) {
        throw new Exception('No puedes cambiar tu propio estado');
    }
    
    // Obtener estado actual
    $stmt = $conexion_master->prepare("SELECT estado FROM usuarios_master WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        $nuevoEstado = ($usuario['estado'] == 'activo') ? 'inactivo' : 'activo';
        
        // Actualizar estado
        $stmt = $conexion_master->prepare("UPDATE usuarios_master SET estado = ? WHERE id = ?");
        $stmt->bind_param("si", $nuevoEstado, $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Estado actualizado a ' . $nuevoEstado,
                'nuevo_estado' => $nuevoEstado
            ]);
        } else {
            throw new Exception('Error al actualizar el estado');
        }
    } else {
        throw new Exception('Usuario no encontrado');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
