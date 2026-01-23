<?php
include '../includes/conexion.php';
header('Content-Type: application/json');

if (isset($_GET['id'])) {
    $mesa_id = intval($_GET['id']);
    
    // Cambiar el estado de la mesa a 'inactiva'
    $sql = "UPDATE " . TBL_MESAS . " SET estado = 'inactiva' WHERE id = $mesa_id";
    
    if ($conexion->query($sql) === TRUE) {
        echo json_encode([
            'success' => true,
            'message' => 'Mesa eliminada exitosamente'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar la mesa'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID de mesa no proporcionado'
    ]);
}
?>
