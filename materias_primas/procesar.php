<?php
/**
 * Procesador del módulo de Materias Primas
 * Maneja operaciones CRUD y cálculos
 */

include '../includes/auth.php';
include '../includes/conexion.php';
include '../includes/funciones_conversiones.php';

// Definir constante para tabla de materias primas
if (!defined('TBL_MATERIAS_PRIMAS')) {
    define('TBL_MATERIAS_PRIMAS', $TABLE_PREFIX . 'materias_primas');
}

header('Content-Type: application/json');

// AJAX para calcular costo unitario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'calcular') {
    try {
        $unidad = $_POST['unidad'] ?? '';
        $cantidad = floatval($_POST['cantidad'] ?? 0);
        $costo = floatval($_POST['costo'] ?? 0);
        
        if (!esUnidadValida($unidad) || $cantidad <= 0 || $costo < 0) {
            throw new Exception('Parámetros inválidos');
        }
        
        $conversion = convertirAUnidadMinima($cantidad, $unidad);
        $costo_unitario = calcularCostoUnitarioMinimo($costo, $cantidad, $unidad);
        
        echo json_encode([
            'exito' => true,
            'cantidad_convertida' => $conversion['cantidad_convertida'],
            'unidad_minima' => $conversion['unidad_minima'],
            'costo_unitario' => $costo_unitario
        ]);
        exit();
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'error' => $e->getMessage()
        ]);
        exit();
    }
}

// AJAX para obtener datos de una materia prima
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'obtener') {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            throw new Exception('ID inválido');
        }
        
        $sql = "SELECT * FROM " . TBL_MATERIAS_PRIMAS . " WHERE id = $id";
        $resultado = $conexion->query($sql);
        
        if (!$resultado || $resultado->num_rows === 0) {
            throw new Exception('Materia prima no encontrada');
        }
        
        $materia_prima = $resultado->fetch_assoc();
        
        echo json_encode([
            'exito' => true,
            'materia_prima' => $materia_prima
        ]);
        exit();
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'error' => $e->getMessage()
        ]);
        exit();
    }
}

// AJAX para eliminar materia prima
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            throw new Exception('ID inválido');
        }
        
        // Verificar que no esté siendo usada en alguna receta
        if (!defined('TBL_RECETA_INGREDIENTES')) {
            define('TBL_RECETA_INGREDIENTES', $TABLE_PREFIX . 'receta_ingredientes');
        }
        
        // Obtener id_materia_prima antes de eliminar
        $sqlGet = "SELECT id_materia_prima FROM " . TBL_MATERIAS_PRIMAS . " WHERE id = $id";
        $resGet = $conexion->query($sqlGet);
        
        if (!$resGet || $resGet->num_rows === 0) {
            throw new Exception('Materia prima no encontrada');
        }
        
        $row = $resGet->fetch_assoc();
        $id_materia_prima = $row['id_materia_prima'];
        
        // Verificar si está en uso
        $sqlUso = "SELECT COUNT(*) as total FROM " . TBL_RECETA_INGREDIENTES . " WHERE id_materia_prima = '$id_materia_prima'";
        $resUso = $conexion->query($sqlUso);
        $rowUso = $resUso->fetch_assoc();
        
        if ($rowUso['total'] > 0) {
            throw new Exception('No puedes eliminar una materia prima que se usa en recetas. Primero actualiza las recetas.');
        }
        
        // Eliminar
        $sql = "DELETE FROM " . TBL_MATERIAS_PRIMAS . " WHERE id = $id";
        
        if (!$conexion->query($sql)) {
            throw new Exception('Error al eliminar: ' . $conexion->error);
        }
        
        echo json_encode([
            'exito' => true,
            'mensaje' => 'Materia prima eliminada'
        ]);
        exit();
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'mensaje' => $e->getMessage()
        ]);
        exit();
    }
}

