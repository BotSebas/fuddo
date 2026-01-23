<?php
include '../includes/auth.php';
include '../includes/conexion.php';
include_once '../lang/idiomas.php';

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
        $fecha_obj = new DateTime($fecha_especifica);
        $dia_semana = $fecha_obj->format('N');
        $fecha_obj->modify('-' . ($dia_semana - 1) . ' days');
        $fecha_inicio = $fecha_obj->format('Y-m-d');
        $fecha_obj->modify('+6 days');
        $fecha_fin = $fecha_obj->format('Y-m-d');
        break;
    case 'mes':
        $fecha_obj = new DateTime($fecha_especifica);
        $fecha_inicio = $fecha_obj->format('Y-m-01');
        $fecha_fin = $fecha_obj->format('Y-m-t');
        break;
}

// SI ES EXPORTACIÓN, PROCESARLA Y SALIR ANTES DE CARGAR EL MENU
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    // Consultas para exportación
    $sql_totales = "SELECT 
                    COUNT(DISTINCT id_servicio) as num_servicios,
                    SUM(total) as total_ventas,
                    AVG(total) as ticket_promedio,
                    MIN(total) as ticket_minimo,
                    MAX(total) as ticket_maximo
                    FROM " . TBL_SERVICIOS_TOTAL . " 
                    WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    $result_totales = $conexion->query($sql_totales);
    $totales = $result_totales->fetch_assoc();

    $sql_metodos = "SELECT 
                    metodo_pago,
                    COUNT(DISTINCT id_servicio) as num_transacciones,
                    SUM(total) as total_ventas
                    FROM " . TBL_SERVICIOS_TOTAL . " 
                    WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                    GROUP BY metodo_pago
                    ORDER BY total_ventas DESC";
    $result_metodos = $conexion->query($sql_metodos);

    $sql_dias = "SELECT 
                 fecha_servicio,
                 COUNT(DISTINCT id_servicio) as num_servicios,
                 SUM(total) as total_ventas
                 FROM " . TBL_SERVICIOS_TOTAL . " 
                 WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                 GROUP BY fecha_servicio
                 ORDER BY fecha_servicio";
    $result_dias = $conexion->query($sql_dias);

    $sql_productos = "SELECT 
                      p.nombre_producto,
                      SUM(s.cantidad) as cantidad_vendida,
                      p.valor_con_iva as precio_unitario,
                      SUM(s.valor_total) as total_ventas,
                      COUNT(DISTINCT s.id_servicio) as num_servicios
                      FROM " . TBL_SERVICIOS . " s
                      INNER JOIN " . TBL_PRODUCTOS . " p ON s.id_producto = p.id_producto
                      WHERE s.estado = 'finalizado' 
                      AND s.fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                      GROUP BY s.id_producto, p.nombre_producto, p.valor_con_iva
                      ORDER BY total_ventas DESC";
    $result_productos = $conexion->query($sql_productos);

    // Enviar headers para Excel
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment;filename="cierre_caja_' . $fecha_inicio . '_' . $fecha_fin . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
    echo '<body>';
    echo '<h1>' . strtoupper($cierre_reporte_titulo) . '</h1>';
    echo '<p>' . ucfirst($cierre_periodo) . ' ' . ucfirst($periodo) . ' | ' . $cierre_rango . ' ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) . '</p>';
    
    echo '<h2>' . strtoupper($cierre_resumen_general) . '</h2>';
    echo '<table border="1">';
    echo '<tr><th>' . $cierre_numero_servicios . '</th><td>' . number_format($totales['num_servicios']) . '</td></tr>';
    echo '<tr><th>' . $cierre_total_ventas . '</th><td>$' . number_format($totales['total_ventas'], 0, ',', '.') . '</td></tr>';
    echo '<tr><th>' . $cierre_ticket_promedio . '</th><td>$' . number_format($totales['ticket_promedio'], 0, ',', '.') . '</td></tr>';
    echo '<tr><th>' . $cierre_ticket_minimo . '</th><td>$' . number_format($totales['ticket_minimo'], 0, ',', '.') . '</td></tr>';
    echo '<tr><th>' . $cierre_ticket_maximo . '</th><td>$' . number_format($totales['ticket_maximo'], 0, ',', '.') . '</td></tr>';
    echo '</table><br>';
    
    echo '<h2>' . strtoupper($cierre_ventas_metodo) . '</h2>';
    echo '<table border="1">';
    echo '<tr><th>' . $cierre_metodo_pago . '</th><th>' . $cierre_transacciones . '</th><th>' . $cierre_total . '</th></tr>';
    while ($metodo = $result_metodos->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . ucfirst($metodo['metodo_pago']) . '</td>';
        echo '<td>' . number_format($metodo['num_transacciones']) . '</td>';
        echo '<td>$' . number_format($metodo['total_ventas'], 0, ',', '.') . '</td>';
        echo '</tr>';
    }
    echo '</table><br>';
    
    echo '<h2>' . strtoupper($cierre_ventas_dia) . '</h2>';
    echo '<table border="1">';
    echo '<tr><th>' . $cierre_fecha . '</th><th>' . $cierre_servicios . '</th><th>' . $cierre_total_ventas . '</th></tr>';
    while ($dia = $result_dias->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . date('d/m/Y', strtotime($dia['fecha_servicio'])) . '</td>';
        echo '<td>' . number_format($dia['num_servicios']) . '</td>';
        echo '<td>$' . number_format($dia['total_ventas'], 0, ',', '.') . '</td>';
        echo '</tr>';
    }
    echo '</table><br>';
    
    echo '<h2>' . strtoupper($cierre_productos_vendidos) . '</h2>';
    echo '<table border="1">';
    echo '<tr><th>' . $cierre_producto . '</th><th>' . $cierre_cantidad . '</th><th>' . $cierre_precio . '</th><th>' . $cierre_total . '</th><th>' . $cierre_servicios . '</th></tr>';
    while ($prod = $result_productos->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($prod['nombre_producto']) . '</td>';
        echo '<td>' . number_format($prod['cantidad_vendida']) . '</td>';
        echo '<td>$' . number_format($prod['precio_unitario'], 0, ',', '.') . '</td>';
        echo '<td>$' . number_format($prod['total_ventas'], 0, ',', '.') . '</td>';
        echo '<td>' . number_format($prod['num_servicios']) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    
    echo '</body></html>';
    exit();
}

