
<?php 
include '../includes/auth.php';
include '../includes/url.php';
include_once '../lang/idiomas.php';

// Verificar permiso del módulo antes de incluir menú
if (!tienePermisoModulo('comandas')) {
    header("Location: ../home.php");
    exit();
}

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

// Verificar permisos de restaurante (si el usuario es de restaurante)
if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'restaurant' && !tienePermisoRestaurante('comandas')) {
    ?>
    <div class="content-wrapper">
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0"><i class="fas fa-exclamation-triangle text-warning"></i> Acceso Denegado</h1>
            </div>
          </div>
        </div>
      </div>
      <section class="content">
        <div class="container-fluid">
          <div class="alert alert-danger">
            <h5><i class="icon fas fa-lock"></i> Permiso Denegado</h5>
            No tienes permisos para acceder a esta sección. Por favor, contacta al administrador del restaurante.
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

    <?php
    // Obtener comandas del día desde comandas_total
    $sql = "SELECT id, id_comanda, total, fecha_comanda, hora_cierre_comanda FROM " . TBL_COMANDAS_TOTAL . " WHERE DATE(fecha_comanda) = CURDATE() ORDER BY id DESC";
    $resultado = $conexion->query($sql);
    $comandas = [];
    if ($resultado && $resultado->num_rows > 0) {
      while($row = $resultado->fetch_assoc()) {
        $comandas[] = $row;
      }
    }
    ?>

    <div class="card">
      <div class="card-header">
        <h3 class="card-title">Historial de Comandas del Día</h3>
      </div>
      <div class="card-body p-0">
        <?php if (count($comandas) > 0): ?>
          <table class="table table-striped">
            <thead>
              <tr>
                <th>Total Venta</th>
                <th>Fecha de Venta</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($comandas as $comanda) : ?>
                <tr>
                  <td><strong style="color: #27ae60; font-size: 1.1em;">$<?= number_format($comanda['total'], 2) ?></strong></td>
                  <td><?= date('d/m/Y H:i', strtotime($comanda['fecha_comanda'] . ' ' . $comanda['hora_cierre_comanda'])) ?></td>
                  <td>
                    <button type="button" class="btn btn-sm btn-info" onclick="verResumenComanda('<?= htmlspecialchars($comanda['id_comanda']) ?>')" data-toggle="modal" data-target="#modalResumenComanda">
                      <i class="fas fa-eye"></i> Ver Detalle
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="p-3 text-center text-muted">
            <p>No hay comandas registradas hoy</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</div>

<!-- SweetAlert2 CSS y JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- Select2 CSS y JS -->
<link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="../plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<style>
  /* Colores verde FUDDO para Select2 */
  .select2-container--bootstrap4 .select2-results__option--highlighted {
    background-color: #28a745 !important;
    color: white !important;
  }
  .select2-container--bootstrap4 .select2-selection--single:focus,
  .select2-container--bootstrap4.select2-container--focus .select2-selection {
    border-color: #28a745 !important;
  }
  .select2-container--bootstrap4 .select2-results__option--selected {
    background-color: #d4edda !important;
    color: #155724 !important;
  }
  .select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.25rem + 2px) !important;
  }
  .select2-container--bootstrap4 .select2-selection__rendered {
    line-height: calc(2.25rem) !important;
  }
</style>
<script src="../plugins/select2/js/select2.full.min.js"></script>

<?php include 'modal_nueva_comanda.php'; ?>
<?php include 'resumen.php'; ?> 
<?php include '../includes/footer.php'; ?>
