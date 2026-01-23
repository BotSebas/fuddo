<?php
session_start();
include '../includes/conexion.php';

// Obtener todos los servicios activos agrupados por mesa
$sql = "SELECT 
            s.id_servicio,
            s.id_mesa,
            m.nombre as nombre_mesa,
            s.id_producto,
            p.nombre_producto,
            s.cantidad,
            s.valor_unitario,
            s.valor_total,
            s.fecha_servicio,
            s.hora_servicio,
            s.estado
        FROM " . TBL_SERVICIOS . " s
        INNER JOIN " . TBL_MESAS . " m ON s.id_mesa = m.id_mesa
        LEFT JOIN " . TBL_PRODUCTOS . " p ON s.id_producto = p.id_producto
        WHERE s.estado = 'activo'
        ORDER BY s.id_servicio, s.id";

$resultado = $conexion->query($sql);

// Agrupar por servicio/mesa
$servicios_agrupados = [];
if ($resultado && $resultado->num_rows > 0) {
    while($row = $resultado->fetch_assoc()) {
        $id_servicio = $row['id_servicio'];
        if (!isset($servicios_agrupados[$id_servicio])) {
            $servicios_agrupados[$id_servicio] = [
                'id_servicio' => $id_servicio,
                'id_mesa' => $row['id_mesa'],
                'nombre_mesa' => $row['nombre_mesa'],
                'fecha_servicio' => $row['fecha_servicio'],
                'hora_servicio' => $row['hora_servicio'],
                'productos' => [],
                'total' => 0
            ];
        }
        
        $servicios_agrupados[$id_servicio]['productos'][] = [
            'nombre' => $row['nombre_producto'],
            'cantidad' => $row['cantidad'],
            'valor_unitario' => $row['valor_unitario'],
            'valor_total' => $row['valor_total']
        ];
        
        $servicios_agrupados[$id_servicio]['total'] += $row['valor_total'];
    }
}

// Generar HTML de los pedidos
if (count($servicios_agrupados) > 0) {
    foreach ($servicios_agrupados as $servicio) {
        ?>
        <div class="col-lg-4 col-md-6 col-12 mb-3">
            <div class="card card-warning card-outline h-100">
                <div class="card-header bg-warning">
                    <h3 class="card-title">
                        <i class="fas fa-concierge-bell"></i>
                        <strong><?php echo strtoupper($servicio['nombre_mesa']); ?></strong>
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light">
                            <?php echo date('H:i', strtotime($servicio['hora_servicio'])); ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($servicio['productos'] as $producto): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">
                                        <i class="fas fa-utensils text-muted"></i>
                                        <?php echo htmlspecialchars($producto['nombre']); ?>
                                    </div>
                                </div>
                                <span class="badge bg-primary rounded-pill" style="font-size: 1.1em;">
                                    x<?php echo $producto['cantidad']; ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    ?>
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-coffee fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">No hay pedidos activos</h3>
                <p class="text-muted">Los nuevos pedidos aparecerán aquí automáticamente</p>
            </div>
        </div>
    </div>
    <?php
}

$conexion->close();
?>
