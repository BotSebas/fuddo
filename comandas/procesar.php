<?php
include '../includes/conexion.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = trim($_POST['descripcion'] ?? '');
    $total = floatval($_POST['total'] ?? 0);
    if ($descripcion !== '' && $total > 0) {
        $stmt = $conexion->prepare("INSERT INTO {$TABLE_PREFIX}comandas (descripcion, total, fecha_creacion) VALUES (?, ?, NOW())");
        $stmt->bind_param('sd', $descripcion, $total);
        if ($stmt->execute()) {
            header('Location: comandas.php?exito=1');
            exit();
        } else {
            header('Location: comandas.php?error=1');
            exit();
        }
    } else {
        header('Location: comandas.php?error=1');
        exit();
    }
}
header('Location: comandas.php');
exit();
