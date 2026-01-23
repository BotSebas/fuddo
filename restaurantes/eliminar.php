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
    
    try {
        // Obtener información del restaurante
        $sql = "SELECT nombre_bd FROM restaurantes WHERE id = ?";
        $stmt = $conexion_master->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Restaurante no encontrado');
        }
        
        $restaurante = $result->fetch_assoc();
        $nombre_bd = $restaurante['nombre_bd'];
        
        // Verificar que no haya usuarios asociados (opcional, puedes eliminarlos automáticamente)
        $sqlUsuarios = "SELECT COUNT(*) as total FROM usuarios_master WHERE id_restaurante = ?";
        $stmtUsuarios = $conexion_master->prepare($sqlUsuarios);
        $stmtUsuarios->bind_param("i", $id);
        $stmtUsuarios->execute();
        $resultUsuarios = $stmtUsuarios->get_result();
        $totalUsuarios = $resultUsuarios->fetch_assoc()['total'];
        
        // Eliminar usuarios del restaurante
        if ($totalUsuarios > 0) {
            $conexion_master->query("DELETE FROM usuarios_master WHERE id_restaurante = $id");
        }
        
        // Eliminar permisos del restaurante
        $conexion_master->query("DELETE FROM restaurante_aplicaciones WHERE id_restaurante = $id");
        
        // Eliminar base de datos del restaurante
        $conexion_master->query("DROP DATABASE IF EXISTS `$nombre_bd`");
        
        // Eliminar restaurante de la tabla
        $sql_delete = "DELETE FROM restaurantes WHERE id = ?";
        $stmt_delete = $conexion_master->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);
        
        if ($stmt_delete->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Restaurante eliminado exitosamente'
            ]);
        } else {
            throw new Exception('Error al eliminar el restaurante');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>
