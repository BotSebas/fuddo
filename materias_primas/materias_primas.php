<?php
include '../includes/auth.php';
include '../includes/url.php';
include_once '../lang/idiomas.php';

// Verificar permiso del módulo
if (!tienePermisoModulo('materias_primas')) {
    header("Location: ../home.php");
    exit();
}

include '../includes/menu.php';
include '../includes/funciones_conversiones.php';

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
          </div>
        </div>
      </section>
    </div>
    <?php
    include '../includes/footer.php';
    exit();
}

include '../includes/conexion.php';

// Definir constante para tabla de materias primas
if (!defined('TBL_MATERIAS_PRIMAS')) define('TBL_MATERIAS_PRIMAS', $TABLE_PREFIX . 'materias_primas');

// Filtro de búsqueda
$busqueda = $_GET['buscar'] ?? '';
$unidad_filtro = $_GET['unidad'] ?? '';

// Consulta base
$sql = "SELECT id, id_materia_prima, nombre, unidad_medida, cantidad_base_comprada, costo_total_base, 
               costo_por_unidad_minima, unidad_minima, cantidad_en_unidad_minima, estado, 
               fecha_ultima_actualizacion
        FROM " . TBL_MATERIAS_PRIMAS . 
        " WHERE 1=1";

if (!empty($busqueda)) {
    $busqueda_escapada = $conexion->real_escape_string($busqueda);
    $sql .= " AND (nombre LIKE '%$busqueda_escapada%' OR id_materia_prima LIKE '%$busqueda_escapada%')";
}

if (!empty($unidad_filtro)) {
    $unidad_filtro = $conexion->real_escape_string($unidad_filtro);
    $sql .= " AND unidad_medida = '$unidad_filtro'";
}

$sql .= " ORDER BY fecha_ultima_actualizacion DESC";

$resultado = $conexion->query($sql);
$materias_primas = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $materias_primas[] = $row;
    }
}

// Paginación
$porPagina = 10;
$total = count($materias_primas);
$paginaActual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$totalPaginas = ceil($total / $porPagina);
$inicio = ($paginaActual - 1) * $porPagina;
$materias_paginadas = array_slice($materias_primas, $inicio, $porPagina);

