<?php
include 'includes/auth.php';
include 'includes/url.php';
include_once 'lang/idiomas.php';
include 'includes/menu.php';

// Verificar si es super-admin
if (!isset($_SESSION['rol_master']) || $_SESSION['rol_master'] !== 'super-admin') {
    header("Location: home.php");
    exit();
}

include 'includes/conexion_master.php';

?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Encabezado -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-database"></i> Homologación de Base de Datos</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="home.php">Inicio</a></li>
            <li class="breadcrumb-item active">Homologación BD</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenido Principal -->
  <section class="content">
    <div class="container-fluid">
      
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-primary">
              <h3 class="card-title">
                <i class="fas fa-sync"></i> Sincronizar Estructura de Base de Datos
              </h3>
            </div>
            <div class="card-body">
              <p class="text-muted">
                Esta herramienta compara la estructura del template con la de todos los restaurantes existentes 
                y te muestra los cambios (columnas faltantes, nuevas tablas, etc.) que necesitas aplicar.
              </p>
              
              <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Instrucciones:</strong>
                <ol class="mb-0">
                  <li>Haz clic en "Escanear Cambios" para detectar diferencias</li>
                  <li>Revisa los cambios sugeridos</li>
                  <li>Copia los queries y ejecútalos en la BD de producción</li>
                </ol>
              </div>

              <button type="button" class="btn btn-primary btn-lg" onclick="escanearCambios()">
                <i class="fas fa-search"></i> Escanear Cambios
              </button>
              <button type="button" class="btn btn-secondary btn-lg" onclick="copiarTodos()" id="btnCopiar" disabled>
                <i class="fas fa-copy"></i> Copiar Todos
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Resultados -->
      <div id="resultados" style="display: none;">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-success">
                <h3 class="card-title">
                  <i class="fas fa-list"></i> Cambios Detectados
                </h3>
              </div>
              <div class="card-body">
                <div id="contenidoCambios"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function escanearCambios() {
  const btn = event.target;
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Escaneando...';

  fetch('procesar_homologacion.php?accion=escanear', {
    method: 'GET'
  })
  .then(response => response.json())
  .then(data => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-search"></i> Escanear Cambios';

    if (data.exito) {
      mostrarResultados(data);
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: data.error || 'Error al escanear cambios'
      });
    }
  })
  .catch(error => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-search"></i> Escanear Cambios';
    console.error('Error:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error al procesar la solicitud'
    });
  });
}

function mostrarResultados(data) {
  const contenido = document.getElementById('contenidoCambios');
  const resultados = document.getElementById('resultados');
  const btnCopiar = document.getElementById('btnCopiar');

  if (data.cambios.length === 0) {
    contenido.innerHTML = `
      <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> 
        <strong>¡Excelente!</strong> Todas las bases de datos están sincronizadas. No hay cambios pendientes.
      </div>
    `;
    btnCopiar.disabled = true;
  } else {
    let html = `<p><strong>Total de cambios: ${data.cambios.length}</strong></p>`;
    
    data.cambios.forEach((cambio, index) => {
      html += `
        <div class="card mb-3">
          <div class="card-header bg-light">
            <h5 class="mb-0">
              <i class="fas fa-database"></i> ${cambio.restaurante}
              <span class="badge badge-warning" style="float: right;">${cambio.tipo}</span>
            </h5>
          </div>
          <div class="card-body">
            <p class="text-muted"><strong>Descripción:</strong> ${cambio.descripcion}</p>
            <div class="form-group">
              <label>Query SQL:</label>
              <textarea class="form-control cambio-query" rows="4" readonly>${cambio.query}</textarea>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="copiarQuery(${index})">
              <i class="fas fa-copy"></i> Copiar
            </button>
          </div>
        </div>
      `;
    });

    contenido.innerHTML = html;
    btnCopiar.disabled = false;
  }

  resultados.style.display = 'block';
}

function copiarQuery(index) {
  const queries = document.querySelectorAll('.cambio-query');
  const textarea = queries[index];
  
  textarea.select();
  document.execCommand('copy');
  
  Swal.fire({
    icon: 'success',
    title: 'Copiado',
    text: 'Query copiado al portapapeles',
    timer: 1500,
    showConfirmButton: false
  });
}

function copiarTodos() {
  const queries = document.querySelectorAll('.cambio-query');
  let todosCombinados = '';
  
  queries.forEach(textarea => {
    todosCombinados += textarea.value + ';\n\n';
  });

  // Copiar al portapapeles
  const textarea = document.createElement('textarea');
  textarea.value = todosCombinados;
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand('copy');
  document.body.removeChild(textarea);

  Swal.fire({
    icon: 'success',
    title: 'Copiados',
    text: `${queries.length} query(s) copiados al portapapeles`,
    timer: 2000,
    showConfirmButton: false
  });
}
</script>

