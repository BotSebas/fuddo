<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
include '../includes/conexion.php';
ob_end_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $mesa_id = intval($_POST['mesa_id']);
        
        // Obtener id_mesa de la mesa
        $sqlMesa = "SELECT id_mesa FROM " . TBL_MESAS . " WHERE id = $mesa_id";
        $resultMesa = $conexion->query($sqlMesa);
        
        if ($resultMesa && $resultMesa->num_rows > 0) {
            $mesa = $resultMesa->fetch_assoc();
            $id_mesa_ref = $conexion->real_escape_string($mesa['id_mesa']);
            
            // Actualizar estado de los servicios activos a cancelado
            $sqlUpdateServicios = "UPDATE " . TBL_SERVICIOS . " SET estado = 'cancelado' WHERE id_mesa = '$id_mesa_ref' AND estado = 'activo'";
            
            if ($conexion->query($sqlUpdateServicios) === TRUE) {
                // Actualizar estado de la mesa a libre
                $sqlUpdateMesa = "UPDATE " . TBL_MESAS . " SET estado = 'libre' WHERE id = $mesa_id";
                $conexion->query($sqlUpdateMesa);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Servicio cancelado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al cancelar el servicio: ' . $conexion->error
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Mesa no encontrada'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>
