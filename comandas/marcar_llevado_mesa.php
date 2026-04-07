<?php
include '../includes/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $comanda_id = isset($_POST['comanda_id']) ? $_POST['comanda_id'] : '';
        $llevado = intval($_POST['llevado'] ?? 0);

        if ($producto_id <= 0 || empty($comanda_id)) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            exit();
        }

        $comanda_id_escaped = $conexion->real_escape_string($comanda_id);

        // Actualizar estado llevado_mesa en tabla comandas
        $sql = "UPDATE " . TBL_COMANDAS . " 
                SET llevado_mesa = $llevado 
                WHERE id = $producto_id 
                AND id_comanda = '$comanda_id_escaped'
                AND estado = 'activo'";

        if ($conexion->query($sql) === TRUE) {
            echo json_encode([
                'success' => true,
                'message' => $llevado ? 'Producto marcado como llevado a mesa' : 'Producto desmarcado'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar: ' . $conexion->error
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
