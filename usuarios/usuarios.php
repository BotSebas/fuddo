<?php
include '../includes/auth.php';

// Verificar que sea super-admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
    header("Location: ../home.php");
    exit();
}

include '../includes/url.php';
include_once '../lang/idiomas.php';
include '../includes/menu.php';
include_once '../includes/conexion_master.php';

// Obtener lista de restaurantes para el dropdown
$restaurantes_query = $conexion_master->query("SELECT id, nombre FROM restaurantes ORDER BY nombre");
$restaurantes = [];
while ($rest = $restaurantes_query->fetch_assoc()) {
    $restaurantes[] = $rest;
}

// Obtener lista de roles disponibles desde la tabla roles_master
$roles_query = $conexion_master->query("SELECT rol FROM roles_master ORDER BY rol");
$roles_disponibles = [];
while ($role = $roles_query->fetch_assoc()) {
    $roles_disponibles[] = $role['rol'];
}

// Filtro de búsqueda
$busqueda = $_GET['buscar'] ?? '';

// Consulta a la base de datos (usuarios_master con restaurante)
$sql = "SELECT um.id, um.usuario, um.nombre, um.email, um.rol, um.estado, um.fecha_creacion, um.foto, 
               um.id_restaurante, r.nombre as nombre_restaurante 
        FROM usuarios_master um 
        LEFT JOIN restaurantes r ON um.id_restaurante = r.id";

if (!empty($busqueda)) {
    $busqueda_escapada = $conexion_master->real_escape_string($busqueda);
    $sql .= " WHERE um.nombre LIKE '%$busqueda_escapada%' OR um.usuario LIKE '%$busqueda_escapada%' OR um.email LIKE '%$busqueda_escapada%'";
}

$sql .= " ORDER BY um.fecha_creacion DESC";

$resultado = $conexion_master->query($sql);
$usuarios = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

// Paginación
$porPagina = 10;
$total = count($usuarios);
$paginaActual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$totalPaginas = ceil($total / $porPagina);
$inicio = ($paginaActual - 1) * $porPagina;
$usuariosPagina = array_slice($usuarios, $inicio, $porPagina);
?>
<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Encabezado -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><?php echo $usuarios_titulo; ?></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../home.php"><?php echo $misc_home ?? 'Home'; ?></a></li>
            <li class="breadcrumb-item active"><?php echo $menu_usuarios; ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenido Principal -->
  <section class="content">
    <div class="container-fluid">
      
      <!-- Botón + búsqueda -->
      <div class="row mb-3">
        <div class="col-md-6">
          <button class="btn btn-fuddo" data-toggle="modal" data-target="#modalUsuario" onclick="abrirModal()">
            <i class="fas fa-user-plus mr-2"></i><?php echo $usuarios_nuevo; ?>
          </button>
        </div>
        <div class="col-md-6">
          <form method="get" class="float-right">
            <div class="input-group">
              <input type="text" name="buscar" class="form-control" placeholder="<?php echo $usuarios_buscar; ?>" value="<?php echo htmlspecialchars($busqueda); ?>">
              <div class="input-group-append">
                <button class="btn btn-primary"><?php echo $usuarios_btn_buscar; ?></button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Tabla de usuarios -->
      <div class="card">
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead class="table-fuddo">
              <tr>
                <th><?php echo $usuarios_foto; ?></th>
                <th><?php echo $usuarios_usuario; ?></th>
                <th><?php echo $usuarios_restaurante; ?></th>
                <th><?php echo $usuarios_rol; ?></th>
                <th><?php echo $usuarios_estado; ?></th>
                <th><?php echo $usuarios_acciones; ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuariosPagina as $usuario): ?>
              <tr>
                <td>
                  <?php 
                  $foto = !empty($usuario['foto']) && file_exists('../' . $usuario['foto']) 
                    ? $BASE_URL . $usuario['foto'] 
                    : $BASE_URL . 'dist/img/user2-160x160.jpg';
                  ?>
                  <img src="<?= $foto ?>" class="img-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="Foto">
                </td>
                <td><strong><?= htmlspecialchars($usuario['usuario']) ?></strong></td>
                <td>
                  <?php if ($usuario['nombre_restaurante']): ?>
                    <span class="badge badge-success"><?= htmlspecialchars($usuario['nombre_restaurante']) ?></span>
                  <?php else: ?>
                    <span class="badge badge-secondary"><?php echo $usuarios_sin_asignar; ?></span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                  $badges = [
                    'super-admin' => 'badge-danger',
                    'admin-restaurante' => 'badge-primary'
                  ];
                  $badgeClass = $badges[$usuario['rol']] ?? 'badge-secondary';
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= ucfirst($usuario['rol']) ?></span>
                </td>
                <td>
                  <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox" class="custom-control-input" id="estadoSwitch<?= $usuario['id'] ?>" 
                           <?= $usuario['estado'] == 'activo' ? 'checked' : '' ?> 
                           onchange="cambiarEstadoToggle(<?= $usuario['id'] ?>)">
                    <label class="custom-control-label" for="estadoSwitch<?= $usuario['id'] ?>">
                      <?= ucfirst($usuario['estado']) ?>
                    </label>
                  </div>
                </td>
                <td>
                  <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalUsuario" onclick='abrirModal(<?= json_encode($usuario) ?>)'>
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="btn btn-danger btn-sm" onclick="eliminarUsuario(<?= $usuario['id'] ?>)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($usuariosPagina)): ?>
                <tr><td colspan="6" class="text-center"><?php echo $usuarios_sin_resultados; ?></td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginador -->
      <?php if ($totalPaginas > 1): ?>
      <nav>
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i == $paginaActual ? 'active' : ''; ?>">
              <a class="page-link" href="?pagina=<?= $i ?>&buscar=<?= urlencode($busqueda) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
      <?php endif; ?>
    </div>
  </section>
