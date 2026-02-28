<?php
include '../includes/auth.php';
include '../includes/url.php';
include_once '../lang/idiomas.php';

// Verificación de permisos ANTES de incluir menu.php
// Permitir solo a admin de organización o super-admin con restaurante asignado
$tiene_permiso = false;

if (isset($_SESSION['rol'])) {
    // Super-admin con restaurante asignado (soporte)
    if ($_SESSION['rol'] === 'super-admin' && isset($_SESSION['id_restaurante'])) {
        $tiene_permiso = true;
    }
    // Admin de restaurante (usuario local del restaurante - rol 'admin')
    else if ($_SESSION['rol'] === 'admin' && isset($_SESSION['id_restaurante']) && isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] === 'restaurant') {
        $tiene_permiso = true;
    }
    // Admin-restaurante (usuario maestro - rol 'admin-restaurante')
    else if ($_SESSION['rol'] === 'admin-restaurante' && isset($_SESSION['id_restaurante'])) {
        $tiene_permiso = true;
    }
}

if (!$tiene_permiso) {
    header("Location: ../home.php");
    exit();
}

// Ahora incluimos menu.php después de las verificaciones
include '../includes/menu.php';

include '../includes/conexion_master.php';

// Obtener usuarios de usuarios_master que pertenecen a este restaurante
$resultado = null;
$usuarios = [];
$error_db = false;
$id_restaurante = $_SESSION['id_restaurante'] ?? null;

