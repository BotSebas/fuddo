<?php
/**
 * Script de migración: Agregar plan='Premium' y fecha_expiracion a restaurantes existentes
 * Ejecutar SOLO UNA VEZ después de actualizar la base de datos
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/conexion_master.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== MIGRACIÓN DE PLAN Y FECHA DE EXPIRACIÓN ===\n\n";

try {
    // 1. Actualizar restaurantes que NO sean 'demo' y no tengan fecha_expiracion
    echo "1. Actualizando restaurantes existentes (Plan Premium + 30 días)...\n";
    
    $sql = "UPDATE restaurantes 
            SET plan = 'Premium',
                fecha_expiracion = DATE_ADD(fecha_creacion, INTERVAL 30 DAY)
            WHERE (plan IS NULL OR plan NOT IN ('demo', 'Premium'))
            AND (fecha_expiracion IS NULL OR fecha_expiracion = '0000-00-00')";
    
    if ($conexion_master->query($sql)) {
        $affected = $conexion_master->affected_rows;
        echo "✓ Se actualizaron $affected restaurantes\n\n";
    } else {
        throw new Exception("Error actualizando restaurantes: " . $conexion_master->error);
    }

    // 2. Verificar resultados
    echo "2. Verificando resultados...\n";
    
    $result = $conexion_master->query("
        SELECT id, nombre, plan, fecha_creacion, fecha_expiracion, estado
        FROM restaurantes
        ORDER BY id DESC
        LIMIT 10
    ");
    
    if ($result) {
        echo "\nÚltimos 10 restaurantes:\n";
        echo str_repeat("-", 100) . "\n";
        printf("%-5s %-40s %-12s %-15s %-15s %-12s\n", "ID", "Nombre", "Plan", "Creación", "Exp.", "Estado");
        echo str_repeat("-", 100) . "\n";
        
        while ($row = $result->fetch_assoc()) {
            printf("%-5d %-40s %-12s %-15s %-15s %-12s\n", 
                $row['id'],
                mb_substr($row['nombre'], 0, 38),
                $row['plan'] ?? 'NULL',
                substr($row['fecha_creacion'], 0, 10),
                substr($row['fecha_expiracion'] ?? '', 0, 10),
                $row['estado']
            );
        }
        echo str_repeat("-", 100) . "\n";
    }

    // 3. Estadísticas finales
    echo "\n3. Estadísticas finales:\n";
    
    $stats = $conexion_master->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN plan = 'demo' THEN 1 ELSE 0 END) as demos,
            SUM(CASE WHEN plan = 'Premium' THEN 1 ELSE 0 END) as premium,
            SUM(CASE WHEN fecha_expiracion IS NULL THEN 1 ELSE 0 END) as sin_expiracion
        FROM restaurantes
    ");
    
    if ($stats) {
        $data = $stats->fetch_assoc();
        echo "Total de restaurantes: " . $data['total'] . "\n";
        echo "  - Demos: " . $data['demos'] . "\n";
        echo "  - Premium: " . $data['premium'] . "\n";
        echo "  - Sin fecha de expiración: " . $data['sin_expiracion'] . "\n\n";
    }

    echo "✓ Migración completada exitosamente\n";

} catch (Exception $e) {
    echo "✗ Error durante la migración: " . $e->getMessage() . "\n";
}

$conexion_master->close();
?>
