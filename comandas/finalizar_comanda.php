<?php
include '../includes/conexion.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $comanda_id = intval($_POST['comanda_id'] ?? 0);
        $total = floatval($_POST['total'] ?? 0);
        $metodo_pago = isset($_POST['metodo_pago']) ? $conexion->real_escape_string($_POST['metodo_pago']) : 'efectivo';

        // Validar método de pago
        $metodos_validos = ['efectivo', 'llave', 'nequi', 'daviplata', 'tarjeta'];
        if (!in_array($metodo_pago, $metodos_validos)) {
            echo json_encode(['success' => false, 'message' => 'Método de pago no válido']);
            exit();
        }

        if ($comanda_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de comanda inválido']);
            exit();
        }

        // Generar id_comanda como 'CMD-<id>'
        $id_comanda = 'CMD-' . $comanda_id;

        // Buscar productos de la comanda
        $sqlComanda = "SELECT fecha_servicio FROM " . TBL_COMANDAS . " WHERE id_comanda = '" . $conexion->real_escape_string($id_comanda) . "' AND estado = 'activo' LIMIT 1";
        $resultComanda = $conexion->query($sqlComanda);
        if (!($resultComanda && $resultComanda->num_rows > 0)) {
            echo json_encode(['success' => false, 'message' => 'No se encontraron productos en la comanda']);
            exit();
        }

        $comanda = $resultComanda->fetch_assoc();
        $fecha_servicio = $comanda['fecha_servicio'];

        // Obtener hora de cierre en zona horaria de Colombia
        date_default_timezone_set('America/Bogota');
        $hora_cierre = date('H:i:s');

        // Insertar en comandas_total con método de pago
        $sqlTotal = "INSERT INTO " . TBL_COMANDAS_TOTAL . " (id_comanda, total, metodo_pago, fecha_comanda, hora_cierre_comanda) 
                    VALUES ('" . $conexion->real_escape_string($id_comanda) . "', $total, '" . $metodo_pago . "', '" . $fecha_servicio . "', '" . $hora_cierre . "')";

        if ($conexion->query($sqlTotal) === TRUE) {
            // Descontar del inventario los productos vendidos
            $sqlProductos = "SELECT c.id_producto, c.cantidad, p.id 
                             FROM " . TBL_COMANDAS . " c 
                             INNER JOIN " . TBL_PRODUCTOS . " p ON c.id_producto = p.id_producto 
                             WHERE c.id_comanda = '" . $conexion->real_escape_string($id_comanda) . "' AND c.estado = 'activo'";
            $resultProductos = $conexion->query($sqlProductos);

            if ($resultProductos && $resultProductos->num_rows > 0) {
                while ($producto = $resultProductos->fetch_assoc()) {
                    $producto_id = intval($producto['id']);
                    $cantidad = intval($producto['cantidad']);
                    $sqlUpdateInventario = "UPDATE " . TBL_PRODUCTOS . " SET inventario = inventario - $cantidad WHERE id = $producto_id";
                    $conexion->query($sqlUpdateInventario);
                }
            }

            // Actualizar estado de los productos de la comanda a finalizado
            $sqlUpdateComandas = "UPDATE " . TBL_COMANDAS . " SET estado = 'finalizado' WHERE id_comanda = '" . $conexion->real_escape_string($id_comanda) . "' AND estado = 'activo'";
            $conexion->query($sqlUpdateComandas);

            echo json_encode([
                'success' => true,
                'message' => 'Comanda finalizada exitosamente'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al guardar el cierre de comanda: ' . $conexion->error
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
    exit();
}
echo json_encode([
    'success' => false,
    'message' => 'Método no permitido'
]);
?>
