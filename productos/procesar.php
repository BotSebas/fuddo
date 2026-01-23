<?php
include '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nombre_producto = $conexion->real_escape_string($_POST['nombre_producto']);
    $valor_sin_iva = floatval($_POST['valor_sin_iva']);
    $valor_con_iva = floatval($_POST['valor_con_iva']);
    $inventario = isset($_POST['inventario']) ? intval($_POST['inventario']) : 0;
    
    // Validar que el inventario no sea negativo
    if ($inventario < 0) {
        header("Location: productos.php?error=inventario_negativo");
        exit();
    }
    
    if ($id > 0) {
        // Actualizar producto existente
        $sql = "UPDATE " . TBL_PRODUCTOS . " SET 
                nombre_producto = '$nombre_producto', 
                valor_sin_iva = $valor_sin_iva, 
                valor_con_iva = $valor_con_iva,
                inventario = $inventario
                WHERE id = $id";
        
        if ($conexion->query($sql) === TRUE) {
            header("Location: productos.php?exito=actualizado");
            exit();
        } else {
            header("Location: productos.php?error=actualizar");
            exit();
        }
    } else {
        // Crear nuevo producto
        // Contar productos existentes para generar el siguiente id_producto
        $sqlCount = "SELECT COUNT(*) as total FROM " . TBL_PRODUCTOS;
        $resultCount = $conexion->query($sqlCount);
        $count = $resultCount->fetch_assoc()['total'];
        $siguiente_numero = $count + 1;
        $id_producto = "PR-" . $siguiente_numero;
        
        $sql = "INSERT INTO " . TBL_PRODUCTOS . " (id_producto, nombre_producto, valor_sin_iva, valor_con_iva, inventario, estado) 
                VALUES ('$id_producto', '$nombre_producto', $valor_sin_iva, $valor_con_iva, $inventario, 'activo')";
        
        if ($conexion->query($sql) === TRUE) {
            header("Location: productos.php?exito=creado");
            exit();
        } else {
            header("Location: productos.php?error=crear");
            exit();
        }
    }
} else {
    header("Location: productos.php");
    exit();
}
?>
