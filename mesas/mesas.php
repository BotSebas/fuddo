<?php 
include '../includes/auth.php';
include '../includes/url.php';
include_once '../lang/idiomas.php';

// Verificar permiso del módulo antes de incluir menú
if (!tienePermisoModulo('mesas')) {
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
if (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'restaurant' && !tienePermisoRestaurante('mesas')) {
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
            <h1 class="m-0"><?php echo $mesas_titulo; ?></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active"><?= $mesas_titulo; ?></li>
            </ol>
          </div>
        </div>
      </div>
  </div>

  <section class="content">
    <div class="mb-3">
      <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalNuevaMesa">
        <i class="fas fa-plus"></i> <?php echo $mesa_nueva; ?>
      </button>
    </div>

    <div class="row">
      <?php
      // Obtener mesas de la base de datos con el total de servicios activos
      $sql = "SELECT m.id, m.ubicacion, m.nombre, m.estado, 
              COALESCE(SUM(s.valor_total), 0) as total_cuenta,
              COUNT(s.id) as cantidad_items
              FROM " . TBL_MESAS . " m
              LEFT JOIN " . TBL_SERVICIOS . " s ON m.id_mesa = s.id_mesa AND s.estado = 'activo'
              GROUP BY m.id, m.ubicacion, m.nombre, m.estado";
      $resultado = $conexion->query($sql);
      
      $vacantes = [];
      if ($resultado->num_rows > 0) {
        while($row = $resultado->fetch_assoc()) {
          // Determinar estado real de la mesa según si tiene productos
          $estado_real = ($row['cantidad_items'] > 0) ? 'ocupada' : 'libre';
          
          $vacantes[] = [
            'id' => $row['id'],
            'titulo' => $row['nombre'],
            'ubicacion' => $row['ubicacion'],
            'estado' => $estado_real,
            'total_cuenta' => $row['total_cuenta']
          ];
        }
      }

      // Paginación
      $porPagina = 10;
      $pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
      $inicio = ($pagina - 1) * $porPagina;
      $vacantesPagina = array_slice($vacantes, $inicio, $porPagina);
      $totalPaginas = ceil(count($vacantes) / $porPagina);
      ?>
        <?php foreach ($vacantesPagina as $vacante) : ?>
        <div class="col-md-4">
          <div class="card">
            <div class="card-header d-flex align-items-center">
              <div style="cursor: pointer; flex-grow: 1;" onclick="cargarProductosMesa(<?= $vacante['id'] ?>, '<?= $vacante['titulo'] ?>')" data-toggle="modal" data-target="#modalProductosMesa">
                <strong>📌 <?= $vacante['titulo'] ?> - </strong>
                <small><?= $vacante['ubicacion'] ?></small>
              </div>
              <i class="fas fa-trash" onclick="event.stopPropagation(); eliminarMesa(<?= $vacante['id'] ?>, '<?= $vacante['titulo'] ?>')" style="color: #dc3545; cursor: pointer; font-size: 1.1em; margin-left: auto;" title="Eliminar mesa"></i>
            </div>
            <div class="card-body" style="cursor: pointer;" onclick="cargarProductosMesa(<?= $vacante['id'] ?>, '<?= $vacante['titulo'] ?>')" data-toggle="modal" data-target="#modalProductosMesa">
              <div class="text-center font-weight-bold">
                Total Cuenta<br>
                <span style="font-size: 1.5em; color: #27ae60;">$<?= number_format($vacante['total_cuenta'], 2) ?></span>
              </div>
            </div>
            <div class="card-footer d-flex justify-content-between" style="cursor: pointer;" onclick="cargarProductosMesa(<?= $vacante['id'] ?>, '<?= $vacante['titulo'] ?>')" data-toggle="modal" data-target="#modalProductosMesa">
              <span class="<?= $vacante['estado'] == 'libre' ? 'text-success' : 'text-warning' ?>">
                <?= $vacante['estado'] == 'libre' ? '✔' : '🔸' ?> <?= $vacante['estado'] == 'libre' ? $mesas_estado_libre : $mesas_estado_ocupada ?>
              </span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Paginador -->
    <nav aria-label="Page navigation">
      <ul class="pagination justify-content-center mt-4">
        <?php for ($i = 1; $i <= $totalPaginas; $i++) : ?>
          <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
            <a class="page-link" href="?pagina=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </section>
</div>

<!-- SweetAlert2 CSS y JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<!-- Script para mostrar alertas Toast -->
<script>
  <?php if (isset($_GET['exito'])): ?>
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    })
    Toast.fire({
      icon: 'success',
      title: '¡Mesa creada con éxito!'
    })
  <?php endif; ?>

  <?php if (isset($_GET['error'])): ?>
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
      }
    })
    Toast.fire({
      icon: 'error',
      title: '<?php echo $mesas_error_crear; ?>'
    })
  <?php endif; ?>

  function eliminarMesa(mesaId, nombreMesa) {
    Swal.fire({
      title: '¿Seguro que quieres eliminar esta mesa?',
      text: 'Mesa: ' + nombreMesa,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        fetch('eliminar.php?id=' + mesaId)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Mesa eliminada',
                text: data.message
              }).then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: '<?php echo $msg_error_titulo; ?>',
                text: data.message
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: '<?php echo $msg_error_titulo; ?>',
              text: '<?php echo $mesas_error_procesar; ?>'
            });
          });
      }
    });
  }
</script>

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

<?php include 'nueva.php'; ?>
<?php include 'servicios.php'; ?>
<?php include '../includes/footer.php'; ?>
