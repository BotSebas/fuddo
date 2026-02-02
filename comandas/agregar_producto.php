<?php
include '../includes/conexion.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}
$comanda_id = intval($_POST['comanda_id'] ?? 0);
$producto_id = intval($_POST['producto_id'] ?? 0);
$cantidad = intval($_POST['cantidad'] ?? 1);
if ($comanda_id <= 0 || $producto_id <= 0 || $cantidad <= 0) {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}
// Generar id_comanda como 'CMD-<id>'
$id_comanda = 'CMD-' . $comanda_id;

// Obtener información del producto
$sqlProducto = "SELECT id_producto, valor_con_iva, inventario FROM " . TBL_PRODUCTOS . " WHERE id = $producto_id";
$resultProducto = $conexion->query($sqlProducto);
if (!($resultProducto && $resultProducto->num_rows > 0)) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    exit();
}
$producto = $resultProducto->fetch_assoc();
$inventario_actual = intval($producto['inventario']);
if ($inventario_actual < $cantidad) {
    echo json_encode(['success' => false, 'message' => 'Stock insuficiente']);
    exit();
}
$id_producto_ref = $conexion->real_escape_string($producto['id_producto']);
$valor_unitario = floatval($producto['valor_con_iva']);
$valor_total = $valor_unitario * $cantidad;
$date = date('Y-m-d');
$time = date('H:i:s');

// Insertar en tabla comandas
$sqlInsert = "INSERT INTO " . TBL_COMANDAS . " (id_comanda, id_producto, cantidad, valor_unitario, valor_total, fecha_servicio, hora_servicio, estado) VALUES ('" . $conexion->real_escape_string($id_comanda) . "', '" . $id_producto_ref . "', $cantidad, $valor_unitario, $valor_total, '$date', '$time', 'activo')";
if ($conexion->query($sqlInsert) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Producto agregado exitosamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al agregar producto']);
}
?>