<?php
/**
 * Procesador del módulo de Recetas
 * Maneja operaciones CRUD de recetas e ingredientes
 * También integra automáticamente con el módulo de Productos
 */

include '../includes/auth.php';
include '../includes/conexion.php';
include '../includes/funciones_conversiones.php';

// Definir constantes de tablas
if (!defined('TBL_RECETAS')) define('TBL_RECETAS', $TABLE_PREFIX . 'recetas');
if (!defined('TBL_RECETA_INGREDIENTES')) define('TBL_RECETA_INGREDIENTES', $TABLE_PREFIX . 'receta_ingredientes');
if (!defined('TBL_MATERIAS_PRIMAS')) define('TBL_MATERIAS_PRIMAS', $TABLE_PREFIX . 'materias_primas');
if (!defined('TBL_PRODUCTOS')) define('TBL_PRODUCTOS', $TABLE_PREFIX . 'productos');

header('Content-Type: application/json');

// GET para obtener ingredientes de una receta
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'obtener_ingredientes') {
    try {
        $id_receta = $conexion->real_escape_string($_GET['id_receta'] ?? '');
        
        if (!$id_receta) {
            throw new Exception('ID de receta inválido');
        }
        
        $sql = "SELECT ri.id, ri.id_materia_prima, ri.cantidad_usada, ri.unidad_cantidad, 
                       ri.costo_ingrediente, ri.nota, ri.orden,
                       mp.nombre as nombre_materia_prima, mp.costo_por_unidad_minima as costo_unitario_materia
                FROM " . TBL_RECETA_INGREDIENTES . " ri
                INNER JOIN " . TBL_MATERIAS_PRIMAS . " mp ON ri.id_materia_prima = mp.id_materia_prima
                WHERE ri.id_receta = '$id_receta'
                ORDER BY ri.orden";
        
        $resultado = $conexion->query($sql);
        $ingredientes = [];
        
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                $ingredientes[] = $row;
            }
        }
        
        echo json_encode([
            'exito' => true,
            'ingredientes' => $ingredientes
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

// AJAX para obtener datos de una receta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'obtener') {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            throw new Exception('ID inválido');
        }
        
        $sql = "SELECT * FROM " . TBL_RECETAS . " WHERE id = $id";
        $resultado = $conexion->query($sql);
        
        if (!$resultado || $resultado->num_rows === 0) {
            throw new Exception('Receta no encontrada');
        }
        
        $receta = $resultado->fetch_assoc();
        
        // Obtener ingredientes
        $sqlIng = "SELECT ri.id, ri.id_materia_prima, ri.cantidad_usada, ri.unidad_cantidad, 
                          ri.costo_ingrediente, ri.nota, ri.orden,
                          mp.nombre as nombre_materia_prima, mp.costo_por_unidad_minima as costo_unitario_materia
                   FROM " . TBL_RECETA_INGREDIENTES . " ri
                   INNER JOIN " . TBL_MATERIAS_PRIMAS . " mp ON ri.id_materia_prima = mp.id_materia_prima
                   WHERE ri.id_receta = '" . $receta['id_receta'] . "'
                   ORDER BY ri.orden";
        
        $resIng = $conexion->query($sqlIng);
        $ingredientes = [];
        
        if ($resIng && $resIng->num_rows > 0) {
            while ($row = $resIng->fetch_assoc()) {
                $ingredientes[] = $row;
            }
        }
        
        echo json_encode([
            'exito' => true,
            'receta' => $receta,
            'ingredientes' => $ingredientes
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

// AJAX para eliminar receta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            throw new Exception('ID inválido');
        }
        
        // Obtener datos de la receta
        $sqlGet = "SELECT id_receta, id_producto_asociado FROM " . TBL_RECETAS . " WHERE id = $id";
        $resGet = $conexion->query($sqlGet);
        
        if (!$resGet || $resGet->num_rows === 0) {
            throw new Exception('Receta no encontrada');
        }
        
        $row = $resGet->fetch_assoc();
        $id_receta = $row['id_receta'];
        $id_producto = $row['id_producto_asociado'];
        
        // Iniciar transacción
        $conexion->begin_transaction();
        
        // Eliminar ingredientes
        $sqlDelIng = "DELETE FROM " . TBL_RECETA_INGREDIENTES . " WHERE id_receta = '$id_receta'";
        if (!$conexion->query($sqlDelIng)) {
            throw new Exception('Error al eliminar ingredientes: ' . $conexion->error);
        }
        
        // Eliminar receta
        $sqlDel = "DELETE FROM " . TBL_RECETAS . " WHERE id = $id";
        if (!$conexion->query($sqlDel)) {
            throw new Exception('Error al eliminar receta: ' . $conexion->error);
        }
        
        // Eliminar producto asociado si existe
        if ($id_producto) {
            $sqlDelProd = "DELETE FROM " . TBL_PRODUCTOS . " WHERE id = $id_producto";
            $conexion->query($sqlDelProd); // No es crítico si falla
        }
        
        $conexion->commit();
        
        echo json_encode([
            'exito' => true,
            'mensaje' => 'Receta eliminada'
        ]);
        exit();
    } catch (Exception $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollback();
        }
        http_response_code(400);
        echo json_encode([
            'exito' => false,
            'mensaje' => $e->getMessage()
        ]);
        exit();
    }
}

