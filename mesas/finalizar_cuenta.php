<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();
include '../includes/conexion.php';
ob_end_clean();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $mesa_id = intval($_POST['mesa_id']);
        $total = floatval($_POST['total']);
        $metodo_pago = isset($_POST['metodo_pago']) ? $conexion->real_escape_string($_POST['metodo_pago']) : 'efectivo';
        
        // Validar método de pago
        $metodos_validos = ['efectivo', 'llave', 'nequi', 'daviplata', 'tarjeta'];
        if (!in_array($metodo_pago, $metodos_validos)) {
            echo json_encode(['success' => false, 'message' => 'Método de pago no válido']);
            exit();
        }
        
        // Obtener id_mesa de la mesa
        $sqlMesa = "SELECT id_mesa FROM " . TBL_MESAS . " WHERE id = $mesa_id";
        $resultMesa = $conexion->query($sqlMesa);
        
        if ($resultMesa && $resultMesa->num_rows > 0) {
            $mesa = $resultMesa->fetch_assoc();
            $id_mesa_ref = $conexion->real_escape_string($mesa['id_mesa']);
            
            // Obtener el id_servicio de los productos activos de esta mesa
            $sqlServicio = "SELECT id_servicio, fecha_servicio FROM " . TBL_SERVICIOS . " WHERE id_mesa = '$id_mesa_ref' AND estado = 'activo' LIMIT 1";
            $resultServicio = $conexion->query($sqlServicio);
            
            if ($resultServicio && $resultServicio->num_rows > 0) {
                $servicio = $resultServicio->fetch_assoc();
                $id_servicio = $conexion->real_escape_string($servicio['id_servicio']);
                $fecha_servicio = $servicio['fecha_servicio'];
                
                // Obtener hora de cierre en zona horaria de Colombia
                date_default_timezone_set('America/Bogota');
                $hora_cierre = date('H:i:s');
                
                // Insertar en servicios_total con método de pago
                $sqlTotal = "INSERT INTO " . TBL_SERVICIOS_TOTAL . " (id_servicio, total, metodo_pago, fecha_servicio, hora_cierre_servicio) 
                            VALUES ('$id_servicio', $total, '$metodo_pago', '$fecha_servicio', '$hora_cierre')";
                
                if ($conexion->query($sqlTotal) === TRUE) {
                    // Descontar del inventario los productos vendidos
                    $sqlProductos = "SELECT s.id_producto, s.cantidad, p.id 
                                     FROM " . TBL_SERVICIOS . " s 
                                     INNER JOIN " . TBL_PRODUCTOS . " p ON s.id_producto = p.id_producto 
                                     WHERE s.id_servicio = '$id_servicio' AND s.estado = 'activo'";
                    $resultProductos = $conexion->query($sqlProductos);
                    
                    if ($resultProductos && $resultProductos->num_rows > 0) {
                        while ($producto = $resultProductos->fetch_assoc()) {
                            $producto_id = intval($producto['id']); // ID numérico de la tabla productos
                            $cantidad = intval($producto['cantidad']);
                            
                            // Descontar del inventario
                            $sqlUpdateInventario = "UPDATE " . TBL_PRODUCTOS . " SET inventario = inventario - $cantidad WHERE id = $producto_id";
                            $conexion->query($sqlUpdateInventario);
                        }
                    }
                    
                    // Actualizar estado de los servicios a finalizado solo para el id_servicio actual
                    $sqlUpdateServicios = "UPDATE " . TBL_SERVICIOS . " SET estado = 'finalizado' WHERE id_servicio = '$id_servicio' AND estado = 'activo'";
                    $conexion->query($sqlUpdateServicios);
                    
                    // Actualizar estado de la mesa a libre
                    $sqlUpdateMesa = "UPDATE " . TBL_MESAS . " SET estado = 'libre' WHERE id = $mesa_id";
                    $conexion->query($sqlUpdateMesa);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Cuenta cerrada exitosamente'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al guardar el cierre de cuenta: ' . $conexion->error
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontró el servicio de la mesa'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Mesa no encontrada'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>
