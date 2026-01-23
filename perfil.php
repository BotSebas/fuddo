<?php
include 'includes/auth.php';
include 'includes/url.php';
include_once 'lang/idiomas.php';
include 'includes/menu.php';
include 'includes/conexion.php';
include 'includes/conexion_master.php';

// Obtener información del usuario desde usuarios_master
$usuario_id = $_SESSION['user_id'];
$sqlUser = "SELECT nombre, usuario, email, fecha_creacion, foto FROM usuarios_master WHERE id = $usuario_id";
$resultUser = $conexion_master->query($sqlUser);
$usuario = $resultUser->fetch_assoc();

// Inicializar variables de estadísticas
$totalServicios = 0;
$totalVentas = 0;
$topProductos = [];
$serviciosRecientes = [];
$totalProductos = 0;
$totalMesas = 0;

// Solo consultar estadísticas si hay restaurante asociado
if (isset($_SESSION['nombre_bd']) && $_SESSION['nombre_bd'] !== null) {
    // Definir nombres de tablas con prefijo
    $tabla_servicios = TBL_SERVICIOS;
    $tabla_productos = TBL_PRODUCTOS;
    $tabla_mesas = TBL_MESAS;
    
    // Total de servicios finalizados
    $sqlServicios = "SELECT COUNT(DISTINCT id_servicio) as total FROM $tabla_servicios WHERE estado = 'finalizado'";
    $resultServicios = $conexion->query($sqlServicios);
    $totalServicios = $resultServicios->fetch_assoc()['total'];

    // Total de ventas
    $sqlVentas = "SELECT COALESCE(SUM(valor_total), 0) as total_ventas FROM $tabla_servicios WHERE estado = 'finalizado'";
    $resultVentas = $conexion->query($sqlVentas);
    $totalVentas = $resultVentas->fetch_assoc()['total_ventas'] ?? 0;

    // Productos más vendidos (top 5)
    $sqlTopProductos = "SELECT p.nombre_producto, SUM(s.cantidad) as total_vendido, p.id_producto 
                         FROM $tabla_servicios s 
                         INNER JOIN $tabla_productos p ON s.id_producto = p.id_producto
                         WHERE s.estado = 'finalizado' 
                         GROUP BY p.id, p.nombre_producto, p.id_producto
                         ORDER BY total_vendido DESC 
                         LIMIT 5";
    $resultTopProductos = $conexion->query($sqlTopProductos);
    if ($resultTopProductos && $resultTopProductos->num_rows > 0) {
        while ($row = $resultTopProductos->fetch_assoc()) {
            $topProductos[] = $row;
        }
    }

    // Servicios recientes (últimos 5)
    $sqlRecientes = "SELECT s.id_servicio, SUM(s.valor_total) as total, s.fecha_servicio, m.nombre 
                     FROM $tabla_servicios s 
                     INNER JOIN $tabla_mesas m ON s.id_mesa = m.id_mesa
                     WHERE s.estado = 'finalizado'
                     GROUP BY s.id_servicio, s.fecha_servicio, m.nombre
                     ORDER BY s.fecha_servicio DESC
                     LIMIT 5";
    $resultRecientes = $conexion->query($sqlRecientes);
    if ($resultRecientes && $resultRecientes->num_rows > 0) {
        while ($row = $resultRecientes->fetch_assoc()) {
            $serviciosRecientes[] = $row;
        }
    }

    // Total de productos activos
    $sqlProductos = "SELECT COUNT(*) as total FROM $tabla_productos WHERE estado = 'activo'";
    $resultProductos = $conexion->query($sqlProductos);
    $totalProductos = $resultProductos->fetch_assoc()['total'];

    // Total de mesas activas
    $sqlMesas = "SELECT COUNT(*) as total FROM $tabla_mesas";
    $resultMesas = $conexion->query($sqlMesas);
    $totalMesas = $resultMesas->fetch_assoc()['total'];
}
?>
<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Content Header -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1><?php echo $perfil_titulo; ?></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="home.php"><?php echo $misc_home; ?></a></li>
            <li class="breadcrumb-item active"><?php echo $menu_perfil; ?></li>
          </ol>
        </div>
      </div>
    </div>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        
        <!-- Columna izquierda - Info del usuario -->
        <div class="col-md-3">
          
          <!-- Tarjeta de perfil -->
          <div class="card card-success card-outline">
            <div class="card-body box-profile">
              <div class="text-center">
                <?php 
                $fotoPerfilUrl = !empty($usuario['foto']) && file_exists($usuario['foto']) 
                  ? $BASE_URL . $usuario['foto'] 
                  : $BASE_URL . 'dist/img/user2-160x160.jpg';
                ?>
                <img src="<?= $fotoPerfilUrl ?>" class="profile-user-img img-fluid img-circle" alt="User Image" style="width: 120px; height: 120px; object-fit: cover;">
              </div>

              <h3 class="profile-username text-center"><?= htmlspecialchars($usuario['nombre']) ?></h3>

              <p class="text-muted text-center"><?php echo $perfil_administrador; ?></p>

              <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item">
                  <b><?php echo $perfil_servicios_totales; ?></b> <a class="float-right"><?= $totalServicios ?></a>
                </li>
                <li class="list-group-item">
                  <b><?php echo $perfil_productos_activos; ?></b> <a class="float-right"><?= $totalProductos ?></a>
                </li>
                <li class="list-group-item">
                  <b><?php echo $perfil_mesas_disponibles; ?></b> <a class="float-right"><?= $totalMesas ?></a>
                </li>
              </ul>

              <a href="mesas/mesas.php" class="btn btn-fuddo btn-block"><b><?php echo $perfil_ir_mesas; ?></b></a>
            </div>
          </div>

        </div>

        <!-- Columna derecha - Tabs -->
        <div class="col-md-9">
          <div class="card">
            <div class="card-header p-2">
              <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" href="#estadisticas" data-toggle="tab"><?php echo $perfil_estadisticas; ?></a></li>
                <li class="nav-item"><a class="nav-link" href="#informacion" data-toggle="tab"><?php echo $perfil_informacion; ?></a></li>
                <li class="nav-item"><a class="nav-link" href="#configuracion" data-toggle="tab"><?php echo $perfil_configuracion; ?></a></li>
              </ul>
            </div>
            <div class="card-body">
              <div class="tab-content">
                
                <!-- Tab Estadísticas -->
                <div class="active tab-pane" id="estadisticas">
                  <h5 class="fuddo-title">🏆 <?php echo $perfil_top5; ?></h5>
                  <hr>
                  
                  <?php if (!empty($topProductos)): ?>
                    <div class="table-responsive">
                      <table class="table table-hover table-fuddo">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th><?php echo $productos_titulo; ?></th>
                            <th><?php echo $perfil_cantidad_vendida; ?></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($topProductos as $index => $prod): ?>
                            <tr>
                              <td><strong><?= $index + 1 ?></strong></td>
                              <td><?= htmlspecialchars($prod['nombre_producto']) ?></td>
                              <td><strong><?= $prod['total_vendido'] ?></strong> <?php echo $perfil_unidades; ?></td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  <?php else: ?>
                    <div class="alert alert-info">
                      <i class="fas fa-info-circle mr-2"></i> Aún no hay productos vendidos
                    </div>
                  <?php endif; ?>

                </div>

                <!-- Tab Información Personal -->
                <div class="tab-pane" id="informacion">
                  <h5 class="fuddo-title">👤 <?php echo $perfil_info_cuenta; ?></h5>
                  <hr>

                  <div class="row">
                    <div class="col-md-6">
                      <strong><i class="fas fa-user mr-2 fuddo-icon"></i> <?php echo $perfil_usuario; ?></strong>
                      <p class="text-muted"><?= htmlspecialchars($usuario['usuario']) ?></p>
                      <hr>

                      <strong><i class="fas fa-envelope mr-2 fuddo-icon"></i> <?php echo $perfil_email; ?></strong>
                      <p class="text-muted"><?= htmlspecialchars($usuario['email']) ?></p>
                      <hr>

                      <strong><i class="fas fa-calendar-alt mr-2 fuddo-icon"></i> <?php echo $perfil_miembro_desde; ?></strong>
                      <p class="text-muted"><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></p>
                    </div>
                    
                    <div class="col-md-6">
                      <strong><i class="fas fa-dollar-sign mr-2 fuddo-icon"></i> <?php echo $perfil_ventas_totales; ?></strong>
                      <p class="text-muted">$<?= number_format($totalVentas, 2) ?></p>
                      <hr>

                      <strong><i class="fas fa-chart-bar mr-2 fuddo-icon"></i> <?php echo $perfil_servicios_procesados; ?></strong>
                      <p class="text-muted"><?= $totalServicios ?></p>
                      <hr>

                      <strong><i class="fas fa-box mr-2 fuddo-icon"></i> <?php echo $perfil_productos_activos; ?></strong>
                      <p class="text-muted"><?= $totalProductos ?></p>
                    </div>
                  </div>

                </div>

                <!-- Tab Configuración -->
                <div class="tab-pane" id="configuracion">
                  <h5 class="fuddo-title">🔐 <?php echo $perfil_cambiar_password; ?></h5>
                  <hr>
                  
                  <form id="formCambiarPassword" class="form-horizontal">
                    <div class="form-group row">
                      <label for="inputPasswordActual" class="col-sm-3 col-form-label"><?php echo $perfil_password_actual; ?></label>
                      <div class="col-sm-9">
                        <input type="password" class="form-control" id="inputPasswordActual" name="password_actual" placeholder="<?php echo $perfil_placeholder_actual; ?>" required>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label for="inputPasswordNueva" class="col-sm-3 col-form-label"><?php echo $perfil_password_nueva; ?></label>
                      <div class="col-sm-9">
                        <input type="password" class="form-control" id="inputPasswordNueva" name="password_nueva" placeholder="<?php echo $perfil_placeholder_nueva; ?>" required minlength="6">
                        <small class="text-muted"><?php echo $perfil_minimo_caracteres; ?></small>
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <label for="inputPasswordConfirmar" class="col-sm-3 col-form-label"><?php echo $perfil_password_confirmar; ?></label>
                      <div class="col-sm-9">
                        <input type="password" class="form-control" id="inputPasswordConfirmar" name="password_confirmar" placeholder="<?php echo $perfil_placeholder_confirmar; ?>" required minlength="6">
                      </div>
                    </div>
                    
                    <div class="form-group row">
                      <div class="offset-sm-3 col-sm-9">
                        <button type="submit" class="btn btn-fuddo">
                          <i class="fas fa-key mr-2"></i><?php echo $perfil_cambiar_password; ?>
                        </button>
                      </div>
                    </div>
                  </form>

                  <hr>

                  <h5 class="fuddo-title">⚙️ <?php echo $perfil_info_cuenta; ?></h5>
                  <hr>
                  
                  <div class="alert alert-fuddo">
                    <h6><i class="fas fa-shield-alt mr-2"></i><?php echo $perfil_seguridad_titulo; ?></h6>
                    <p class="mb-0">
                      • <?php echo $perfil_seguridad_encriptada; ?><br>
                      • <?php echo $perfil_seguridad_cambiar; ?><br>
                      • <?php echo $perfil_seguridad_no_compartir; ?><br>
                      • <?php echo $perfil_seguridad_cerrar; ?>
                    </p>
                  </div>

                </div>

              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('formCambiarPassword').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const passwordNueva = document.getElementById('inputPasswordNueva').value;
  const passwordConfirmar = document.getElementById('inputPasswordConfirmar').value;
  
  if (passwordNueva !== passwordConfirmar) {
    Swal.fire({
      icon: 'error',
      title: '<?php echo $msg_error_titulo; ?>',
      text: '<?php echo $perfil_passwords_no_coinciden; ?>',
      confirmButtonColor: '#27ae60'
    });
    return;
  }
  
  const formData = new FormData(this);
  
  fetch('cambiar_password.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: '<?php echo $perfil_exito; ?>',
        text: data.message,
        confirmButtonColor: '#27ae60'
      }).then(() => {
        document.getElementById('formCambiarPassword').reset();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: '<?php echo $msg_error_titulo; ?>',
        text: data.message,
        confirmButtonColor: '#27ae60'
      });
    }
  })
  .catch(error => {
    Swal.fire({
      icon: 'error',
      title: '<?php echo $msg_error_titulo; ?>',
      text: '<?php echo $perfil_error_cambiar; ?>',
      confirmButtonColor: '#27ae60'
    });
  });
});
</script>

<?php
include 'includes/footer.php';
?>
