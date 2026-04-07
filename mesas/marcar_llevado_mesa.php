<?php
include '../includes/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $producto_id = intval($_POST['producto_id'] ?? 0);
        $mesa_id = intval($_POST['mesa_id'] ?? 0);
        $llevado = intval($_POST['llevado'] ?? 0);

        if ($producto_id <= 0 || $mesa_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Datos inválidos'
            ]);
            exit();
        }

        // Obtener id_mesa de la mesa
        $sqlMesa = "SELECT id_mesa FROM " . TBL_MESAS . " WHERE id = $mesa_id";
        $resultMesa = $conexion->query($sqlMesa);
        
        if (!($resultMesa && $resultMesa->num_rows > 0)) {
            echo json_encode([
                'success' => false,
                'message' => 'Mesa no encontrada'
            ]);
            exit();
        }

        $mesa = $resultMesa->fetch_assoc();
        $id_mesa_ref = $conexion->real_escape_string($mesa['id_mesa']);

        // Actualizar estado llevado_mesa
        $sql = "UPDATE " . TBL_SERVICIOS . " 
                SET llevado_mesa = $llevado 
                WHERE id = $producto_id 
                AND id_mesa = '$id_mesa_ref'
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