// Si no es exportación, cargar el layout normal
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
              <h1 class="m-0"><i class="fas fa-exclamation-triangle text-warning"></i> <?php echo $msg_acceso_restringido; ?></h1>
            </div>
          </div>
        </div>
      </div>
      <section class="content">
        <div class="container-fluid">
          <div class="alert alert-warning">
            <h5><i class="icon fas fa-info-circle"></i> <?php echo $msg_informacion; ?></h5>
            <?php echo $msg_soporte_restaurante; ?>
            <br><br>
            <?php echo $msg_ve_modulo; ?> <a href="../restaurantes/restaurantes.php" class="alert-link"><strong><?php echo $msg_restaurantes; ?></strong></a> <?php echo $msg_dar_soporte; ?>
          </div>
        </div>
      </section>
    </div>
    <?php
    include '../includes/footer.php';
    exit();
}

include '../includes/conexion.php';

// Verificar permiso de acceso al reporte
if (!tieneReporte('cierre_caja')) {
    ?>
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class="fas fa-ban text-danger"></i> <?php echo $msg_acceso_denegado; ?></h1>
            </div>
          </div>
        </div>
      </div>
      <section class="content">
        <div class="container-fluid">
          <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> <?php echo $msg_sin_permisos; ?></h5>
            <?php echo $msg_sin_permisos_desc; ?>
            <br><br>
            <a href="../reportes.php" class="btn btn-secondary">
              <i class="fas fa-arrow-left"></i> <?php echo $msg_volver_reportes; ?>
            </a>
          </div>
        </div>
      </section>
    </div>
    <?php
    include '../includes/footer.php';
    exit();
}

// 1. TOTALES GENERALES
$sql_totales = "SELECT 
                COUNT(DISTINCT id_servicio) as num_servicios,
                SUM(total) as total_ventas,
                AVG(total) as ticket_promedio,
                MIN(total) as ticket_minimo,
                MAX(total) as ticket_maximo
                FROM " . TBL_SERVICIOS_TOTAL . " 
                WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$result_totales = $conexion->query($sql_totales);
