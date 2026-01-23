<?php
session_start();
include '../includes/conexion_master.php';

header('Content-Type: application/json');

// Verificar que sea super-admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    echo json_encode(['success' => false, 'message' => 'No tienes permisos para realizar esta acción']);
    exit();
}

try {
    $id_restaurante = intval($_POST['restaurante']);
    $id_aplicacion = intval($_POST['aplicacion']);
    $accion = $_POST['accion']; // 'asignar' o 'revocar'
    
    if ($accion === 'asignar') {
        // Verificar si ya existe
        $sqlCheck = "SELECT id FROM restaurante_aplicaciones WHERE id_restaurante = ? AND id_aplicacion = ?";
        $stmtCheck = $conexion_master->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $id_restaurante, $id_aplicacion);
        $stmtCheck->execute();
        $resultCheck = $stmtCheck->get_result();
        
        if ($resultCheck->num_rows === 0) {
            // Insertar nuevo permiso
            $sql = "INSERT INTO restaurante_aplicaciones (id_restaurante, id_aplicacion) VALUES (?, ?)";
            $stmt = $conexion_master->prepare($sql);
            $stmt->bind_param("ii", $id_restaurante, $id_aplicacion);
            
            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Permiso asignado correctamente'
                ]);
            } else {
                throw new Exception('Error al asignar el permiso');
            }
        } else {
            echo json_encode([
                'success' => true,
                'message' => 'El permiso ya estaba asignado'
            ]);
        }
    } else if ($accion === 'revocar') {
        // Eliminar permiso
        $sql = "DELETE FROM restaurante_aplicaciones WHERE id_restaurante = ? AND id_aplicacion = ?";
        $stmt = $conexion_master->prepare($sql);
        $stmt->bind_param("ii", $id_restaurante, $id_aplicacion);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Permiso revocado correctamente'
            ]);
        } else {
            throw new Exception('Error al revocar el permiso');
        }
    } else {
        throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
