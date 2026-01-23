<?php
include '../includes/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mesa_id = intval($_POST['mesa_id']);
    $producto_id = intval($_POST['producto_id']);
    $cantidad = intval($_POST['cantidad']);
    
    // Obtener id_mesa de la mesa
    $sqlMesa = "SELECT id_mesa FROM " . TBL_MESAS . " WHERE id = $mesa_id";
    $resultMesa = $conexion->query($sqlMesa);
    
    if ($resultMesa && $resultMesa->num_rows > 0) {
        $mesa = $resultMesa->fetch_assoc();
        $id_mesa_ref = $conexion->real_escape_string($mesa['id_mesa']);
        
        // Verificar si la mesa ya tiene un servicio activo
        $sqlServicioActivo = "SELECT id_servicio FROM " . TBL_SERVICIOS . " WHERE id_mesa = '$id_mesa_ref' AND estado = 'activo' LIMIT 1";
        $resultServicio = $conexion->query($sqlServicioActivo);
        
        if ($resultServicio && $resultServicio->num_rows > 0) {
            // Usar el id_servicio existente
            $servicioRow = $resultServicio->fetch_assoc();
            $id_servicio = $servicioRow['id_servicio'];
        } else {
            // Generar nuevo id_servicio
            $sqlCountServicios = "SELECT COUNT(DISTINCT id_servicio) as total FROM " . TBL_SERVICIOS;
            $resultCountServicios = $conexion->query($sqlCountServicios);
            $countServicios = $resultCountServicios->fetch_assoc()['total'];
            $siguiente_servicio = $countServicios + 1;
            $id_servicio = "SR-" . $siguiente_servicio;
        }
        
        // Obtener información del producto y verificar inventario
        $sqlProducto = "SELECT id_producto, valor_con_iva, inventario FROM " . TBL_PRODUCTOS . " WHERE id = $producto_id";
        $resultProducto = $conexion->query($sqlProducto);
        
        if ($resultProducto && $resultProducto->num_rows > 0) {
            $producto = $resultProducto->fetch_assoc();
            $inventario_actual = intval($producto['inventario']);
            
            // Verificar que haya stock disponible
            if ($inventario_actual <= 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Producto sin stock disponible'
                ]);
                exit;
            }
            
            // Verificar que la cantidad solicitada no exceda el inventario
            if ($cantidad > $inventario_actual) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Stock insuficiente. Disponible: ' . $inventario_actual
                ]);
                exit;
            }
            
            $id_producto_ref = $conexion->real_escape_string($producto['id_producto']);
            $valor_unitario = floatval($producto['valor_con_iva']);
            $valor_total = $valor_unitario * $cantidad;
            
            // Obtener fecha y hora actual de Colombia
            date_default_timezone_set('America/Bogota');
            $fecha_servicio = date('Y-m-d');
            $hora_servicio = date('H:i:s');
            
            // Insertar en la tabla servicios
            $sql = "INSERT INTO " . TBL_SERVICIOS . " (id_servicio, id_mesa, id_producto, cantidad, valor_unitario, valor_total, fecha_servicio, hora_servicio, estado) 
                    VALUES ('$id_servicio', '$id_mesa_ref', '$id_producto_ref', $cantidad, $valor_unitario, $valor_total, '$fecha_servicio', '$hora_servicio', 'activo')";
            
            if ($conexion->query($sql) === TRUE) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Producto agregado exitosamente'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al agregar el producto'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Producto no encontrado'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Mesa no encontrada'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>
