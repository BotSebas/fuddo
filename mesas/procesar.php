<?php
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $ubicacion = $conexion->real_escape_string($_POST['ubicacion']);
    $estado = "libre";
    
    // Contar mesas existentes para generar el siguiente id_mesa
    $sqlCount = "SELECT COUNT(*) as total FROM " . TBL_MESAS;
    $resultCount = $conexion->query($sqlCount);
    $count = $resultCount->fetch_assoc()['total'];
    $siguiente_numero = $count + 1;
    $id_mesa = "ME-" . $siguiente_numero;
    
    $sql = "INSERT INTO " . TBL_MESAS . " (id_mesa, nombre, ubicacion, estado) VALUES ('$id_mesa', '$nombre', '$ubicacion', '$estado')";
    
    if ($conexion->query($sql) === TRUE) {
        header("Location: mesas.php?exito=1");
        exit();
    } else {
        header("Location: mesas.php?error=1");
        exit();
    }
} else {
    header("Location: mesas.php");
    exit();
}
?>