$totales = $result_totales->fetch_assoc();

// 2. DESGLOSE POR MÉTODO DE PAGO
$sql_metodos = "SELECT 
                metodo_pago,
                COUNT(*) as num_transacciones,
                SUM(total) as total,
                AVG(total) as ticket_promedio,
                MIN(total) as ticket_minimo,
                MAX(total) as ticket_maximo
                FROM " . TBL_SERVICIOS_TOTAL . " 
                WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                GROUP BY metodo_pago
                ORDER BY total DESC";
$result_metodos = $conexion->query($sql_metodos);

// 3. DESGLOSE POR DÍA
$sql_dias = "SELECT 
             fecha_servicio,
             COUNT(DISTINCT id_servicio) as num_servicios,
             SUM(total) as total_ventas,
             AVG(total) as ticket_promedio
             FROM " . TBL_SERVICIOS_TOTAL . " 
             WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
             GROUP BY fecha_servicio
             ORDER BY fecha_servicio ASC";
$result_dias = $conexion->query($sql_dias);

// 4. DESGLOSE POR HORA (solo para día específico)
$ventas_por_hora = [];
if ($periodo == 'dia') {
    $sql_horas = "SELECT 
                  HOUR(hora_cierre_servicio) as hora,
                  COUNT(*) as num_servicios,
                  SUM(total) as total_ventas
                  FROM " . TBL_SERVICIOS_TOTAL . " 
                  WHERE fecha_servicio = '$fecha_inicio'
                  GROUP BY HOUR(hora_cierre_servicio)
                  ORDER BY hora ASC";
    $result_horas = $conexion->query($sql_horas);
    if ($result_horas) {
        while($row = $result_horas->fetch_assoc()) {
            $ventas_por_hora[$row['hora']] = $row;
        }
    }
}

// 5. PRODUCTOS VENDIDOS CON TOTALES
$sql_productos = "SELECT 
                  p.nombre_producto,
                  SUM(s.cantidad) as cantidad_vendida,
                  p.valor_con_iva as precio_unitario,
                  SUM(s.valor_total) as total_ventas,
                  COUNT(DISTINCT s.id_servicio) as num_servicios
                  FROM " . TBL_SERVICIOS . " s
                  INNER JOIN " . TBL_PRODUCTOS . " p ON s.id_producto = p.id_producto
                  WHERE s.estado = 'finalizado' 
                  AND s.fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                  GROUP BY s.id_producto, p.nombre_producto, p.valor_con_iva
                  ORDER BY total_ventas DESC";
