<?php
include 'includes/auth.php';
include 'includes/url.php';
include_once 'lang/idiomas.php';
include 'includes/menu.php';
include 'includes/conexion_master.php';

// Solo super-admin puede acceder
if (!isset($_SESSION['rol_master']) || $_SESSION['rol_master'] !== 'super-admin') {
    header('Location: index.php');
    exit();
}

// Procesar guardado de permisos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_permisos'])) {
    $id_restaurante = $_POST['id_restaurante'];
    $reportes_seleccionados = isset($_POST['reportes']) ? $_POST['reportes'] : [];
    
    // Eliminar permisos actuales
    $sql_delete = "DELETE FROM restaurante_reportes WHERE id_restaurante = ?";
    $stmt = $conexion_master->prepare($sql_delete);
    $stmt->bind_param("i", $id_restaurante);
    $stmt->execute();
    
    // Insertar nuevos permisos
    if (!empty($reportes_seleccionados)) {
        $sql_insert = "INSERT INTO restaurante_reportes (id_restaurante, id_reporte) VALUES (?, ?)";
        $stmt = $conexion_master->prepare($sql_insert);
        
        foreach ($reportes_seleccionados as $id_reporte) {
            $stmt->bind_param("ii", $id_restaurante, $id_reporte);
            $stmt->execute();
        }
    }
    
    $mensaje_exito = "Permisos de reportes actualizados correctamente";
}

// Obtener todos los reportes
$sql_reportes = "SELECT * FROM reportes WHERE estado = 'activo' ORDER BY orden, nombre";
$result_reportes = $conexion_master->query($sql_reportes);

// Obtener todos los restaurantes
$sql_restaurantes = "SELECT id, nombre FROM restaurantes WHERE estado = 'activo' ORDER BY nombre";
$result_restaurantes = $conexion_master->query($sql_restaurantes);

// Si se seleccionó un restaurante, obtener sus permisos actuales
$id_restaurante_seleccionado = isset($_GET['id']) ? $_GET['id'] : (isset($_POST['id_restaurante']) ? $_POST['id_restaurante'] : null);
$reportes_asignados = [];

