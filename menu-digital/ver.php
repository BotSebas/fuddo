<?php
// Vista pública del menú digital - NO REQUIERE LOGIN
$restaurante = $_GET['r'] ?? '';

if (empty($restaurante)) {
    die('Restaurante no especificado');
}

// Conectar a base de datos principal para obtener info del restaurante
$host = 'localhost';
$usuario = 'root';
$password = '';
$dbPrincipal = 'mgacgdnjkg';

$conexionMaster = new mysqli($host, $usuario, $password, $dbPrincipal);
$conexionMaster->set_charset("utf8mb4");

if ($conexionMaster->connect_error) {
    die("Error de conexión: " . $conexionMaster->connect_error);
}

// Buscar restaurante por slug
$restaurante_escapado = $conexionMaster->real_escape_string($restaurante);
$sqlRestaurante = "SELECT id, nombre, identificador FROM restaurantes WHERE LOWER(REPLACE(nombre, ' ', '-')) = '$restaurante_escapado' OR identificador = '$restaurante_escapado' LIMIT 1";
$resultRestaurante = $conexionMaster->query($sqlRestaurante);

if (!$resultRestaurante || $resultRestaurante->num_rows == 0) {
    die('Restaurante no encontrado');
}

$restauranteData = $resultRestaurante->fetch_assoc();
$nombreRestaurante = $restauranteData['nombre'];
$identificador = $restauranteData['identificador'];

// Usar la misma base de datos (todo está en mgacgdnjkg)
$dbRestaurante = $dbPrincipal;
$conexion = new mysqli($host, $usuario, $password, $dbRestaurante);
$conexion->set_charset("utf8mb4");

if ($conexion->connect_error) {
    die("Error de conexión al restaurante: " . $conexion->connect_error);
}

// Definir constantes de tablas con prefijo
$TABLE_PREFIX = 'fuddo_' . $identificador . '_';
define('TBL_MENU_DIGITAL', $TABLE_PREFIX . 'menu_digital');
define('TBL_PRODUCTOS', $TABLE_PREFIX . 'productos');

// Obtener bloques del menú
$sqlMenu = "SELECT * FROM " . TBL_MENU_DIGITAL . " WHERE estado = 'activo' ORDER BY orden ASC";
$resultMenu = $conexion->query($sqlMenu);
$bloques = [];
$colorTema = 'verde'; // Color por defecto
$modoOscuro = 0; // Modo claro por defecto
$logoMenu = ''; // Logo del menú

if ($resultMenu && $resultMenu->num_rows > 0) {
    while ($row = $resultMenu->fetch_assoc()) {
        // Obtener el tema de color, modo y logo del primer bloque
        if (empty($bloques)) {
            $colorTema = $row['color_tema'] ?? 'verde';
            $modoOscuro = isset($row['modo_oscuro']) ? intval($row['modo_oscuro']) : 0;
            $logoMenu = $row['logo_menu'] ?? '';
        }
        
        // Obtener productos de este bloque
        $productosIds = $row['productos_ids'];
        $productos = [];
        
        if (!empty($productosIds)) {
            // Escapar y agregar comillas a cada ID
            $idsArray = explode(',', $productosIds);
            $idsEscapados = array_map(function($id) use ($conexion) {
                return "'" . $conexion->real_escape_string(trim($id)) . "'";
            }, $idsArray);
            $idsString = implode(',', $idsEscapados);
            
            $sqlProductos = "SELECT nombre_producto, valor_con_iva FROM " . TBL_PRODUCTOS . " 
                            WHERE id_producto IN (" . $idsString . ") AND estado = 'activo' 
                            ORDER BY nombre_producto ASC";
            $resultProductos = $conexion->query($sqlProductos);
            
            if ($resultProductos && $resultProductos->num_rows > 0) {
                while ($prod = $resultProductos->fetch_assoc()) {
                    $productos[] = $prod;
                }
            }
        }
        
        $bloques[] = [
            'titulo' => $row['titulo_seccion'],
            'productos' => $productos
        ];
    }
}

