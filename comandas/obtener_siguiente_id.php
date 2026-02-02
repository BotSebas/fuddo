<?php
include '../includes/conexion.php';
header('Content-Type: application/json');

// Contar cuántas comandas hay en total en comandas_total
$sql = "SELECT COUNT(DISTINCT id_comanda) as total FROM " . TBL_COMANDAS_TOTAL;
$result = $conexion->query($sql);

if ($result) {
    $row = $result->fetch_assoc();
    $total = intval($row['total']);
    $siguiente_id = $total + 1;
    
    echo json_encode([
        'success' => true,
        'siguiente_id' => $siguiente_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'siguiente_id' => 1
    ]);
}
?>
