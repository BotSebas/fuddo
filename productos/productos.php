<?php
include '../includes/auth.php';
include '../includes/url.php';
include_once '../lang/idiomas.php';
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

include '../includes/conexion.php';

// Filtro de búsqueda
$busqueda = $_GET['buscar'] ?? '';

// Consulta a la base de datos
$sql = "SELECT id, nombre_producto, valor_sin_iva, valor_con_iva, inventario, minimo_inventario, estado FROM " . TBL_PRODUCTOS;

if (!empty($busqueda)) {
    $busqueda_escapada = $conexion->real_escape_string($busqueda);
    $sql .= " WHERE nombre_producto LIKE '%$busqueda_escapada%'";
}

$resultado = $conexion->query($sql);
$productos = [];
if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        $productos[] = $row;
    }
}

// Paginación
$porPagina = 8;
$total = count($productos);
$paginaActual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$totalPaginas = ceil($total / $porPagina);
$inicio = ($paginaActual - 1) * $porPagina;
$productosPagina = array_slice($productos, $inicio, $porPagina);
?>
<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Encabezado -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><?php echo $productos_titulo; ?></h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#"><?php echo $misc_home; ?></a></li>
              <li class="breadcrumb-item active"><?php echo $productos_titulo; ?></li>
            </ol>
          </div>
      </div>
    </div>
  </div>

  <!-- Contenido Principal -->
  <section class="content">
    <div class="container-fluid">
      
      <?php if(isset($_GET['error']) && $_GET['error'] == 'inventario_negativo'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong><?php echo $msg_error_titulo; ?>:</strong> <?php echo $productos_error_inventario; ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_GET['exito']) && $_GET['exito'] == 'creado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong><?php echo $msg_exito_titulo; ?>:</strong> <?php echo $productos_creado; ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_GET['exito']) && $_GET['exito'] == 'actualizado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong><?php echo $msg_exito_titulo; ?>:</strong> <?php echo $productos_actualizado; ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_GET['exito']) && $_GET['exito'] == 'carga_masiva'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong><i class="fas fa-check-circle"></i> ¡Carga Masiva Exitosa!</strong><br>
          Se han creado <strong><?php echo isset($_GET['total']) ? intval($_GET['total']) : 0; ?></strong> productos correctamente.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_GET['error']) && $_GET['error'] == 'acceso_denegado'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error:</strong> Solo super-admins pueden realizar carga masiva.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_GET['error']) && $_GET['error'] == 'sin_archivo'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error:</strong> No se recibió ningún archivo.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_GET['error']) && $_GET['error'] == 'formato_invalido'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error:</strong> El archivo debe ser formato Excel (.xlsx o .xls).
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_GET['error']) && $_GET['error'] == 'sin_productos'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Advertencia:</strong> No se pudo crear ningún producto. Verifica el formato del archivo.
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <?php if(isset($_GET['error']) && $_GET['error'] == 'excepcion'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Error:</strong> <?php echo isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'Error al procesar el archivo'; ?>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      <?php endif; ?>
      
      <!-- Botón + búsqueda -->
      <div class="row mb-3">
        <div class="col-md-6">
          <button class="btn btn-success" data-toggle="modal" data-target="#modalProducto" onclick="abrirModal()"><?php echo $producto_nuevo; ?></button>
          <?php if (isset($_SESSION['rol_master']) && $_SESSION['rol_master'] === 'super-admin' && isset($_SESSION['id_restaurante'])): ?>
            <button class="btn btn-info ml-2" data-toggle="modal" data-target="#modalCargaMasiva">
              <i class="fas fa-file-csv"></i> Carga Masiva (CSV)
            </button>
          <?php endif; ?>
        </div>
        <div class="col-md-6">
          <form method="get" class="float-right">
            <div class="input-group">
              <input type="text" name="buscar" class="form-control" placeholder="<?php echo $productos_buscar_placeholder; ?>" value="<?php echo htmlspecialchars($busqueda); ?>">
              <div class="input-group-append">
                <button class="btn btn-primary"><?php echo $btn_buscar; ?></button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Tabla de productos -->
      <div class="card">
        <div class="card-body table-responsive p-0">
          <table class="table table-hover text-nowrap">
            <thead>
              <tr>
                <th><?php echo $producto_nombre; ?></th>
                <th><?php echo $producto_valor_sin_iva; ?></th>
                <th><?php echo $producto_valor_con_iva; ?></th>
                <th><?php echo $producto_inventario; ?></th>
                <th><?php echo $producto_estado; ?></th>
                <th><?php echo $producto_acciones; ?></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($productosPagina as $producto): ?>
              <tr>
                <td><?= htmlspecialchars($producto['nombre_producto']) ?></td>
                <td>$<?= number_format($producto['valor_sin_iva'], 2) ?></td>
                <td>$<?= number_format($producto['valor_con_iva'], 2) ?></td>
                <td><?= $producto['inventario'] ?></td>
                <td>
                  <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                    <input type="checkbox" class="custom-control-input" id="estadoSwitch<?= $producto['id'] ?>" <?= $producto['estado'] == 'activo' ? 'checked' : '' ?> onchange="cambiarEstadoToggle(<?= $producto['id'] ?>)">
                    <label class="custom-control-label" for="estadoSwitch<?= $producto['id'] ?>"><?= ucfirst($producto['estado']) ?></label>
                  </div>
                </td>
                <td>
                  <button class="btn btn-info btn-sm" onclick="abrirModal(<?= htmlspecialchars(json_encode($producto)) ?>)"><?php echo $btn_editar; ?></button>
                  <button class="btn btn-danger btn-sm" onclick="eliminarProducto(<?= $producto['id'] ?>)"><?php echo $btn_eliminar; ?></button>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if (empty($productosPagina)): ?>
                <tr><td colspan="7" class="text-center"><?php echo $productos_no_encontrados; ?></td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Paginador -->
      <nav>
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?php echo $i == $paginaActual ? 'active' : ''; ?>">
              <a class="page-link" href="?pagina=<?= $i ?>&buscar=<?= urlencode($busqueda) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    </div>
  </section>
</div>

<!-- Modal -->
<div class="modal fade" id="modalProducto" tabindex="-1" role="dialog" aria-labelledby="modalProductoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form id="formProducto" method="POST" action="procesar.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo $producto_modal_titulo; ?></h5>
          <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="productoId" name="id">
          <div class="form-group">
            <label for="nombreProducto"><?php echo $producto_nombre; ?></label>
            <input type="text" id="nombreProducto" name="nombre_producto" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="valorSinIva"><?php echo $producto_valor_sin_iva; ?></label>
            <input type="number" step="0.01" id="valorSinIva" name="valor_sin_iva" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="valorConIva"><?php echo $producto_valor_con_iva; ?></label>
            <input type="number" step="0.01" id="valorConIva" name="valor_con_iva" class="form-control" required>
          </div>
          <div class="form-group">
            <label for="inventario"><?php echo $producto_inventario; ?></label>
            <input type="number" id="inventario" name="inventario" class="form-control" value="0" min="0" required>
          </div>
          <div class="form-group">
            <label for="minimoInventario">Mínimo Inventario</label>
            <input type="number" id="minimoInventario" name="minimo_inventario" class="form-control" value="2" min="1" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary"><?php echo $btn_guardar; ?></button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo $btn_cancelar; ?></button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Modal Carga Masiva -->
<?php if (isset($_SESSION['rol_master']) && $_SESSION['rol_master'] === 'super-admin' && isset($_SESSION['id_restaurante'])): ?>
<div class="modal fade" id="modalCargaMasiva" tabindex="-1" role="dialog" aria-labelledby="modalCargaMasivaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalCargaMasivaLabel">
          <i class="fas fa-file-excel"></i> Carga Masiva de Productos
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="alert alert-info">
          <strong><i class="fas fa-info-circle"></i> Instrucciones:</strong>
          <ul class="mb-0 mt-2">
            <li>El archivo debe ser formato <strong>CSV (.csv)</strong></li>
            <li>Puedes crear el CSV desde Excel: <em>Archivo → Guardar como → CSV (delimitado por comas)</em></li>
            <li>La primera fila debe contener los encabezados</li>
            <li>Columnas requeridas (separadas por comas):
              <ol>
                <li>Nombre del Producto</li>
                <li>Valor sin IVA</li>
                <li>Valor con IVA</li>
                <li>Inventario</li>
                <li>Mínimo Inventario (opcional, por defecto 2)</li>
              </ol>
            </li>
            <li>Ejemplo: <code>Pizza Margarita,8000,9500,50,5</code></li>
            <li>Los productos se crearán con estado 'activo'</li>
            <li>Se generará un ID automático (PR-X) para cada producto</li>
          </ul>
        </div>
        <form id="formCargaMasiva" method="POST" action="carga_masiva.php" enctype="multipart/form-data">
          <div class="form-group">
            <label for="archivoExcel">
              <i class="fas fa-upload"></i> Seleccionar archivo CSV
            </label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="archivoExcel" name="archivo" accept=".csv" required>
              <label class="custom-file-label" for="archivoExcel">Seleccionar archivo CSV...</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="submit" form="formCargaMasiva" class="btn btn-info">
          <i class="fas fa-upload"></i> Cargar Productos
        </button>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
// Actualizar nombre del archivo seleccionado
$(document).ready(function() {
  $('.custom-file-input').on('change', function() {
    var fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName);
  });
});

function abrirModal(producto = null) {
  if (producto) {
    document.getElementById('productoId').value = producto.id;
    document.getElementById('nombreProducto').value = producto.nombre_producto;
    document.getElementById('valorSinIva').value = producto.valor_sin_iva;
    document.getElementById('valorConIva').value = producto.valor_con_iva;
    document.getElementById('inventario').value = producto.inventario || 0;
    document.getElementById('minimoInventario').value = producto.minimo_inventario || 2;
  } else {
    document.getElementById('productoId').value = '';
    document.getElementById('formProducto').reset();
  }
  $('#modalProducto').modal('show');
}

function eliminarProducto(id) {
  if (confirm("<?php echo $productos_confirmar_eliminar; ?>")) {
    window.location.href = 'eliminar.php?id=' + id;
  }
}

function cambiarEstadoToggle(id) {
  window.location.href = 'cambiar_estado.php?id=' + id;
}
</script>

<?php include '../includes/footer.php'; ?>
<script src="<?php echo $BASE_URL; ?>plugins/select2/js/select2.full.min.js"></script>
<script>
  $('.select2').select2({ theme: 'bootstrap4' });
</script>
