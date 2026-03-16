<?php
/**
 * Script para descargar plantilla CSV de materias primas
 */

// Incluir autenticación
include '../includes/auth.php';

// Verificar permisos (super-admin o usuario con app 'productos')
$tienePermiso = false;

if (isset($_SESSION['rol_master']) && $_SESSION['rol_master'] === 'super-admin') {
    $tienePermiso = true;
} elseif (isset($_SESSION['rol']) && tienePermiso('productos')) {
    $tienePermiso = true;
}

if (!$tienePermiso) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No tienes permisos para descargar la plantilla']);
    exit();
}

// Generar contenido CSV
$csv = "nombre,unidad_medida,cantidad_base_comprada,costo_total_base\n";
$csv .= "Carne de Res Molida,kg,10.5,250.50\n";
$csv .= "Pechuga de Pollo,kg,5.0,75.00\n";
$csv .= "Tomate Fresco,kg,2.0,8.50\n";
$csv .= "Cebolla Blanca,kg,3.0,9.00\n";
$csv .= "Papa Blanca,kg,15.0,22.50\n";
$csv .= "Aceite de Oliva,l,5.0,85.00\n";
$csv .= "Leche Entera,l,10.0,45.00\n";
$csv .= "Queso Mozzarella,kg,2.5,125.00\n";
$csv .= "Harina de Trigo,kg,5.0,18.00\n";
$csv .= "Sal Común,kg,1.0,2.50\n";
$csv .= "Huevos Frescos,und,30.0,12.00\n";
$csv .= "Pimiento Rojo,kg,1.5,10.50\n";
$csv .= "Lechuga Fresca,kg,2.0,5.00\n";
$csv .= "Zanahoria,kg,4.0,6.00\n";
$csv .= "Azúcar Blanca,kg,5.0,8.50\n";

// Configurar headers para descarga
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="plantilla_materias_primas_' . date('Ymd_Hi') . '.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Enviar BOM UTF-8 (para que Excel reconozca caracteres especiales como ñ)
echo "\xEF\xBB\xBF" . $csv;
exit();
?>
