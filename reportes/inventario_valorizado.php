<?php
include '../includes/auth.php';
include '../includes/conexion.php';
include_once '../lang/idiomas.php';

// Consulta principal de inventario
$sql_inventario = "SELECT 
                   nombre_producto,
                   valor_con_iva as precio_venta,
                   inventario as stock_actual,
                   (valor_con_iva * inventario) as valor_total,
                   estado,
                   CASE 
                     WHEN inventario = 0 THEN '" . $inventario_sin_stock_label . "'
                     WHEN inventario <= 5 THEN '" . $inventario_stock_critico_label . "'
                     WHEN inventario <= 10 THEN '" . $inventario_stock_bajo . "'
                     ELSE '" . $inventario_stock_normal . "'
                   END as estado_stock
                   FROM " . TBL_PRODUCTOS . "
                   ORDER BY estado, inventario ASC";
$result_inventario = $conexion->query($sql_inventario);

// Totales
$total_valor_inventario = 0;
$total_productos = 0;
$productos_sin_stock = 0;
$productos_stock_critico = 0;

$inventario_data = [];
if ($result_inventario && $result_inventario->num_rows > 0) {
    while($row = $result_inventario->fetch_assoc()) {
        $inventario_data[] = $row;
        $total_valor_inventario += $row['valor_total'];
        $total_productos++;
        if ($row['stock_actual'] == 0) $productos_sin_stock++;
        if ($row['stock_actual'] > 0 && $row['stock_actual'] <= 5) $productos_stock_critico++;
    }
}

