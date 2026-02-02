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

// Obtener nombre del restaurante
$nombreRestaurante = isset($_SESSION['nombre_restaurante']) ? $_SESSION['nombre_restaurante'] : 'Restaurante';
$slugRestaurante = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $nombreRestaurante)));

// Obtener productos disponibles
$sqlProductos = "SELECT id_producto, nombre_producto, valor_con_iva FROM " . TBL_PRODUCTOS . " WHERE estado = 'activo' ORDER BY nombre_producto ASC";
$resultProductos = $conexion->query($sqlProductos);
$productos = [];
if ($resultProductos && $resultProductos->num_rows > 0) {
    while ($row = $resultProductos->fetch_assoc()) {
        $productos[] = $row;
    }
}

// Obtener bloques del menú digital
$sqlMenu = "SELECT * FROM " . TBL_MENU_DIGITAL . " WHERE estado = 'activo' ORDER BY orden ASC";
$resultMenu = $conexion->query($sqlMenu);
$bloques = [];
$logoMenu = '';
if ($resultMenu && $resultMenu->num_rows > 0) {
    while ($row = $resultMenu->fetch_assoc()) {
        // Obtener logo del primer bloque
        if (empty($bloques) && isset($row['logo_menu'])) {
            $logoMenu = $row['logo_menu'];
        }
        $bloques[] = $row;
    }
}

// Generar URL del menú público
$urlBase = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
$urlMenuPublico = $urlBase . dirname($_SERVER['PHP_SELF']) . "/ver.php?r=" . $slugRestaurante;
?>