</div>

<!-- Modal para agregar/editar usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #27ae60; color: white;">
        <h5 class="modal-title" id="modalTitulo"><?php echo $usuarios_nuevo; ?></h5>
        <button type="button" class="close" data-dismiss="modal" style="color: white;">
          <span>&times;</span>
        </button>
      </div>
      <form id="formUsuario" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" id="usuario_id" name="id">
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="usuario"><?php echo $usuarios_usuario; ?> *</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="nombre"><?php echo $usuarios_nombre_completo; ?> *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="email"><?php echo $usuarios_email; ?></label>
                <input type="email" class="form-control" id="email" name="email">
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="id_restaurante"><?php echo $usuarios_restaurante_label; ?></label>
                <select class="form-control" id="id_restaurante" name="id_restaurante">
                  <option value=""><?php echo $sel_seleccionar_restaurante_dots; ?></option>
                  <?php foreach ($restaurantes as $rest): ?>
                    <option value="<?= $rest['id'] ?>"><?= htmlspecialchars($rest['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="rol"><?php echo $usuarios_rol_label; ?> <span id="rol_requerido">*</span></label>
                <select class="form-control" id="rol" name="rol">
                  <option value=""><?php echo $usuarios_rol_mantener; ?></option>
                  <?php foreach ($roles_disponibles as $role): ?>
                    <option value="<?= htmlspecialchars($role) ?>"><?= htmlspecialchars($role) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="password"><?php echo $usuarios_password; ?> <span id="password_opcional" style="color: #999;"><?php echo $usuarios_password_opcional; ?></span></label>
                <div class="input-group">
                  <input type="password" class="form-control" id="password" name="password" minlength="6">
                  <div class="input-group-append">
                    <button type="button" class="btn btn-outline-secondary" id="btnGenerarPassword" onclick="generarPasswordSugerida()" title="Generar contraseña aleatoria de 10 caracteres">
                      <i class="fas fa-dice"></i> Sugerir
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btnMostrarPassword" onclick="togglePasswordVisibility()" title="Mostrar/Ocultar contraseña">
                      <i class="fas fa-eye"></i>
                    </button>
                  </div>
                </div>
                <small class="text-muted">Mínimo 6 caracteres • Sugerencia: 10 caracteres (mayúsculas, minúsculas, números)</small>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="foto"><?php echo $usuarios_foto_label; ?></label>
                <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*">
                <small class="text-muted"><?php echo $usuarios_foto_desc; ?></small>
                <div id="preview_foto" class="mt-2"></div>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $usuarios_btn_cancelar; ?></button>
          <button type="submit" class="btn btn-fuddo"><?php echo $usuarios_btn_guardar; ?></button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="<?php echo $BASE_URL; ?>assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
<script>
let modoEdicion = false;

function abrirModal(usuario = null) {
  modoEdicion = usuario !== null;
  
  if (modoEdicion) {
    document.getElementById('modalTitulo').textContent = '<?php echo $usuarios_editar_titulo; ?>';
    document.getElementById('usuario_id').value = usuario.id;
    document.getElementById('usuario').value = usuario.usuario;
    document.getElementById('nombre').value = usuario.nombre;
    document.getElementById('email').value = usuario.email || '';
    document.getElementById('id_restaurante').value = usuario.id_restaurante || '';
    document.getElementById('rol').value = ''; // Vacío para mantener rol actual
    document.getElementById('rol').removeAttribute('required'); // NO es requerido en edición
    document.getElementById('rol_requerido').style.display = 'none'; // Ocultar asterisco
    document.getElementById('password').required = false;
    document.getElementById('password_opcional').style.display = 'inline';
    
    // Mostrar foto actual
    if (usuario.foto) {
      const fotoUrl = '<?= $BASE_URL ?>' + usuario.foto;
      document.getElementById('preview_foto').innerHTML = 
        `<img src="${fotoUrl}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;" alt="Foto actual">`;
    }
  } else {
    document.getElementById('modalTitulo').textContent = '<?php echo $usuarios_nuevo; ?>';
    document.getElementById('formUsuario').reset();
    document.getElementById('usuario_id').value = '';
    document.getElementById('rol').setAttribute('required', 'required'); // Requerido para nuevo
    document.getElementById('rol_requerido').style.display = 'inline'; // Mostrar asterisco
    document.getElementById('password').required = true;
    document.getElementById('password_opcional').style.display = 'none';
    document.getElementById('preview_foto').innerHTML = '';
  }
}

// Preview de foto
document.getElementById('foto').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('preview_foto').innerHTML = 
        `<img src="${e.target.result}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;" alt="Preview">`;
    };
    reader.readAsDataURL(file);
  }
});