$unidades = obtenerUnidadesDisponibles();
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Encabezado -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-leaf"></i> Materias Primas</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../home.php">Inicio</a></li>
            <li class="breadcrumb-item active">Materias Primas</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenido Principal -->
  <section class="content">
    <div class="container-fluid">
      
      <?php if(isset($_GET['exito'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <i class="fas fa-check-circle"></i> 
          <?php
          if ($_GET['exito'] === 'creado') {
              echo 'Materia prima creada exitosamente';
          } elseif ($_GET['exito'] === 'actualizado') {
              echo 'Materia prima actualizada exitosamente';
          } elseif ($_GET['exito'] === 'eliminado') {
              echo 'Materia prima eliminada exitosamente';
          }
          ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-circle"></i> 
          <?php
          if ($_GET['error'] === 'duplicado') {
              echo 'Ya existe una materia prima con este nombre';
          } elseif ($_GET['error'] === 'crear') {
              echo 'Error al crear la materia prima';
          } elseif ($_GET['error'] === 'no_encontrado') {
              echo 'La materia prima no fue encontrada';
          }
          ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <!-- Barra de herramientas -->
      <div class="row mb-3">
        <div class="col-md-6">
          <form method="GET" class="form-inline">
            <div class="form-group mr-2 flex-grow-1">
              <input type="text" name="buscar" class="form-control w-100" placeholder="Buscar por nombre..."
                     value="<?php echo htmlspecialchars($busqueda); ?>">
            </div>
            <div class="form-group mr-2">
              <select name="unidad" class="form-control" onchange="this.form.submit()">
                <option value="">Todas las unidades</option>
                <?php foreach ($unidades as $clave => $descripcion): ?>
                  <option value="<?php echo $clave; ?>" <?php echo ($unidad_filtro === $clave) ? 'selected' : ''; ?>>
                    <?php echo $descripcion; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button type="submit" class="btn" style="background-color: #27ae60; color: white;">
              <i class="fas fa-search"></i> Buscar
            </button>
          </form>
        </div>
        <div class="col-md-6 text-right">
          <button type="button" class="btn" style="background-color: #27ae60; color: white;" data-toggle="modal" data-target="#modalNuevaMateriaPrima">
            <i class="fas fa-plus"></i> Nueva Materia Prima
          </button>
        </div>
      </div>

      <!-- Tabla de Materias Primas -->
      <div class="card">
        <div class="card-header" style="background-color: #27ae60;">
          <h3 class="card-title" style="color: white;">Lista de Materias Primas</h3>
        </div>
        <div class="card-body">
          <?php if (count($materias_primas) > 0): ?>
            <div class="table-responsive">
              <table class="table table-hover table-sm">
                <thead class="bg-light">
                  <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Unidad</th>
                    <th>Cantidad Comprada</th>
                    <th>Costo Total</th>
                    <th>Costo/Unidad Mín.</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($materias_paginadas as $mp): ?>
                    <tr>
                      <td><span class="badge" style="background-color: #27ae60;"><?php echo htmlspecialchars($mp['id_materia_prima']); ?></span></td>
                      <td><strong><?php echo htmlspecialchars($mp['nombre']); ?></strong></td>
                      <td>
                        <span class="badge badge-primary"><?php echo strtoupper($mp['unidad_medida']); ?></span>
                      </td>
                      <td class="text-right">
                        <?php echo number_format($mp['cantidad_base_comprada'], 3, '.', ','); ?>
                        <small><?php echo strtoupper($mp['unidad_medida']); ?></small>
                      </td>
                      <td class="text-right">
                        <strong>$<?php echo number_format($mp['costo_total_base'], 2, '.', ','); ?></strong>
                      </td>
                      <td class="text-right" style="color: #27ae60;">
                        <strong>$<?php echo number_format($mp['costo_por_unidad_minima'], 2, '.', ','); ?></strong>
                        <small>/<?php echo strtoupper($mp['unidad_minima']); ?></small>
                      </td>
                      <td>
                        <?php if ($mp['estado'] === 'activo'): ?>
                          <span class="badge badge-fuddo">Activo</span>
                        <?php else: ?>
                          <span class="badge badge-secondary">Inactivo</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm" style="background-color: #27ae60; color: white;"
                                onclick="editarMateriaPrima(<?php echo $mp['id']; ?>)"
                                data-toggle="modal" data-target="#modalEditarMateriaPrima">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" 
                                onclick="confirmarEliminacion(<?php echo $mp['id']; ?>, '<?php echo htmlspecialchars($mp['nombre']); ?>')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
              <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm justify-content-center">
                  <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                    <li class="page-item <?php echo ($i === $paginaActual) ? 'active' : ''; ?>">
                      <a class="page-link" href="?pagina=<?php echo $i; ?>&buscar=<?php echo urlencode($busqueda); ?>&unidad=<?php echo urlencode($unidad_filtro); ?>">
                        <?php echo $i; ?>
                      </a>
                    </li>
                  <?php endfor; ?>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <div class="alert alert-info text-center">
              <i class="fas fa-info-circle"></i> No hay materias primas registradas. 
              <a href="#" data-toggle="modal" data-target="#modalNuevaMateriaPrima" class="alert-link">Crear una nueva materia prima</a>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </section>

</div>

<!-- Modal para Nueva Materia Prima -->
<div class="modal fade" id="modalNuevaMateriaPrima" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #27ae60;">
        <h5 class="modal-title" id="modalLabel">
          <i class="fas fa-plus-circle"></i> Nueva Materia Prima
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="procesar.php">
        <div class="modal-body">
          <input type="hidden" name="accion" value="crear">
          
          <div class="form-group">
            <label for="nombre"><strong>Nombre de la materia prima *</strong></label>
            <input type="text" class="form-control" id="nombre" name="nombre" required 
                   placeholder="Ej: Pollo desmenuzado, Aceite de oliva, Harina">
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="unidad_medida"><strong>Unidad de medida *</strong></label>
                <select class="form-control" id="unidad_medida" name="unidad_medida" required 
                        onchange="actualizarUnidadMinima()">
                  <option value="">-- Seleccionar --</option>
                  <optgroup label="Peso">
                    <option value="kg">Kilogramo (kg)</option>
                    <option value="g">Gramo (g)</option>
                    <option value="lb">Libra (lb)</option>
                  </optgroup>
                  <optgroup label="Volumen">
                    <option value="l">Litro (l)</option>
                    <option value="ml">Mililitro (ml)</option>
                  </optgroup>
                  <optgroup label="Unidad">
                    <option value="und">Unidad (und)</option>
                  </optgroup>
                </select>
                <small class="form-text text-muted">Selecciona la unidad en que compraste la materia prima</small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="cantidad_base_comprada"><strong>Cantidad comprada *</strong></label>
                <div class="input-group">
                  <input type="number" class="form-control" id="cantidad_base_comprada" 
                         name="cantidad_base_comprada" step="0.001" min="0.001" required
                         placeholder="Ej: 1, 5.5, 25">
                  <div class="input-group-append">
                    <span class="input-group-text" id="unidad_display">--</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group">
            <label for="costo_total_base"><strong>Costo total de la cantidad base *</strong></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">$</span>
              </div>
              <input type="number" class="form-control" id="costo_total_base" 
                     name="costo_total_base" step="0.01" min="0" required
                     placeholder="Ej: 20000, 5500.50"
                     onblur="calcularCostoUnitario()">
            </div>
            <small class="form-text text-muted">Costo total que pagaste por la cantidad comprada</small>
          </div>

          <div class="alert alert-info">
            <strong><i class="fas fa-calculator"></i> Cálculo automático:</strong><br>
            <div id="calculoVisual">
              <p>Ingresa los datos para ver el costo por unidad mínima</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Guardar Materia Prima
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para Editar Materia Prima -->
<div class="modal fade" id="modalEditarMateriaPrima" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #27ae60;">
        <h5 class="modal-title">
          <i class="fas fa-edit"></i> Editar Materia Prima
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="procesar.php" id="formEditarMP">
        <div class="modal-body" id="modalEditarContenido">
          <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para Confirmar Eliminación -->
<div class="modal fade" id="modalConfirmarEliminacion" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Confirmar Eliminación</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>¿Estás seguro de que deseas eliminar la materia prima <strong id="nombreMP"></strong>?</p>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i> 
          <strong>Advertencia:</strong> Si esta materia prima se usa en alguna receta, la operación será rechazada.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btnEliminarConfirmado">
          <i class="fas fa-trash"></i> Eliminar
        </button>
      </div>
    </div>
  </div>
</div>

<script>
// Datos temporales para la edición y eliminación
let idMP_Editar = null;
let idMP_Eliminar = null;

// Actualizar unidad mínima cuando selecciona unidad
function actualizarUnidadMinima() {
    const unidad = document.getElementById('unidad_medida').value;
    const display = document.getElementById('unidad_display');
    
    if (unidad) {
        display.textContent = unidad.toUpperCase();
        calcularCostoUnitario();
    } else {
        display.textContent = '--';
    }
}

// Calcular costo unitario automáticamente
function calcularCostoUnitario() {
    const unidad = document.getElementById('unidad_medida').value;
    const cantidad = parseFloat(document.getElementById('cantidad_base_comprada').value);
    const costo = parseFloat(document.getElementById('costo_total_base').value);
    
    if (!unidad || !cantidad || !costo || cantidad <= 0 || costo < 0) {
        document.getElementById('calculoVisual').innerHTML = '<p>Ingresa los datos para ver el costo por unidad mínima</p>';
        return;
    }
    
    // Realizar AJAX para obtener el cálculo
    const formData = new FormData();
    formData.append('accion', 'calcular');
    formData.append('unidad', unidad);
    formData.append('cantidad', cantidad);
    formData.append('costo', costo);
    
    fetch('procesar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            const html = `
                <p><strong>${cantidad} ${unidad}</strong> = <strong>${data.cantidad_convertida.toFixed(0)} ${data.unidad_minima}</strong></p>
                <p>Costo total: <strong>$${parseFloat(costo).toFixed(2)}</strong></p>
                <p><strong>Costo por ${data.unidad_minima}:</strong> <strong>$${data.costo_unitario.toFixed(0)}</strong></p>
            `;
            document.getElementById('calculoVisual').innerHTML = html;
        }
    })
    .catch(error => console.error('Error:', error));
}

// Editar materia prima
function editarMateriaPrima(id) {
    idMP_Editar = id;
    const formData = new FormData();
    formData.append('accion', 'obtener');
    formData.append('id', id);
    
    fetch('procesar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito && data.materia_prima) {
            const mp = data.materia_prima;
            let html = `
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" name="id" value="${mp.id}">
                
                <div class="form-group">
                    <label><strong>ID</strong></label>
                    <input type="text" class="form-control" value="${mp.id_materia_prima}" readonly>
                </div>
                
                <div class="form-group">
                    <label for="edit_nombre"><strong>Nombre *</strong></label>
                    <input type="text" class="form-control" id="edit_nombre" name="nombre" 
                           value="${mp.nombre}" required>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Unidad de medida</strong></label>
                            <input type="text" class="form-control" value="${mp.unidad_medida.toUpperCase()}" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><strong>Cantidad comprada</strong></label>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       value="${parseFloat(mp.cantidad_base_comprada).toFixed(3)}" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text">${mp.unidad_medida.toUpperCase()}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_costo"><strong>Costo total *</strong></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div>
                        <input type="number" class="form-control" id="edit_costo" 
                               name="costo_total_base" step="0.01" min="0" 
                               value="${parseFloat(mp.costo_total_base).toFixed(2)}" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="edit_estado"><strong>Estado</strong></label>
                    <select class="form-control" id="edit_estado" name="estado">
                        <option value="activo" ${mp.estado === 'activo' ? 'selected' : ''}>Activo</option>
                        <option value="inactivo" ${mp.estado === 'inactivo' ? 'selected' : ''}>Inactivo</option>
                    </select>
                </div>
                
                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Información:</strong><br>
                    <p>Cantidad en unidad mínima: <strong>${parseFloat(mp.cantidad_en_unidad_minima).toFixed(0)} ${mp.unidad_minima}</strong></p>
                    <p>Costo por ${mp.unidad_minima}: <strong>$${parseFloat(mp.costo_por_unidad_minima).toFixed(0)}</strong></p>
                </div>
            `;
            document.getElementById('modalEditarContenido').innerHTML = html;
        }
    })
    .catch(error => console.error('Error:', error));
}

// Confirmar eliminación
function confirmarEliminacion(id, nombre) {
    idMP_Eliminar = id;
    document.getElementById('nombreMP').textContent = nombre;
    $('#modalConfirmarEliminacion').modal('show');
}

// Eliminar materia prima confirmado
document.getElementById('btnEliminarConfirmado')?.addEventListener('click', function() {
    const formData = new FormData();
    formData.append('accion', 'eliminar');
    formData.append('id', idMP_Eliminar);
    
    fetch('procesar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            location.href = 'materias_primas.php?exito=eliminado';
        } else {
            alert(data.mensaje || 'Error al eliminar');
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>

<?php include '../includes/footer.php'; ?>