// Definir colores según el tema seleccionado
$temas = [
    'verde' => ['primario' => '#28a745', 'secundario' => '#20c997'],
    'azul' => ['primario' => '#007bff', 'secundario' => '#00c9ff'],
    'rojo' => ['primario' => '#dc3545', 'secundario' => '#ff6b6b'],
    'amarillo' => ['primario' => '#f39c12', 'secundario' => '#f1c40f'],
    'naranja' => ['primario' => '#ff6348', 'secundario' => '#ff9a76'],
    'violeta' => ['primario' => '#667eea', 'secundario' => '#764ba2'],
    'purpura' => ['primario' => '#e83e8c', 'secundario' => '#c471ed'],
    'gris' => ['primario' => '#6c757d', 'secundario' => '#95a5a6'],
    'negro' => ['primario' => '#212529', 'secundario' => '#495057'],
    'turquesa' => ['primario' => '#20c997', 'secundario' => '#17a2b8'],
    'cafe' => ['primario' => '#795548', 'secundario' => '#8d6e63'],
    'vino' => ['primario' => '#8e0038', 'secundario' => '#c2185b'],
    'menta' => ['primario' => '#6ddccf', 'secundario' => '#4db6ac'],
    'petroleo' => ['primario' => '#0f4c5c', 'secundario' => '#1a6b7f'],
    'lavanda' => ['primario' => '#b497d6', 'secundario' => '#9575cd'],
    'arena' => ['primario' => '#e0c097', 'secundario' => '#d4a574']
];

$colores = $temas[$colorTema] ?? $temas['verde'];

// Colores según modo claro/oscuro
if ($modoOscuro === 1) {
    $bgBody = '#141414';
    $bgContainer = '#2c2d2f';
    $colorTexto = '#ffffff';
    $bgHover = '#3a3b3d';
} else {
    $bgBody = '#f0f9f4';
    $bgContainer = '#ffffff';
    $colorTexto = '#333';
    $bgHover = '#f8f9fa';
}