if ($id_restaurante) {
    // Filtro de búsqueda
    $busqueda = $_GET['buscar'] ?? '';
    
    // Obtener todos los usuarios de usuarios_master que pertenecen a este restaurante
    $sql = "SELECT id, usuario, nombre, email, rol, estado, fecha_creacion FROM usuarios_master WHERE id_restaurante = $id_restaurante";
    
    if (!empty($busqueda)) {
        $busqueda_escapada = $conexion_master->real_escape_string($busqueda);
        $sql .= " AND (nombre LIKE '%$busqueda_escapada%' OR usuario LIKE '%$busqueda_escapada%' OR email LIKE '%$busqueda_escapada%')";
    }
    
    $sql .= " ORDER BY fecha_creacion DESC";
    
    $resultado = $conexion_master->query($sql);
    
    if (!$resultado) {
        $error_db = true;
        $usuarios = [];
    } elseif ($resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
}

// Paginación
$porPagina = 10;
$total = count($usuarios);
$paginaActual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$totalPaginas = ceil($total / $porPagina);
$inicio = ($paginaActual - 1) * $porPagina;
$usuariosPagina = array_slice($usuarios, $inicio, $porPagina);

// Definir roles disponibles (todos los roles válidos en usuarios_master)
$roles = [
    'admin-restaurante' => 'Admin de la Organización',
    'admin' => 'Administrador',
    'mesero' => 'Mesero',
    'cocinero' => 'Cocinero',
    'vendedor' => 'Vendedor',
    'mesero_vendedor' => 'Mesero + Vendedor',
    'super-admin' => 'Super Admin'
];
?>
<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Encabezado -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-users"></i> Usuarios de la Organización</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="../home.php">Inicio</a></li>
              <li class="breadcrumb-item active">Usuarios</li>
            </ol>
          </div>
      </div>
    </div>
  </div>

  <!-- Contenido Principal -->
  <section class="content">
    <div class="container-fluid">
      
      <?php if(isset($_GET['exito']) && $_GET['exito'] == 'creado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong><i class="fas fa-check-circle"></i> ¡Éxito!</strong> Usuario creado correctamente.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <?php if(isset($_GET['exito']) && $_GET['exito'] == 'actualizado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong><i class="fas fa-check-circle"></i> ¡Éxito!</strong> Usuario actualizado correctamente.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <?php if($error_db): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong><i class="fas fa-info-circle"></i> Información:</strong> El módulo de usuarios está siendo inicializado. Por favor, intenta en unos momentos.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <?php if(isset($_GET['error']) && $_GET['error'] == 'usuario_existe'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong><i class="fas fa-exclamation-circle"></i> Error:</strong> El nombre de usuario ya existe.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <?php if(isset($_GET['error']) && $_GET['error'] == 'campos_vacios'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong><i class="fas fa-exclamation-circle"></i> Error:</strong> Completa todos los campos requeridos.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <!-- Row de búsqueda y botón -->
      <div class="row mb-3">
        <div class="col-md-8">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Buscar por nombre, usuario o email..." value="<?php echo htmlspecialchars($busqueda); ?>" id="buscar">
            <div class="input-group-append">
              <button class="btn btn-primary" type="button" onclick="buscarUsuarios()">
                <i class="fas fa-search"></i> Buscar
              </button>
            </div>
          </div>
        </div>
        <div class="col-md-4 text-right">
          <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalNuevoUsuario" <?php echo $error_db ? 'disabled' : ''; ?>>
            <i class="fas fa-plus"></i> Nuevo Usuario
          </button>
        </div>
      </div>

      <!-- Tarjeta de Usuarios -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Listado de Usuarios (<?php echo $total; ?> total)</h3>
        </div>
        <div class="card-body">
          <?php if(count($usuariosPagina) > 0): ?>
            <div class="table-responsive">
              <table class="table table-striped table-hover">
                <thead class="bg-light">
                  <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Creación</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach($usuariosPagina as $usuario): ?>
                  <tr>
                    <td><strong><?php echo htmlspecialchars($usuario['usuario']); ?></strong></td>
                    <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($usuario['email'] ?? '-'); ?></td>
                    <td>
                      <span class="badge badge-info">
                        <?php echo $roles[$usuario['rol']] ?? $usuario['rol']; ?>
                      </span>
                    </td>
                    <td>
                      <?php if($usuario['estado'] == 'activo'): ?>
                        <span class="badge badge-success">Activo</span>
                      <?php else: ?>
                        <span class="badge badge-danger">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td>
                      <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])); ?></small>
                    </td>
                    <td>
                      <button type="button" class="btn btn-sm btn-info" onclick="editarUsuario(<?php echo $usuario['id']; ?>, '<?php echo htmlspecialchars($usuario['usuario']); ?>', '<?php echo htmlspecialchars($usuario['nombre']); ?>', '<?php echo htmlspecialchars($usuario['email'] ?? ''); ?>', '<?php echo $usuario['rol']; ?>', '<?php echo $usuario['estado']; ?>')">
                        <i class="fas fa-edit"></i> Editar
                      </button>
                      <button type="button" class="btn btn-sm btn-danger" onclick="cambiarEstado(<?php echo $usuario['id']; ?>, '<?php echo $usuario['estado'] == 'activo' ? 'inactivo' : 'activo'; ?>')">
                        <i class="fas fa-toggle-off"></i>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <?php if($totalPaginas > 1): ?>
            <nav aria-label="Page navigation" class="mt-4">
              <ul class="pagination justify-content-center">
                <?php if($paginaActual > 1): ?>
                <li class="page-item">
                  <a class="page-link" href="?pagina=1<?php echo !empty($busqueda) ? '&buscar=' . urlencode($busqueda) : ''; ?>">Primera</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="?pagina=<?php echo $paginaActual - 1; ?><?php echo !empty($busqueda) ? '&buscar=' . urlencode($busqueda) : ''; ?>">Anterior</a>
                </li>
                <?php endif; ?>

                <?php for($i = max(1, $paginaActual - 2); $i <= min($totalPaginas, $paginaActual + 2); $i++): ?>
                  <?php if($i == $paginaActual): ?>
                  <li class="page-item active">
                    <span class="page-link"><?php echo $i; ?></span>
                  </li>
                  <?php else: ?>
                  <li class="page-item">
                    <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo !empty($busqueda) ? '&buscar=' . urlencode($busqueda) : ''; ?>"><?php echo $i; ?></a>
                  </li>
                  <?php endif; ?>
                <?php endfor; ?>

                <?php if($paginaActual < $totalPaginas): ?>
                <li class="page-item">
                  <a class="page-link" href="?pagina=<?php echo $paginaActual + 1; ?><?php echo !empty($busqueda) ? '&buscar=' . urlencode($busqueda) : ''; ?>">Siguiente</a>
                </li>
                <li class="page-item">
                  <a class="page-link" href="?pagina=<?php echo $totalPaginas; ?><?php echo !empty($busqueda) ? '&buscar=' . urlencode($busqueda) : ''; ?>">Última</a>
                </li>
                <?php endif; ?>
              </ul>
            </nav>
            <?php endif; ?>
          <?php else: ?>
            <div class="alert alert-info">
              <i class="fas fa-info-circle"></i> No hay usuarios registrados. <a href="#" data-toggle="modal" data-target="#modalNuevoUsuario">Crear el primer usuario</a>.
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Modal Nuevo Usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">
          <i class="fas fa-user-plus"></i> Nuevo Usuario
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <form id="formUsuario" method="POST" action="procesar_organizacion.php">
        <div class="modal-body">
          <input type="hidden" name="accion" value="crear" id="accionForm">
          <input type="hidden" name="id_usuario" value="" id="idUsuario">

          <div class="form-group">
            <label for="usuario"><strong>Nombre de Usuario *</strong></label>
            <input type="text" class="form-control" id="usuario" name="usuario" required>
          </div>

          <div class="form-group">
            <label for="password"><strong>Contraseña *</strong></label>
            <input type="password" class="form-control" id="password" name="password" required>
            <small class="form-text text-muted">Mínimo 6 caracteres</small>
          </div>

          <div class="form-group" id="divPasswordConfirm" style="display: none;">
            <label for="password_confirm"><strong>Confirmar Contraseña *</strong></label>
            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
          </div>

          <div class="form-group">
            <label for="nombre"><strong>Nombre Completo *</strong></label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
          </div>

          <div class="form-group">
            <label for="email"><strong>Email</strong></label>
            <input type="email" class="form-control" id="email" name="email">
          </div>

          <div class="form-group">
            <label for="rol"><strong>Rol *</strong></label>
            <select class="form-control" id="rol" name="rol" required>
              <option value="">-- Selecciona un rol --</option>
              <?php foreach($roles as $key => $nombre): ?>
              <option value="<?php echo $key; ?>"><?php echo $nombre; ?></option>
              <?php endforeach; ?>
            </select>
            <small class="form-text text-muted">
              <strong>Roles:</strong><br>
              • Admin de la Organización: Gestiona usuarios y permisos<br>
              • Administrador: Acceso administrativo completo<br>
              • Mesero: Acceso a mesas y comandas<br>
              • Cocinero: Acceso solo a cocina<br>
              • Vendedor: Acceso a mesas y comandas<br>
              • Mesero + Vendedor: Mesero y vendedor combinado<br>
              • Super Admin: Acceso total a todo
            </small>
          </div>

          <div class="form-group">
            <label for="estado"><strong>Estado *</strong></label>
            <select class="form-control" id="estado" name="estado" required>
              <option value="activo" selected>Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success" id="btnGuardar">
            <i class="fas fa-save"></i> Crear Usuario
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function buscarUsuarios() {
  const termino = document.getElementById('buscar').value;
  const url = new URL(window.location);
  if (termino) {
    url.searchParams.set('buscar', termino);
    url.searchParams.set('pagina', '1');
  } else {
    url.searchParams.delete('buscar');
  }
  window.location = url.toString();
}

function editarUsuario(id, usuario, nombre, email, rol, estado) {
  // Limpiar el formulario
  document.getElementById('formUsuario').reset();
  
  // Llenar datos
  document.getElementById('idUsuario').value = id;
  document.getElementById('usuario').value = usuario;
  document.getElementById('usuario').readOnly = true;
  document.getElementById('nombre').value = nombre;
  document.getElementById('email').value = email;
  document.getElementById('rol').value = rol;
  document.getElementById('estado').value = estado;
  document.getElementById('accionForm').value = 'actualizar';
  
  // Mostrar/ocultar el campo de confirmación de contraseña
  document.getElementById('password').value = '';
  document.getElementById('password').removeAttribute('required');
  document.getElementById('password').placeholder = 'Dejar en blanco para no cambiar';
  document.getElementById('divPasswordConfirm').style.display = 'block';
  document.getElementById('password_confirm').value = '';
  
  // Cambiar título del modal
  document.querySelector('#modalNuevoUsuario .modal-title').innerHTML = '<i class="fas fa-user-edit"></i> Editar Usuario';
  document.getElementById('btnGuardar').innerHTML = '<i class="fas fa-save"></i> Actualizar Usuario';
  document.getElementById('btnGuardar').className = 'btn btn-primary';
  
  // Mostrar el modal
  $('#modalNuevoUsuario').modal('show');
}

function cambiarEstado(id, nuevoEstado) {
  if (confirm('¿Estás seguro de cambiar el estado del usuario?')) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'procesar_organizacion.php';
    form.innerHTML = `
      <input type="hidden" name="accion" value="cambiar_estado">
      <input type="hidden" name="id_usuario" value="${id}">
      <input type="hidden" name="estado" value="${nuevoEstado}">
    `;
    document.body.appendChild(form);
    form.submit();
  }
}

// Reset del modal cuando se cierra
$('#modalNuevoUsuario').on('hidden.bs.modal', function() {
  document.getElementById('formUsuario').reset();
  document.getElementById('usuario').readOnly = false;
  document.getElementById('password').setAttribute('required', 'required');
  document.getElementById('password').placeholder = '';
  document.getElementById('divPasswordConfirm').style.display = 'none';
  document.querySelector('#modalNuevoUsuario .modal-title').innerHTML = '<i class="fas fa-user-plus"></i> Nuevo Usuario';
  document.getElementById('btnGuardar').innerHTML = '<i class="fas fa-save"></i> Crear Usuario';
  document.getElementById('btnGuardar').className = 'btn btn-success';
  document.getElementById('accionForm').value = 'crear';
});

// Permitir buscar con Enter
document.getElementById('buscar').addEventListener('keypress', function(event) {
  if (event.key === 'Enter') {
    buscarUsuarios();
  }
});
</script>

<?php
include '../includes/footer.php';
?>
