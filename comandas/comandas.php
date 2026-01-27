
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
?>
<div class="content-wrapper">
  <div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Comandas</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Comandas</li>
            </ol>
          </div>
        </div>
      </div>
  </div>

  <section class="content">
    <div class="mb-3">
      <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalNuevaComanda">
        <i class="fas fa-plus"></i> Nueva Comanda
      </button>
    </div>

    <div class="row">
      <?php
      // Obtener comandas del día
      $sql = "SELECT id, descripcion, total, fecha_creacion FROM ".$TABLE_PREFIX."comandas WHERE DATE(fecha_creacion) = CURDATE() ORDER BY fecha_creacion DESC";
      $resultado = $conexion->query($sql);
      $comandas = [];
      if ($resultado && $resultado->num_rows > 0) {
        while($row = $resultado->fetch_assoc()) {
          $comandas[] = $row;
        }
      }
      ?>
      <?php foreach ($comandas as $comanda) : ?>
        <div class="col-md-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <div style="cursor: pointer; flex-grow: 1;" onclick="verResumenComanda(<?= $comanda['id'] ?>)" data-toggle="modal" data-target="#modalResumenComanda">
                <strong>🧾 <?= htmlspecialchars($comanda['descripcion']) ?></strong>
              </div>
            </div>
            <div class="card-body" style="cursor: pointer;" onclick="verResumenComanda(<?= $comanda['id'] ?>)" data-toggle="modal" data-target="#modalResumenComanda">
              <div class="text-center font-weight-bold">
                Total Venta<br>
                <span style="font-size: 1.5em; color: #27ae60;">$<?= number_format($comanda['total'], 2) ?></span>
              </div>
              <div class="text-center mt-2">
                <small><?= date('H:i', strtotime($comanda['fecha_creacion'])) ?></small>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </section>
</div>

<!-- SweetAlert2 CSS y JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
function verResumenComanda(id) {
  // Aquí puedes hacer un fetch/ajax para cargar el resumen de la comanda
  // y mostrarlo en el modal
  // Por ahora solo abre el modal
  // Implementar lógica en resumen.php
}
</script>

<?php include 'modal_nueva_comanda.php'; ?>
<?php include 'resumen.php'; ?> 
<?php include '../includes/footer.php'; ?>
