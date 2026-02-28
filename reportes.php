<?php
include 'includes/auth.php';
include 'includes/url.php';
include_once 'lang/idiomas.php';

// Verificar permiso del módulo antes de incluir menú
if (!tienePermisoModulo('reportes')) {
    header("Location: home.php");
    exit();
}

include 'includes/menu.php';

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
            Ve al módulo <a href="restaurantes/restaurantes.php" class="alert-link"><strong>Restaurantes</strong></a> y selecciona "Dar Soporte" al restaurante que deseas gestionar.
          </div>
        </div>
      </section>
    </div>
    <?php
    include 'includes/footer.php';
    exit();
}

include 'includes/conexion.php';

// Establecer zona horaria de Colombia
date_default_timezone_set('America/Bogota');

// Obtener parámetros de filtro
$periodo = isset($_GET['periodo']) ? $_GET['periodo'] : 'dia';
$fecha_especifica = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Calcular rango de fechas según el período
$fecha_inicio = '';
$fecha_fin = '';

switch($periodo) {
    case 'dia':
        $fecha_inicio = $fecha_especifica;
        $fecha_fin = $fecha_especifica;
        break;
    case 'semana':
        // Calcular inicio y fin de la semana
        $fecha_obj = new DateTime($fecha_especifica);
        $dia_semana = $fecha_obj->format('N'); // 1 (lunes) a 7 (domingo)
        $fecha_obj->modify('-' . ($dia_semana - 1) . ' days'); // Ir al lunes
        $fecha_inicio = $fecha_obj->format('Y-m-d');
        $fecha_obj->modify('+6 days'); // Ir al domingo
        $fecha_fin = $fecha_obj->format('Y-m-d');
        break;
    case 'mes':
        $fecha_obj = new DateTime($fecha_especifica);
        $fecha_inicio = $fecha_obj->format('Y-m-01'); // Primer día del mes
        $fecha_fin = $fecha_obj->format('Y-m-t'); // Último día del mes
        break;
}

// Consulta para totales de ventas
$tabla_servicios_total = TBL_SERVICIOS_TOTAL;
$sql_totales = "SELECT 
                COUNT(DISTINCT id_servicio) as num_servicios,
                SUM(total) as total_ventas,
                AVG(total) as ticket_promedio
                FROM $tabla_servicios_total 
                WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$result_totales = $conexion->query($sql_totales);
$totales = $result_totales->fetch_assoc();

// Consulta para ventas por método de pago
$sql_metodos = "SELECT 
                metodo_pago,
                COUNT(*) as cantidad,
                SUM(total) as total
                FROM $tabla_servicios_total 
                WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                GROUP BY metodo_pago
                ORDER BY total DESC";
$result_metodos = $conexion->query($sql_metodos);

// Consulta para productos más vendidos
$tabla_servicios = TBL_SERVICIOS;
$tabla_productos = TBL_PRODUCTOS;
$sql_productos = "SELECT 
                  p.nombre_producto,
                  SUM(s.cantidad) as cantidad_vendida,
                  SUM(s.valor_total) as total_ventas
                  FROM $tabla_servicios s
                  INNER JOIN $tabla_productos p ON s.id_producto = p.id_producto
                  WHERE s.estado = 'finalizado' 
                  AND s.fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                  GROUP BY s.id_producto, p.nombre_producto
                  ORDER BY cantidad_vendida DESC
                  LIMIT 10";
$result_productos = $conexion->query($sql_productos);

// Consulta para ventas por día (para gráfica)
$sql_dias = "SELECT 
             fecha_servicio,
             COUNT(DISTINCT id_servicio) as num_servicios,
             SUM(total) as total
             FROM $tabla_servicios_total 
             WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
             GROUP BY fecha_servicio
             ORDER BY fecha_servicio ASC";