<!-- Content Wrapper -->
<div class="content-wrapper">
  <!-- Encabezado -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><i class="fas fa-qrcode"></i> Menú Digital</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="../home.php">Inicio</a></li>
            <li class="breadcrumb-item active">Menú Digital</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Contenido Principal -->
  <section class="content">
    <div class="container-fluid">
      
      <!-- Alertas -->
      <?php if(isset($_GET['exito']) && $_GET['exito'] == 'guardado'): ?>
        <div class="alert alert-success alert-dismissible fade show">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <i class="icon fas fa-check"></i> Menú digital guardado exitosamente
        </div>
      <?php endif; ?>

      <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
          <button type="button" class="close" data-dismiss="alert">&times;</button>
          <i class="icon fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
      <?php endif; ?>

      <!-- Acordeón de configuración -->
      <div class="accordion" id="accordionMenuDigital">
        
        <form id="formMenuDigital" action="procesar.php" method="POST" enctype="multipart/form-data">
        
        <!-- Sección 1: Enlace del Menú Digital -->
        <div class="card">
          <div class="card-header" id="headingEnlace">
            <h2 class="mb-0">
              <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseEnlace" aria-expanded="true" aria-controls="collapseEnlace">
                <i class="fas fa-link"></i> Enlace del Menú Digital
              </button>
            </h2>
          </div>
          <div id="collapseEnlace" class="collapse show" aria-labelledby="headingEnlace" data-parent="#accordionMenuDigital">
            <div class="card-body">
              <div class="row">
                <div class="col-md-8">
                  <div class="form-group">
                    <label>URL Pública del Menú:</label>
                    <div class="input-group">
                      <input type="text" class="form-control" id="urlMenuPublico" value="<?php echo $urlMenuPublico; ?>" readonly>
                      <div class="input-group-append">
                        <button class="btn btn-primary" onclick="copiarURL()">
                          <i class="fas fa-copy"></i> Copiar
                        </button>
                        <a href="<?php echo $urlMenuPublico; ?>" target="_blank" class="btn btn-info">
                          <i class="fas fa-external-link-alt"></i> Ver
                        </a>
                      </div>
                    </div>
                    <small class="text-muted">Comparte este enlace con tus clientes para que vean tu menú digital</small>
                  </div>
                </div>
                <div class="col-md-4 text-center">
                  <label>Código QR:</label><br>
                  <div id="qrcode" style="display: inline-block;"></div><br>
                  <button class="btn btn-sm btn-success mt-2" onclick="descargarQR()">
                    <i class="fas fa-download"></i> Descargar QR
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sección 2: Colores del Menú -->
        <div class="card">
          <div class="card-header" id="headingColores">
            <h2 class="mb-0">
              <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseColores" aria-expanded="false" aria-controls="collapseColores">
                <i class="fas fa-palette"></i> Colores del Menú
              </button>
            </h2>
          </div>
          <div id="collapseColores" class="collapse" aria-labelledby="headingColores" data-parent="#accordionMenuDigital">
            <div class="card-body">
                
                <div class="row">
                  <!-- Logo del Menú -->
                  <div class="col-md-4">
                    <div class="form-group mb-4">
                      <label><i class="fas fa-image"></i> Logo del Menú:</label>
                      <?php if (!empty($logoMenu) && file_exists("../assets/img/menu/" . $logoMenu)): ?>
                        <div class="mb-2">
                          <img src="../assets/img/menu/<?php echo htmlspecialchars($logoMenu); ?>" alt="Logo" style="max-height: 80px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        </div>
                      <?php endif; ?>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="logoMenu" name="logo_menu" accept="image/*">
                        <label class="custom-file-label" for="logoMenu">Seleccionar...</label>
                      </div>
                      <small class="text-muted d-block mt-1">Aparecerá en el menú público</small>
                    </div>
                  </div>
                  
                  <!-- Selector de Modo Claro/Oscuro -->
                  <div class="col-md-4">
                    <div class="form-group mb-4">
                      <label><i class="fas fa-moon"></i> Modo de Visualización:</label>
                      <div class="row">
                        <?php
                        $modoOscuro = 0;
                        if (count($bloques) > 0 && isset($bloques[0]['modo_oscuro'])) {
                          $modoOscuro = intval($bloques[0]['modo_oscuro']);
                        }
                        ?>
                        <div class="col-6 mb-2">
                          <label class="modo-option">
                            <input type="radio" name="modo_oscuro" value="0" <?php echo ($modoOscuro === 0) ? 'checked' : ''; ?> required>
                            <div class="modo-card modo-claro">
                              <i class="fas fa-sun fa-2x mb-2"></i>
                              <span class="modo-nombre">Modo Claro</span>
                            </div>
                          </label>
                        </div>
                        <div class="col-6 mb-2">
                          <label class="modo-option">
                            <input type="radio" name="modo_oscuro" value="1" <?php echo ($modoOscuro === 1) ? 'checked' : ''; ?> required>
                            <div class="modo-card modo-oscuro">
                              <i class="fas fa-moon fa-2x mb-2"></i>
                              <span class="modo-nombre">Modo Oscuro</span>
                            </div>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Selector de Tema de Color -->
                  <div class="col-md-4">
                    <div class="form-group mb-4">
                      <label><i class="fas fa-palette"></i> Tema de Color:</label>
                      <div class="tema-scroll-container">
                        <?php
                        $temas = [
                            'verde' => ['nombre' => 'Verde','gradiente' => 'linear-gradient(135deg, #1e7e34 0%, #28a745 100%)'],
                            'azul' => ['nombre' => 'Azul','gradiente' => 'linear-gradient(135deg, #0056b3 0%, #007bff 100%)'],
                            'rojo' => ['nombre' => 'Rojo','gradiente' => 'linear-gradient(135deg, #a71d2a 0%, #dc3545 100%)'],
                            'amarillo' => ['nombre' => 'Amarillo','gradiente' => 'linear-gradient(135deg, #e0a800 0%, #ffc107 100%)'],
                            'naranja' => ['nombre' => 'Naranja','gradiente' => 'linear-gradient(135deg, #e8590c 0%, #fd7e14 100%)'],
                            'violeta' => ['nombre' => 'Violeta','gradiente' => 'linear-gradient(135deg, #59339d 0%, #6f42c1 100%)'],
                            'purpura' => ['nombre' => 'Púrpura','gradiente' => 'linear-gradient(135deg, #c2185b 0%, #e83e8c 100%)'],
                            'gris' => ['nombre' => 'Gris','gradiente' => 'linear-gradient(135deg, #495057 0%, #6c757d 100%)'],
                            'negro' => ['nombre' => 'Negro','gradiente' => 'linear-gradient(135deg, #000000 0%, #212529 100%)'],
                            'turquesa' => ['nombre' => 'Turquesa','gradiente' => 'linear-gradient(135deg, #138f75 0%, #20c997 100%)'],
                            'cafe' => ['nombre' => 'Café','gradiente' => 'linear-gradient(135deg, #5d4037 0%, #795548 100%)'],
                            'vino' => ['nombre' => 'Vino','gradiente' => 'linear-gradient(135deg, #5a0023 0%, #8e0038 100%)'],
                            'menta' => ['nombre' => 'Menta','gradiente' => 'linear-gradient(135deg, #3cb8a4 0%, #6ddccf 100%)'],
                            'petroleo' => ['nombre' => 'Petróleo','gradiente' => 'linear-gradient(135deg, #092f36 0%, #0f4c5c 100%)'],
                            'lavanda' => ['nombre' => 'Lavanda','gradiente' => 'linear-gradient(135deg, #8e7cc3 0%, #b497d6 100%)'],
                            'arena' => ['nombre' => 'Arena','gradiente' => 'linear-gradient(135deg, #c9a66b 0%, #e0c097 100%)']
                        ];
                        
                        $colorActual = 'verde';
                        if (count($bloques) > 0 && isset($bloques[0]['color_tema'])) {
                          $colorActual = $bloques[0]['color_tema'];
                        }
                        
                        foreach ($temas as $clave => $tema):
                          $checked = ($clave === $colorActual) ? 'checked' : '';
                        ?>
                        <label class="tema-option-compact">
                          <input type="radio" name="color_tema" value="<?php echo $clave; ?>" <?php echo $checked; ?> required>
                          <div class="tema-card-compact">
                            <span class="tema-nombre-compact"><?php echo $tema['nombre']; ?></span>
                            <div class="tema-barras-compact" style="background: <?php echo $tema['gradiente']; ?>"></div>
                          </div>
                        </label>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                </div>
                
            </div>
          </div>
        </div>

        <!-- Sección 3: Secciones del Menú -->
        <div class="card">
          <div class="card-header" id="headingSecciones">
            <h2 class="mb-0">
              <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseSecciones" aria-expanded="false" aria-controls="collapseSecciones">
                <i class="fas fa-list"></i> Secciones del Menú
              </button>
            </h2>
          </div>
          <div id="collapseSecciones" class="collapse" aria-labelledby="headingSecciones" data-parent="#accordionMenuDigital">
            <div class="card-body">
            
            <div id="bloques-container">
              <?php if (count($bloques) > 0): ?>
                <?php foreach ($bloques as $index => $bloque): ?>
                  <div class="bloque-menu mb-4 p-3 border rounded" data-orden="<?php echo $index; ?>">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <h5 class="mb-0"><i class="fas fa-grip-vertical text-muted"></i> Sección <?php echo ($index + 1); ?></h5>
                      <button type="button" class="btn btn-danger btn-sm" onclick="eliminarBloque(this)">
                        <i class="fas fa-trash"></i> Eliminar
                      </button>
                    </div>
                    
                    <div class="form-group">
                      <label>Título de la Sección:</label>
                      <input type="text" class="form-control" name="bloques[<?php echo $index; ?>][titulo]" 
                             value="<?php echo htmlspecialchars($bloque['titulo_seccion']); ?>" 
                             placeholder="Ej: Entradas, Platos Fuertes, Bebidas..." required>
                    </div>
                    
                    <div class="form-group">
                      <label>Productos de esta sección:</label>
                      <select class="duallistbox" name="bloques[<?php echo $index; ?>][productos][]" multiple="multiple">
                        <?php
                        $productosSeleccionados = explode(',', $bloque['productos_ids']);
                        foreach ($productos as $prod): 
                          $selected = in_array($prod['id_producto'], $productosSeleccionados) ? 'selected' : '';
                        ?>
                          <option value="<?php echo $prod['id_producto']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($prod['nombre_producto']); ?> - $<?php echo number_format($prod['valor_con_iva'], 0, ',', '.'); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    
                    <input type="hidden" name="bloques[<?php echo $index; ?>][id]" value="<?php echo $bloque['id']; ?>">
                    <input type="hidden" name="bloques[<?php echo $index; ?>][orden]" value="<?php echo $index; ?>">
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <!-- Bloque inicial vacío -->
                <div class="bloque-menu mb-4 p-3 border rounded" data-orden="0">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0"><i class="fas fa-grip-vertical text-muted"></i> Sección 1</h5>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarBloque(this)">
                      <i class="fas fa-trash"></i> Eliminar
                    </button>
                  </div>
                  
                  <div class="form-group">
                    <label>Título de la Sección:</label>
                    <input type="text" class="form-control" name="bloques[0][titulo]" 
                           placeholder="Ej: Entradas, Platos Fuertes, Bebidas..." required>
                  </div>
                  
                  <div class="form-group">
                    <label>Productos de esta sección:</label>
                    <select class="duallistbox" name="bloques[0][productos][]" multiple="multiple">
                      <?php foreach ($productos as $prod): ?>
                        <option value="<?php echo $prod['id_producto']; ?>">
                          <?php echo htmlspecialchars($prod['nombre_producto']); ?> - $<?php echo number_format($prod['valor_con_iva'], 0, ',', '.'); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  
                  <input type="hidden" name="bloques[0][orden]" value="0">
                </div>
              <?php endif; ?>
            </div>

            <button type="button" class="btn btn-success" onclick="agregarBloque()">
              <i class="fas fa-plus-circle"></i> Agregar Nueva Sección
            </button>

            </div>
          </div>
        </div>

        </form>
        
      </div>
      <!-- Fin Acordeón -->

      <!-- Botón de guardado general (fuera del acordeón) -->
      <div class="mt-3 text-right">
        <button type="submit" form="formMenuDigital" class="btn btn-primary btn-lg">
          <i class="fas fa-save"></i> Guardar Todo el Menú
        </button>
        <button type="button" class="btn btn-secondary btn-lg" onclick="window.location.href='index.php'">
          <i class="fas fa-times"></i> Cancelar
        </button>
      </div>

    </div>
  </section>
