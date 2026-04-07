<?php
session_start();
header('Content-Type: application/json');

// Verificar si es super-admin
if (!isset($_SESSION['rol_master']) || $_SESSION['rol_master'] !== 'super-admin') {
    http_response_code(403);
    echo json_encode([
        'exito' => false,
        'error' => 'Acceso denegado'
    ]);
    exit();
}

require_once 'includes/conexion_master.php';

$accion = $_GET['accion'] ?? '';

if ($accion === 'escanear') {
    try {
        $cambios = [];

        // Obtener lista de restaurantes desde la BD maestra mgacgdnjkg
        $sqlRestaurantes = "SELECT id, nombre, nombre_bd FROM mgacgdnjkg.restaurantes WHERE estado = 'activo' ORDER BY nombre ASC";
        $resultRestaurantes = $conexion_master->query($sqlRestaurantes);

        if (!$resultRestaurantes || $resultRestaurantes->num_rows === 0) {
            echo json_encode([
                'exito' => true,
                'cambios' => []
            ]);
            exit();
        }

        // Obtener todas las BDs que existen (que empiezan con fuddo_)
        $bdDisponibles = [];
        $sqlBDs = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME LIKE 'fuddo_%' AND SCHEMA_NAME NOT LIKE '%_master'";
        $resultBDs = $conexion_master->query($sqlBDs);
        
        if ($resultBDs && $resultBDs->num_rows > 0) {
            while ($rowBD = $resultBDs->fetch_assoc()) {
                $bdDisponibles[] = $rowBD['SCHEMA_NAME'];
            }
        }

        // Leer template SQL
        $templateFile = __DIR__ . '/sql/template_restaurante.sql';
        if (!file_exists($templateFile)) {
            throw new Exception('Template SQL no encontrado');
        }

        $templateContent = file_get_contents($templateFile);
        $tablas_template = extraerTablasTemplate($templateContent);

        // Tablas a ignorar
        $tablasIgnorar = ['servicios_total', 'comandas_total', 'receta_ingredientes'];

        // Conectar a cada BD disponible y comparar estructura
        foreach ($bdDisponibles as $nombreBD) {
            // El nombre del restaurante - intentar obtener de la tabla restaurantes en mgacgdnjkg
            $nombreRestaurante = $nombreBD;
            $sqlNombreRest = "SELECT nombre FROM mgacgdnjkg.restaurantes WHERE nombre_bd = '$nombreBD' OR nombre_bd = '${nombreBD}_' LIMIT 1";
            $resultNombreRest = $conexion_master->query($sqlNombreRest);
            if ($resultNombreRest && $resultNombreRest->num_rows > 0) {
                $rowNombreRest = $resultNombreRest->fetch_assoc();
                $nombreRestaurante = $rowNombreRest['nombre'];
            }
            
            // Conexión a la BD
            $db_host = 'localhost';
            $db_user = 'root';
            $db_pass = '';

            $conexionRest = new mysqli($db_host, $db_user, $db_pass, $nombreBD);

            if ($conexionRest->connect_error) {
                continue;
            }

            $conexionRest->set_charset("utf8mb4");

            // El prefijo de tablas es: nombre_bd + "_"
            $tablePrefix = $nombreBD . '_';

            // Verificar cada tabla
            foreach ($tablas_template as $nombreTabla => $columnas) {
                if (in_array($nombreTabla, $tablasIgnorar)) continue;

                $nombreTablaReal = $tablePrefix . $nombreTabla;

                // Verificar si la tabla existe
                $sqlCheck = "SHOW TABLES LIKE '$nombreTablaReal'";
                $resultCheck = $conexionRest->query($sqlCheck);

                if (!$resultCheck || $resultCheck->num_rows === 0) {
                    // Tabla no existe - necesita CREATE
                    $createSQL = generarCreateTable($nombreTablaReal, $columnas);
                    $cambios[] = [
                        'restaurante' => $nombreRestaurante,
                        'tipo' => 'CREATE TABLE',
                        'descripcion' => "Tabla '$nombreTablaReal' no existe y necesita ser creada",
                        'query' => $createSQL
                    ];
                } else {
                    // Tabla existe - verificar columnas
                    $sqlDescribe = "DESCRIBE $nombreTablaReal";
                    $resultDescribe = $conexionRest->query($sqlDescribe);
                    $columnasExistentes = [];

                    if ($resultDescribe) {
                        while ($colRow = $resultDescribe->fetch_assoc()) {
                            $columnasExistentes[$colRow['Field']] = $colRow;
                        }
                    }

                    // Comparar columnas
                    foreach ($columnas as $nombreCol => $definicion) {
                        if (!isset($columnasExistentes[$nombreCol])) {
                            // Columna faltante
                            $alterSQL = "ALTER TABLE $nombreTablaReal ADD COLUMN " . $definicion['completo'] . ";";
                            $cambios[] = [
                                'restaurante' => $nombreRestaurante,
                                'tipo' => 'ALTER TABLE',
                                'descripcion' => "Columna '$nombreCol' faltante en tabla '$nombreTablaReal'",
                                'query' => $alterSQL
                            ];
                        }
                    }
                }
            }

            $conexionRest->close();
        }

        echo json_encode([
            'exito' => true,
            'cambios' => $cambios,
            'total' => count($cambios),
            'mensaje' => count($cambios) === 0 ? 'No se encontraron cambios pendientes o no se pudo conectar a las bases de datos.' : null
        ]);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode([
        'exito' => false,
        'error' => 'Acción no válida'
    ]);
}

/**
 * Extrae las tablas y columnas del template SQL
 */
function extraerTablasTemplate($contenido) {
    $tablas = [];
    
    // Buscar bloques CREATE TABLE
    preg_match_all('/CREATE TABLE IF NOT EXISTS `\{PREFIX\}(\w+)`\s*\((.*?)\)\s*ENGINE/is', $contenido, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $nombreTabla = $match[1];
        $definicionTabla = $match[2];
        
        $columnas = [];
        
        // Buscar columnas (líneas que no sean PRIMARY KEY o KEY)
        preg_match_all('/`(\w+)`\s+([^,]+?)(?:,|$)/i', $definicionTabla, $colMatches, PREG_SET_ORDER);
        
        foreach ($colMatches as $colMatch) {
            $nombreCol = $colMatch[1];
            $tipo = trim($colMatch[2]);
            
            // Saltar keys y constraints
            if (preg_match('/^(PRIMARY|KEY|UNIQUE|CONSTRAINT)/i', $tipo)) {
                continue;
            }
            
            $columnas[$nombreCol] = [
                'nombre' => $nombreCol,
                'tipo' => $tipo,
                'completo' => "`$nombreCol` $tipo"
            ];
        }
        
        $tablas[$nombreTabla] = $columnas;
    }
    
    return $tablas;
}

/**
 * Genera un CREATE TABLE SQL
 */
function generarCreateTable($nombreTabla, $columnas) {
    $sql = "CREATE TABLE IF NOT EXISTS `$nombreTabla` (\n";
    
    $lineas = [];
    foreach ($columnas as $col) {
        $lineas[] = "    " . $col['completo'];
    }
    
    $sql .= implode(",\n", $lineas);
    $sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    return $sql;
}

?>
