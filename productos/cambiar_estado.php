<?php
include '../includes/conexion.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Obtener el estado actual del producto
    $sql = "SELECT estado FROM " . TBL_PRODUCTOS . " WHERE id = $id";
    $resultado = $conexion->query($sql);
    
    if ($resultado && $resultado->num_rows > 0) {
        $producto = $resultado->fetch_assoc();
        $nuevoEstado = ($producto['estado'] == 'activo') ? 'inactivo' : 'activo';
        
        // Cambiar el estado
        $sqlUpdate = "UPDATE " . TBL_PRODUCTOS . " SET estado = '$nuevoEstado' WHERE id = $id";
        
        if ($conexion->query($sqlUpdate) === TRUE) {
            header("Location: productos.php?exito=estado_cambiado");
            exit();
        } else {
            header("Location: productos.php?error=cambiar_estado");
            exit();
        }
    } else {
        header("Location: productos.php?error=producto_no_encontrado");
        exit();
    }
} else {
    header("Location: productos.php");
    exit();
}
?>
