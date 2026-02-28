<?php
include '../includes/auth.php';
include '../includes/url.php';
include_once '../lang/idiomas.php';

// Verificar permiso del módulo
if (!tienePermisoModulo('recetas')) {
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

// Definir constantes de tablas
if (!defined('TBL_RECETAS')) define('TBL_RECETAS', $TABLE_PREFIX . 'recetas');
if (!defined('TBL_RECETA_INGREDIENTES')) define('TBL_RECETA_INGREDIENTES', $TABLE_PREFIX . 'receta_ingredientes');
if (!defined('TBL_MATERIAS_PRIMAS')) define('TBL_MATERIAS_PRIMAS', $TABLE_PREFIX . 'materias_primas');

// Filtro de búsqueda
$busqueda = $_GET['buscar'] ?? '';

// Consulta base
$sql = "SELECT id, id_receta, nombre_platillo, descripcion, costo_total_receta, 
               id_producto_asociado, estado, fecha_ultima_actualizacion
        FROM " . TBL_RECETAS . 
        " WHERE 1=1";

if (!empty($busqueda)) {
    $busqueda_escapada = $conexion->real_escape_string($busqueda);
    $sql .= " AND (nombre_platillo LIKE '%$busqueda_escapada%' OR id_receta LIKE '%$busqueda_escapada%')";
}

$sql .= " ORDER BY fecha_ultima_actualizacion DESC";

$resultado = $conexion->query($sql);
$recetas = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $recetas[] = $row;
    }
}

// Paginación
$porPagina = 10;
$total = count($recetas);
$paginaActual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$totalPaginas = ceil($total / $porPagina);
$inicio = ($paginaActual - 1) * $porPagina;
$recetas_paginadas = array_slice($recetas, $inicio, $porPagina);

// Obtener materias primas para el formulario
$sqlMP = "SELECT id_materia_prima, nombre, unidad_minima, costo_por_unidad_minima 
          FROM " . TBL_MATERIAS_PRIMAS . " 
          WHERE estado = 'activo'
          ORDER BY nombre ASC";
