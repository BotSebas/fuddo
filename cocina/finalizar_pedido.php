<?php
session_start();
include '../includes/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_servicio = $_POST['id_servicio'] ?? '';

    if (empty($id_servicio)) {
        echo json_encode(['success' => false, 'message' => 'ID de servicio no proporcionado']);
        exit();
    }

    // Actualizar estado del servicio a 'finalizado'
    $stmt = $conexion->prepare("UPDATE " . TBL_SERVICIOS . " SET estado = 'finalizado' WHERE id_servicio = ?");
    $stmt->bind_param("s", $id_servicio);

    if ($stmt->execute()) {
        // Actualizar estado de la mesa a 'libre' si es el último servicio activo
        $check_servicios = $conexion->query("
            SELECT COUNT(*) as total 
            FROM " . TBL_SERVICIOS . " 
            WHERE id_servicio = '$id_servicio' 
            AND estado = 'activo'
        ");
        
        $row = $check_servicios->fetch_assoc();
        
        // Si no quedan servicios activos para esta mesa, obtener la mesa
        if ($row['total'] == 0) {
            $get_mesa = $conexion->query("
                SELECT id_mesa 
                FROM " . TBL_SERVICIOS . " 
                WHERE id_servicio = '$id_servicio' 
                LIMIT 1
            ");
            
            if ($get_mesa && $get_mesa->num_rows > 0) {
                $mesa_data = $get_mesa->fetch_assoc();
                $id_mesa = $mesa_data['id_mesa'];
                
                // Verificar si hay otros servicios activos en esa mesa
                $check_mesa = $conexion->query("
                    SELECT COUNT(*) as total 
                    FROM " . TBL_SERVICIOS . " 
                    WHERE id_mesa = '$id_mesa' 
                    AND estado = 'activo'
                ");
                
                $mesa_row = $check_mesa->fetch_assoc();
                
                // Si no hay servicios activos, liberar la mesa
                if ($mesa_row['total'] == 0) {
                    $conexion->query("UPDATE " . TBL_MESAS . " SET estado = 'libre' WHERE id_mesa = '$id_mesa'");
                }
            }
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Pedido marcado como listo'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Error al actualizar el pedido: ' . $conexion->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

$conexion->close();
?>
