<?php
include '../includes/auth.php';
include '../includes/url.php';
include_once '../lang/idiomas.php';
include '../includes/menu.php';

// Verificar si es super-admin sin restaurante asignado
if (isset($_SESSION['rol_master']) && $_SESSION['rol_master'] === 'super-admin' && !isset($_SESSION['id_restaurante'])) {
    ?>
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class="fas fa-exclamation-triangle text-warning"></i> Acceso Restringido</h1>
            </div>
          </div>
        </div>
      </div>
      <section class="content">
        <div class="container-fluid">
          <div class="alert alert-warning">
            <h5><i class="icon fas fa-info-circle"></i> Información</h5>
            Para acceder a esta sección debes estar dando soporte a un restaurante específico.
            <br><br>
            Ve al módulo <a href="../restaurantes/restaurantes.php" class="alert-link"><strong>Restaurantes</strong></a> y selecciona "Dar Soporte" al restaurante que deseas gestionar.
          </div>
        </div>
      </section>
    </div>
    <?php
    include '../includes/footer.php';
    exit();
}

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
?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fas fa-utensils"></i> <?php echo $cocina_titulo; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../home.php"><?php echo $misc_home; ?></a></li>
                        <li class="breadcrumb-item active"><?php echo $menu_cocina; ?></li>
                    </ol>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <button type="button" class="btn btn-secondary btn-sm" id="btnPantallaCompleta">
                        <i class="fas fa-expand"></i> <?php echo $mesa_pantalla_completa; ?>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row" id="contenedorPedidos">
                <?php if (count($servicios_agrupados) > 0): ?>
                    <?php foreach ($servicios_agrupados as $servicio): ?>
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
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-coffee fa-4x text-muted mb-3"></i>
                                <h3 class="text-muted">No hay pedidos activos</h3>
                                <p class="text-muted">Los nuevos pedidos aparecerán aquí automáticamente</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Función para actualizar pedidos
    function actualizarPedidos() {
        $.ajax({
            url: 'obtener_pedidos.php',
            method: 'GET',
            success: function(html) {
                $('#contenedorPedidos').html(html);
            },
            error: function() {
                console.error('Error al actualizar pedidos');
            }
        });
    }

    // Actualizar cada 2 segundos
    setInterval(actualizarPedidos, 2000);

    // Pantalla completa
    let pantallaCompleta = false;
    $('#btnPantallaCompleta').click(function() {
        const contentWrapper = $('.content-wrapper');
        const mainSidebar = $('.main-sidebar');
        const mainHeader = $('.main-header');
        const mainFooter = $('.main-footer');
        const btn = $(this);

        if (!pantallaCompleta) {
            // Activar pantalla completa
            mainSidebar.css('display', 'none');
            mainHeader.css('display', 'none');
            mainFooter.css('display', 'none');
            contentWrapper.attr('style', 'margin-left: 0px !important; margin-top: 0px; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 9999; overflow-y: auto;');
            $('body').addClass('sidebar-collapse');
            btn.html('<i class="fas fa-compress"></i> Salir Pantalla Completa');
            pantallaCompleta = true;
        } else {
            // Desactivar pantalla completa
            mainSidebar.css('display', '');
            mainHeader.css('display', '');
            mainFooter.css('display', '');
            contentWrapper.attr('style', '');
            $('body').removeClass('sidebar-collapse');
            btn.html('<i class="fas fa-expand"></i> Pantalla Completa');
            pantallaCompleta = false;
        }
    });

    // También permitir salir con ESC
    $(document).keyup(function(e) {
        if (e.key === "Escape" && pantallaCompleta) {
            $('#btnPantallaCompleta').click();
        }
    });

    // También permitir salir con ESC
    $(document).keyup(function(e) {
        if (e.key === "Escape" && pantallaCompleta) {
            $('#btnPantallaCompleta').click();
        }
    });
});
</script>

<style>
.card-warning {
    border-top: 3px solid #ffc107;
}

.card-warning .card-header {
    background-color: #ffc107;
    color: #000;
    font-weight: bold;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:last-child {
    border-bottom: none;
}

.badge.rounded-pill {
    padding: 0.5em 0.8em;
}

.btn-finalizar-pedido {
    font-weight: bold;
    text-transform: uppercase;
}

@media (max-width: 768px) {
    .content-wrapper {
        padding: 0;
    }
}
</style>