$result_productos = $conexion->query($sql_productos);
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-cash-register"></i> <?php echo $cierre_titulo; ?></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../home.php"><?php echo $misc_home; ?></a></li>
            <li class="breadcrumb-item"><a href="../reportes.php"><?php echo $menu_reportes; ?></a></li>
            <li class="breadcrumb-item active"><?php echo $reporte_cierre_caja; ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      
      <!-- Filtros -->
      <div class="card">
        <div class="card-header" style="background-color: #27ae60; color: white;">
          <h3 class="card-title"><i class="fas fa-filter"></i> <?php echo $cierre_filtros; ?></h3>
        </div>
        <div class="card-body">
          <form method="GET" action="">
            <div class="row">
              <div class="col-md-3">
                <div class="form-group">
                  <label><?php echo $cierre_periodo; ?></label>
                  <select name="periodo" class="form-control" onchange="this.form.submit()">
                    <option value="dia" <?php echo $periodo == 'dia' ? 'selected' : ''; ?>><?php echo $cierre_dia; ?></option>
                    <option value="semana" <?php echo $periodo == 'semana' ? 'selected' : ''; ?>><?php echo $cierre_semana; ?></option>
                    <option value="mes" <?php echo $periodo == 'mes' ? 'selected' : ''; ?>><?php echo $cierre_mes; ?></option>
                  </select>
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label><?php echo $cierre_fecha; ?></label>
                  <input type="date" name="fecha" class="form-control" value="<?php echo $fecha_especifica; ?>" onchange="this.form.submit()">
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>&nbsp;</label>
                  <div>
                    <button type="submit" class="btn btn-success">
                      <i class="fas fa-search"></i> <?php echo $cierre_buscar; ?>
                    </button>
                    <a href="?periodo=<?php echo $periodo; ?>&fecha=<?php echo $fecha_especifica; ?>&export=excel" class="btn btn-primary">
                      <i class="fas fa-file-excel"></i> <?php echo $cierre_exportar; ?>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <div class="alert alert-info">
              <strong><?php echo $cierre_rango; ?></strong> 
              <?php 
              echo date('d/m/Y', strtotime($fecha_inicio)); 
              if ($fecha_inicio != $fecha_fin) {
                  echo ' al ' . date('d/m/Y', strtotime($fecha_fin));
              }
              ?>
            </div>
          </form>
        </div>
      </div>

      <!-- Resumen General -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>$<?php echo number_format($totales['total_ventas'] ?? 0, 0, ',', '.'); ?></h3>
              <p><?php echo $cierre_total_ingresos; ?></p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?php echo $totales['num_servicios'] ?? 0; ?></h3>
              <p><?php echo $cierre_servicios; ?></p>
            </div>
            <div class="icon"><i class="fas fa-receipt"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3>$<?php echo number_format($totales['ticket_promedio'] ?? 0, 0, ',', '.'); ?></h3>
              <p><?php echo $cierre_ticket_promedio; ?></p>
            </div>
            <div class="icon"><i class="fas fa-chart-line"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3>$<?php echo number_format($totales['ticket_maximo'] ?? 0, 0, ',', '.'); ?></h3>
              <p><?php echo $cierre_ticket_maximo; ?></p>
            </div>
            <div class="icon"><i class="fas fa-arrow-up"></i></div>
          </div>
        </div>
      </div>

      <!-- Desglose por Método de Pago -->
      <div class="card">
        <div class="card-header" style="background-color: #27ae60; color: white;">
          <h3 class="card-title"><i class="fas fa-credit-card"></i> <?php echo $cierre_desglose_metodos; ?></h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="bg-light">
                <tr>
                  <th><?php echo $cierre_metodo; ?></th>
                  <th><?php echo $cierre_transacciones; ?></th>
                  <th><?php echo $cierre_total; ?></th>
                  <th><?php echo $cierre_porcentaje; ?></th>
                  <th><?php echo $cierre_ticket_prom; ?></th>
                  <th><?php echo $cierre_ticket_min; ?></th>
                  <th><?php echo $cierre_ticket_max; ?></th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if ($result_metodos && $result_metodos->num_rows > 0) {
                    $result_metodos->data_seek(0);
                    while($metodo = $result_metodos->fetch_assoc()) {
                        $porcentaje = ($totales['total_ventas'] > 0) ? ($metodo['total'] / $totales['total_ventas'] * 100) : 0;
                        echo "<tr>";
                        echo "<td><strong>" . ucfirst($metodo['metodo_pago']) . "</strong></td>";
                        echo "<td>" . $metodo['num_transacciones'] . "</td>";
                        echo "<td><strong>$" . number_format($metodo['total'], 0, ',', '.') . "</strong></td>";
                        echo "<td>" . number_format($porcentaje, 1) . "%</td>";
                        echo "<td>$" . number_format($metodo['ticket_promedio'], 0, ',', '.') . "</td>";
                        echo "<td>$" . number_format($metodo['ticket_minimo'], 0, ',', '.') . "</td>";
                        echo "<td>$" . number_format($metodo['ticket_maximo'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>" . $cierre_no_datos . "</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <?php if ($periodo != 'dia'): ?>
      <!-- Desglose por Día -->
      <div class="card">
        <div class="card-header" style="background-color: #27ae60; color: white;">
          <h3 class="card-title"><i class="fas fa-calendar-day"></i> <?php echo $cierre_desglose_dia; ?></h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="bg-light">
                <tr>
                  <th><?php echo $cierre_fecha; ?></th>
                  <th><?php echo $cierre_dia_label; ?></th>
                  <th><?php echo $cierre_servicios; ?></th>
                  <th><?php echo $cierre_total_ventas; ?></th>
                  <th><?php echo $cierre_ticket_promedio; ?></th>
                </tr>
              </thead>
              <tbody>
                <?php 
                if ($result_dias && $result_dias->num_rows > 0) {
                    $dias_semana = [$dia_domingo, $dia_lunes, $dia_martes, $dia_miercoles, $dia_jueves, $dia_viernes, $dia_sabado];
                    while($dia = $result_dias->fetch_assoc()) {
                        $fecha_obj = new DateTime($dia['fecha_servicio']);
                        $nombre_dia = $dias_semana[$fecha_obj->format('w')];
                        echo "<tr>";
                        echo "<td>" . date('d/m/Y', strtotime($dia['fecha_servicio'])) . "</td>";
                        echo "<td>" . $nombre_dia . "</td>";
                        echo "<td>" . $dia['num_servicios'] . "</td>";
                        echo "<td><strong>$" . number_format($dia['total_ventas'], 0, ',', '.') . "</strong></td>";
                        echo "<td>$" . number_format($dia['ticket_promedio'], 0, ',', '.') . "</td>";
                        echo "</tr>";
                    }
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($periodo == 'dia' && count($ventas_por_hora) > 0): ?>
      <!-- Desglose por Hora -->
      <div class="card">
        <div class="card-header" style="background-color: #27ae60; color: white;">
          <h3 class="card-title"><i class="fas fa-clock"></i> <?php echo $cierre_desglose_hora; ?></h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="bg-light">
                <tr>
                  <th><?php echo $cierre_hora; ?></th>
                  <th><?php echo $cierre_servicios; ?></th>
                  <th><?php echo $cierre_total_ventas; ?></th>
                </tr>
              </thead>
              <tbody>
                <?php 
                for($h = 0; $h < 24; $h++) {
                    if (isset($ventas_por_hora[$h])) {
                        echo "<tr>";
                        echo "<td>" . sprintf('%02d:00', $h) . " - " . sprintf('%02d:59', $h) . "</td>";
                        echo "<td>" . $ventas_por_hora[$h]['num_servicios'] . "</td>";
                        echo "<td><strong>$" . number_format($ventas_por_hora[$h]['total_ventas'], 0, ',', '.') . "</strong></td>";
                        echo "</tr>";
                    }
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <!-- Productos Vendidos -->
      <div class="card">
        <div class="card-header" style="background-color: #27ae60; color: white;">
          <h3 class="card-title"><i class="fas fa-boxes"></i> <?php echo $cierre_detalle_productos; ?></h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead class="bg-light">
                <tr>
                  <th><?php echo $cierre_producto; ?></th>
                  <th><?php echo $cierre_cantidad; ?></th>
                  <th><?php echo $cierre_precio_unitario; ?></th>
                  <th><?php echo $cierre_total_ventas; ?></th>
                  <th><?php echo $cierre_servicios; ?></th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $result_productos->data_seek(0);
                $total_productos = 0;
                $total_cantidad = 0;
                if ($result_productos && $result_productos->num_rows > 0) {
                    while($prod = $result_productos->fetch_assoc()) {
                        $total_productos += $prod['total_ventas'];
                        $total_cantidad += $prod['cantidad_vendida'];
                        echo "<tr>";
                        echo "<td>" . $prod['nombre_producto'] . "</td>";
                        echo "<td>" . $prod['cantidad_vendida'] . "</td>";
                        echo "<td>$" . number_format($prod['precio_unitario'], 0, ',', '.') . "</td>";
                        echo "<td><strong>$" . number_format($prod['total_ventas'], 0, ',', '.') . "</strong></td>";
                        echo "<td>" . $prod['num_servicios'] . "</td>";
                        echo "</tr>";
                    }
                    echo "<tr class='font-weight-bold bg-light'>";
                    echo "<td>" . $cierre_total_label . "</td>";
                    echo "<td>" . $total_cantidad . "</td>";
                    echo "<td>-</td>";
                    echo "<td><strong>$" . number_format($total_productos, 0, ',', '.') . "</strong></td>";
                    echo "<td>-</td>";
                    echo "</tr>";
                } else {
                    echo "<tr><td colspan='5' class='text-center'>" . $cierre_no_datos . "</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php include '../includes/footer.php'; ?>
