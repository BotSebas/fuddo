<?php
include '../includes/conexion.php';
header('Content-Type: application/json');
if (!isset($_GET['id_comanda'])) {
    echo json_encode(['success' => false, 'productos' => [], 'metodo_pago' => '']);
    exit();
}
$id_comanda = $conexion->real_escape_string($_GET['id_comanda']);
$sql = "SELECT c.id, p.nombre_producto, c.cantidad, c.valor_unitario, c.valor_total 
        FROM " . TBL_COMANDAS . " c
        LEFT JOIN " . TBL_PRODUCTOS . " p ON c.id_producto = p.id_producto
        WHERE c.id_comanda = '" . $id_comanda . "' AND c.estado IN ('activo', 'finalizado') ORDER BY c.id ASC";
$resultado = $conexion->query($sql);
$productos = [];
$metodo_pago = '';
if ($resultado && $resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
        $productos[] = [
            'id' => $row['id'],
            'nombre' => $row['nombre_producto'],
            'cantidad' => $row['cantidad'],
            'valor_unitario' => $row['valor_unitario'],
            'valor_total' => $row['valor_total']
        ];
    }
}

// Obtener método de pago desde comandas_total
$sqlMetodo = "SELECT metodo_pago FROM " . TBL_COMANDAS_TOTAL . " WHERE id_comanda = '" . $id_comanda . "' LIMIT 1";
$resultMetodo = $conexion->query($sqlMetodo);
if ($resultMetodo && $resultMetodo->num_rows > 0) {
    $rowMetodo = $resultMetodo->fetch_assoc();
    $metodo_pago = ucfirst($rowMetodo['metodo_pago']);
}

echo json_encode(['success' => true, 'productos' => $productos, 'metodo_pago' => $metodo_pago]);
?>