if ($id_restaurante_seleccionado) {
    $sql_asignados = "SELECT id_reporte FROM restaurante_reportes WHERE id_restaurante = ?";
    $stmt = $conexion_master->prepare($sql_asignados);
    $stmt->bind_param("i", $id_restaurante_seleccionado);
    $stmt->execute();
    $result_asignados = $stmt->get_result();
    
    while ($row = $result_asignados->fetch_assoc()) {
        $reportes_asignados[] = $row['id_reporte'];
    }
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-file-chart-line"></i> Permisos de Reportes</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Permisos Reportes</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      
      <?php if (isset($mensaje_exito)): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <i class="fas fa-check-circle"></i> <?php echo $mensaje_exito; ?>
      </div>
      <?php endif; ?>

      <!-- Info Box -->
      <div class="row mb-3">
        <div class="col-12">
          <div class="alert alert-info">
            <h5><i class="fas fa-info-circle"></i> Gestión de Permisos de Reportes</h5>
            Seleccione un restaurante y asigne los reportes que podrán visualizar. Los reportes no asignados no aparecerán en el menú de ese restaurante.
          </div>
        </div>
      </div>

      <!-- Card principal -->
      <div class="card">
        <div class="card-header" style="background-color: #2c3e50; color: white;">
          <h3 class="card-title"><i class="fas fa-cog"></i> Configurar Permisos</h3>
        </div>
        <div class="card-body">
          
          <!-- Selector de restaurante -->
          <div class="form-group">
            <label for="restaurante_select">Seleccionar Restaurante:</label>
            <select id="restaurante_select" class="form-control select2" style="width: 100%;" onchange="cargarPermisos(this.value)">
              <option value="">-- Seleccione un restaurante --</option>
              <?php 
              $result_restaurantes->data_seek(0);
              while ($rest = $result_restaurantes->fetch_assoc()): 
                $selected = ($id_restaurante_seleccionado == $rest['id']) ? 'selected' : '';
              ?>
              <option value="<?php echo $rest['id']; ?>" <?php echo $selected; ?>>
                <?php echo htmlspecialchars($rest['nombre']); ?>
              </option>
              <?php endwhile; ?>
            </select>
          </div>

          <?php if ($id_restaurante_seleccionado): ?>
          <!-- Formulario de permisos -->
          <form method="POST" id="form_permisos">
            <input type="hidden" name="id_restaurante" value="<?php echo $id_restaurante_seleccionado; ?>">
            
            <hr>
            <h5><i class="fas fa-list-check"></i> Reportes Disponibles</h5>
            <p class="text-muted">Seleccione los reportes que este restaurante podrá visualizar:</p>
            
            <div class="row">
              <?php 
              $result_reportes->data_seek(0);
              while ($reporte = $result_reportes->fetch_assoc()): 
                $checked = in_array($reporte['id'], $reportes_asignados) ? 'checked' : '';
              ?>
              <div class="col-md-6 mb-3">
                <div class="custom-control custom-switch custom-switch-lg">
                  <input type="checkbox" 
                         class="custom-control-input" 
                         id="reporte_<?php echo $reporte['id']; ?>" 
                         name="reportes[]" 
                         value="<?php echo $reporte['id']; ?>"
                         <?php echo $checked; ?>>
                  <label class="custom-control-label" for="reporte_<?php echo $reporte['id']; ?>">
                    <strong><i class="<?php echo $reporte['icono']; ?>"></i> <?php echo htmlspecialchars($reporte['nombre']); ?></strong>
                    <br>
                    <small class="text-muted"><?php echo htmlspecialchars($reporte['descripcion']); ?></small>
                  </label>
                </div>
              </div>
              <?php endwhile; ?>
            </div>

            <hr>
            <div class="text-right">
              <button type="button" class="btn btn-secondary" onclick="seleccionarTodos()">
                <i class="fas fa-check-double"></i> Seleccionar Todos
              </button>
              <button type="button" class="btn btn-secondary" onclick="deseleccionarTodos()">
                <i class="fas fa-times"></i> Deseleccionar Todos
              </button>
              <button type="submit" name="guardar_permisos" class="btn btn-success">
                <i class="fas fa-save"></i> Guardar Permisos
              </button>
            </div>
          </form>
          <?php else: ?>
          <div class="alert alert-warning mt-3">
            <i class="fas fa-arrow-up"></i> Seleccione un restaurante para configurar sus permisos de reportes
          </div>
          <?php endif; ?>

        </div>
      </div>

      <!-- Estadísticas de permisos -->
      <div class="card">
        <div class="card-header" style="background-color: #34495e; color: white;">
          <h3 class="card-title"><i class="fas fa-chart-pie"></i> Resumen de Permisos por Restaurante</h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Restaurante</th>
                  <th class="text-center">Reportes Asignados</th>
                  <th class="text-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $result_restaurantes->data_seek(0);
                while ($rest = $result_restaurantes->fetch_assoc()): 
                  // Contar reportes asignados
                  $sql_count = "SELECT COUNT(*) as total FROM restaurante_reportes WHERE id_restaurante = " . $rest['id'];
                  $result_count = $conexion_master->query($sql_count);
                  $count = $result_count->fetch_assoc()['total'];
                  
                  // Total de reportes disponibles
                  $sql_total = "SELECT COUNT(*) as total FROM reportes WHERE estado = 'activo'";
                  $result_total = $conexion_master->query($sql_total);
                  $total = $result_total->fetch_assoc()['total'];
                ?>
                <tr>
                  <td><?php echo htmlspecialchars($rest['nombre']); ?></td>
                  <td class="text-center">
                    <span class="badge badge-<?php echo $count > 0 ? 'success' : 'secondary'; ?> badge-lg">
                      <?php echo $count; ?> / <?php echo $total; ?>
                    </span>
                  </td>
                  <td class="text-center">
                    <a href="?id=<?php echo $rest['id']; ?>" class="btn btn-sm btn-primary">
                      <i class="fas fa-edit"></i> Configurar
                    </a>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<script>
function cargarPermisos(idRestaurante) {
    if (idRestaurante) {
        window.location.href = '?id=' + idRestaurante;
    }
}

function seleccionarTodos() {
    document.querySelectorAll('input[name="reportes[]"]').forEach(function(checkbox) {
        checkbox.checked = true;
    });
}

function deseleccionarTodos() {
    document.querySelectorAll('input[name="reportes[]"]').forEach(function(checkbox) {
        checkbox.checked = false;
    });
}

// Inicializar Select2
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap4'
    });
});
</script>

<?php include 'includes/footer.php'; ?>
