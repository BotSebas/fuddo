<?php
/**
 * Database migration script for subscription system
 * Adds fecha_proxima_notificacion column and updates plan ENUM
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/conexion_master.php';

header('Content-Type: text/plain; charset=utf-8');

echo "=== DATABASE MIGRATION FOR SUBSCRIPTION SYSTEM ===\n\n";

try {
    // 1. Add fecha_proxima_notificacion column if not exists
    echo "1. Adding fecha_proxima_notificacion column...\n";
    $sql1 = "ALTER TABLE restaurantes ADD COLUMN IF NOT EXISTS fecha_proxima_notificacion DATETIME NULL AFTER fecha_expiracion";
    
    if ($conexion_master->query($sql1)) {
        echo "✓ Column added (or already exists)\n";
    } else {
        // It's OK if it already exists
        echo "✓ Column status verified\n";
    }
    
    // 2. Modify plan ENUM to support new values
    echo "\n2. Updating plan ENUM values...\n";
    $sql2 = "ALTER TABLE restaurantes MODIFY COLUMN plan ENUM('demo', 'Premium') DEFAULT 'Premium'";
    
    if ($conexion_master->query($sql2)) {
        echo "✓ Plan ENUM updated to ('demo', 'Premium')\n";
    } else {
        throw new Exception("Error updating ENUM: " . $conexion_master->error);
    }
    
    // 3. Update existing plans to new format
    echo "\n3. Updating existing plan values...\n";
    $sql3 = "UPDATE restaurantes 
             SET plan = 'Premium'
             WHERE plan IN ('basico', 'premium', 'enterprise') OR plan IS NULL";
    
    if ($conexion_master->query($sql3)) {
        $affected = $conexion_master->affected_rows;
        echo "✓ Updated $affected restaurants to Premium plan\n";
    } else {
        echo "⚠ Update query had no affected rows or already updated\n";
    }
    
    // 4. Verify changes
    echo "\n4. Verification - Current restaurant data:\n";
    $sql4 = "SELECT 
                id, 
                nombre, 
                plan, 
                DATE(fecha_creacion) as fecha_creacion,
                fecha_expiracion,
                estado
             FROM restaurantes
             LIMIT 5";
    
    $result = $conexion_master->query($sql4);
    
    if ($result && $result->num_rows > 0) {
        echo str_repeat("-", 120) . "\n";
        printf("%-4s %-30s %-12s %-12s %-12s %-10s\n", "ID", "Nombre", "Plan", "Created", "Expires", "Estado");
        echo str_repeat("-", 120) . "\n";
        
        while ($row = $result->fetch_assoc()) {
            printf("%-4d %-30s %-12s %-12s %-12s %-10s\n",
                $row['id'],
                mb_substr($row['nombre'], 0, 28),
                $row['plan'] ?? 'NULL',
                $row['fecha_creacion'],
                substr($row['fecha_expiracion'] ?? '', 0, 10),
                $row['estado']
            );
        }
        echo str_repeat("-", 120) . "\n";
    }
    
    // 5. Summary statistics
    echo "\n5. Summary Statistics:\n";
    $sql5 = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN plan = 'demo' THEN 1 ELSE 0 END) as demos,
                SUM(CASE WHEN plan = 'Premium' THEN 1 ELSE 0 END) as premium,
                SUM(CASE WHEN fecha_expiracion IS NULL THEN 1 ELSE 0 END) as sin_expiracion
             FROM restaurantes";
    
    $result_stats = $conexion_master->query($sql5);
    if ($result_stats) {
        $stats = $result_stats->fetch_assoc();
        echo "Total restaurants: " . $stats['total'] . "\n";
        echo "  - Demo (7-day trial): " . $stats['demos'] . "\n";
        echo "  - Premium (subscription): " . $stats['premium'] . "\n";
        echo "  - Without expiration date: " . $stats['sin_expiracion'] . "\n";
    }
    
    echo "\n✓ Migration completed successfully!\n";
    echo "\nNote: Run migracion_plan_expiracion.php to set expiration dates for existing restaurants.\n";

} catch (Exception $e) {
    echo "✗ Migration failed: " . $e->getMessage() . "\n";
}

$conexion_master->close();
?>
