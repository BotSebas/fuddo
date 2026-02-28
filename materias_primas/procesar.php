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
            $id_materia_prima = "MP-" . str_pad($siguiente_numero, 5, '0', STR_PAD_LEFT);
            
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

// Si no hay acción válida
header("Location: materias_primas.php");
exit();
?>