// Enviar formulario
document.getElementById('formUsuario').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  
  fetch('procesar.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: '<?php echo isset($msg_exito_titulo) ? $msg_exito_titulo : "Éxito"; ?>!',
        text: data.message,
        confirmButtonColor: '#27ae60'
      }).then(() => {
        location.reload();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: '<?php echo isset($msg_error_titulo) ? $msg_error_titulo : "Error"; ?>',
        text: data.message,
        confirmButtonColor: '#27ae60'
      });
    }
  })
  .catch(error => {
    Swal.fire({
      icon: 'error',
      title: '<?php echo isset($msg_error_titulo) ? $msg_error_titulo : "Error"; ?>',
      text: 'Error al procesar la solicitud',
      confirmButtonColor: '#27ae60'
    });
  });
});

// Cambiar estado
function cambiarEstadoToggle(id) {
  fetch('cambiar_estado.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `id=${id}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      Swal.fire({
        icon: 'success',
        title: '<?php echo isset($msg_exito_titulo) ? $msg_exito_titulo : "Éxito"; ?>!',
        text: data.message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: '<?php echo isset($msg_error_titulo) ? $msg_error_titulo : "Error"; ?>',
        text: data.message,
        confirmButtonColor: '#27ae60'
      });
      location.reload();
    }
  });
}

// Manejar cambio de rol para hacer opcional el restaurante
$(document).ready(function() {
  $('#rol').on('change', function() {
    const rol = $(this).val();
    const $restaurante = $('#id_restaurante');
    
    if (rol === 'super-admin') {
      $restaurante.prop('required', false);
      $('#restaurante_opcional').show();
    } else {
      $restaurante.prop('required', true);
      $('#restaurante_opcional').hide();
    }
  });
  
  // Trigger inicial
  $('#rol').trigger('change');
});

// Eliminar usuario
function eliminarUsuario(id) {
  Swal.fire({
    title: '<?php echo isset($msg_confirmar_eliminar) ? $msg_confirmar_eliminar : "¿Estás seguro?"; ?>',
    text: "<?php echo isset($msg_accion_no_deshacer) ? $msg_accion_no_deshacer : 'Esta acción no se puede deshacer'; ?>",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#27ae60',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete',
    cancelButtonText: '<?php echo $usuarios_btn_cancelar; ?>'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch('eliminar.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: data.message,
            confirmButtonColor: '#27ae60'
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: '<?php echo isset($msg_error_titulo) ? $msg_error_titulo : "Error"; ?>',
            text: data.message,
            confirmButtonColor: '#27ae60'
          });
        }
      });
    }
  });
}

// Función para generar contraseña sugerida
function generarPasswordSugerida() {
  const mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  const minusculas = 'abcdefghijklmnopqrstuvwxyz';
  const numeros = '0123456789';
  const caracteres = mayusculas + minusculas + numeros;
  
  let password = '';
  
  // Asegurar al menos uno de cada tipo
  password += mayusculas.charAt(Math.floor(Math.random() * mayusculas.length));
  password += minusculas.charAt(Math.floor(Math.random() * minusculas.length));
  password += numeros.charAt(Math.floor(Math.random() * numeros.length));
  
  // Llenar el resto aleatoriamente (10 caracteres totales)
  for (let i = password.length; i < 10; i++) {
    password += caracteres.charAt(Math.floor(Math.random() * caracteres.length));
  }
  
  // Mezclar la contraseña
  password = password.split('').sort(() => Math.random() - 0.5).join('');
  
  // Asignar al campo
  const passwordField = document.getElementById('password');
  passwordField.value = password;
  passwordField.type = 'text'; // Mostrar la contraseña generada
  
  // Cambiar el icono del botón temporalmente
  const btnGenerar = document.getElementById('btnGenerarPassword');
  const iconoOriginal = btnGenerar.innerHTML;
  btnGenerar.innerHTML = '<i class="fas fa-check text-success"></i> Copiada';
  btnGenerar.disabled = true;
  
  // Restaurar el botón después de 2 segundos
  setTimeout(() => {
    btnGenerar.innerHTML = iconoOriginal;
    btnGenerar.disabled = false;
  }, 2000);
}

// Función para mostrar/ocultar contraseña
function togglePasswordVisibility() {
  const passwordField = document.getElementById('password');
  const btnMostrar = document.getElementById('btnMostrarPassword');
  
  if (passwordField.type === 'password') {
    passwordField.type = 'text';
    btnMostrar.innerHTML = '<i class="fas fa-eye-slash"></i>';
  } else {
    passwordField.type = 'password';
    btnMostrar.innerHTML = '<i class="fas fa-eye"></i>';
  }
}
</script>

<?php include '../includes/footer.php'; ?>