$result_dias = $conexion->query($sql_dias);
?>
  <div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><i class="fas fa-chart-line"></i> <?php echo $reportes_titulo; ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?php echo $misc_home; ?></a></li>
              <li class="breadcrumb-item active"><?php echo $menu_reportes; ?></li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- Info Box -->
        <div class="row mb-3">
          <div class="col-md-12">
            <div class="alert alert-info">
              <h5><i class="fas fa-info-circle"></i> <?php echo $reportes_disponibles; ?></h5>
              <?php echo $reportes_seleccione; ?>
            </div>
          </div>
        </div>

        <!-- Reportes Disponibles -->
        <div class="row">
          
          <!-- Cierre de Caja -->
          <?php if (tieneReporte('cierre_caja')): ?>
          <div class="col-lg-4 col-md-6">
            <div class="card">
              <div class="card-header" style="background-color: #27ae60; color: white;">
                <h3 class="card-title"><i class="fas fa-cash-register"></i> <?php echo $reportes_cierre_detallado; ?></h3>
              </div>
              <div class="card-body">
                <p><?php echo $reportes_cierre_desc; ?></p>
                <ul>
                  <li><?php echo $reportes_cierre_desglose; ?></li>
                  <li><?php echo $reportes_cierre_analisis; ?></li>
                  <li><?php echo $reportes_cierre_detalle; ?></li>
                  <li><?php echo $reportes_cierre_tickets; ?></li>
                  <li><?php echo $reportes_cierre_excel; ?></li>
                </ul>
                <a href="reportes/cierre_caja.php" class="btn btn-success btn-block">
                  <i class="fas fa-eye"></i> <?php echo $msg_ver_reporte; ?>
                </a>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <!-- Inventario Valorizado -->
          <?php if (tieneReporte('inventario_valorizado')): ?>
          <div class="col-lg-4 col-md-6">
            <div class="card">
              <div class="card-header" style="background-color: #27ae60; color: white;">
                <h3 class="card-title"><i class="fas fa-warehouse"></i> <?php echo $reportes_inventario_titulo; ?></h3>
              </div>
              <div class="card-body">
                <p><?php echo $reportes_inventario_desc; ?></p>
                <ul>
                  <li><?php echo $reportes_inventario_valor; ?></li>
                  <li><?php echo $reportes_inventario_alertas; ?></li>
                  <li><?php echo $reportes_inventario_sin; ?></li>
                  <li><?php echo $reportes_inventario_valorizacion; ?></li>
                  <li><?php echo $reportes_cierre_exportable; ?></li>
                </ul>
                <a href="reportes/inventario_valorizado.php" class="btn btn-success btn-block">
                  <i class="fas fa-eye"></i> <?php echo $reportes_ver_reporte; ?>
                </a>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <!-- Resumen de Ventas (Actual) -->
          <div class="col-lg-4 col-md-6">
            <div class="card">
              <div class="card-header" style="background-color: #27ae60; color: white;">
                <h3 class="card-title"><i class="fas fa-chart-bar"></i> <?php echo $reportes_resumen_ventas; ?></h3>
              </div>
              <div class="card-body">
                <p><?php echo $reportes_vista_rapida; ?></p>
                <ul>
                  <li><?php echo $reportes_total_ventas; ?></li>
                  <li><?php echo $reportes_servicios; ?></li>
                  <li><?php echo $reportes_ticket_promedio; ?></li>
                  <li><?php echo $reportes_top10; ?></li>
                  <li><?php echo $reportes_grafica; ?></li>
                </ul>
                <button class="btn btn-success btn-block" onclick="scrollToResumen()">
                  <i class="fas fa-eye"></i> <?php echo $reportes_ver_resumen; ?>
                </button>
              </div>
            </div>
          </div>

        </div>
        
        <!-- Mensaje si no tiene permisos de reportes -->
        <?php
        if (!tieneReporte('cierre_caja') && !tieneReporte('inventario_valorizado') && $_SESSION['rol'] !== 'super-admin'):
        ?>
        <div class="row">
          <div class="col-12">
            <div class="alert alert-warning">
              <h5><i class="fas fa-exclamation-triangle"></i> <?php echo $reportes_sin_asignados; ?></h5>
              <?php echo $reportes_sin_permisos; ?>
            </div>
          </div>
        </div>
        <?php endif; ?>

        <hr class="my-4">

        <!-- Resumen de Ventas (contenido actual) -->
        <div id="resumenVentas">
        <h3 class="mb-3"><i class="fas fa-chart-bar"></i> <?php echo $reportes_resumen_rapido; ?></h3>
        
        <div class="card">
          <div class="card-header" style="background-color: #27ae60; color: white;">
            <h3 class="card-title"><i class="fas fa-filter"></i> <?php echo $reportes_filtros; ?></h3>
          </div>
          <div class="card-body">
            <form method="GET" action="">
              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label><?php echo $reportes_periodo; ?></label>
                    <select name="periodo" class="form-control" id="periodo" onchange="this.form.submit()">
                      <option value="dia" <?php echo $periodo == 'dia' ? 'selected' : ''; ?>><?php echo $reporte_dia; ?></option>
                      <option value="semana" <?php echo $periodo == 'semana' ? 'selected' : ''; ?>><?php echo $reporte_semana; ?></option>
                      <option value="mes" <?php echo $periodo == 'mes' ? 'selected' : ''; ?>><?php echo $reporte_mes; ?></option>
                    </select>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label><?php echo $reportes_fecha; ?></label>
                    <input type="date" name="fecha" class="form-control" value="<?php echo $fecha_especifica; ?>" onchange="this.form.submit()">
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label>&nbsp;</label>
                    <div>
                      <button type="submit" class="btn btn-success">
                        <i class="fas fa-search"></i> <?php echo $btn_buscar; ?>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="alert alert-info">
                    <strong><?php echo $reportes_rango_seleccionado; ?>:</strong> 
                    <?php 
                    echo date('d/m/Y', strtotime($fecha_inicio)); 
                    if ($fecha_inicio != $fecha_fin) {
                        echo ' al ' . date('d/m/Y', strtotime($fecha_fin));
                    }
                    ?>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="row">
          <div class="col-lg-4 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <h3>$<?php echo number_format($totales['total_ventas'] ?? 0, 0, ',', '.'); ?></h3>
                <p><?php echo $reportes_total_ventas; ?></p>
              </div>
              <div class="icon">
                <i class="fas fa-dollar-sign"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $totales['num_servicios'] ?? 0; ?></h3>
                <p><?php echo $reportes_servicios; ?></p>
              </div>
              <div class="icon">
                <i class="fas fa-receipt"></i>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>$<?php echo number_format($totales['ticket_promedio'] ?? 0, 0, ',', '.'); ?></h3>
                <p><?php echo $reportes_ticket_promedio; ?></p>
              </div>
              <div class="icon">
                <i class="fas fa-chart-bar"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-md-6">
            <!-- VENTAS POR MÉTODO DE PAGO -->
            <div class="card">
              <div class="card-header" style="background-color: #27ae60; color: white;">
                <h3 class="card-title"><i class="fas fa-credit-card"></i> <?php echo $reportes_ventas_metodo; ?></h3>
              </div>
              <div class="card-body">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th><?php echo $reportes_metodo; ?></th>
                      <th><?php echo $reportes_cantidad; ?></th>
                      <th><?php echo $reportes_total; ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    if ($result_metodos && $result_metodos->num_rows > 0) {
                        while($metodo = $result_metodos->fetch_assoc()) {
                            $nombre_metodo = ucfirst($metodo['metodo_pago']);
                            echo "<tr>";
                            echo "<td><strong>$nombre_metodo</strong></td>";
                            echo "<td>{$metodo['cantidad']}</td>";
                            echo "<td>$" . number_format($metodo['total'], 0, ',', '.') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No hay datos para este período</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-md-6">
            <!-- PRODUCTOS MÁS VENDIDOS -->
            <div class="card">
              <div class="card-header" style="background-color: #27ae60; color: white;">
                <h3 class="card-title"><i class="fas fa-trophy"></i> <?php echo $reportes_top10; ?></h3>
              </div>
              <div class="card-body">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th><?php echo $mesas_producto; ?></th>
                      <th><?php echo $reportes_cantidad; ?></th>
                      <th><?php echo $reportes_total; ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                    if ($result_productos && $result_productos->num_rows > 0) {
                        while($producto = $result_productos->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>{$producto['nombre_producto']}</td>";
                            echo "<td>{$producto['cantidad_vendida']}</td>";
                            echo "<td>$" . number_format($producto['total_ventas'], 0, ',', '.') . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>No hay datos para este período</td></tr>";
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Gráfica de Ventas por Día -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header" style="background-color: #27ae60; color: white;">
                <h3 class="card-title"><i class="fas fa-chart-line"></i> <?php echo $reportes_tendencia; ?></h3>
              </div>
              <div class="card-body">
                <canvas id="ventasChart" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
              </div>
            </div>
          </div>
        </div>

      </div>
      </div><!-- fin resumenVentas -->
      
    </div>
    </section>
  </div>

<script>
// Datos para la gráfica de ventas por día
const ventasData = {
  labels: [
    <?php 
    if ($result_dias && $result_dias->num_rows > 0) {
        $result_dias->data_seek(0); // Reset pointer
        $labels = [];
        while($dia = $result_dias->fetch_assoc()) {
            $labels[] = "'" . date('d/m', strtotime($dia['fecha_servicio'])) . "'";
        }
        echo implode(',', $labels);
    }
    ?>
  ],
  datasets: [{
    label: '<?php echo $reportes_ventas_dinero; ?>',
    data: [
      <?php 
      if ($result_dias && $result_dias->num_rows > 0) {
          $result_dias->data_seek(0);
          $valores = [];
          while($dia = $result_dias->fetch_assoc()) {
              $valores[] = $dia['total'];
          }
          echo implode(',', $valores);
      }
      ?>
    ],
    backgroundColor: 'rgba(39, 174, 96, 0.2)',
    borderColor: 'rgba(39, 174, 96, 1)',
    borderWidth: 2,
    fill: true,
    tension: 0.4
  }]
};

// Configuración del Chart
const ventasConfig = {
  type: 'line',
  data: ventasData,
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        position: 'top',
      },
      title: {
        display: false
      }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            return '$' + value.toLocaleString('es-CO');
          }
        }
      }
    }
  }
};

// Renderizar gráfica
document.addEventListener('DOMContentLoaded', function() {
  const ctx = document.getElementById('ventasChart');
  if (ctx) {
    new Chart(ctx, ventasConfig);
  }
});

// Función para scroll al resumen
function scrollToResumen() {
  document.getElementById('resumenVentas').scrollIntoView({ behavior: 'smooth' });
}
</script>

<?php include 'includes/footer.php'; ?>