<?php
include '../includes/conexion.php';
header('Content-Type: application/json');

$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}
// Marcar como cancelado en tabla comandas
$sql = "UPDATE " . TBL_COMANDAS . " SET estado = 'cancelado' WHERE id = $id";
if ($conexion->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Producto eliminado']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
}
?>