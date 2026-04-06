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
    // Consulta para servicios (mesas) con factura electrónica Y productos
    $sql_servicios = "SELECT 
                      st.id,
                      st.id_servicio,
                      st.total,
                      st.metodo_pago,
                      st.fecha_servicio,
                      st.hora_cierre_servicio,
                      st.correo_factura_electronica,
                      GROUP_CONCAT(
                        CONCAT(p.nombre_producto, ' (x', s.cantidad, ')') 
                        SEPARATOR ', '
                      ) as productos,
                      'Mesa' as tipo_cierre
                      FROM " . TBL_SERVICIOS_TOTAL . " st
                      LEFT JOIN " . TBL_SERVICIOS . " s ON st.id_servicio = s.id_servicio
                      LEFT JOIN " . TBL_PRODUCTOS . " p ON s.id_producto = p.id_producto
                      WHERE st.fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                      GROUP BY st.id
                      ORDER BY st.fecha_servicio DESC, st.hora_cierre_servicio DESC";
    $result_servicios = $conexion->query($sql_servicios);

    // Consulta para comandas con factura electrónica Y productos
    $sql_comandas = "SELECT 
                     ct.id,
                     ct.id_comanda,
                     ct.total,
                     ct.metodo_pago,
                     ct.fecha_comanda as fecha_servicio,
                     ct.hora_cierre_comanda as hora_cierre_servicio,
                     ct.correo_factura_electronica,
                     GROUP_CONCAT(
                       CONCAT(p.nombre_producto, ' (x', c.cantidad, ')') 
                       SEPARATOR ', '
                     ) as productos,
                     'Comanda' as tipo_cierre
                     FROM " . TBL_COMANDAS_TOTAL . " ct
                     LEFT JOIN " . TBL_COMANDAS . " c ON ct.id_comanda = c.id_comanda
                     LEFT JOIN " . TBL_PRODUCTOS . " p ON c.id_producto = p.id_producto
                     WHERE ct.fecha_comanda BETWEEN '$fecha_inicio' AND '$fecha_fin'
                     GROUP BY ct.id
                     ORDER BY ct.fecha_comanda DESC, ct.hora_cierre_comanda DESC";
    $result_comandas = $conexion->query($sql_comandas);

    // Consulta para totales
    $sql_totales_servicios = "SELECT 
                              COUNT(*) as cantidad,
                              SUM(total) as total
                              FROM " . TBL_SERVICIOS_TOTAL . "
                              WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    $result_tot_servicios = $conexion->query($sql_totales_servicios);
    $totales_servicios = $result_tot_servicios->fetch_assoc();

    $sql_totales_comandas = "SELECT 
                             COUNT(*) as cantidad,
                             SUM(total) as total
                             FROM " . TBL_COMANDAS_TOTAL . "
                             WHERE fecha_comanda BETWEEN '$fecha_inicio' AND '$fecha_fin'";
    $result_tot_comandas = $conexion->query($sql_totales_comandas);
    $totales_comandas = $result_tot_comandas->fetch_assoc();

    // Enviar headers para Excel
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment;filename="facturacion_electronica_' . $fecha_inicio . '_' . $fecha_fin . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head>';
    echo '<body>';
    echo '<h1>REPORTE DE FACTURACIÓN ELECTRÓNICA</h1>';
    echo '<p>Período ' . ucfirst($periodo) . ' | Rango: ' . date('d/m/Y', strtotime($fecha_inicio)) . ' - ' . date('d/m/Y', strtotime($fecha_fin)) . '</p>';
    
    // Resumen general
    $total_general_cantidad = intval($totales_servicios['cantidad']) + intval($totales_comandas['cantidad']);
    $total_general_monto = floatval($totales_servicios['total']) + floatval($totales_comandas['total']);

    echo '<h2>RESUMEN GENERAL</h2>';
    echo '<table border="1">';
    echo '<tr><th>Total de Cierres</th><td>' . $total_general_cantidad . '</td></tr>';
    echo '<tr><th>Total Monto</th><td>$' . number_format($total_general_monto, 0, ',', '.') . '</td></tr>';
    echo '<tr><th>Cierres en Mesas</th><td>' . $totales_servicios['cantidad'] . ' | $' . number_format($totales_servicios['total'], 0, ',', '.') . '</td></tr>';
    echo '<tr><th>Cierres en Comandas</th><td>' . $totales_comandas['cantidad'] . ' | $' . number_format($totales_comandas['total'], 0, ',', '.') . '</td></tr>';
    echo '</table><br>';

    // Tabla de servicios (mesas)
    echo '<h2>CIERRES EN MESAS</h2>';
    echo '<table border="1">';
    echo '<tr>';
    echo '<th>Fecha</th>';
    echo '<th>Hora Cierre</th>';
    echo '<th>Productos</th>';
    echo '<th>Monto</th>';
    echo '<th>Método Pago</th>';
    echo '<th>Correo Factura</th>';
    echo '</tr>';
    
    // Resetear result_servicios después de export
    $sql_servicios_export = "SELECT 
                      st.id,
                      st.total,
                      st.metodo_pago,
                      st.fecha_servicio,
                      st.hora_cierre_servicio,
                      st.correo_factura_electronica,
                      GROUP_CONCAT(
                        CONCAT(p.nombre_producto, ' (x', s.cantidad, ')') 
                        SEPARATOR ', '
                      ) as productos
                      FROM " . TBL_SERVICIOS_TOTAL . " st
                      LEFT JOIN " . TBL_SERVICIOS . " s ON st.id_servicio = s.id_servicio AND s.estado = 'finalizado'
                      LEFT JOIN " . TBL_PRODUCTOS . " p ON s.id_producto = p.id_producto
                      WHERE st.fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                      GROUP BY st.id
                      ORDER BY st.fecha_servicio DESC, st.hora_cierre_servicio DESC";
    $result_servicios_export = $conexion->query($sql_servicios_export);
    
    while ($servicio = $result_servicios_export->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . date('d/m/Y', strtotime($servicio['fecha_servicio'])) . '</td>';
        echo '<td>' . $servicio['hora_cierre_servicio'] . '</td>';
        echo '<td>' . (!empty($servicio['productos']) ? $servicio['productos'] : 'Sin productos') . '</td>';
        echo '<td>$' . number_format($servicio['total'], 0, ',', '.') . '</td>';
        echo '<td>' . ucfirst($servicio['metodo_pago']) . '</td>';
        echo '<td>' . (!empty($servicio['correo_factura_electronica']) ? $servicio['correo_factura_electronica'] : 'Sin correo para factura') . '</td>';
        echo '</tr>';
    }
    echo '</table><br>';

    // Tabla de comandas
    echo '<h2>CIERRES EN COMANDAS</h2>';
    echo '<table border="1">';
    echo '<tr>';
    echo '<th>Fecha</th>';
    echo '<th>Hora Cierre</th>';
    echo '<th>Productos</th>';
    echo '<th>Monto</th>';
    echo '<th>Método Pago</th>';
    echo '<th>Correo Factura</th>';
    echo '</tr>';
    
    $sql_comandas_export = "SELECT 
                     ct.id,
                     ct.total,
                     ct.metodo_pago,
                     ct.fecha_comanda as fecha_servicio,
                     ct.hora_cierre_comanda as hora_cierre_servicio,
                     ct.correo_factura_electronica,
                     GROUP_CONCAT(
                       CONCAT(p.nombre_producto, ' (x', c.cantidad, ')') 
                       SEPARATOR ', '
                     ) as productos
                     FROM " . TBL_COMANDAS_TOTAL . " ct
                     LEFT JOIN " . TBL_COMANDAS . " c ON ct.id_comanda = c.id_comanda AND c.estado = 'finalizado'
                     LEFT JOIN " . TBL_PRODUCTOS . " p ON c.id_producto = p.id_producto
                     WHERE ct.fecha_comanda BETWEEN '$fecha_inicio' AND '$fecha_fin'
                     GROUP BY ct.id
                     ORDER BY ct.fecha_comanda DESC, ct.hora_cierre_comanda DESC";
    $result_comandas_export = $conexion->query($sql_comandas_export);
    
    while ($comanda = $result_comandas_export->fetch_assoc()) {
        echo '<tr>';
        echo '<td>' . date('d/m/Y', strtotime($comanda['fecha_servicio'])) . '</td>';
        echo '<td>' . $comanda['hora_cierre_servicio'] . '</td>';
        echo '<td>' . (!empty($comanda['productos']) ? $comanda['productos'] : 'Sin productos') . '</td>';
        echo '<td>$' . number_format($comanda['total'], 0, ',', '.') . '</td>';
        echo '<td>' . ucfirst($comanda['metodo_pago']) . '</td>';
        echo '<td>' . (!empty($comanda['correo_factura_electronica']) ? $comanda['correo_factura_electronica'] : 'Sin correo para factura') . '</td>';
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

// Consultas para visualización
$sql_servicios = "SELECT 
                  st.id,
                  st.total,
                  st.metodo_pago,
                  st.fecha_servicio,
                  st.hora_cierre_servicio,
                  st.correo_factura_electronica,
                  GROUP_CONCAT(
                    CONCAT(p.nombre_producto, ' (x', s.cantidad, ')') 
                    SEPARATOR ', '
                  ) as productos
                  FROM " . TBL_SERVICIOS_TOTAL . " st
                  LEFT JOIN " . TBL_SERVICIOS . " s ON st.id_servicio = s.id_servicio AND s.estado = 'finalizado'
                  LEFT JOIN " . TBL_PRODUCTOS . " p ON s.id_producto = p.id_producto
                  WHERE st.fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'
                  GROUP BY st.id
                  ORDER BY st.fecha_servicio DESC, st.hora_cierre_servicio DESC";
$result_servicios = $conexion->query($sql_servicios);

$sql_comandas = "SELECT 
                 ct.id,
                 ct.total,
                 ct.metodo_pago,
                 ct.fecha_comanda as fecha_servicio,
                 ct.hora_cierre_comanda as hora_cierre_servicio,
                 ct.correo_factura_electronica,
                 GROUP_CONCAT(
                   CONCAT(p.nombre_producto, ' (x', c.cantidad, ')') 
                   SEPARATOR ', '
                 ) as productos
                 FROM " . TBL_COMANDAS_TOTAL . " ct
                 LEFT JOIN " . TBL_COMANDAS . " c ON ct.id_comanda = c.id_comanda AND c.estado = 'finalizado'
                 LEFT JOIN " . TBL_PRODUCTOS . " p ON c.id_producto = p.id_producto
                 WHERE ct.fecha_comanda BETWEEN '$fecha_inicio' AND '$fecha_fin'
                 GROUP BY ct.id
                 ORDER BY ct.fecha_comanda DESC, ct.hora_cierre_comanda DESC";
$result_comandas = $conexion->query($sql_comandas);

// Consultas para totales
$sql_totales_servicios = "SELECT 
                          COUNT(*) as cantidad,
                          SUM(total) as total
                          FROM " . TBL_SERVICIOS_TOTAL . "
                          WHERE fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$result_tot_servicios = $conexion->query($sql_totales_servicios);
$totales_servicios = $result_tot_servicios->fetch_assoc();

$sql_totales_comandas = "SELECT 
                         COUNT(*) as cantidad,
                         SUM(total) as total
                         FROM " . TBL_COMANDAS_TOTAL . "
                         WHERE fecha_comanda BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$result_tot_comandas = $conexion->query($sql_totales_comandas);
$totales_comandas = $result_tot_comandas->fetch_assoc();

// Consulta para cierres con correo vs sin correo
$sql_con_correo_servicios = "SELECT COUNT(*) as cantidad FROM " . TBL_SERVICIOS_TOTAL . " 
                             WHERE correo_factura_electronica IS NOT NULL AND correo_factura_electronica != '' 
                             AND fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$result_con_correo_servicios = $conexion->query($sql_con_correo_servicios);
$con_correo_servicios = $result_con_correo_servicios->fetch_assoc();

$sql_sin_correo_servicios = "SELECT COUNT(*) as cantidad FROM " . TBL_SERVICIOS_TOTAL . " 
                             WHERE (correo_factura_electronica IS NULL OR correo_factura_electronica = '') 
                             AND fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$result_sin_correo_servicios = $conexion->query($sql_sin_correo_servicios);
$sin_correo_servicios = $result_sin_correo_servicios->fetch_assoc();

$sql_con_correo_comandas = "SELECT COUNT(*) as cantidad FROM " . TBL_COMANDAS_TOTAL . " 
                            WHERE correo_factura_electronica IS NOT NULL AND correo_factura_electronica != '' 
                            AND fecha_comanda BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$result_con_correo_comandas = $conexion->query($sql_con_correo_comandas);
$con_correo_comandas = $result_con_correo_comandas->fetch_assoc();

$sql_sin_correo_comandas = "SELECT COUNT(*) as cantidad FROM " . TBL_COMANDAS_TOTAL . " 
                            WHERE (correo_factura_electronica IS NULL OR correo_factura_electronica = '') 
                            AND fecha_comanda BETWEEN '$fecha_inicio' AND '$fecha_fin'";
$result_sin_correo_comandas = $conexion->query($sql_sin_correo_comandas);
$sin_correo_comandas = $result_sin_correo_comandas->fetch_assoc();
?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-file-invoice"></i> Reporte de Facturación Electrónica</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../reportes.php">Reportes</a></li>
            <li class="breadcrumb-item active">Facturación Electrónica</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      <!-- Filtros -->
      <div class="row mb-3">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Filtros</h3>
            </div>
            <div class="card-body">
              <form method="GET" class="form-inline">
                <div class="form-group mr-2">
                  <label for="periodo" class="mr-2">Período:</label>
                  <select class="form-control" id="periodo" name="periodo" onchange="this.form.submit()">
                    <option value="dia" <?php echo ($periodo == 'dia') ? 'selected' : ''; ?>>Día</option>
                    <option value="semana" <?php echo ($periodo == 'semana') ? 'selected' : ''; ?>>Semana</option>
                    <option value="mes" <?php echo ($periodo == 'mes') ? 'selected' : ''; ?>>Mes</option>
                  </select>
                </div>

                <div class="form-group mr-2">
                  <label for="fecha" class="mr-2">Fecha:</label>
                  <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $fecha_especifica; ?>" onchange="this.form.submit()">
                </div>

                <button type="submit" class="btn btn-primary mr-2">
                  <i class="fas fa-search"></i> Buscar
                </button>

                <a href="?periodo=<?php echo $periodo; ?>&fecha=<?php echo $fecha_especifica; ?>&export=excel" class="btn btn-success">
                  <i class="fas fa-file-excel"></i> Exportar Excel
                </a>
              </form>
            </div>
          </div>
        </div>
      </div>

      <!-- Resumen General -->
      <div class="row mb-3">
        <div class="col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-receipt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Cierres</span>
              <span class="info-box-number">
                <?php echo intval($totales_servicios['cantidad']) + intval($totales_comandas['cantidad']); ?>
              </span>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Total Monto</span>
              <span class="info-box-number">
                $<?php echo number_format(floatval($totales_servicios['total']) + floatval($totales_comandas['total']), 0, ',', '.'); ?>
              </span>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Con Factura</span>
              <span class="info-box-number">
                <?php echo intval($con_correo_servicios['cantidad']) + intval($con_correo_comandas['cantidad']); ?>
              </span>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="fas fa-times-circle"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Sin Factura</span>
              <span class="info-box-number">
                <?php echo intval($sin_correo_servicios['cantidad']) + intval($sin_correo_comandas['cantidad']); ?>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs para Mesas y Comandas -->
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <ul class="nav nav-tabs" id="facturacionTabs" role="tablist">
                <li class="nav-item">
                  <a class="nav-link active" id="mesas-tab" data-toggle="tab" href="#mesas" role="tab">
                    <i class="fas fa-table"></i> Cierres en Mesas (<?php echo $totales_servicios['cantidad']; ?>)
                  </a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" id="comandas-tab" data-toggle="tab" href="#comandas" role="tab">
                    <i class="fas fa-list"></i> Cierres en Comandas (<?php echo $totales_comandas['cantidad']; ?>)
                  </a>
                </li>
              </ul>
            </div>

            <div class="tab-content">
              <!-- Tab Mesas -->
              <div class="tab-pane fade show active" id="mesas" role="tabpanel">
                <div class="table-responsive p-3">
                  <table class="table table-hover table-bordered">
                    <thead class="table-light">
                      <tr>
                        <th>Fecha</th>
                        <th>Hora Cierre</th>
                        <th>Productos Consumidos</th>
                        <th>Monto</th>
                        <th>Método Pago</th>
                        <th>Correo Factura Electrónica</th>
                        <th class="text-center">Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($result_servicios && $result_servicios->num_rows > 0) {
                          while ($servicio = $result_servicios->fetch_assoc()) {
                              $tiene_correo = !empty($servicio['correo_factura_electronica']);
                              $badge_class = $tiene_correo ? 'badge-success' : 'badge-warning';
                              $badge_texto = $tiene_correo ? 'Factura Asignada' : 'Sin Factura';
                              ?>
                              <tr>
                                <td><?php echo date('d/m/Y', strtotime($servicio['fecha_servicio'])); ?></td>
                                <td><?php echo $servicio['hora_cierre_servicio']; ?></td>
                                <td>
                                  <?php 
                                  if (!empty($servicio['productos'])) {
                                      echo '<small>' . htmlspecialchars($servicio['productos']) . '</small>';
                                  } else {
                                      echo '<small class="text-muted">Sin productos</small>';
                                  }
                                  ?>
                                </td>
                                <td>$<?php echo number_format($servicio['total'], 0, ',', '.'); ?></td>
                                <td><?php echo ucfirst($servicio['metodo_pago']); ?></td>
                                <td>
                                  <?php 
                                  if ($tiene_correo) {
                                      echo '<small><strong>' . htmlspecialchars($servicio['correo_factura_electronica']) . '</strong></small>';
                                  } else {
                                      echo '<small class="text-muted">Sin correo para factura</small>';
                                  }
                                  ?>
                                </td>
                                <td class="text-center">
                                  <span class="badge <?php echo $badge_class; ?>"><?php echo $badge_texto; ?></span>
                                </td>
                              </tr>
                              <?php
                          }
                      } else {
                          ?>
                          <tr>
                            <td colspan="7" class="text-center text-muted">No hay cierres de mesas en este período</td>
                          </tr>
                          <?php
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Tab Comandas -->
              <div class="tab-pane fade" id="comandas" role="tabpanel">
                <div class="table-responsive p-3">
                  <table class="table table-hover table-bordered">
                    <thead class="table-light">
                      <tr>
                        <th>Fecha</th>
                        <th>Hora Cierre</th>
                        <th>Productos Comprados</th>
                        <th>Monto</th>
                        <th>Método Pago</th>
                        <th>Correo Factura Electrónica</th>
                        <th class="text-center">Estado</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if ($result_comandas && $result_comandas->num_rows > 0) {
                          while ($comanda = $result_comandas->fetch_assoc()) {
                              $tiene_correo = !empty($comanda['correo_factura_electronica']);
                              $badge_class = $tiene_correo ? 'badge-success' : 'badge-warning';
                              $badge_texto = $tiene_correo ? 'Factura Asignada' : 'Sin Factura';
                              ?>
                              <tr>
                                <td><?php echo date('d/m/Y', strtotime($comanda['fecha_servicio'])); ?></td>
                                <td><?php echo $comanda['hora_cierre_servicio']; ?></td>
                                <td>
                                  <?php 
                                  if (!empty($comanda['productos'])) {
                                      echo '<small>' . htmlspecialchars($comanda['productos']) . '</small>';
                                  } else {
                                      echo '<small class="text-muted">Sin productos</small>';
                                  }
                                  ?>
                                </td>
                                <td>$<?php echo number_format($comanda['total'], 0, ',', '.'); ?></td>
                                <td><?php echo ucfirst($comanda['metodo_pago']); ?></td>
                                <td>
                                  <?php 
                                  if ($tiene_correo) {
                                      echo '<small><strong>' . htmlspecialchars($comanda['correo_factura_electronica']) . '</strong></small>';
                                  } else {
                                      echo '<small class="text-muted">Sin correo para factura</small>';
                                  }
                                  ?>
                                </td>
                                <td class="text-center">
                                  <span class="badge <?php echo $badge_class; ?>"><?php echo $badge_texto; ?></span>
                                </td>
                              </tr>
                              <?php
                          }
                      } else {
                          ?>
                          <tr>
                            <td colspan="7" class="text-center text-muted">No hay cierres de comandas en este período</td>
                          </tr>
                          <?php
                      }
                      ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Resumen por Método de Pago -->
      <div class="row mt-3">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Resumen Detallado</h3>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <h5>Cierres en Mesas</h5>
                  <table class="table table-sm table-bordered">
                    <tr>
                      <th>Descripción</th>
                      <th class="text-right">Cantidad</th>
                      <th class="text-right">Monto</th>
                    </tr>
                    <tr>
                      <td>Total Cierres</td>
                      <td class="text-right"><?php echo $totales_servicios['cantidad']; ?></td>
                      <td class="text-right">$<?php echo number_format($totales_servicios['total'], 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="table-success">
                      <td>Con Factura Electrónica</td>
                      <td class="text-right"><?php echo $con_correo_servicios['cantidad']; ?></td>
                      <td class="text-right">
                        <?php 
                        $sql_monto_con_correo = "SELECT SUM(total) as monto FROM " . TBL_SERVICIOS_TOTAL . " 
                                                 WHERE correo_factura_electronica IS NOT NULL AND correo_factura_electronica != '' 
                                                 AND fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                        $result_monto = $conexion->query($sql_monto_con_correo);
                        $monto = $result_monto->fetch_assoc();
                        echo '$' . number_format($monto['monto'] ?? 0, 0, ',', '.');
                        ?>
                      </td>
                    </tr>
                    <tr class="table-warning">
                      <td>Sin Factura Electrónica</td>
                      <td class="text-right"><?php echo $sin_correo_servicios['cantidad']; ?></td>
                      <td class="text-right">
                        <?php 
                        $sql_monto_sin_correo = "SELECT SUM(total) as monto FROM " . TBL_SERVICIOS_TOTAL . " 
                                                WHERE (correo_factura_electronica IS NULL OR correo_factura_electronica = '') 
                                                AND fecha_servicio BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                        $result_monto = $conexion->query($sql_monto_sin_correo);
                        $monto = $result_monto->fetch_assoc();
                        echo '$' . number_format($monto['monto'] ?? 0, 0, ',', '.');
                        ?>
                      </td>
                    </tr>
                  </table>
                </div>

                <div class="col-md-6">
                  <h5>Cierres en Comandas</h5>
                  <table class="table table-sm table-bordered">
                    <tr>
                      <th>Descripción</th>
                      <th class="text-right">Cantidad</th>
                      <th class="text-right">Monto</th>
                    </tr>
                    <tr>
                      <td>Total Cierres</td>
                      <td class="text-right"><?php echo $totales_comandas['cantidad']; ?></td>
                      <td class="text-right">$<?php echo number_format($totales_comandas['total'], 0, ',', '.'); ?></td>
                    </tr>
                    <tr class="table-success">
                      <td>Con Factura Electrónica</td>
                      <td class="text-right"><?php echo $con_correo_comandas['cantidad']; ?></td>
                      <td class="text-right">
                        <?php 
                        $sql_monto_con_correo = "SELECT SUM(total) as monto FROM " . TBL_COMANDAS_TOTAL . " 
                                                 WHERE correo_factura_electronica IS NOT NULL AND correo_factura_electronica != '' 
                                                 AND fecha_comanda BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                        $result_monto = $conexion->query($sql_monto_con_correo);
                        $monto = $result_monto->fetch_assoc();
                        echo '$' . number_format($monto['monto'] ?? 0, 0, ',', '.');
                        ?>
                      </td>
                    </tr>
                    <tr class="table-warning">
                      <td>Sin Factura Electrónica</td>
                      <td class="text-right"><?php echo $sin_correo_comandas['cantidad']; ?></td>
                      <td class="text-right">
                        <?php 
                        $sql_monto_sin_correo = "SELECT SUM(total) as monto FROM " . TBL_COMANDAS_TOTAL . " 
                                                WHERE (correo_factura_electronica IS NULL OR correo_factura_electronica = '') 
                                                AND fecha_comanda BETWEEN '$fecha_inicio' AND '$fecha_fin'";
                        $result_monto = $conexion->query($sql_monto_sin_correo);
                        $monto = $result_monto->fetch_assoc();
                        echo '$' . number_format($monto['monto'] ?? 0, 0, ',', '.');
                        ?>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row mt-3">
        <div class="col-md-12">
          <a href="../reportes.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver a Reportes
          </a>
        </div>
      </div>
    </div>
  </section>
</div>

<?php
include '../includes/footer.php';
?>