// CREAR O ACTUALIZAR (formulario tradicional)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];
    
    if ($accion === 'crear') {
        try {
            $nombre = $conexion->real_escape_string($_POST['nombre'] ?? '');
            $unidad_medida = strtolower(trim($_POST['unidad_medida'] ?? ''));
            $cantidad_base = floatval($_POST['cantidad_base_comprada'] ?? 0);
            $costo_total = floatval($_POST['costo_total_base'] ?? 0);
            
            // Validar
            if (empty($nombre)) {
                header("Location: materias_primas.php?error=nombre_vacio");
                exit();
            }
            
            if (!esUnidadValida($unidad_medida)) {
                header("Location: materias_primas.php?error=unidad_invalida");
                exit();
            }
            
            if ($cantidad_base <= 0 || $costo_total < 0) {
                header("Location: materias_primas.php?error=valores_invalidos");
                exit();
            }
            
            // Verificar que no exista
            $sqlCheck = "SELECT id FROM " . TBL_MATERIAS_PRIMAS . " WHERE nombre = '$nombre'";
            $resCheck = $conexion->query($sqlCheck);
            
            if ($resCheck && $resCheck->num_rows > 0) {
                header("Location: materias_primas.php?error=duplicado");
                exit();
            }
            
            // Calcular conversión
            $conversion = convertirAUnidadMinima($cantidad_base, $unidad_medida);
            $costo_unitario = calcularCostoUnitarioMinimo($costo_total, $cantidad_base, $unidad_medida);
            
            // Generar ID único
            $sqlCount = "SELECT COUNT(*) as total FROM " . TBL_MATERIAS_PRIMAS;
            $resCount = $conexion->query($sqlCount);
            $rowCount = $resCount->fetch_assoc();
            $siguiente_numero = $rowCount['total'] + 1;
            $id_materia_prima = "MP-" . $siguiente_numero;
            
            // Insertar
            $sql = "INSERT INTO " . TBL_MATERIAS_PRIMAS . " 
                    (id_materia_prima, nombre, unidad_medida, cantidad_base_comprada, costo_total_base, 
                     costo_por_unidad_minima, unidad_minima, cantidad_en_unidad_minima, estado)
                    VALUES ('$id_materia_prima', '$nombre', '$unidad_medida', $cantidad_base, $costo_total,
                            $costo_unitario, '{$conversion['unidad_minima']}', {$conversion['cantidad_convertida']}, 'activo')";
            
            if (!$conexion->query($sql)) {
                throw new Exception("Error: " . $conexion->error);
            }
            
            header("Location: materias_primas.php?exito=creado");
            exit();
            
        } catch (Exception $e) {
            header("Location: materias_primas.php?error=crear&msg=" . urlencode($e->getMessage()));
            exit();
        }
    }
    
    elseif ($accion === 'actualizar') {
        try {
            $id = intval($_POST['id'] ?? 0);
            $nombre = $conexion->real_escape_string($_POST['nombre'] ?? '');
            $costo_total = floatval($_POST['costo_total_base'] ?? 0);
            $estado = in_array($_POST['estado'] ?? 'activo', ['activo', 'inactivo']) ? $_POST['estado'] : 'activo';
            
            if ($id <= 0 || empty($nombre) || $costo_total < 0) {
                header("Location: materias_primas.php?error=valores_invalidos");
                exit();
            }
            
            // Obtener datos actuales para recalcular
            $sqlGet = "SELECT cantidad_base_comprada, unidad_medida FROM " . TBL_MATERIAS_PRIMAS . " WHERE id = $id";
            $resGet = $conexion->query($sqlGet);
            
            if (!$resGet || $resGet->num_rows === 0) {
                header("Location: materias_primas.php?error=no_encontrado");
                exit();
            }
            
            $row = $resGet->fetch_assoc();
            $cantidad_base = floatval($row['cantidad_base_comprada']);
            $unidad_medida = $row['unidad_medida'];
            
            // Recalcular costo unitario con nuevo costo
            $conversion = convertirAUnidadMinima($cantidad_base, $unidad_medida);
            $costo_unitario = calcularCostoUnitarioMinimo($costo_total, $cantidad_base, $unidad_medida);
            
            // Actualizar
            $sql = "UPDATE " . TBL_MATERIAS_PRIMAS . " 
                    SET nombre = '$nombre', 
                        costo_total_base = $costo_total,
                        costo_por_unidad_minima = $costo_unitario,
                        estado = '$estado',
                        fecha_ultima_actualizacion = NOW()
                    WHERE id = $id";
            
            if (!$conexion->query($sql)) {
                throw new Exception("Error: " . $conexion->error);
            }
            
            header("Location: materias_primas.php?exito=actualizado");
            exit();
            
        } catch (Exception $e) {
            header("Location: materias_primas.php?error=actualizar&msg=" . urlencode($e->getMessage()));
            exit();
        }
    }
}