$resMP = $conexion->query($sqlMP);
$materias_primas = [];
if ($resMP && $resMP->num_rows > 0) {
    while ($row = $resMP->fetch_assoc()) {
        $materias_primas[] = $row;
    }
}
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Encabezado -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-recipe"></i> Recetas</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../home.php">Inicio</a></li>
            <li class="breadcrumb-item active">Recetas</li>
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
              echo 'Receta creada exitosamente';
          } elseif ($_GET['exito'] === 'actualizado') {
              echo 'Receta actualizada exitosamente';
          } elseif ($_GET['exito'] === 'eliminado') {
              echo 'Receta eliminada exitosamente';
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
          if ($_GET['error'] === 'sin_ingredientes') {
              echo 'La receta debe tener al menos un ingrediente';
          } elseif ($_GET['error'] === 'no_materias_primas') {
              echo 'No hay materias primas disponibles. Primero crea materias primas.';
          } elseif ($_GET['error'] === 'no_encontrado') {
              echo 'La receta no fue encontrada';
          }
          ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>

      <?php if (count($materias_primas) === 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <i class="fas fa-exclamation-triangle"></i> 
          <strong>Advertencia:</strong> No hay materias primas disponibles. 
          <a href="../materias_primas/materias_primas.php" class="alert-link">Crea materias primas primero</a>
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
            <button type="submit" class="btn" style="background-color: #27ae60; color: white;">
              <i class="fas fa-search"></i> Buscar
            </button>
          </form>
        </div>
        <div class="col-md-6 text-right">
          <?php if (count($materias_primas) > 0): ?>
            <button type="button" class="btn" style="background-color: #27ae60; color: white;" data-toggle="modal" data-target="#modalNuevaReceta">
              <i class="fas fa-plus"></i> Nueva Receta
            </button>
          <?php endif; ?>
        </div>
      </div>

      <!-- Tabla de Recetas -->
      <div class="card">
        <div class="card-header" style="background-color: #27ae60;">
          <h3 class="card-title" style="color: white;">Lista de Recetas</h3>
        </div>
        <div class="card-body">
          <?php if (count($recetas) > 0): ?>
            <div class="table-responsive">
              <table class="table table-hover table-sm">
                <thead class="bg-light">
                  <tr>
                    <th>ID</th>
                    <th>Nombre del Platillo</th>
                    <th>Ingredientes</th>
                    <th>Costo Total</th>
                    <th>Producto</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($recetas_paginadas as $receta): 
                    // Contar ingredientes
                    $sqlIng = "SELECT COUNT(*) as total FROM " . TBL_RECETA_INGREDIENTES . 
                              " WHERE id_receta = '" . $receta['id_receta'] . "'";
                    $resIng = $conexion->query($sqlIng);
                    $rowIng = $resIng->fetch_assoc();
                    $totalIngredientes = $rowIng['total'];
                    ?>
                    <tr>
                      <td><span class="badge" style="background-color: #27ae60;"><?php echo htmlspecialchars($receta['id_receta']); ?></span></td>
                      <td><strong><?php echo htmlspecialchars($receta['nombre_platillo']); ?></strong></td>
                      <td>
                        <span class="badge badge-secondary"><?php echo $totalIngredientes; ?> ingredientes</span>
                      </td>
                      <td class="text-right">
                        <strong style="color: #27ae60;">
                          $<?php echo number_format($receta['costo_total_receta'], 2, '.', ','); ?>
                        </strong>
                      </td>
                      <td>
                        <?php if ($receta['id_producto_asociado']): ?>
                          <span class="badge badge-fuddo">
                            <i class="fas fa-check-circle"></i> Vinculado
                          </span>
                        <?php else: ?>
                          <span class="badge badge-warning">
                            <i class="fas fa-link"></i> Sin vincular
                          </span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <?php if ($receta['estado'] === 'activo'): ?>
                          <span class="badge badge-fuddo">Activo</span>
                        <?php else: ?>
                          <span class="badge badge-secondary">Inactivo</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <button type="button" class="btn btn-sm" style="background-color: #27ae60; color: white;"
                                onclick="editarReceta(<?php echo $receta['id']; ?>)"
                                data-toggle="modal" data-target="#modalEditarReceta">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm" style="background-color: #27ae60; color: white;"
                                onclick="verDetalles(<?php echo $receta['id']; ?>)"
                                data-toggle="modal" data-target="#modalDetallesReceta">
                          <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" 
                                onclick="confirmarEliminacion(<?php echo $receta['id']; ?>, '<?php echo htmlspecialchars($receta['nombre_platillo']); ?>')">
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
                      <a class="page-link" href="?pagina=<?php echo $i; ?>&buscar=<?php echo urlencode($busqueda); ?>">
                        <?php echo $i; ?>
                      </a>
                    </li>
                  <?php endfor; ?>
                </ul>
              </nav>
            <?php endif; ?>
          <?php else: ?>
            <div class="alert alert-info text-center">
              <i class="fas fa-info-circle"></i> No hay recetas registradas. 
              <?php if (count($materias_primas) > 0): ?>
                <a href="#" data-toggle="modal" data-target="#modalNuevaReceta" class="alert-link">Crear una nueva receta</a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </section>

</div>

<!-- Modal para Nueva Receta -->
<div class="modal fade" id="modalNuevaReceta" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #27ae60;">
        <h5 class="modal-title" id="modalLabel">
          <i class="fas fa-plus-circle"></i> Nueva Receta
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="procesar.php" id="formNuevaReceta">
        <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
          <input type="hidden" name="accion" value="crear">
          
          <div class="form-group">
            <label for="nombre_platillo"><strong>Nombre del platillo *</strong></label>
            <input type="text" class="form-control" id="nombre_platillo" name="nombre_platillo" required 
                   placeholder="Ej: Pollo a la naranja, Pasta Alfredo">
          </div>

          <div class="form-group">
            <label for="descripcion"><strong>Descripción (opcional)</strong></label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"
                      placeholder="Describe los detalles de la receta..."></textarea>
          </div>

          <hr>
          <h5><i class="fas fa-list"></i> Ingredientes</h5>
          
          <div id="contenedorIngredientes">
            <!-- Los ingredientes se agregarán dinámicamente aquí -->
            <div class="ingrediente-item" data-ingrediente="0">
              <div class="card card-outline card-primary mb-2">
                <div class="card-body p-2">
                  <div class="row">
                    <div class="col-md-6">
                      <label class="small"><strong>Materia Prima *</strong></label>
                      <select name="materia_prima[]" class="form-control form-control-sm ingrediente-select"
                              onchange="actualizarUnidadIngrediente(0)" required>
                        <option value="">-- Seleccionar --</option>
                        <?php foreach ($materias_primas as $mp): ?>
                          <option value="<?php echo $mp['id_materia_prima']; ?>" 
                                  data-unidad="<?php echo $mp['unidad_minima']; ?>"
                                  data-costo="<?php echo $mp['costo_por_unidad_minima']; ?>">
                            <?php echo htmlspecialchars($mp['nombre']) . " (" . $mp['unidad_minima'] . ")"; ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-4">
                      <label class="small"><strong>Cantidad *</strong></label>
                      <div class="input-group input-group-sm">
                        <input type="number" name="cantidad[]" class="form-control cantidad-ingrediente" 
                               step="0.001" min="0.001" placeholder="0.000" required
                               onchange="recalcularCostoIngrediente(0)">
                        <div class="input-group-append">
                          <span class="input-group-text unidad-ingrediente">--</span>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-2 text-right">
                      <label class="small"><strong>Costo</strong></label>
                      <p class="form-control-plaintext small costo-ingrediente">$0.00</p>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-md-12">
                      <small class="form-text text-muted">
                        <input type="text" class="form-control form-control-sm" name="nota[]" 
                               placeholder="Nota (opcional)">
                      </small>
                    </div>
                  </div>
                  <button type="button" class="btn btn-sm btn-danger mt-2" 
                          onclick="eliminarIngrediente(0)">
                    <i class="fas fa-trash-alt"></i> Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <button type="button" class="btn btn-outline-primary btn-sm" id="btnAgregarIngrediente">
            <i class="fas fa-plus"></i> Agregar Ingrediente
          </button>

          <hr>

          <div class="alert alert-info">
            <strong><i class="fas fa-calculator"></i> Costo Total de la Receta:</strong><br>
            <h4 style="color: #27ae60;"><strong id="costoTotalReceta">$0.00</strong></h4>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn" style="background-color: #27ae60; color: white;">
            <i class="fas fa-save"></i> Guardar Receta
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para Editar Receta -->
<div class="modal fade" id="modalEditarReceta" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #27ae60;">
        <h5 class="modal-title">
          <i class="fas fa-edit"></i> Editar Receta
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="procesar.php" id="formEditarReceta">
        <div class="modal-body" id="modalEditarContenido" style="max-height: 600px; overflow-y: auto;">
          <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning">
            <i class="fas fa-save"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal para Ver Detalles -->
<div class="modal fade" id="modalDetallesReceta" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-white" style="background-color: #27ae60;">
        <h5 class="modal-title"><i class="fas fa-eye"></i> Detalles de la Receta</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="modalDetallesContenido">
        <p class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
      </div>
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
        <p>¿Estás seguro de que deseas eliminar la receta <strong id="nombreReceta"></strong>?</p>
        <div class="alert alert-warning">
          <i class="fas fa-exclamation-triangle"></i> 
          <strong>Advertencia:</strong> También se eliminará el producto asociado si existe.
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
// Variables globales
let contadorIngredientes = 1;
let idReceta_Eliminar = null;
const materiasPrimas = <?php echo json_encode($materias_primas); ?>;

// Agregar ingrediente dinámico
document.getElementById('btnAgregarIngrediente')?.addEventListener('click', function() {
    agregarIngrediente();
});

function agregarIngrediente() {
    const contenedor = document.getElementById('contenedorIngredientes');
    const html = `
        <div class="ingrediente-item" data-ingrediente="${contadorIngredientes}">
            <div class="card card-outline card-primary mb-2">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="small"><strong>Materia Prima *</strong></label>
                            <select name="materia_prima[]" class="form-control form-control-sm ingrediente-select"
                                    onchange="actualizarUnidadIngrediente(${contadorIngredientes})" required>
                                <option value="">-- Seleccionar --</option>
                                ${materiasPrimas.map(mp => 
                                    `<option value="${mp.id_materia_prima}" 
                                            data-unidad="${mp.unidad_minima}"
                                            data-costo="${mp.costo_por_unidad_minima}">
                                        ${mp.nombre} (${mp.unidad_minima})
                                    </option>`
                                ).join('')}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small"><strong>Cantidad *</strong></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="cantidad[]" class="form-control cantidad-ingrediente" 
                                       step="0.001" min="0.001" placeholder="0.000" required
                                       onchange="recalcularCostoIngrediente(${contadorIngredientes})">
                                <div class="input-group-append">
                                    <span class="input-group-text unidad-ingrediente">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-right">
                            <label class="small"><strong>Costo</strong></label>
                            <p class="form-control-plaintext small costo-ingrediente">$0.00</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <small class="form-text text-muted">
                                <input type="text" class="form-control form-control-sm" name="nota[]" 
                                       placeholder="Nota (opcional)">
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger mt-2" 
                            onclick="eliminarIngrediente(${contadorIngredientes})">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    `;
    contenedor.insertAdjacentHTML('beforeend', html);
    contadorIngredientes++;
}

function actualizarUnidadIngrediente(index) {
    const select = document.querySelector(`[data-ingrediente="${index}"] .ingrediente-select`);
    const option = select.options[select.selectedIndex];
    const unidad = option.dataset.unidad || '--';
    
    document.querySelector(`[data-ingrediente="${index}"] .unidad-ingrediente`).textContent = unidad;
    recalcularCostoIngrediente(index);
}

function recalcularCostoIngrediente(index) {
    const select = document.querySelector(`[data-ingrediente="${index}"] .ingrediente-select`);
    const cantidad = parseFloat(document.querySelector(`[data-ingrediente="${index}"] .cantidad-ingrediente`).value);
    const costoUnitarioStr = select.options[select.selectedIndex].dataset.costo;
    
    let costoTotal = 0;
    if (cantidad && costoUnitarioStr) {
        const costoUnitario = parseFloat(costoUnitarioStr);
        costoTotal = cantidad * costoUnitario;
    }
    
    document.querySelector(`[data-ingrediente="${index}"] .costo-ingrediente`).textContent = 
        '$' + costoTotal.toFixed(2);
    
    recalcularCostoTotalReceta();
}

function recalcularCostoTotalReceta() {
    let costoTotal = 0;
    document.querySelectorAll('.costo-ingrediente').forEach(el => {
        const costo = parseFloat(el.textContent.replace('$', ''));
        costoTotal += costo || 0;
    });
    
    document.getElementById('costoTotalReceta').textContent = '$' + costoTotal.toFixed(2);
}

function eliminarIngrediente(index) {
    const item = document.querySelector(`[data-ingrediente="${index}"]`);
    if (item) {
        item.remove();
        recalcularCostoTotalReceta();
    }
}

// Editar receta
function editarReceta(id) {
    const formData = new FormData();
    formData.append('accion', 'obtener');
    formData.append('id', id);
    
    fetch('procesar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito && data.receta) {
            const receta = data.receta;
            let html = `
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" name="id" value="${receta.id}">
                
                <div class="form-group">
                    <label><strong>ID</strong></label>
                    <input type="text" class="form-control" value="${receta.id_receta}" readonly>
                </div>
                
                <div class="form-group">
                    <label for="edit_nombre_platillo"><strong>Nombre del platillo *</strong></label>
                    <input type="text" class="form-control" id="edit_nombre_platillo" 
                           name="nombre_platillo" value="${receta.nombre_platillo}" required>
                </div>

                <div class="form-group">
                    <label for="edit_descripcion"><strong>Descripción (opcional)</strong></label>
                    <textarea class="form-control" id="edit_descripcion" name="descripcion" rows="3">${receta.descripcion || ''}</textarea>
                </div>

                <hr>
                <h5><i class="fas fa-list"></i> Ingredientes</h5>
                
                <div id="contenedorIngredientesEdit">
                    <!-- Los ingredientes se cargarán aquí -->
                </div>
                
                <button type="button" class="btn btn-outline-primary btn-sm" id="btnAgregarIngredienteEdit">
                    <i class="fas fa-plus"></i> Agregar Ingrediente
                </button>

                <hr>

                <div class="form-group">
                    <label for="edit_estado"><strong>Estado</strong></label>
                    <select class="form-control" id="edit_estado" name="estado">
                        <option value="activo" ${receta.estado === 'activo' ? 'selected' : ''}>Activo</option>
                        <option value="inactivo" ${receta.estado === 'inactivo' ? 'selected' : ''}>Inactivo</option>
                    </select>
                </div>

                <div class="alert alert-info">
                    <strong><i class="fas fa-calculator"></i> Costo Total de la Receta:</strong><br>
                    <h4 class="text-success"><strong id="costoTotalRecetaEdit">$${parseFloat(receta.costo_total_receta).toFixed(2)}</strong></h4>
                </div>
            `;
            document.getElementById('modalEditarContenido').innerHTML = html;
            
            // Cargar ingredientes
            fetch('procesar.php?accion=obtener_ingredientes&id_receta=' + receta.id_receta)
                .then(resp => resp.json())
                .then(dataIng => {
                    if (dataIng.exito && dataIng.ingredientes) {
                        cargarIngredientesEnEdit(dataIng.ingredientes);
                    }
                });
            
            // Evento para agregar ingredientes
            document.getElementById('btnAgregarIngredienteEdit')?.addEventListener('click', agregarIngredienteEdit);
        }
    })
    .catch(error => console.error('Error:', error));
}

