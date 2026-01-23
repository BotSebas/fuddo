<?php
include '../includes/conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Eliminar el producto
    $sql = "DELETE FROM " . TBL_PRODUCTOS . " WHERE id = $id";
    
    if ($conexion->query($sql) === TRUE) {
        header("Location: productos.php?exito=eliminado");
        exit();
    } else {
        header("Location: productos.php?error=eliminar");
        exit();
    }
} else {
    header("Location: productos.php");
    exit();
}
?>