</div>

<!-- Template para nuevos bloques -->
<template id="template-bloque">
  <div class="bloque-menu mb-4 p-3 border rounded" data-orden="">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0"><i class="fas fa-grip-vertical text-muted"></i> Sección <span class="numero-seccion"></span></h5>
      <button type="button" class="btn btn-danger btn-sm" onclick="eliminarBloque(this)">
        <i class="fas fa-trash"></i> Eliminar
      </button>
    </div>
    
    <div class="form-group">
      <label>Título de la Sección:</label>
      <input type="text" class="form-control titulo-input" name="" placeholder="Ej: Entradas, Platos Fuertes, Bebidas..." required>
    </div>
    
    <div class="form-group">
      <label>Productos de esta sección:</label>
      <select class="duallistbox productos-select" name="" multiple="multiple">
        <?php foreach ($productos as $prod): ?>
          <option value="<?php echo $prod['id_producto']; ?>">
            <?php echo htmlspecialchars($prod['nombre_producto']); ?> - $<?php echo number_format($prod['valor_con_iva'], 0, ',', '.'); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    
    <input type="hidden" class="orden-input" name="" value="">
  </div>
</template>

<!-- Bootstrap Duallistbox CSS -->
<link rel="stylesheet" href="../plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">

<!-- Estilos personalizados para selector de temas -->
<style>
/* Estilos para acordeón */
.accordion .card {
  margin-bottom: 10px;
  border: 1px solid #dee2e6;
  border-radius: 5px;
}