// CREAR RECETA (formulario tradicional)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'crear') {
    try {
        $nombre_platillo = $conexion->real_escape_string($_POST['nombre_platillo'] ?? '');
        $descripcion = $conexion->real_escape_string($_POST['descripcion'] ?? '');
        
        // Obtener ingredientes del formulario
        $materias = $_POST['materia_prima'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        $notas = $_POST['nota'] ?? [];
        
        // Validar
        if (empty($nombre_platillo)) {
            header("Location: recetas.php?error=nombre_vacio");
            exit();
        }
        
        if (count($materias) === 0 || count(array_filter($materias)) === 0) {
            header("Location: recetas.php?error=sin_ingredientes");
            exit();
        }
        
        // Iniciar transacción
        $conexion->begin_transaction();
        
        // Generar ID único para la receta
        $sqlCount = "SELECT COUNT(*) as total FROM " . TBL_RECETAS;
        $resCount = $conexion->query($sqlCount);
        $rowCount = $resCount->fetch_assoc();
        $siguiente_numero = $rowCount['total'] + 1;
        $id_receta = "REC-" . str_pad($siguiente_numero, 5, '0', STR_PAD_LEFT);
        
        // Calcular costo total e insertar receta
        $costo_total_receta = 0;
        $ingredientes_validos = 0;
        
        // Primero, validar y calcular costos
        foreach ($materias as $key => $id_materia) {
            if (empty($id_materia)) continue;
            
            if (empty($cantidades[$key])) {
                throw new Exception("Falta cantidad para un ingrediente");
            }
            
            $cantidad = floatval($cantidades[$key]);
            if ($cantidad <= 0) {
                throw new Exception("Cantidad debe ser mayor a 0");
            }
            
            // Obtener datos de la materia prima
            $id_materia_esc = $conexion->real_escape_string($id_materia);
            $sqlMP = "SELECT costo_por_unidad_minima, unidad_minima FROM " . TBL_MATERIAS_PRIMAS . 
                     " WHERE id_materia_prima = '$id_materia_esc'";
            $resMP = $conexion->query($sqlMP);
            
            if (!$resMP || $resMP->num_rows === 0) {
                throw new Exception("Materia prima $id_materia no encontrada");
            }
            
            $rowMP = $resMP->fetch_assoc();
            $costo_unitario = floatval($rowMP['costo_por_unidad_minima']);
            $unidad = $rowMP['unidad_minima'];
            
            $costo_ingrediente = $cantidad * $costo_unitario;
            $costo_total_receta += $costo_ingrediente;
            $ingredientes_validos++;
        }
        
        if ($ingredientes_validos === 0) {
            throw new Exception("La receta debe tener al menos un ingrediente válido");
        }
        
        // Insertar receta
        $sql = "INSERT INTO " . TBL_RECETAS . " 
                (id_receta, nombre_platillo, descripcion, costo_total_receta, estado)
                VALUES ('$id_receta', '$nombre_platillo', '$descripcion', $costo_total_receta, 'activo')";
        
        if (!$conexion->query($sql)) {
            throw new Exception("Error: " . $conexion->error);
        }
        
        $id_receta_bd = $conexion->insert_id;
        
        // Insertar ingredientes
        $orden = 0;
        foreach ($materias as $key => $id_materia) {
            if (empty($id_materia)) continue;
            
            $cantidad = floatval($cantidades[$key]);
            $nota = $conexion->real_escape_string($notas[$key] ?? '');
            
            // Obtener datos de la materia prima
            $id_materia_esc = $conexion->real_escape_string($id_materia);
            $sqlMP = "SELECT costo_por_unidad_minima, unidad_minima FROM " . TBL_MATERIAS_PRIMAS . 
                     " WHERE id_materia_prima = '$id_materia_esc'";
            $resMP = $conexion->query($sqlMP);
            $rowMP = $resMP->fetch_assoc();
            
            $costo_unitario = floatval($rowMP['costo_por_unidad_minima']);
            $unidad = $rowMP['unidad_minima'];
            $costo_ingrediente = $cantidad * $costo_unitario;
            
            $sqlIng = "INSERT INTO " . TBL_RECETA_INGREDIENTES . " 
                       (id_receta, id_materia_prima, cantidad_usada, unidad_cantidad, 
                        costo_ingrediente, nota, orden)
                       VALUES ('$id_receta', '$id_materia_esc', $cantidad, '$unidad', 
                               $costo_ingrediente, '$nota', $orden)";
            
            if (!$conexion->query($sqlIng)) {
                throw new Exception("Error inserting ingredient: " . $conexion->error);
            }
            
            $orden++;
        }
        
        // Crear producto automáticamente
        // Calcular IVA estándar (19%)
        $porcentaje_iva = 0.19; // 19% es estándar en Colombia
        $costo_sin_iva = $costo_total_receta;
        $costo_con_iva = $costo_total_receta * (1 + $porcentaje_iva);
        
        // Generar ID único para el producto
        $sqlCountProd = "SELECT COUNT(*) as total FROM " . TBL_PRODUCTOS;
        $resCountProd = $conexion->query($sqlCountProd);
        $rowCountProd = $resCountProd->fetch_assoc();
        $siguienteProd = $rowCountProd['total'] + 1;
        $id_producto = "PR-" . $siguienteProd;
        
        $sqlProd = "INSERT INTO " . TBL_PRODUCTOS . " 
                    (id_producto, nombre_producto, costo_producto, valor_sin_iva, valor_con_iva, 
                     inventario, minimo_inventario, estado)
                    VALUES ('$id_producto', '$nombre_platillo', $costo_sin_iva, $costo_sin_iva, $costo_con_iva,
                            0, 1, 'activo')";
        
        if ($conexion->query($sqlProd)) {
            $id_producto_bd = $conexion->insert_id;
            
            // Actualizar receta con ID del producto asociado
            $sqlUpd = "UPDATE " . TBL_RECETAS . " SET id_producto_asociado = $id_producto_bd WHERE id = $id_receta_bd";
            $conexion->query($sqlUpd);
        }
        
        $conexion->commit();
        
        header("Location: recetas.php?exito=creado");
        exit();
        
    } catch (Exception $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollback();
        }
        header("Location: recetas.php?error=crear&msg=" . urlencode($e->getMessage()));
        exit();
    }
}

