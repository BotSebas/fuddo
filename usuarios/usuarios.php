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
          <h1 class="m-0">Gestión de Usuarios</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../home.php">Home</a></li>
            <li class="breadcrumb-item active">Usuarios</li>
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
            <i class="fas fa-user-plus mr-2"></i>Nuevo Usuario
          </button>
        </div>
        <div class="col-md-6">
          <form method="get" class="float-right">
            <div class="input-group">
              <input type="text" name="buscar" class="form-control" placeholder="Buscar usuario..." value="<?php echo htmlspecialchars($busqueda); ?>">
              <div class="input-group-append">
                <button class="btn btn-primary">Buscar</button>
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
                <th>Foto</th>
                <th>Usuario</th>
                <th>Nombre</th>
                <th>Email</th>
                <th>Restaurante</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Fecha Creación</th>
                <th>Acciones</th>
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
                <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                <td><?= htmlspecialchars($usuario['email'] ?? 'N/A') ?></td>
                <td>
                  <?php if ($usuario['nombre_restaurante']): ?>
                    <span class="badge badge-success"><?= htmlspecialchars($usuario['nombre_restaurante']) ?></span>
                  <?php else: ?>
                    <span class="badge badge-secondary">Sin asignar</span>
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
                <td><?= date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])) ?></td>
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
                <tr><td colspan="8" class="text-center">No se encontraron usuarios</td></tr>
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
        <h5 class="modal-title" id="modalTitulo">Nuevo Usuario</h5>
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
                <label for="usuario">Usuario *</label>
                <input type="text" class="form-control" id="usuario" name="usuario" required>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="nombre">Nombre Completo *</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email">
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="id_restaurante">Restaurante <span id="restaurante_opcional" style="color: #999;">(no requerido para Super Admin)</span></label>
                <select class="form-control" id="id_restaurante" name="id_restaurante">
                  <option value="">Seleccionar restaurante...</option>
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
                <label for="rol">Rol *</label>
                <select class="form-control" id="rol" name="rol" required>
                  <option value="admin-restaurante">Admin Restaurante</option>
                  <option value="super-admin">Super Admin</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="password">Contraseña <span id="password_opcional" style="color: #999;">(dejar vacío para mantener)</span></label>
                <input type="password" class="form-control" id="password" name="password" minlength="6">
                <small class="text-muted">Mínimo 6 caracteres</small>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label for="foto">Foto de Perfil</label>
                <input type="file" class="form-control-file" id="foto" name="foto" accept="image/*">
                <small class="text-muted">JPG, PNG o GIF (máx. 2MB)</small>
                <div id="preview_foto" class="mt-2"></div>
              </div>
            </div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-fuddo">Guardar</button>
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
    document.getElementById('modalTitulo').textContent = 'Editar Usuario';
    document.getElementById('usuario_id').value = usuario.id;
    document.getElementById('usuario').value = usuario.usuario;
    document.getElementById('nombre').value = usuario.nombre;
    document.getElementById('email').value = usuario.email || '';
    document.getElementById('id_restaurante').value = usuario.id_restaurante || '';
    document.getElementById('rol').value = usuario.rol;
    document.getElementById('password').required = false;
    document.getElementById('password_opcional').style.display = 'inline';
    
    // Mostrar foto actual
    if (usuario.foto) {
      const fotoUrl = '<?= $BASE_URL ?>' + usuario.foto;
      document.getElementById('preview_foto').innerHTML = 
        `<img src="${fotoUrl}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;" alt="Foto actual">`;
    }
  } else {
    document.getElementById('modalTitulo').textContent = 'Nuevo Usuario';
    document.getElementById('formUsuario').reset();
    document.getElementById('usuario_id').value = '';
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
        title: '¡Éxito!',
        text: data.message,
        confirmButtonColor: '#27ae60'
      }).then(() => {
        location.reload();
      });
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: data.message,
        confirmButtonColor: '#27ae60'
      });
    }
  })
  .catch(error => {
    Swal.fire({
      icon: 'error',
      title: 'Error',
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
        title: '¡Éxito!',
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
        title: 'Error',
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
    title: '¿Estás seguro?',
    text: "Esta acción no se puede deshacer",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#27ae60',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
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
            title: '¡Eliminado!',
            text: data.message,
            confirmButtonColor: '#27ae60'
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message,
            confirmButtonColor: '#27ae60'
          });
        }
      });
    }
  });
}
</script>

<?php include '../includes/footer.php'; ?>
