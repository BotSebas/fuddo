<?php
// menu.php ya está en includes/, no necesita incluir url.php de nuevo
include_once __DIR__ . '/../lang/idiomas.php';

// Incluir conexiones
include_once __DIR__ . '/conexion.php';
include_once __DIR__ . '/conexion_master.php';

// Consultar productos con inventario <= 2 (solo si hay restaurante asociado)
$productosBajoStock = [];
$totalNotificaciones = 0;

if (isset($_SESSION['nombre_bd']) && $_SESSION['nombre_bd'] !== null && defined('TBL_PRODUCTOS')) {
    $tabla_productos = TBL_PRODUCTOS;
    $sqlBajoStock = "SELECT id_producto, nombre_producto, inventario FROM $tabla_productos WHERE inventario <= 2 AND estado = 'activo' ORDER BY inventario ASC";
    $resultBajoStock = $conexion->query($sqlBajoStock);
    if ($resultBajoStock && $resultBajoStock->num_rows > 0) {
        while ($row = $resultBajoStock->fetch_assoc()) {
            $productosBajoStock[] = $row;
        }
    }
    $totalNotificaciones = count($productosBajoStock);
}

// Obtener foto del usuario actual desde usuarios_master
$fotoUsuario = 'dist/img/user2-160x160.jpg'; // Foto por defecto
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $sqlFoto = "SELECT foto FROM usuarios_master WHERE id = $user_id";
    $resultFoto = $conexion_master->query($sqlFoto);
    if ($resultFoto && $resultFoto->num_rows > 0) {
        $rowFoto = $resultFoto->fetch_assoc();
        if (!empty($rowFoto['foto']) && file_exists(__DIR__ . '/../' . $rowFoto['foto'])) {
            $fotoUsuario = $rowFoto['foto'];
        }
    }
}

// Obtener permisos del restaurante (solo si no es super-admin)
$permisos_aplicaciones = [];
$permisos_reportes = [];
if (isset($_SESSION['rol']) && $_SESSION['rol'] !== 'super-admin' && isset($_SESSION['id_restaurante'])) {
    $id_rest = $_SESSION['id_restaurante'];
    
    // Permisos de aplicaciones
    $sqlPermisos = "SELECT a.clave 
                    FROM restaurante_aplicaciones ra 
                    INNER JOIN aplicaciones a ON ra.id_aplicacion = a.id 
                    WHERE ra.id_restaurante = $id_rest AND a.estado = 'activo'";
    $resultPermisos = $conexion_master->query($sqlPermisos);
    if ($resultPermisos && $resultPermisos->num_rows > 0) {
        while ($row = $resultPermisos->fetch_assoc()) {
            $permisos_aplicaciones[] = $row['clave'];
        }
    }
    
    // Permisos de reportes
    $sqlReportes = "SELECT r.clave 
                    FROM restaurante_reportes rr 
                    INNER JOIN reportes r ON rr.id_reporte = r.id 
                    WHERE rr.id_restaurante = $id_rest AND r.estado = 'activo'";
    $resultReportes = $conexion_master->query($sqlReportes);
    if ($resultReportes && $resultReportes->num_rows > 0) {
        while ($row = $resultReportes->fetch_assoc()) {
            $permisos_reportes[] = $row['clave'];
        }
    }
}

// Función helper para verificar permisos de aplicaciones
function tienePermiso($clave) {
    global $permisos_aplicaciones;
    // Super-admin tiene acceso a todo
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'super-admin') {
        return true;
    }
    // Verificar si el restaurante tiene el permiso
    return in_array($clave, $permisos_aplicaciones);
}

// Función helper para verificar permisos de reportes
function tieneReporte($clave) {
    global $permisos_reportes;
    // Super-admin tiene acceso a todo
    if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'super-admin') {
        return true;
    }
    // Verificar si el restaurante tiene el permiso del reporte
    return in_array($clave, $permisos_reportes);
}