.accordion .card-header {
  background-color: #f8f9fa;
  border-bottom: 1px solid #dee2e6;
}

.accordion .btn-link {
  color: #343a40;
  text-decoration: none;
  font-weight: 600;
  font-size: 1.1rem;
  padding: 15px 20px;
}

.accordion .btn-link:hover {
  color: #2eab62;
  text-decoration: none;
}

.accordion .btn-link i {
  margin-right: 10px;
}

/* Estilos para QR Code */
#qrcode {
  padding: 10px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  display: inline-block;
}

#qrcode canvas {
  border-radius: 5px;
}

/* Estilos para Duallistbox */
.bootstrap-duallistbox-container select {
  height: 250px !important;
}

.bootstrap-duallistbox-container select option:checked,
.bootstrap-duallistbox-container select option:hover:checked {
  background-color: #28a745 !important;
  background-image: linear-gradient(0deg, #28a745 0%, #28a745 100%) !important;
  color: white !important;
}

.bootstrap-duallistbox-container select option:hover {
  background-color: #d4edda !important;
  color: #155724 !important;
}

.bootstrap-duallistbox-container .btn-primary {
  background-color: #28a745 !important;
  border-color: #28a745 !important;
}

.bootstrap-duallistbox-container .btn-primary:hover {
  background-color: #218838 !important;
  border-color: #1e7e34 !important;
}

/* Estilos para temas compactos */
.tema-scroll-container {
  max-height: 200px;
  overflow-y: auto;
  border: 1px solid #dee2e6;
  border-radius: 5px;
  padding: 5px;
}

.tema-option-compact {
  display: block;
  cursor: pointer;
  margin-bottom: 5px;
}

.tema-option-compact input[type="radio"] {
  display: none;
}

.tema-card-compact {
  display: flex;
  align-items: center;
  padding: 8px 10px;
  border-radius: 5px;
  border: 2px solid #dee2e6;
  transition: all 0.2s ease;
  background: white;
}

.tema-card-compact:hover {
  border-color: #007bff;
  background: #f8f9fa;
}

.tema-option-compact input[type="radio"]:checked + .tema-card-compact {
  border-color: #007bff;
  background: #e7f3ff;
}

.tema-nombre-compact {
  flex: 1;
  font-size: 13px;
  font-weight: 500;
  color: #333;
}

.tema-barras-compact {
  width: 40px;
  height: 20px;
  border-radius: 3px;
  margin-left: 10px;
}
.tema-option {
  cursor: pointer;
  margin-bottom: 0;
}

.tema-option input[type="radio"] {
  display: none;
}

.tema-card {
  border-radius: 10px;
  padding: 30px 10px;
  text-align: center;
  transition: all 0.3s ease;
  border: 3px solid transparent;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.tema-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.tema-option input[type="radio"]:checked + .tema-card {
  border-color: #fff;
  box-shadow: 0 0 0 3px rgba(0,123,255,0.5);
  transform: scale(1.05);
}

.tema-nombre {
  color: white;
  font-weight: bold;
  font-size: 16px;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}
.tema-option {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  padding: 12px;
  background: #f3f3f3;
  border-radius: 6px;
  cursor: pointer;
}

.tema-option input[type="radio"] {
  margin-top: 6px;
  transform: scale(1.1);
}

.tema-card {
  width: 100%;
}

.tema-nombre {
  font-size: 14px;
  font-weight: 500;
  color: #222;
  margin-bottom: 6px;
  display: block;
}

.tema-barras {
  display: flex;
  height: 12px;
  border-radius: 3px;
  overflow: hidden;
}

.tema-barras span {
  flex: 1;
}

.modo-option {
  cursor: pointer;
  margin-bottom: 0;
}

.modo-option input[type="radio"] {
  display: none;
}

.modo-card {
  border-radius: 10px;
  padding: 30px 20px;
  text-align: center;
  transition: all 0.3s ease;
  border: 3px solid #dee2e6;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.modo-claro {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  color: #212529;
}

.modo-oscuro {
  background: linear-gradient(135deg, #2c2d2f 0%, #141414 100%);
  color: #ffffff;
}

.modo-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modo-option input[type="radio"]:checked + .modo-card {
  border-color: #007bff;
  box-shadow: 0 0 0 3px rgba(0,123,255,0.3);
  transform: scale(1.05);
}

.modo-nombre {
  font-weight: bold;
  font-size: 16px;
}
@media (max-width: 768px) {
    .mb-2, .my-2 {
        margin-bottom: .5rem !important;
        max-width: 33.33%;
    }
}
</style>

<!-- Scripts después del footer -->
<script>
// Variable para almacenar los productos (necesaria para el template)
const productosDisponibles = <?php echo json_encode($productos); ?>;
</script>

<?php include '../includes/footer.php'; ?>

<!-- Bootstrap Duallistbox JS -->
<script src="../plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>

<!-- QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Inicializar duallistbox existentes
$(document).ready(function() {
  $('.duallistbox').bootstrapDualListbox({
    nonSelectedListLabel: 'Productos Disponibles',
    selectedListLabel: 'Productos Seleccionados',
    preserveSelectionOnMove: 'moved',
    moveOnSelect: false,
    filterTextClear: 'Mostrar todos',
    filterPlaceHolder: 'Buscar...',
    infoText: 'Mostrando todos {0}',
    infoTextFiltered: '<span class="label label-warning">Filtrados</span> {0} de {1}',
    infoTextEmpty: 'Lista vacía'
  });
  
  // Generar código QR
  var qrcode = new QRCode(document.getElementById("qrcode"), {
    text: "<?php echo $urlMenuPublico; ?>",
    width: 200,
    height: 200,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H // Nivel alto de corrección para soportar el logo
  });
  
  <?php if (!empty($logoMenu) && file_exists("../assets/img/menu/" . $logoMenu)): ?>
    // Agregar logo circular en el centro después de generar el QR
    function addLogoToQR() {
      var qrContainer = document.getElementById("qrcode");
      
      // Ocultar la imagen generada por QRCode.js (solo queremos el canvas)
      var qrImg = qrContainer.querySelector('img');
      if (qrImg) {
        qrImg.style.display = 'none';
      }
      
      var canvas = qrContainer.querySelector('canvas');
      if (canvas) {
        // Asegurar que el canvas sea visible
        canvas.style.display = 'block';
        
        var ctx = canvas.getContext('2d');
        var img = new Image();
        img.crossOrigin = "anonymous";
        img.onload = function() {
          // Configuración del logo
          var logoSize = 70; // Tamaño del logo (máximo sin afectar QR)
          var x = (canvas.width - logoSize) / 2;
          var y = (canvas.height - logoSize) / 2;
          
          // Dibujar fondo blanco circular con borde
          ctx.fillStyle = '#ffffff';
          ctx.beginPath();
          ctx.arc(canvas.width / 2, canvas.height / 2, logoSize / 2 + 8, 0, 2 * Math.PI);
          ctx.fill();
          
          // Dibujar logo circular
          ctx.save();
          ctx.beginPath();
          ctx.arc(canvas.width / 2, canvas.height / 2, logoSize / 2, 0, 2 * Math.PI);
          ctx.clip();
          ctx.drawImage(img, x, y, logoSize, logoSize);
          ctx.restore();
        };
        img.onerror = function() {
          console.error('Error al cargar logo del QR');
        };
        img.src = '../assets/img/menu/<?php echo htmlspecialchars($logoMenu); ?>';
      }
    }
    
    // Intentar múltiples veces para asegurar que el canvas esté listo
    setTimeout(addLogoToQR, 100);
    setTimeout(addLogoToQR, 300);
    setTimeout(addLogoToQR, 500);
  <?php endif; ?>
  
  // Actualizar label del input file
  $('.custom-file-input').on('change', function() {
    const fileName = $(this).val().split('\\').pop();
    $(this).next('.custom-file-label').html(fileName || 'Seleccionar imagen...');
    
    // Regenerar QR con el nuevo logo si se sube una imagen
    if (this.files && this.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        // Limpiar QR existente
        $('#qrcode').empty();
        
        // Generar nuevo QR con logo
        var qrcode = new QRCode(document.getElementById("qrcode"), {
          text: "<?php echo $urlMenuPublico; ?>",
          width: 200,
          height: 200,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H
        });
        
        // Agregar nuevo logo circular
        setTimeout(function() {
          var qrContainer = document.getElementById("qrcode");
          var canvas = qrContainer.querySelector('canvas');
          if (canvas) {
            var ctx = canvas.getContext('2d');
            var img = new Image();
            img.onload = function() {
              var logoSize = 70;
              var x = (canvas.width - logoSize) / 2;
              var y = (canvas.height - logoSize) / 2;
              
              // Fondo blanco circular
              ctx.fillStyle = '#ffffff';
              ctx.beginPath();
              ctx.arc(canvas.width / 2, canvas.height / 2, logoSize / 2 + 8, 0, 2 * Math.PI);
              ctx.fill();
              
              // Logo circular
              ctx.save();
              ctx.beginPath();
              ctx.arc(canvas.width / 2, canvas.height / 2, logoSize / 2, 0, 2 * Math.PI);
              ctx.clip();
              ctx.drawImage(img, x, y, logoSize, logoSize);
              ctx.restore();
            };
            img.src = e.target.result;
          }
        }, 100);
      };
      reader.readAsDataURL(this.files[0]);
    }
  });
});

// Agregar nuevo bloque
function agregarBloque() {
  const container = document.getElementById('bloques-container');
  const bloques = container.querySelectorAll('.bloque-menu');
  const nuevoIndex = bloques.length;
  
  const template = document.getElementById('template-bloque');
  const clone = template.content.cloneNode(true);
  
  // Actualizar atributos
  clone.querySelector('.bloque-menu').setAttribute('data-orden', nuevoIndex);
  clone.querySelector('.numero-seccion').textContent = nuevoIndex + 1;
  clone.querySelector('.titulo-input').setAttribute('name', `bloques[${nuevoIndex}][titulo]`);
  clone.querySelector('.productos-select').setAttribute('name', `bloques[${nuevoIndex}][productos][]`);
  clone.querySelector('.orden-input').setAttribute('name', `bloques[${nuevoIndex}][orden]`);
  clone.querySelector('.orden-input').value = nuevoIndex;
  
  container.appendChild(clone);
  
  // Inicializar duallistbox del nuevo bloque
  const nuevoSelect = container.querySelectorAll('.duallistbox')[nuevoIndex];
  $(nuevoSelect).bootstrapDualListbox({
    nonSelectedListLabel: 'Productos Disponibles',
    selectedListLabel: 'Productos Seleccionados',
    preserveSelectionOnMove: 'moved',
    moveOnSelect: false,
    filterTextClear: 'Mostrar todos',
    filterPlaceHolder: 'Buscar...'
  });
  
  renumerarBloques();
}

// Eliminar bloque
function eliminarBloque(btn) {
  const bloque = btn.closest('.bloque-menu');
  const container = document.getElementById('bloques-container');
  
  if (container.querySelectorAll('.bloque-menu').length <= 1) {
    Swal.fire({
      icon: 'warning',
      title: 'Advertencia',
      text: 'Debe haber al menos una sección en el menú'
    });
    return;
  }
  
  Swal.fire({
    title: '¿Eliminar sección?',
    text: 'Esta acción no se puede deshacer',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      // Destruir duallistbox antes de eliminar
      const select = bloque.querySelector('.duallistbox');
      if (select) {
        $(select).bootstrapDualListbox('destroy');
      }
      bloque.remove();
      renumerarBloques();
    }
  });
}

// Renumerar bloques después de agregar/eliminar
function renumerarBloques() {
  const bloques = document.querySelectorAll('.bloque-menu');
  bloques.forEach((bloque, index) => {
    bloque.setAttribute('data-orden', index);
    bloque.querySelector('.numero-seccion').textContent = index + 1;
    bloque.querySelector('.titulo-input').setAttribute('name', `bloques[${index}][titulo]`);
    bloque.querySelector('.productos-select').setAttribute('name', `bloques[${index}][productos][]`);
    bloque.querySelector('.orden-input').setAttribute('name', `bloques[${index}][orden]`);
    bloque.querySelector('.orden-input').value = index;
  });
}

// Copiar URL
function copiarURL() {
  const input = document.getElementById('urlMenuPublico');
  input.select();
  document.execCommand('copy');
  
  Swal.fire({
    icon: 'success',
    title: 'Copiado',
    text: 'URL copiada al portapapeles',
    timer: 1500,
    showConfirmButton: false
  });
}

// Descargar QR
function descargarQR() {
  const qrCanvas = document.querySelector('#qrcode canvas');
  if (qrCanvas) {
    const url = qrCanvas.toDataURL('image/png');
    const link = document.createElement('a');
    link.download = 'menu-qr-<?php echo $slugRestaurante; ?>.png';
    link.href = url;
    link.click();
  }
}
</script>

