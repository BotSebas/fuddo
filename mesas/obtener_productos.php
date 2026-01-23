<?php
include '../includes/conexion.php';
header('Content-Type: application/json');

if (isset($_GET['mesa_id'])) {
    $mesa_id = intval($_GET['mesa_id']);
    
    // Obtener id_mesa de la mesa
    $sqlMesa = "SELECT id_mesa FROM " . TBL_MESAS . " WHERE id = $mesa_id";
    $resultMesa = $conexion->query($sqlMesa);
    
    if ($resultMesa && $resultMesa->num_rows > 0) {
        $mesa = $resultMesa->fetch_assoc();
        $id_mesa_ref = $mesa['id_mesa'];
        
        // Obtener productos/servicios de la mesa con JOIN
        $sql = "SELECT s.id, p.nombre_producto, s.cantidad, s.valor_unitario, s.valor_total 
                FROM " . TBL_SERVICIOS . " s
                INNER JOIN " . TBL_PRODUCTOS . " p ON s.id_producto = p.id_producto
                WHERE s.id_mesa = '" . $conexion->real_escape_string($id_mesa_ref) . "' 
                AND s.estado = 'activo'
                ORDER BY s.id DESC";
        
        $resultado = $conexion->query($sql);
        $productos = [];
        
        if ($resultado && $resultado->num_rows > 0) {
            while($row = $resultado->fetch_assoc()) {
                $productos[] = [
                    'id' => $row['id'],
                    'nombre' => $row['nombre_producto'] . ' (x' . $row['cantidad'] . ')',
                    'valor' => $row['valor_total']
                ];
            }
        }
        
        echo json_encode([
            'success' => true,
            'productos' => $productos
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'productos' => []
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'productos' => []
    ]);
}
?>