function cargarIngredientesEnEdit(ingredientes) {
    const contenedor = document.getElementById('contenedorIngredientesEdit');
    contenedor.innerHTML = '';
    
    ingredientes.forEach((ing, index) => {
        const html = `
            <div class="ingrediente-item" data-ingrediente="edit_${index}">
                <div class="card card-outline card-primary mb-2">
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="small"><strong>Materia Prima *</strong></label>
                                <select name="materia_prima[]" class="form-control form-control-sm ingrediente-select"
                                        onchange="actualizarUnidadIngredienteEdit('edit_${index}')" required>
                                    <option value="">-- Seleccionar --</option>
                                    ${materiasPrimas.map(mp => 
                                        `<option value="${mp.id_materia_prima}" 
                                                data-unidad="${mp.unidad_minima}"
                                                data-costo="${mp.costo_por_unidad_minima}"
                                                ${mp.id_materia_prima === ing.id_materia_prima ? 'selected' : ''}>
                                            ${mp.nombre} (${mp.unidad_minima})
                                        </option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small"><strong>Cantidad *</strong></label>
                                <div class="input-group input-group-sm">
                                    <input type="number" name="cantidad[]" class="form-control cantidad-ingrediente" 
                                           step="0.001" min="0.001" value="${parseFloat(ing.cantidad_usada).toFixed(3)}" required
                                           onchange="recalcularCostoIngredienteEdit('edit_${index}')">
                                    <div class="input-group-append">
                                        <span class="input-group-text unidad-ingrediente">${ing.unidad_cantidad}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 text-right">
                                <label class="small"><strong>Costo</strong></label>
                                <p class="form-control-plaintext small costo-ingrediente">$${parseFloat(ing.costo_ingrediente).toFixed(2)}</p>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <small class="form-text text-muted">
                                    <input type="text" class="form-control form-control-sm" name="nota[]" 
                                           value="${ing.nota || ''}" placeholder="Nota (opcional)">
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger mt-2" 
                                onclick="eliminarIngredienteEdit('edit_${index}')">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </div>
            </div>
        `;
        contenedor.insertAdjacentHTML('beforeend', html);
    });
}

function actualizarUnidadIngredienteEdit(index) {
    const select = document.querySelector(`[data-ingrediente="${index}"] .ingrediente-select`);
    const option = select.options[select.selectedIndex];
    const unidad = option.dataset.unidad || '--';
    
    document.querySelector(`[data-ingrediente="${index}"] .unidad-ingrediente`).textContent = unidad;
    recalcularCostoIngredienteEdit(index);
}

function recalcularCostoIngredienteEdit(index) {
    const select = document.querySelector(`[data-ingrediente="${index}"] .ingrediente-select`);
    const cantidad = parseFloat(document.querySelector(`[data-ingrediente="${index}"] .cantidad-ingrediente`).value);
    const costoUnitarioStr = select.options[select.selectedIndex].dataset.costo;
    
    let costoTotal = 0;
    if (cantidad && costoUnitarioStr) {
        const costoUnitario = parseFloat(costoUnitarioStr);
        costoTotal = cantidad * costoUnitario;
    }
    
    document.querySelector(`[data-ingrediente="${index}"] .costo-ingrediente`).textContent = 
        '$' + costoTotal.toFixed(2);
    
    recalcularCostoTotalRecetaEdit();
}

function recalcularCostoTotalRecetaEdit() {
    let costoTotal = 0;
    document.querySelectorAll('#modalEditarContenido .costo-ingrediente').forEach(el => {
        const costo = parseFloat(el.textContent.replace('$', ''));
        costoTotal += costo || 0;
    });
    
    document.getElementById('costoTotalRecetaEdit').textContent = '$' + costoTotal.toFixed(2);
}

function eliminarIngredienteEdit(index) {
    const item = document.querySelector(`[data-ingrediente="${index}"]`);
    if (item) {
        item.remove();
        recalcularCostoTotalRecetaEdit();
    }
}

function agregarIngredienteEdit() {
    const contenedor = document.getElementById('contenedorIngredientesEdit');
    const index = 'new_' + Date.now();
    const html = `
        <div class="ingrediente-item" data-ingrediente="${index}">
            <div class="card card-outline card-primary mb-2">
                <div class="card-body p-2">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="small"><strong>Materia Prima *</strong></label>
                            <select name="materia_prima[]" class="form-control form-control-sm ingrediente-select"
                                    onchange="actualizarUnidadIngredienteEdit('${index}')" required>
                                <option value="">-- Seleccionar --</option>
                                ${materiasPrimas.map(mp => 
                                    `<option value="${mp.id_materia_prima}" 
                                            data-unidad="${mp.unidad_minima}"
                                            data-costo="${mp.costo_por_unidad_minima}">
                                        ${mp.nombre} (${mp.unidad_minima})
                                    </option>`
                                ).join('')}
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="small"><strong>Cantidad *</strong></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="cantidad[]" class="form-control cantidad-ingrediente" 
                                       step="0.001" min="0.001" placeholder="0.000" required
                                       onchange="recalcularCostoIngredienteEdit('${index}')">
                                <div class="input-group-append">
                                    <span class="input-group-text unidad-ingrediente">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-right">
                            <label class="small"><strong>Costo</strong></label>
                            <p class="form-control-plaintext small costo-ingrediente">$0.00</p>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <small class="form-text text-muted">
                                <input type="text" class="form-control form-control-sm" name="nota[]" 
                                       placeholder="Nota (opcional)">
                            </small>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-danger mt-2" 
                            onclick="eliminarIngredienteEdit('${index}')">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    `;
    contenedor.insertAdjacentHTML('beforeend',html);
}

// Ver detalles
function verDetalles(id) {
    const formData = new FormData();
    formData.append('accion', 'obtener');
    formData.append('id', id);
    
    fetch('procesar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito && data.receta && data.ingredientes) {
            const receta = data.receta;
            const ingredientes = data.ingredientes;
            
            let html = `
                <h5>${receta.nombre_platillo}</h5>
                <p><strong>ID Receta:</strong> ${receta.id_receta}</p>
                ${receta.descripcion ? `<p><strong>Descripción:</strong> ${receta.descripcion}</p>` : ''}
                
                <h6 class="mt-3">Ingredientes:</h6>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Materia Prima</th>
                            <th>Cantidad</th>
                            <th>Costo Unitario</th>
                            <th>Costo Total</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            ingredientes.forEach(ing => {
                html += `
                    <tr>
                        <td>${ing.nombre_materia_prima}</td>
                        <td>${parseFloat(ing.cantidad_usada).toFixed(0)} ${ing.unidad_cantidad}</td>
                        <td>$${parseFloat(ing.costo_unitario_materia).toFixed(2)}</td>
                        <td>$${parseFloat(ing.costo_ingrediente).toFixed(2)}</td>
                    </tr>
                `;
            });
            
            html += `
                    </tbody>
                </table>
                
                <hr>
                <h5>Costo Total de la Receta: <span class="text-success">$${parseFloat(receta.costo_total_receta).toFixed(2)}</span></h5>
            `;
            
            document.getElementById('modalDetallesContenido').innerHTML = html;
        }
    })
    .catch(error => console.error('Error:', error));
}

// Confirmar eliminación
function confirmarEliminacion(id, nombre) {
    idReceta_Eliminar = id;
    document.getElementById('nombreReceta').textContent = nombre;
    $('#modalConfirmarEliminacion').modal('show');
}

// Eliminar receta confirmado
document.getElementById('btnEliminarConfirmado')?.addEventListener('click', function() {
    const formData = new FormData();
    formData.append('accion', 'eliminar');
    formData.append('id', idReceta_Eliminar);
    
    fetch('procesar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exito) {
            location.href = 'recetas.php?exito=eliminado';
        } else {
            alert(data.mensaje || 'Error al eliminar');
        }
    })
    .catch(error => console.error('Error:', error));
});

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    recalcularCostoTotalReceta();
});
</script>

<?php include '../includes/footer.php'; ?>