// Exportar a Excel
if (isset($_GET['export']) && $_GET['export'] == 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="inventario_valorizado_' . date('Y-m-d') . '.xls"');
    header('Cache-Control: max-age=0');
    
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head><meta charset="UTF-8"></head>';
    echo '<body>';
    
    echo '<h2>' . strtoupper($inventario_titulo) . '</h2>';
    echo '<p><strong>' . $inventario_fecha_generacion . '</strong> ' . date('d/m/Y H:i:s') . '</p>';
    
    echo '<h3>' . strtoupper($inventario_resumen) . '</h3>';
    echo '<table border="1">';
    echo '<tr><th>' . $inventario_concepto . '</th><th>' . $inventario_valor . '</th></tr>';
    echo '<tr><td>' . $inventario_total_productos . '</td><td>' . $total_productos . '</td></tr>';
    echo '<tr><td>' . $inventario_productos_sin_stock . '</td><td>' . $productos_sin_stock . '</td></tr>';
    echo '<tr><td>' . $inventario_productos_stock_critico . '</td><td>' . $productos_stock_critico . '</td></tr>';
    echo '<tr><td>' . $inventario_valor_total . '</td><td>$' . number_format($total_valor_inventario, 2) . '</td></tr>';
    echo '</table>';
    
    echo '<h3>' . strtoupper($inventario_detalle_titulo) . '</h3>';
    echo '<table border="1">';
    echo '<tr><th>' . $inventario_producto . '</th><th>' . $inventario_precio_venta . '</th><th>' . $inventario_stock_actual . '</th><th>' . $inventario_valor_total . '</th><th>' . $inventario_estado . '</th><th>' . $inventario_alerta . '</th></tr>';
    foreach($inventario_data as $prod) {
        echo '<tr>';
        echo '<td>' . $prod['nombre_producto'] . '</td>';
        echo '<td>$' . number_format($prod['precio_venta'], 2) . '</td>';
        echo '<td>' . $prod['stock_actual'] . '</td>';
        echo '<td>$' . number_format($prod['valor_total'], 2) . '</td>';
        echo '<td>' . ucfirst($prod['estado']) . '</td>';
        echo '<td>' . $prod['estado_stock'] . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    
    echo '</body></html>';
    exit();
}

// Si no es exportación, cargar el layout normal
include '../includes/url.php';
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
if (!tieneReporte('inventario_valorizado')) {
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

?>

<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-warehouse"></i> <?php echo $inventario_titulo; ?></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../home.php"><?php echo $misc_home; ?></a></li>
            <li class="breadcrumb-item"><a href="../reportes.php"><?php echo $menu_reportes; ?></a></li>
            <li class="breadcrumb-item active"><?php echo $inventario_titulo; ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <section class="content">
    <div class="container-fluid">
      
      <!-- Botón Exportar -->
      <div class="mb-3">
        <a href="?export=excel" class="btn btn-primary">
          <i class="fas fa-file-excel"></i> <?php echo $inventario_exportar; ?>
        </a>
      </div>

      <!-- Resumen -->
      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h3>$<?php echo number_format($total_valor_inventario, 0, ',', '.'); ?></h3>
              <p><?php echo $inventario_valor_total; ?></p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              <h3><?php echo $total_productos; ?></h3>
              <p><?php echo $inventario_total_productos; ?></p>
            </div>
            <div class="icon"><i class="fas fa-boxes"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h3><?php echo $productos_stock_critico; ?></h3>
              <p><?php echo $inventario_stock_critico; ?></p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              <h3><?php echo $productos_sin_stock; ?></h3>
              <p><?php echo $inventario_sin_stock; ?></p>
            </div>
            <div class="icon"><i class="fas fa-times-circle"></i></div>
          </div>
        </div>
      </div>

      <!-- Tabla de Inventario -->
      <div class="card">
        <div class="card-header" style="background-color: #27ae60; color: white;">
          <h3 class="card-title"><i class="fas fa-list"></i> <?php echo $inventario_detalle; ?></h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
              <thead class="bg-light">
                <tr>
                  <th><?php echo $cierre_producto; ?></th>
                  <th><?php echo $inventario_precio_venta; ?></th>
                  <th><?php echo $inventario_stock_actual; ?></th>
                  <th><?php echo $inventario_valor_total; ?></th>
                  <th><?php echo $inventario_estado; ?></th>
                  <th><?php echo $inventario_alerta; ?></th>
                </tr>
              </thead>
              <tbody>
                <?php 
                foreach($inventario_data as $prod) {
                    $class_alerta = '';
                    if ($prod['stock_actual'] == 0) $class_alerta = 'table-danger';
                    elseif ($prod['stock_actual'] <= 5) $class_alerta = 'table-warning';
                    
                    echo "<tr class='$class_alerta'>";
                    echo "<td><strong>" . $prod['nombre_producto'] . "</strong></td>";
                    echo "<td>$" . number_format($prod['precio_venta'], 0, ',', '.') . "</td>";
                    echo "<td><strong>" . $prod['stock_actual'] . "</strong></td>";
                    echo "<td>$" . number_format($prod['valor_total'], 0, ',', '.') . "</td>";
                    echo "<td>" . ucfirst($prod['estado']) . "</td>";
                    echo "<td>";
                    if ($prod['stock_actual'] == 0) {
                        echo "<span class='badge badge-danger'>" . $inventario_sin_stock_label . "</span>";
                    } elseif ($prod['stock_actual'] <= 5) {
                        echo "<span class='badge badge-warning'>" . $inventario_stock_critico_label . "</span>";
                    } elseif ($prod['stock_actual'] <= 10) {
                        echo "<span class='badge badge-info'>" . $inventario_stock_bajo . "</span>";
                    } else {
                        echo "<span class='badge badge-success'>" . $inventario_stock_normal . "</span>";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
                <tr class="font-weight-bold bg-light">
                  <td colspan="3"><?php echo $inventario_valor_total_label; ?></td>
                  <td colspan="3"><strong style="font-size: 1.2em; color: #27ae60;">$<?php echo number_format($total_valor_inventario, 0, ',', '.'); ?></strong></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Alertas de Reabastecimiento -->
      <?php if ($productos_sin_stock > 0 || $productos_stock_critico > 0): ?>
      <div class="card">
        <div class="card-header bg-danger text-white">
          <h3 class="card-title"><i class="fas fa-bell"></i> <?php echo $inventario_alertas; ?></h3>
        </div>
        <div class="card-body">
          <h5><?php echo $inventario_productos_atencion; ?></h5>
          <ul>
            <?php
            foreach($inventario_data as $prod) {
                if ($prod['stock_actual'] == 0) {
                    echo "<li><strong>" . $prod['nombre_producto'] . "</strong> - <span class='text-danger'>" . $inventario_sin_stock_msg . "</span></li>";
                } elseif ($prod['stock_actual'] <= 5) {
                    echo "<li><strong>" . $prod['nombre_producto'] . "</strong> - <span class='text-warning'>" . $inventario_solo_quedan . ' ' . $prod['stock_actual'] . ' ' . $inventario_unidades . "</span></li>";
                }
            }
            ?>
          </ul>
        </div>
      </div>
      <?php endif; ?>

    </div>
  </section>
</div>

<?php include '../includes/footer.php'; ?>