// IMPORTAR CSV
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'importar_csv') {
    try {
        // Verificar permisos (super-admin o user con app 'productos')
        $tienePermiso = false;
        
        if (isset($_SESSION['rol_master']) && $_SESSION['rol_master'] === 'super-admin') {
            $tienePermiso = true;
        } elseif (isset($_SESSION['rol']) && tienePermiso('productos')) {
            $tienePermiso = true;
        }
        
        if (!$tienePermiso) {
            throw new Exception('No tienes permisos para importar materias primas');
        }
        
        // Validar archivo
        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir el archivo');
        }
        
        $file = $_FILES['csv_file'];
        $filePath = $file['tmp_name'];
        
        // Validar tipo y tamaño
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            throw new Exception('El archivo supera 5MB');
        }
        
        if (!in_array($file['type'], ['text/csv', 'application/vnd.ms-excel', 'text/plain'])) {
            if (!preg_match('/\.csv$/i', $file['name'])) {
                throw new Exception('El archivo debe ser formato CSV');
            }
        }
        
        // Abrir y procesar CSV
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new Exception('No se puede leer el archivo');
        }
        
        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception('Error al abrir el archivo');
        }
        
        // Detectar separador: leer primera línea y contar comas vs punto-comas
        $primeraLinea = fgets($handle);
        if (!$primeraLinea) {
            fclose($handle);
            throw new Exception('El archivo está vacío');
        }
        
        // Normalizar la línea (remover espacios, comillas extras)
        $primeraLinea = trim($primeraLinea);
        
        // Detectar separador automáticamente
        $contarComas = substr_count($primeraLinea, ',');
        $contarPuntoComas = substr_count($primeraLinea, ';');
        $separador = ($contarPuntoComas > $contarComas) ? ';' : ',';
        
        error_log("DEBUG: Primera línea: $primeraLinea");
        error_log("DEBUG: Separador detectado: $separador (comas: $contarComas, punto-comas: $contarPuntoComas)");
        
        // Volver al inicio para procesar con el separador correcto
        rewind($handle);
        
        // Leer encabezado con el separador detectado
        $encabezado = fgetcsv($handle, 0, $separador);
        if (!$encabezado) {
            fclose($handle);
            throw new Exception('El archivo está vacío o no es un CSV válido');
        }
        
        // Normalizar encabezado - ULTRA ROBUSTO (igual que frontend)
        $encabezado = array_map(function($col) {
            $col = trim($col);
            
            // Remover BOM UTF-8 si existe (carácter especial al inicio)
            if (substr($col, 0, 3) === "\xEF\xBB\xBF") {
                $col = substr($col, 3);
            }
            
            // Remover comillas dobles
            $col = str_replace('"', '', $col);
            // Remover comillas simples
            $col = str_replace("'", '', $col);
            
            return trim($col);
        }, $encabezado);
        
        // Convertir a minúsculas
        $encabezado = array_map('strtolower', $encabezado);
        
        // Filtrar columnas vacías
        $encabezado = array_filter($encabezado, function($col) {
            return !empty($col);
        });
        
        // Validar columnas requeridas (SIN id_materia_prima porque se genera automáticamente)
        $columnasRequeridas = ['nombre', 'unidad_medida', 'cantidad_base_comprada', 'costo_total_base'];
        
        // Usar array_intersect para encontrar columnas que existen EN AMBOS arrays
        $columnasEncontradas = array_intersect($columnasRequeridas, $encabezado);
        
        error_log("DEBUG: Encabezado procesado: " . json_encode($encabezado));
        error_log("DEBUG: Columnas requeridas: " . json_encode($columnasRequeridas));
        error_log("DEBUG: Columnas encontradas: " . json_encode($columnasEncontradas));
        error_log("DEBUG: Validación: " . count($columnasEncontradas) . " de " . count($columnasRequeridas));
        
        if (count($columnasEncontradas) !== count($columnasRequeridas)) {
            $faltantes = array_diff($columnasRequeridas, $columnasEncontradas);
            fclose($handle);
            error_log("ERROR: Faltantes: " . json_encode($faltantes));
            throw new Exception('El CSV no tiene todas las columnas requeridas: ' . implode(', ', $columnasRequeridas) . ' | Encontrado: ' . implode(', ', $encabezado));
        }
        
        // Obtener el próximo número de ID automático
        $sqlMaxId = "SELECT id_materia_prima FROM " . TBL_MATERIAS_PRIMAS . " WHERE id_materia_prima LIKE 'MP-%' ORDER BY CAST(SUBSTRING(id_materia_prima, 4) AS UNSIGNED) DESC LIMIT 1";
        $resMaxId = $conexion->query($sqlMaxId);
        $proximoNumero = 1;
        if ($resMaxId && $resMaxId->num_rows > 0) {
            $rowMaxId = $resMaxId->fetch_assoc();
            $ultimoId = $rowMaxId['id_materia_prima'];
            // Extraer número de MP-123 → 123
            $numero = intval(substr($ultimoId, 3));
            $proximoNumero = $numero + 1;
        }
        
        // Procesar datos
        $insertadas = 0;
        $errores = 0;
        $erroresDetalle = [];
        $idosExistentes = [];
        $linea = 2; // Contador de línea (comenzando en 2 porque 1 es encabezado)
        
        // Preparar statement
        $sql = "INSERT INTO " . TBL_MATERIAS_PRIMAS . " 
                (id_materia_prima, nombre, unidad_medida, cantidad_base_comprada, costo_total_base, 
                 costo_por_unidad_minima, unidad_minima, cantidad_en_unidad_minima, estado, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'activo', NOW())";
        
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception('Error al preparar la consulta: ' . $conexion->error);
        }
        
        while (($fila = fgetcsv($handle, 0, $separador)) !== false) {
            // Limpiar y normalizar todos los valores de la fila
            $fila = array_map(function($valor) {
                $valor = trim($valor);
                
                // Remover BOM UTF-8 si existe
                if (substr($valor, 0, 3) === "\xEF\xBB\xBF") {
                    $valor = substr($valor, 3);
                }
                
                // Remover comillas
                $valor = str_replace('"', '', $valor);
                $valor = str_replace("'", '', $valor);
                
                return trim($valor);
            }, $fila);
            
            // Mapear a índices (SIN id_materia_prima porque se genera automáticamente)
            $nombre = $fila[array_search('nombre', $encabezado)] ?? '';
            $unidad = strtolower($fila[array_search('unidad_medida', $encabezado)] ?? '');
            $cantidad = floatval($fila[array_search('cantidad_base_comprada', $encabezado)] ?? 0);
            $costo = floatval($fila[array_search('costo_total_base', $encabezado)] ?? 0);
            
            // Generar ID automático
            $idMateria = 'MP-' . $proximoNumero;
            
            // Validaciones
            if (empty($nombre) || empty($unidad) || $cantidad <= 0 || $costo < 0) {
                $errores++;
                $erroresDetalle[] = "Línea $linea: Datos inválidos o incompletos";
                $linea++;
                continue;
            }
            
            // Validar unidad
            $unidadesValidas = ['kg', 'g', 'lb', 'l', 'ml', 'und'];
            if (!in_array($unidad, $unidadesValidas)) {
                $errores++;
                $erroresDetalle[] = "Línea $linea: Unidad inválida '$unidad'";
                $linea++;
                continue;
            }
            
            // Calcular conversión
            try {
                $conversion = convertirAUnidadMinima($cantidad, $unidad);
                $costoUnitario = calcularCostoUnitarioMinimo($costo, $cantidad, $unidad);
            } catch (Exception $e) {
                $errores++;
                $erroresDetalle[] = "Línea $linea: " . $e->getMessage();
                $linea++;
                continue;
            }
            
            // Escapar strings
            $idMateria = $conexion->real_escape_string($idMateria);
            $nombre = $conexion->real_escape_string($nombre);
            $unidadMinima = $conexion->real_escape_string($conversion['unidad_minima']);
            
            // Preparar bind
            $bind = [
                's', // id_materia_prima
                's', // nombre
                's', // unidad_medida
                'd', // cantidad_base_comprada
                'd', // costo_total_base
                'd', // costo_por_unidad_minima
                's', // unidad_minima
                'd'  // cantidad_en_unidad_minima
            ];
            
            $stmt->bind_param(
                implode('', $bind),
                $idMateria,
                $nombre,
                $unidad,
                $cantidad,
                $costo,
                $costoUnitario,
                $unidadMinima,
                $conversion['cantidad_convertida']
            );
            
            if (!$stmt->execute()) {
                $errores++;
                $erroresDetalle[] = "Línea $linea: " . $conexion->error;
            } else {
                $insertadas++;
                $proximoNumero++; // Incrementar para la siguiente iteración
            }
            
            $linea++;
        }
        
        fclose($handle);
        $stmt->close();
        
        // Responder con resumen
        $mensaje = "Importación completada";
        if (count($idosExistentes) > 0) {
            $mensaje .= "\n" . count($idosExistentes) . " registros actualizados (ya existían)";
        }
        
        echo json_encode([
            'exito' => true,
            'insertadas' => $insertadas,
            'errores' => $errores,
            'mensaje' => $mensaje,
            'detalles' => array_slice($erroresDetalle, 0, 5) // Máximo 5 errores
        ]);
        exit();
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'mensaje' => $e->getMessage()
        ]);
        exit();
    }
}

// Si no hay acción válida
header("Location: materias_primas.php");
exit();
?>
