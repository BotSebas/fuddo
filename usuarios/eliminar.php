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
        throw new Exception('No puedes eliminar tu propio usuario');
    }
    
    // Obtener foto para eliminarla del servidor
    $stmt = $conexion_master->prepare("SELECT foto FROM usuarios_master WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        // Eliminar foto si existe
        if (!empty($usuario['foto']) && file_exists('../' . $usuario['foto'])) {
            unlink('../' . $usuario['foto']);
        }
        
        // Eliminar usuario
        $stmt = $conexion_master->prepare("DELETE FROM usuarios_master WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Usuario eliminado exitosamente'
            ]);
        } else {
            throw new Exception('Error al eliminar el usuario');
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