$conexionMaster->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

     <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="../assets/icons/logo-fuddo.ico">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú - <?php echo htmlspecialchars($nombreRestaurante); ?></title>
    
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: <?php echo $bgBody; ?>;
            min-height: 100vh;
            padding: 20px 40px;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('../assets/img/<?php echo $modoOscuro === 1 ? 'bg-menu-digital-final-blanco.svg' : 'bg-menu-digital-final.svg'; ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            opacity: 0.50;
            z-index: -2;
        }
        
        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: <?php echo $colores['primario']; ?>;
            mix-blend-mode: overlay;
            opacity: 0.3;
            z-index: -1;
            pointer-events: none;
        }
        
        .menu-container {
            max-width: 900px;
            margin: 0 auto;
            background: <?php echo $bgContainer; ?>;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .menu-header {
            background: linear-gradient(135deg, <?php echo $colores['primario']; ?> 0%, <?php echo $colores['secundario']; ?> 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
        }
        
        .menu-header .logo-container {
            margin-bottom: 15px;
        }
        
        .menu-header .logo-container img {
            max-height: 120px;
            max-width: 200px;
            border-radius: 50%;
            box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            background: white;
        }
        
        .menu-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            margin: 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .menu-header p {
            margin: 10px 0 0 0;
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        .menu-content {
            padding: 30px;
        }
        
        /* Estilos de tabs */
        .card-header {
            padding: 0;
            background: transparent;
            border-bottom: 2px solid <?php echo $colores['primario']; ?>;
        }
        
        .nav-tabs {
            border-bottom: none;
        }
        
        .nav-tabs .nav-link {
            border: none;
            color: <?php echo $modoOscuro ? '#aaa' : '#666'; ?>;
            font-weight: 500;
            padding: 15px 20px;
            margin-right: 5px;
            border-radius: 0;
            transition: all 0.3s ease;
            font-size: 20px;
        }
        
        .nav-tabs .nav-link:hover {
            color: <?php echo $colores['primario']; ?>;
            background: <?php echo $bgHover; ?>;
            font-size: 20px;
        }
        
        .nav-tabs .nav-link.active {
            color: <?php echo $colores['primario']; ?>;
            background: transparent;
            border-bottom: 3px solid <?php echo $colores['primario']; ?>;
            font-weight: bold;1
            font-size: 20px;
        }
        
        .tab-content {
            padding: 30px 0;
        }
        
        .menu-item {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            padding: 15px 0;
            border-bottom: 1px dashed <?php echo $modoOscuro ? '#444' : '#e0e0e0'; ?>;
            transition: all 0.3s ease;
        }
        
        .menu-item:last-child {
            border-bottom: none;
        }
        
        .menu-item:hover {
            background-color: <?php echo $bgHover; ?>;
            padding-left: 10px;
            padding-right: 10px;
            border-radius: 5px;
        }
        
        .item-name {
            font-size: 1.1rem;
            font-weight: 500;
            color: <?php echo $colorTexto; ?>;
            flex: 1;
        }
        
        .item-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: <?php echo $colores['primario']; ?>;
            margin-left: 20px;
            white-space: nowrap;
        }
        
        .card {
            background: transparent;
            border: none;
        }
        
        .card-body {
            background: transparent;
        }
        
        .menu-footer {
            background: <?php echo $modoOscuro ? '#1a1a1a' : '#f8f9fa'; ?>;
            padding: 20px;
            text-align: center;
            color: <?php echo $modoOscuro ? '#aaa' : '#6c757d'; ?>;
            font-size: 0.9rem;
        }
        
        .empty-menu {
            text-align: center;
            padding: 60px 20px;
            color: <?php echo $modoOscuro ? '#aaa' : '#6c757d'; ?>;
        }
        
        .empty-menu i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        .empty-menu h3 {
            color: <?php echo $colorTexto; ?>;
        }
        
        @media (max-width: 768px) {
            .menu-header h1 {
                font-size: 2rem;
            }
            
            .nav-tabs .nav-link {
                padding: 12px 15px;
                font-size: 18px;
            }
            
            .menu-content {
                padding: 20px;
            }
            
            .item-name {
                font-size: 1rem;
            }
            
            .item-price {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <!-- Header -->
        <div class="menu-header">
            <?php if (!empty($logoMenu) && file_exists("../assets/img/menu/" . $logoMenu)): ?>
                <div class="logo-container">
                    <img src="../assets/img/menu/<?php echo htmlspecialchars($logoMenu); ?>" alt="<?php echo htmlspecialchars($nombreRestaurante); ?>">
                </div>
            <?php else: ?>
                <i class="fas fa-utensils fa-3x mb-3"></i>
            <?php endif; ?>
            <h1><?php echo htmlspecialchars($nombreRestaurante); ?></h1>
            <p>Nuestro Menú Digital</p>
        </div>
        
        <!-- Content -->
        <div class="menu-content">
            <?php if (count($bloques) > 0): ?>
                <!-- Card con Tabs -->
                <div class="card card-outline card-outline-tabs">
                    <div class="card-header p-0 border-bottom-0">
                        <ul class="nav nav-tabs" id="menuTabs" role="tablist">
                            <?php foreach ($bloques as $index => $bloque): ?>
                                <li class="nav-item">
                                    <a class="nav-link <?php echo $index === 0 ? 'active' : ''; ?>" 
                                       id="tab-<?php echo $index; ?>" 
                                       data-toggle="pill" 
                                       href="#content-<?php echo $index; ?>" 
                                       role="tab" 
                                       aria-controls="content-<?php echo $index; ?>" 
                                       aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>">
                                        <?php echo htmlspecialchars($bloque['titulo']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="menuTabContent">
                            <?php foreach ($bloques as $index => $bloque): ?>
                                <div class="tab-pane fade <?php echo $index === 0 ? 'show active' : ''; ?>" 
                                     id="content-<?php echo $index; ?>" 
                                     role="tabpanel" 
                                     aria-labelledby="tab-<?php echo $index; ?>">
                                    <?php if (count($bloque['productos']) > 0): ?>
                                        <?php foreach ($bloque['productos'] as $producto): ?>
                                            <div class="menu-item">
                                                <span class="item-name"><?php echo htmlspecialchars($producto['nombre_producto']); ?></span>
                                                <span class="item-price">$<?php echo number_format($producto['valor_con_iva'], 0, ',', '.'); ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <p class="text-muted text-center">No hay productos en esta sección</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-menu">
                    <i class="fas fa-book-open"></i>
                    <h3>Menú en construcción</h3>
                    <p>Pronto tendremos nuestro menú digital disponible</p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer -->
        <div class="menu-footer">
           <strong>Copyright © 2026 FUDDO. TODOS los derechos reservados.<a href="https://fuddo.co/" class="a-fuddo">FUDDO</a>.</strong>
            <div class="float-right d-none d-sm-inline-block">
            <!-- <b>Versión</b> 1.0 -->
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
