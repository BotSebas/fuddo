<?php
include '../includes/conexion.php';
header('Content-Type: application/json');

if (isset($_GET['mesa_id'])) {
    $id_comanda = $conexion->real_escape_string($_GET['mesa_id']);
    
    // Obtener productos de la comanda usando tabla comandas
    $sql = "SELECT c.id, p.nombre_producto, c.cantidad, c.valor_unitario, c.valor_total 
            FROM " . TBL_COMANDAS . " c
            INNER JOIN " . TBL_PRODUCTOS . " p ON c.id_producto = p.id_producto
            WHERE c.id_comanda = '$id_comanda' 
            AND c.estado = 'activo'
            ORDER BY c.id DESC";
    
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
?>