// Definir BASE_URL si no está definido o corregir si está mal formada
if (!isset($BASE_URL) || strpos($BASE_URL, '/fuddo/') === false) {
  $BASE_URL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
  $BASE_URL .= "://" . $_SERVER['HTTP_HOST'];
  $BASE_URL .= "/fuddo/";
}
// Normalizar para evitar duplicados o rutas erróneas
$BASE_URL = rtrim($BASE_URL, "/") . "/";
?>
<!DOCTYPE html>
<html lang="<?php echo $idioma; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FUDDO | Dashboard</title>

  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="<?php echo $BASE_URL; ?>assets/icons/logo-fuddo.ico">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/jqvmap/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>dist/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/daterangepicker/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/summernote/summernote-bs4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- FUDDO style -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/fuddo-internas.css">
  <style>
    /* Scroll para el sidebar */
    .main-sidebar {
      height: 100vh;
      position: fixed;
      overflow: hidden;
    }
    
    .main-sidebar .sidebar {
      height: calc(100vh - 58px);
      overflow-y: auto;
      overflow-x: hidden;
      padding-bottom: 30px;
    }
    
    /* Personalizar scrollbar */
    .main-sidebar .sidebar::-webkit-scrollbar {
      width: 8px;
    }
    
    .main-sidebar .sidebar::-webkit-scrollbar-track {
      background: rgba(255,255,255,0.05);
      border-radius: 10px;
    }
    
    .main-sidebar .sidebar::-webkit-scrollbar-thumb {
      background: #27ae60;
      border-radius: 10px;
    }
    
    .main-sidebar .sidebar::-webkit-scrollbar-thumb:hover {
      background: #229954;
    }
    
    /* Asegurar que el menú no se corte */
    .sidebar-mini.sidebar-collapse .main-sidebar .sidebar {
      overflow: visible;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
      <!-- Preloader -->
    <div class="preloader flex-column justify-content-center align-items-center">
      <img class="animation__shake" src="<?php echo $BASE_URL; ?>assets/img/logo-fuddo-blanco.png" height="60" width="60">
    </div>
      <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <?php if (isset($_SESSION['nombre_restaurante']) && $_SESSION['nombre_restaurante'] !== null): ?>
      <li class="nav-item d-none d-sm-inline-block">
        <span class="nav-link" style="color: #27ae60; font-weight: bold;">
          <i class="fas fa-store"></i> <?php echo htmlspecialchars($_SESSION['nombre_restaurante']); ?>
        </span>
      </li>
      <?php endif; ?>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <?php if ($totalNotificaciones > 0): ?>
          <span class="badge badge-danger navbar-badge"><?= $totalNotificaciones ?></span>
          <?php endif; ?>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-item dropdown-header"><?= $totalNotificaciones ?> <?= $totalNotificaciones != 1 ? $notif_notificaciones : $notif_notificacion ?> <?= $notif_stock_bajo ?></span>
          <div class="dropdown-divider"></div>
          
          <?php if ($totalNotificaciones > 0): ?>
            <?php foreach ($productosBajoStock as $producto): ?>
              <a href="<?= $BASE_URL ?>productos/productos.php" class="dropdown-item">
                <i class="fas fa-exclamation-triangle text-warning mr-2"></i> 
                <strong><?= htmlspecialchars($producto['nombre_producto']) ?></strong>
                <span class="float-right text-danger" style="font-weight: bold;"><?= $notif_stock ?>: <?= $producto['inventario'] ?></span>
              </a>
              <div class="dropdown-divider"></div>
            <?php endforeach; ?>
            <a href="<?= $BASE_URL ?>productos/productos.php" class="dropdown-item dropdown-footer" style="color: #27ae60;"><?= $notif_ver_todos_productos ?></a>
          <?php else: ?>
            <a href="#" class="dropdown-item text-center text-muted">
              <i class="fas fa-check-circle mr-2"></i> <?= $notif_no_productos_bajo_stock ?>
            </a>
            <div class="dropdown-divider"></div>
            <a href="<?= $BASE_URL ?>productos/productos.php" class="dropdown-item dropdown-footer" style="color: #27ae60;"><?= $notif_ver_productos ?></a>
          <?php endif; ?>
        </div>
      </li>

      <!-- Soporte Restaurantes (Solo Super-Admin) -->
      <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'super-admin'): ?>
      <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#modalSoporteRestaurante" title="Soporte a Restaurantes">
          <i class="fas fa-tools"></i>
        </a>
      </li>
      <?php endif; ?>

      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#" style="background: #ffffff26;border-radius: 50%;" aria-expanded="false">
          <i class="fas fa-angle-double-down"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="min-width: 225px; left: inherit; right: 0px;">
          <div class="dropdown-divider"></div>
          <!-- <a href="../helpdesk/helpdesk.php" class="dropdown-item dropdown-footer">
            <i class="fas fa-address-card"></i> Mesa de ayuda.
          </a> -->
           <a href="<?php echo $BASE_URL; ?>perfil.php" class="dropdown-item dropdown-footer">
            <i class="fas fa-user-circle"></i> <?php echo $menu_perfil; ?>
            <!-- <span class="float-right text-muted text-sm">2 days</span> -->
          </a> 
          <a href="<?php echo $BASE_URL; ?>logout.php" class="dropdown-item dropdown-footer">
            <i class="fas fa-power-off"></i> <?php echo $menu_cerrar_sesion; ?>
            <!--<span class="float-right text-muted text-sm">2 days</span>-->
          </a>
          <div class="dropdown-divider"></div>
          <p class="dropdown-footer" style="color: #27ae60;"><?php echo $footer_optimizado; ?></p>
        </div>
      </li>
    </ul>
  </nav>
    <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?php echo $BASE_URL; ?>home.php" class="brand-link logo-menu">
        <img src="<?php echo $BASE_URL; ?>assets/img/logo-fuddohorizontal-blanco.png" class="logo-full" style="opacity: .8;width: 150px; ">
        <img src="<?php echo $BASE_URL; ?>assets/img/logo-fuddo-blanco.png" class="logo-mini" style="opacity: .8;width: 40px; ">
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?php echo $BASE_URL . $fotoUsuario; ?>" class="img-circle elevation-2" alt="User Image" style="width: 40px; height: 40px; object-fit: cover;">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?php echo isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario'; ?></a>
        </div>
      </div>

      <!-- Sidebar Menu -->
           <!-- Sidebar Menu -->
           <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column"
    data-widget="treeview" role="menu" data-accordion="false">


  <!-- Mesas -->
  <?php if (tienePermiso('mesas')): ?>
  <li class="nav-item">
    <a href="<?php echo $BASE_URL; ?>mesas/mesas.php" class="nav-link">
      <span class="nav-icon-svg">
        <?php include __DIR__ . '/../assets/icons/silla.svg'; ?>
      </span>
      <p><?php echo $menu_mesas; ?></p>
    </a>
  </li>
  <?php endif; ?>

  <!-- Comandas -->
  <?php if (tienePermiso('comandas')): ?>
  <li class="nav-item">
    <a href="<?php echo $BASE_URL; ?>comandas/comandas.php" class="nav-link">
      <span class="nav-icon-svg">
        <i class="fas fa-receipt"></i>
      </span>
      <p><?php echo $menu_comandas; ?></p>
    </a>
  </li>
  <?php endif; ?>

  <!-- Cocina -->
  <?php if (tienePermiso('cocina')): ?>
  <li class="nav-item">
    <a href="<?php echo $BASE_URL; ?>cocina/cocina.php" class="nav-link">
      <span class="nav-icon-svg">
        <?php include __DIR__ . '/../assets/icons/cocinando.svg'; ?>
      </span>
      <p><?php echo $menu_cocina; ?></p>
    </a>
  </li>
  <?php endif; ?>

  <!-- Productos -->
  <?php if (tienePermiso('productos')): ?>
  <li class="nav-item">
    <a href="<?php echo $BASE_URL; ?>productos/productos.php" class="nav-link">
      <span class="nav-icon-svg">
        <?php include __DIR__ . '/../assets/icons/inventario.svg'; ?>
      </span>
      <p><?php echo $menu_productos; ?></p>
    </a>
  </li>
  <?php endif; ?>

  <!-- Reportes -->
  <?php if (tienePermiso('reportes')): ?>
  <li class="nav-item">
    <a href="<?php echo $BASE_URL; ?>reportes.php" class="nav-link">
      <span class="nav-icon-svg">
        <?php include __DIR__ . '/../assets/icons/documento.svg'; ?>
      </span>
      <p><?php echo $menu_reportes; ?></p>
    </a>
  </li>
  <?php endif; ?>

  <!-- Pedidos -->
  <?php if (tienePermiso('pedidos')): ?>
  <li class="nav-item">
    <a href="<?php echo $BASE_URL; ?>pedidos.html" class="nav-link">
      <span class="nav-icon-svg">
        <?php include __DIR__ . '/../assets/icons/carrito.svg'; ?>
      </span>
      <p>Pedidos</p>
    </a>
  </li>
  <?php endif; ?>

  <!-- Separador para opciones de super-admin -->
  <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'super-admin'): ?>
  <li class="nav-header">ADMINISTRACIÓN</li>
  
  <!-- Usuarios (solo para super-admin) -->
  <li class="nav-item">
    <a href="<?php echo $BASE_URL; ?>usuarios/usuarios.php" class="nav-link">
      <i class="nav-icon fas fa-users"></i>
      <p>Usuarios</p>
    </a>
  </li>

  <!-- Restaurantes (solo para super-admin) -->
  <li class="nav-item">
    <a href="<?php echo $BASE_URL; ?>restaurantes/restaurantes.php" class="nav-link">
      <i class="nav-icon fas fa-store"></i>
      <p>Restaurantes</p>
    </a>
  </li>

  <!-- Permisos (solo para super-admin) -->
  <li class="nav-item has-treeview">
    <a href="#" class="nav-link">
      <i class="nav-icon fas fa-shield-alt"></i>
      <p>
        Permisos
        <i class="right fas fa-angle-left"></i>
      </p>
    </a>
    <ul class="nav nav-treeview">
      <li class="nav-item">
        <a href="<?php echo $BASE_URL; ?>permisos_restaurantes.php" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Aplicaciones</p>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo $BASE_URL; ?>permisos_reportes.php" class="nav-link">
          <i class="far fa-circle nav-icon"></i>
          <p>Reportes</p>
        </a>
      </li>
    </ul>
  </li>
  <?php endif; ?>

</ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  </div>
  <!-- jQuery -->
<script src="<?php echo $BASE_URL; ?>plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo $BASE_URL; ?>plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?php echo $BASE_URL; ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="<?php echo $BASE_URL; ?>plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?php echo $BASE_URL; ?>plugins/sparklines/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?php echo $BASE_URL; ?>plugins/jqvmap/jquery.vmap.min.js"></script>
<script src="<?php echo $BASE_URL; ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo $BASE_URL; ?>plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo $BASE_URL; ?>plugins/moment/moment.min.js"></script>
<script src="<?php echo $BASE_URL; ?>plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?php echo $BASE_URL; ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?php echo $BASE_URL; ?>plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?php echo $BASE_URL; ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo $BASE_URL; ?>dist/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo $BASE_URL; ?>dist/js/pages/dashboard.js"></script>

<!-- Modal Soporte Restaurante (Solo Super-Admin) -->
<?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'super-admin'): ?>
<div class="modal fade" id="modalSoporteRestaurante" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #27ae60; color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-tools"></i> Soporte a Restaurantes
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: white;">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formSoporteRestaurante">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        Selecciona un restaurante para conectarte a su base de datos y brindar soporte técnico.
                    </div>
                    
                    <div class="form-group">
                        <label for="restaurante_soporte">Seleccionar Restaurante <span class="text-danger">*</span></label>
                        <select class="form-control" id="restaurante_soporte" name="id_restaurante" required>
                            <option value="">-- Seleccione un restaurante --</option>
                            <?php
                            $sqlRestaurantes = "SELECT id, nombre, nombre_bd FROM restaurantes WHERE estado = 'activo' ORDER BY nombre ASC";
                            $resultRestaurantes = $conexion_master->query($sqlRestaurantes);
                            if ($resultRestaurantes && $resultRestaurantes->num_rows > 0) {
                                while($rest = $resultRestaurantes->fetch_assoc()) {
                                    echo '<option value="' . $rest['id'] . '" data-nombre-bd="' . $rest['nombre_bd'] . '">' 
                                         . htmlspecialchars($rest['nombre']) . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div id="infoRestauranteActual" class="alert alert-warning" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Actualmente estás conectado a: <strong id="restauranteActualNombre"></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-fuddo">
                        <i class="fas fa-sign-in-alt"></i> Iniciar Soporte
                    </button>
                    <?php if (isset($_SESSION['id_restaurante']) && $_SESSION['id_restaurante'] !== null): ?>
                    <button type="button" class="btn btn-danger" id="btnSalirSoporte">
                        <i class="fas fa-sign-out-alt"></i> Salir de Soporte
                    </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Mostrar info de restaurante actual si está en modo soporte
    <?php if (isset($_SESSION['id_restaurante']) && $_SESSION['id_restaurante'] !== null && isset($_SESSION['nombre_restaurante'])): ?>
    $('#infoRestauranteActual').show();
    $('#restauranteActualNombre').text('<?php echo htmlspecialchars($_SESSION['nombre_restaurante']); ?>');
    <?php endif; ?>

    // Enviar formulario de soporte
    $('#formSoporteRestaurante').submit(function(e) {
        e.preventDefault();
        
        const idRestaurante = $('#restaurante_soporte').val();
        const nombreBd = $('#restaurante_soporte option:selected').data('nombre-bd');
        
        if (!idRestaurante) {
            alert('Por favor selecciona un restaurante');
            return;
        }
        
        $.ajax({
            url: '<?php echo $BASE_URL; ?>includes/iniciar_soporte.php',
            method: 'POST',
            data: { 
                id_restaurante: idRestaurante,
                nombre_bd: nombreBd
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error al conectar con el restaurante');
            }
        });
    });

    // Salir del modo soporte
    $('#btnSalirSoporte').click(function() {
        $.ajax({
            url: '<?php echo $BASE_URL; ?>includes/salir_soporte.php',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error al salir del modo soporte');
            }
        });
    });
});
</script>
<?php endif; ?>

</body>
</html>