// ACTUALIZAR RECETA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
    try {
        $id = intval($_POST['id'] ?? 0);
        $nombre_platillo = $conexion->real_escape_string($_POST['nombre_platillo'] ?? '');
        $descripcion = $conexion->real_escape_string($_POST['descripcion'] ?? '');
        $estado = in_array($_POST['estado'] ?? 'activo', ['activo', 'inactivo']) ? $_POST['estado'] : 'activo';
        
        // Obtener ingredientes del formulario
        $materias = $_POST['materia_prima'] ?? [];
        $cantidades = $_POST['cantidad'] ?? [];
        $notas = $_POST['nota'] ?? [];
        
        // Validar
        if ($id <= 0 || empty($nombre_platillo)) {
            header("Location: recetas.php?error=valores_invalidos");
            exit();
        }
        
        if (count($materias) === 0 || count(array_filter($materias)) === 0) {
            header("Location: recetas.php?error=sin_ingredientes");
            exit();
        }
        
        // Iniciar transacción
        $conexion->begin_transaction();
        
        // Obtener ID de receta e ID de producto
        $sqlGet = "SELECT id_receta, id_producto_asociado FROM " . TBL_RECETAS . " WHERE id = $id";
        $resGet = $conexion->query($sqlGet);
        
        if (!$resGet || $resGet->num_rows === 0) {
            throw new Exception('Receta no encontrada');
        }
        
        $rowGet = $resGet->fetch_assoc();
        $id_receta = $rowGet['id_receta'];
        $id_producto = $rowGet['id_producto_asociado'];
        
        // Calcular costo total
        $costo_total_receta = 0;
        
        foreach ($materias as $key => $id_materia) {
            if (empty($id_materia)) continue;
            
            $cantidad = floatval($cantidades[$key]);
            
            // Obtener datos de la materia prima
            $id_materia_esc = $conexion->real_escape_string($id_materia);
            $sqlMP = "SELECT costo_por_unidad_minima FROM " . TBL_MATERIAS_PRIMAS . 
                     " WHERE id_materia_prima = '$id_materia_esc'";
            $resMP = $conexion->query($sqlMP);
            $rowMP = $resMP->fetch_assoc();
            
            $costo_unitario = floatval($rowMP['costo_por_unidad_minima']);
            $costo_total_receta += ($cantidad * $costo_unitario);
        }
        
        // Actualizar receta
        $sql = "UPDATE " . TBL_RECETAS . " 
                SET nombre_platillo = '$nombre_platillo', 
                    descripcion = '$descripcion',
                    costo_total_receta = $costo_total_receta,
                    estado = '$estado',
                    fecha_ultima_actualizacion = NOW()
                WHERE id = $id";
        
        if (!$conexion->query($sql)) {
            throw new Exception("Error: " . $conexion->error);
        }
        
        // Eliminar ingredientes existentes
        $sqlDelIng = "DELETE FROM " . TBL_RECETA_INGREDIENTES . " WHERE id_receta = '$id_receta'";
        if (!$conexion->query($sqlDelIng)) {
            throw new Exception("Error al eliminar ingredientes: " . $conexion->error);
        }
        
        // Insertar nuevos ingredientes
        $orden = 0;
        foreach ($materias as $key => $id_materia) {
            if (empty($id_materia)) continue;
            
            $cantidad = floatval($cantidades[$key]);
            $nota = $conexion->real_escape_string($notas[$key] ?? '');
            
            // Obtener datos de la materia prima
            $id_materia_esc = $conexion->real_escape_string($id_materia);
            $sqlMP = "SELECT costo_por_unidad_minima, unidad_minima FROM " . TBL_MATERIAS_PRIMAS . 
                     " WHERE id_materia_prima = '$id_materia_esc'";
            $resMP = $conexion->query($sqlMP);
            $rowMP = $resMP->fetch_assoc();
            
            $costo_unitario = floatval($rowMP['costo_por_unidad_minima']);
            $unidad = $rowMP['unidad_minima'];
            $costo_ingrediente = $cantidad * $costo_unitario;
            
            $sqlIng = "INSERT INTO " . TBL_RECETA_INGREDIENTES . " 
                       (id_receta, id_materia_prima, cantidad_usada, unidad_cantidad, 
                        costo_ingrediente, nota, orden)
                       VALUES ('$id_receta', '$id_materia_esc', $cantidad, '$unidad', 
                               $costo_ingrediente, '$nota', $orden)";
            
            if (!$conexion->query($sqlIng)) {
                throw new Exception("Error inserting ingredient: " . $conexion->error);
            }
            
            $orden++;
        }
        
        // Actualizar producto asociado
        if ($id_producto) {
            $porcentaje_iva = 0.19;
            $costo_sin_iva = $costo_total_receta;
            $costo_con_iva = $costo_total_receta * (1 + $porcentaje_iva);
            
            $sqlUpdProd = "UPDATE " . TBL_PRODUCTOS . " 
                           SET nombre_producto = '$nombre_platillo',
                               costo_producto = $costo_sin_iva,
                               valor_sin_iva = $costo_sin_iva,
                               valor_con_iva = $costo_con_iva
                           WHERE id = $id_producto";
            
            $conexion->query($sqlUpdProd);
        }
        
        $conexion->commit();
        
        header("Location: recetas.php?exito=actualizado");
        exit();
        
    } catch (Exception $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollback();
        }
        header("Location: recetas.php?error=actualizar&msg=" . urlencode($e->getMessage()));
        exit();
    }
}

// Si no hay acción válida
header("Location: recetas.php");
exit();
?>
