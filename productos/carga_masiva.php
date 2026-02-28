<?php
session_start();
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
require_once '../includes/conexion.php';
    // Asegurar que la conexión use UTF-8
    $conexion->set_charset("utf8mb4");

// Verificar que sea super-admin
if (!isset($_SESSION['rol_master']) || $_SESSION['rol_master'] !== 'super-admin' || !isset($_SESSION['id_restaurante'])) {
    header("Location: productos.php?error=acceso_denegado");
    exit();
}

// Verificar que se haya subido un archivo
if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] !== UPLOAD_ERR_OK) {
    header("Location: productos.php?error=sin_archivo");
    exit();
}

$archivo = $_FILES['archivo'];
$extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));

// Validar extensión (CSV o Excel)
if (!in_array($extension, ['csv', 'xlsx', 'xls'])) {
    header("Location: productos.php?error=formato_invalido");
    exit();
}

try {
    // Guardar archivo temporalmente
    $nombreArchivo = 'temp_' . time() . '.' . $extension;
    $rutaTemporal = 'uploads_temp/' . $nombreArchivo;
    
    // Crear carpeta si no existe
    if (!file_exists('uploads_temp')) {
        mkdir('uploads_temp', 0777, true);
    }
    
    // Mover archivo
    if (!move_uploaded_file($archivo['tmp_name'], $rutaTemporal)) {
        header("Location: productos.php?error=error_guardar");
        exit();
    }
    
    $filas = [];
    
    // Leer archivo según formato
    if ($extension === 'csv') {
        // Configurar locale para Windows
        $contenido = file_get_contents($rutaTemporal);

// Detectar encoding real
$encoding = mb_detect_encoding(
    $contenido,
    ['UTF-8', 'ISO-8859-1', 'Windows-1252'],
    true
);

// Convertir SOLO si no es UTF-8
if ($encoding !== 'UTF-8') {
    $contenido = iconv($encoding, 'UTF-8//IGNORE', $contenido);
}
// Quitar BOM si existe
$contenido = preg_replace('/^\xEF\xBB\xBF/', '', $contenido);

        // Dividir por líneas
        $lineas = explode("\n", $contenido);
        
        // Procesar cada línea
        foreach ($lineas as $linea) {
            $linea = trim($linea);
            if (empty($linea)) continue;
            
            // Dividir por punto y coma
            $data = str_getcsv($linea, ';');
            $filas[] = $data;
        }
    } else {
        // Para Excel, convertir a CSV primero o usar lectura simple con ZipArchive
        // Por simplicidad, mostrar error pidiendo CSV
        unlink($rutaTemporal);
        header("Location: productos.php?error=usar_csv");
        exit();
    }
    
    // Obtener el próximo número de producto
    $sqlCount = "SELECT COUNT(*) as total FROM " . TBL_PRODUCTOS;
    $resultCount = $conexion->query($sqlCount);
    $countInicial = $resultCount->fetch_assoc()['total'];
        
    $productosCreados = 0;
    $errores = [];
    
    // DEBUG: Guardar contenido para debug
    file_put_contents('uploads_temp/debug.txt', print_r($filas, true));
    
    // Quitar encabezados (primera fila)
    unset($filas[0]);
    
    // Procesar cada fila
    foreach ($filas as $indice => $fila) {
        $row = $indice + 1; // Para mostrar número de fila correcto en errores
        
        // DEBUG: Ver qué contiene cada fila
        file_put_contents('uploads_temp/debug.txt', "Procesando fila $row: " . print_r($fila, true) . "\n", FILE_APPEND);
        
        // Evitar filas vacías
        if (empty($fila[0])) {
            file_put_contents('uploads_temp/debug.txt', "Fila $row: vacía, saltando\n", FILE_APPEND);
            continue;
        }
        
        // Leer valores de las columnas (índice 0-5)
        $nombreProducto = trim($fila[0]);
        $costoProducto = isset($fila[1]) ? $fila[1] : 0;
        $valorSinIva = isset($fila[2]) ? $fila[2] : 0;
        $valorConIva = isset($fila[3]) ? $fila[3] : 0;
        $inventario = isset($fila[4]) ? $fila[4] : 0;
        $minimoInventario = isset($fila[5]) ? $fila[5] : 2;
        
        // Limpiar y convertir valores
        $costoProducto = floatval(str_replace(',', '', $costoProducto));
        $valorSinIva = floatval(str_replace(',', '', $valorSinIva));
        $valorConIva = floatval(str_replace(',', '', $valorConIva));
        $inventario = intval($inventario);
        $minimoInventario = !empty($minimoInventario) ? intval($minimoInventario) : 2;
        
        file_put_contents('uploads_temp/debug.txt', "Valores convertidos - Nombre: $nombreProducto, Costo: $costoProducto, SinIVA: $valorSinIva, ConIVA: $valorConIva, Inv: $inventario, Min: $minimoInventario\n", FILE_APPEND);
        
        // Validaciones básicas
        if ($valorSinIva <= 0 || $valorConIva <= 0) {
            $errores[] = "Fila $row: Valores de precio inválidos";
            file_put_contents('uploads_temp/debug.txt', "Fila $row: ERROR - Precios inválidos\n", FILE_APPEND);
            continue;
        }
        
        if ($inventario < 0) {
            $errores[] = "Fila $row: Inventario no puede ser negativo";
            file_put_contents('uploads_temp/debug.txt', "Fila $row: ERROR - Inventario negativo\n", FILE_APPEND);
            continue;
        }
        
        if ($minimoInventario < 1) {
            $minimoInventario = 2;
        }
        
        // Generar ID automático
        $siguiente_numero = $countInicial + $productosCreados + 1;
        $id_producto = "PR-" . $siguiente_numero;

        // Insertar producto usando prepared statement
        $stmt = $conexion->prepare("INSERT INTO " . TBL_PRODUCTOS . " 
                (id_producto, nombre_producto, costo_producto, valor_sin_iva, valor_con_iva, inventario, minimo_inventario, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'activo')");
        
        $stmt->bind_param("ssdddii", $id_producto, $nombreProducto, $costoProducto, $valorSinIva, $valorConIva, $inventario, $minimoInventario);
        
        file_put_contents('uploads_temp/debug.txt', "SQL con nombre: $nombreProducto\n", FILE_APPEND);
        
        if ($stmt->execute()) {
            $productosCreados++;
            file_put_contents('uploads_temp/debug.txt', "Fila $row: ÉXITO - Producto creado\n", FILE_APPEND);
        } else {
            $errores[] = "Fila $row: Error al crear producto - " . $stmt->error;
            file_put_contents('uploads_temp/debug.txt', "Fila $row: ERROR SQL - " . $stmt->error . "\n", FILE_APPEND);
        }
        
        $stmt->close();
    }
    
    // Eliminar archivo temporal
    unlink($rutaTemporal);
    
    // Redirigir con resultado
    if ($productosCreados > 0) {
        header("Location: productos.php?exito=carga_masiva&total=$productosCreados");
    } else {
        header("Location: productos.php?error=sin_productos");
    }
    
} catch (Exception $e) {
    // Eliminar archivo temporal si existe
    if (isset($rutaTemporal) && file_exists($rutaTemporal)) {
        unlink($rutaTemporal);
    }
    
    header("Location: productos.php?error=excepcion&msg=" . urlencode($e->getMessage()));
}
?>
