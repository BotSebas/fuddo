<?php
include 'includes/url.php';
include_once 'lang/idiomas.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FUDDO | <?php echo $forgot_titulo; ?></title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>dist/css/adminlte.min.css">
  <!-- Custom Login Style -->
  <link rel="stylesheet" href="<?php echo $BASE_URL; ?>assets/css/fuddo-login.css">
</head>
<body class="hold-transition login-page fuddo-login-bg">
<div class="login-box">
  <div class="card card-outline" style="border-top: 3px solid #27ae60;">
    <div class="card-header text-center">
      <img src="<?php echo $BASE_URL; ?>assets/img/logo-fuddohorizontal.png" alt="FUDDO Logo" style="max-width: 200px;">
    </div>
    <div class="card-body">
      <p class="login-box-msg"><strong><?php echo $forgot_mensaje; ?></strong></p>
      <form action="recover-password.html" method="post">
        <div class="input-group mb-3">
          <input type="email" class="form-control" placeholder="<?php echo $forgot_email_placeholder; ?>">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-block" style="background-color: #27ae60; color: white; border-color: #27ae60;"><?php echo $forgot_btn_solicitar; ?></button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <p class="mt-3 mb-1">
        <a href="<?php echo $BASE_URL; ?>login.php"><?php echo $forgot_link_login; ?></a>
      </p>
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?php echo $BASE_URL; ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo $BASE_URL; ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo $BASE_URL; ?>dist/js/adminlte.min.js"></script>
</body>
</html>
