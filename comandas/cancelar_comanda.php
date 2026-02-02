<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
include '../includes/conexion.php';
ob_end_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $comanda_id = $_POST['comanda_id'];
        
        if (empty($comanda_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'ID de comanda no proporcionado'
            ]);
            exit();
        }
        
        $comanda_id_escaped = $conexion->real_escape_string($comanda_id);
        
        // Verificar que la comanda existe
        $sqlComanda = "SELECT id_comanda FROM " . TBL_COMANDAS . " WHERE id_comanda = '$comanda_id_escaped' AND estado = 'activo'";
        $resultComanda = $conexion->query($sqlComanda);
        
        if ($resultComanda && $resultComanda->num_rows > 0) {
            // Actualizar estado de la comanda a cancelado
            $sqlUpdateComanda = "UPDATE " . TBL_COMANDAS . " SET estado = 'cancelado' WHERE id_comanda = '$comanda_id_escaped'";
            
            if ($conexion->query($sqlUpdateComanda) === TRUE) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Comanda cancelada exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al cancelar la comanda: ' . $conexion->error
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Comanda no encontrada o ya fue procesada'
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
