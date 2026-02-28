<?php
/**
 * Script de migración para aplicar el sistema de costeo automático
 * a restaurantes existentes
 * 
 * Esto se puede ejecutar desde una página de administración
 */

// Solo permitir acceso local o a super-admin
if (php_sapi_name() !== 'cli') {
    // Si es web, verificar autenticación
    include 'includes/auth.php';
    
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'super-admin') {
        die('Acceso denegado. Se requieren permisos de super administrador.');
    }
}

include 'includes/conexion_master.php';

/**
 * Función para aplicar la migración a un restaurante
 */
function aplicarMigracionCosteo($conexion, $identificador) {
    $prefix = 'fuddo_' . $identificador . '_';
    
    // Array de sentencias SQL a ejecutar
    $sentencias = [
        // Crear tabla de Materias Primas
        "CREATE TABLE IF NOT EXISTS `{$prefix}materias_primas` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_materia_prima` varchar(50) NOT NULL,
            `nombre` varchar(200) NOT NULL,
            `unidad_medida` enum('kg','g','lb','l','ml','und') NOT NULL COMMENT 'Unidad de medida',
            `cantidad_base_comprada` decimal(10,3) NOT NULL,
            `costo_total_base` decimal(10,2) NOT NULL,
            `costo_por_unidad_minima` decimal(15,6) NOT NULL,
            `unidad_minima` varchar(10) NOT NULL,
            `cantidad_en_unidad_minima` decimal(15,3) NOT NULL,
            `estado` enum('activo','inactivo') DEFAULT 'activo',
            `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
            `fecha_ultima_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_id_materia_prima` (`id_materia_prima`),
            KEY `idx_unidad_medida` (`unidad_medida`),
            KEY `idx_estado` (`estado`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Crear tabla de Recetas
        "CREATE TABLE IF NOT EXISTS `{$prefix}recetas` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_receta` varchar(50) NOT NULL,
            `nombre_platillo` varchar(200) NOT NULL,
            `descripcion` text,
            `costo_total_receta` decimal(10,2) DEFAULT 0.00,
            `id_producto_asociado` int(11),
            `estado` enum('activo','inactivo') DEFAULT 'activo',
            `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
            `fecha_ultima_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_id_receta` (`id_receta`),
            KEY `idx_estado` (`estado`),
            KEY `idx_producto_asociado` (`id_producto_asociado`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Crear tabla de Ingredientes de Recetas
        "CREATE TABLE IF NOT EXISTS `{$prefix}receta_ingredientes` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `id_receta` varchar(50) NOT NULL,
            `id_materia_prima` varchar(50) NOT NULL,
            `cantidad_usada` decimal(10,3) NOT NULL,
            `unidad_cantidad` varchar(10) NOT NULL,
            `costo_ingrediente` decimal(10,2) NOT NULL,
            `orden` int(11) DEFAULT 0,
            `nota` text,
            `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_receta` (`id_receta`),
            KEY `idx_materia_prima` (`id_materia_prima`),
            FOREIGN KEY (`id_receta`) REFERENCES `{$prefix}recetas` (`id_receta`) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (`id_materia_prima`) REFERENCES `{$prefix}materias_primas` (`id_materia_prima`) ON DELETE RESTRICT ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
        
        // Crear índice
        "CREATE INDEX idx_receta_materia ON `{$prefix}receta_ingredientes` (`id_receta`, `id_materia_prima`)"
    ];
    
    // Ejecutar cada sentencia
    foreach ($sentencias as $sql) {
        if (!$conexion->query($sql)) {
            return [
                'exito' => false,
                'error' => $conexion->error,
                'sql' => $sql
            ];
        }
    }
    
    return [
        'exito' => true,
        'mensaje' => "Migración completada para restaurante: $identificador"
    ];
}

// Si es llamada por CLI
if (php_sapi_name() === 'cli') {
    echo "=== Migración Sistema de Costeo Automático ===\n\n";
    
    // Obtener todos los restaurantes
    $sql = "SELECT id, identificador, nombre FROM restaurantes WHERE estado = 'activo'";
    $resultado = $conexion_master->query($sql);
    
    if (!$resultado || $resultado->num_rows === 0) {
        echo "No hay restaurantes activos.\n";
        exit();
    }
    
    $migraciones_exitosas = 0;
    $migraciones_fallidas = 0;
    
    while ($row = $resultado->fetch_assoc()) {
        echo "Procesando: " . $row['nombre'] . " ({$row['identificador']})... ";
        
        // Crear conexión dinámica para el restaurante
        $conexion_rest = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        $conexion_rest->set_charset("utf8mb4");
        
        if ($conexion_rest->connect_error) {
            echo "ERROR: No se pudo conectar\n";
            $migraciones_fallidas++;
            continue;
        }
        
        $resultado_migracion = aplicarMigracionCosteo($conexion_rest, $row['identificador']);
        
        if ($resultado_migracion['exito']) {
            echo "✓ OK\n";
            $migraciones_exitosas++;
        } else {
            echo "✗ FALLO: " . $resultado_migracion['error'] . "\n";
            $migraciones_fallidas++;
        }
        
        $conexion_rest->close();
    }
    
    echo "\n=== Resumen ===\n";
    echo "Exitosas: $migraciones_exitosas\n";
    echo "Fallidas: $migraciones_fallidas\n";
    exit();
}

// Si es unacall AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'migrar') {
    header('Content-Type: application/json');
    
    $identificador = $_POST['identificador'] ?? '';
    
    if (empty($identificador)) {
        http_response_code(400);
        echo json_encode(['exito' => false, 'error' => 'Identificador no proporcionado']);
        exit();
    }
    
    // Crear conexión
    $conexion_rest = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conexion_rest->set_charset("utf8mb4");
    
    if ($conexion_rest->connect_error) {
        echo json_encode(['exito' => false, 'error' => 'Error de conexión']);
        exit();
    }
    
    $resultado = aplicarMigracionCosteo($conexion_rest, $identificador);
    $conexion_rest->close();
    
    echo json_encode($resultado);
    exit();
}

?>
