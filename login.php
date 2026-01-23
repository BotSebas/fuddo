<?php include_once 'lang/idiomas.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FUDDO | <?php echo $login_titulo_pagina; ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- SweetAlert2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <!-- Custom Login Style -->
  <link rel="stylesheet" href="assets/css/fuddo-login.css">
</head>
<body class="hold-transition login-page fuddo-login-bg">
<div class="login-box">
  <div class="card card-outline" style="border-top: 3px solid #27ae60;">
    <div class="card-header text-center">
      <img src="assets/img/logo-fuddohorizontal.png" alt="FUDDO Logo" style="max-width: 200px;">
    </div>
    <div class="card-body">
      <p class="login-box-msg"><strong><?php echo $login_mensaje; ?></strong></p>

      <form action="validar.php" method="post" id="loginForm">
        <div class="input-group mb-3">
          <input type="text" class="form-control" placeholder="<?php echo $login_usuario_placeholder; ?>" name="usuario" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" placeholder="<?php echo $login_password_placeholder; ?>" name="password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                <?php echo $login_recordarme; ?>
              </label>
            </div>
          </div>
          <div class="col-4">
            <button type="submit" class="btn btn-block" style="background-color: #27ae60; color: white;"><?php echo $login_btn_entrar; ?></button>
          </div>
        </div>
      </form>

      <p class="mb-1 mt-3">
        <a href="forgot-password.php"><?php echo $login_olvide_password; ?></a>
      </p>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

<script>
<?php if (isset($_GET['error'])): ?>
  <?php
  $errorMessages = [
    'campos_vacios' => 'Por favor completa todos los campos',
    'usuario_inactivo' => 'Tu usuario está inactivo. Contacta al administrador',
    'restaurante_inactivo' => 'Plataforma inhabilitada. Comuníquese con el área de soporte',
    'bd_no_existe' => 'Error de configuración. Contacta al soporte',
    'conexion_restaurante' => 'Error al conectar con la base de datos',
    'usuario_no_local' => 'Usuario no encontrado en el sistema local',
    'credenciales_invalidas' => 'Usuario o contraseña incorrectos',
    'usuario_no_existe' => 'Usuario o contraseña incorrectos',
    '1' => 'Usuario o contraseña incorrectos'
  ];
  $errorMsg = $errorMessages[$_GET['error']] ?? 'Error de autenticación';
  ?>
  Swal.fire({
    icon: 'error',
    title: 'Error de autenticación',
    text: '<?= $errorMsg ?>',
    confirmButtonColor: '#27ae60'
  });
<?php endif; ?>

<?php if (isset($_GET['sesion'])): ?>
  Swal.fire({
    icon: 'warning',
    title: 'Sesión expirada',
    text: 'Por favor, inicia sesión nuevamente',
    confirmButtonColor: '#27ae60'
  });
<?php endif; ?>
</script>
</body>
</html>
