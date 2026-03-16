<?php
/**
 * Script de verificación: Asignación de Aplicaciones por Tipo de Negocio
 * Verifica que los demos creados tengan las aplicaciones correctas asignadas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/conexion_master.php';

header('Content-Type: text/html; charset=utf-8');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación - Asignación de Aplicaciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }
        
        h1 {
            color: #333;
            border-bottom: 3px solid #27ae60;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        h2 {
            color: #555;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 1.3rem;
        }
        
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 8px;
            border-left: 4px solid #27ae60;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        table th {
            background: #27ae60;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
        }
        
        table tr:hover {
            background: #f0f0f0;
        }
        
        .status-ok {
            background: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
        }
        
        .status-warning {
            background: #fff3cd;
            color: #856404;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-right: 4px;
            margin-bottom: 4px;
        }
        
        .badge-app {
            background: #e7f3ff;
            color: #0066cc;
        }
        
        .tipo {
            font-weight: 600;
            color: #27ae60;
        }
        
        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .info-box strong {
            color: #2c3e50;
        }
        
        .no-data {
            text-align: center;
            color: #999;
            padding: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-check-circle"></i> Verificación: Asignación de Aplicaciones por Tipo de Negocio</h1>
        
        <?php
        
        // ===== 1. VERIFICAR APLICACIONES DISPONIBLES =====
        echo '<div class="section">';
        echo '<h2><i class="fas fa-th"></i> Aplicaciones Disponibles en el Sistema</h2>';
        
        $sqlApps = "SELECT id, clave, nombre, estado FROM aplicaciones ORDER BY id";
        $resultApps = $conexion_master->query($sqlApps);
        
        if ($resultApps && $resultApps->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Clave</th><th>Nombre</th><th>Estado</th></tr>';
            
            while ($row = $resultApps->fetch_assoc()) {
                $estado_class = $row['estado'] === 'activo' ? 'status-ok' : 'status-error';
                $estado_text = $row['estado'];
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td><code>{$row['clave']}</code></td>";
                echo "<td>{$row['nombre']}</td>";
                echo "<td><span class='$estado_class'>$estado_text</span></td>";
                echo "</tr>";
            }
            
            echo '</table>';
        } else {
            echo '<div class="no-data">No hay aplicaciones registradas</div>';
        }
        
        echo '</div>';
        
        // ===== 2. MAPEO DE APLICACIONES POR TIPO =====
        echo '<div class="section">';
        echo '<h2><i class="fas fa-map"></i> Mapeo de Aplicaciones por Tipo de Negocio</h2>';
        
        $mapeo = [
            'Restaurante' => ['mesas', 'cocina', 'productos', 'reportes', 'menu_digital', 'usuarios'],
            'Bar' => ['mesas', 'cocina', 'productos', 'reportes', 'menu_digital', 'usuarios'],
            'Restaurante-Bar' => ['mesas', 'cocina', 'productos', 'reportes', 'menu_digital', 'usuarios'],
            'Minimarket' => ['comandas', 'productos', 'menu_digital', 'usuarios'],
        ];
        
        echo '<table>';
        echo '<tr><th>Tipo de Negocio</th><th>Aplicaciones Asignadas</th></tr>';
        
        foreach ($mapeo as $tipo => $apps) {
            $apps_html = '';
            foreach ($apps as $app) {
                $apps_html .= '<span class="badge badge-app">' . htmlspecialchars($app) . '</span>';
            }
            echo "<tr><td><span class='tipo'>$tipo</span></td><td>$apps_html</td></tr>";
        }
        
        echo '</table>';
        
        echo '</div>';
        
        // ===== 3. VERIFICAR DEMOS CREADOS RECIENTEMENTE =====
        echo '<div class="section">';
        echo '<h2><i class="fas fa-newspaper"></i> Demostraciones Creadas (Últimos 7 días)</h2>';
        
        $sqlDemos = "
            SELECT 
                r.id,
                r.nombre,
                r.identificador,
                r.plan,
                r.fecha_creacion,
                DATEDIFF(r.fecha_expiracion, CURDATE()) as dias_restantes,
                r.estado
            FROM restaurantes r
            WHERE r.plan = 'demo'
            AND r.fecha_creacion >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY r.fecha_creacion DESC
        ";
        
        $resultDemos = $conexion_master->query($sqlDemos);
        
        if ($resultDemos && $resultDemos->num_rows > 0) {
            echo '<table>';
            echo '<tr><th>ID</th><th>Nombre</th><th>Identificador</th><th>Estado</th><th>Creación</th><th>Días Restantes</th><th>Aplicaciones</th></tr>';
            
            while ($demo = $resultDemos->fetch_assoc()) {
                // Obtener aplicaciones del demo
                $sqlAppsDemo = "
                    SELECT a.clave, a.nombre 
                    FROM restaurante_aplicaciones ra
                    INNER JOIN aplicaciones a ON ra.id_aplicacion = a.id
                    WHERE ra.id_restaurante = ?
                    ORDER BY a.orden
                ";
                
                $stmtAppsDemo = $conexion_master->prepare($sqlAppsDemo);
                $stmtAppsDemo->bind_param('i', $demo['id']);
                $stmtAppsDemo->execute();
                $resultAppsDemo = $stmtAppsDemo->get_result();
                
                $apps_asignadas = [];
                while ($app = $resultAppsDemo->fetch_assoc()) {
                    $apps_asignadas[] = $app['clave'];
                }
                
                $apps_html = '';
                foreach ($apps_asignadas as $app) {
                    $apps_html .= '<span class="badge badge-app">' . htmlspecialchars($app) . '</span>';
                }
                
                $estado_class = $demo['estado'] === 'activo' ? 'status-ok' : 'status-warning';
                $creacion = date('d/m/Y H:i', strtotime($demo['fecha_creacion']));
                
                echo "<tr>";
                echo "<td>{$demo['id']}</td>";
                echo "<td>{$demo['nombre']}</td>";
                echo "<td><code>{$demo['identificador']}</code></td>";
                echo "<td><span class='$estado_class'>{$demo['estado']}</span></td>";
                echo "<td>$creacion</td>";
                echo "<td>{$demo['dias_restantes']} días</td>";
                echo "<td>$apps_html</td>";
                echo "</tr>";
                
                $stmtAppsDemo->close();
            }
            
            echo '</table>';
        } else {
            echo '<div class="no-data">No hay demostraciones creadas en los últimos 7 días</div>';
        }
        
        echo '</div>';
        
        // ===== 4. ESTADÍSTICAS =====
        echo '<div class="section">';
        echo '<h2><i class="fas fa-chart-bar"></i> Estadísticas</h2>';
        
        $sqlStats = "
            SELECT 
                COUNT(DISTINCT r.id) as total_demos,
                COUNT(DISTINCT ra.id) as total_asignaciones,
                COUNT(DISTINCT a.id) as aplicaciones_diferentes
            FROM restaurantes r
            LEFT JOIN restaurante_aplicaciones ra ON r.id = ra.id_restaurante
            LEFT JOIN aplicaciones a ON ra.id_aplicacion = a.id
            WHERE r.plan = 'demo'
        ";
        
        $resultStats = $conexion_master->query($sqlStats);
        $stats = $resultStats->fetch_assoc();
        
        echo '<div class="info-box">';
        echo '<strong>Total de Demostraciones:</strong> ' . $stats['total_demos'] . '<br>';
        echo '<strong>Total de Asignaciones:</strong> ' . $stats['total_asignaciones'] . '<br>';
        echo '<strong>Aplicaciones Diferentes:</strong> ' . $stats['aplicaciones_diferentes'];
        echo '</div>';
        
        // Aplicaciones por demo
        if ($stats['total_demos'] > 0) {
            $promedio = round($stats['total_asignaciones'] / $stats['total_demos'], 2);
            echo '<div class="info-box">';
            echo '<strong>Promedio de Aplicaciones por Demo:</strong> ' . $promedio;
            echo '</div>';
        }
        
        echo '</div>';
        
        // ===== 5. VERIFICACIÓN DE INTEGRIDAD =====
        echo '<div class="section">';
        echo '<h2><i class="fas fa-shield-alt"></i> Verificación de Integridad</h2>';
        
        // Verificar que todo demo tenga al menos 1 aplicación
        $sqlIntegridad = "
            SELECT 
                r.id,
                r.nombre,
                r.plan,
                COUNT(ra.id) as num_apps
            FROM restaurantes r
            LEFT JOIN restaurante_aplicaciones ra ON r.id = ra.id_restaurante
            WHERE r.plan = 'demo'
            GROUP BY r.id
            HAVING num_apps = 0
        ";
        
        $resultIntegridad = $conexion_master->query($sqlIntegridad);
        $demos_sin_apps = $resultIntegridad->num_rows;
        
        if ($demos_sin_apps === 0) {
            echo '<div class="info-box" style="background: #d4edda; border-color: #28a745; color: #155724;">';
            echo '<i class="fas fa-check-circle"></i> <strong>✓ OK:</strong> Todos los demos tienen aplicaciones asignadas';
            echo '</div>';
        } else {
            echo '<div class="info-box" style="background: #f8d7da; border-color: #dc3545; color: #721c24;">';
            echo '<i class="fas fa-exclamation-circle"></i> <strong>⚠ ADVERTENCIA:</strong> Hay ' . $demos_sin_apps . ' demo(s) sin aplicaciones asignadas';
            echo '</div>';
            
            if ($resultIntegridad->num_rows > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Nombre</th><th>Aplicaciones</th></tr>';
                echo '<tr>';
                $row = $resultIntegridad->fetch_assoc();
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['nombre'] . '</td>';
                echo '<td><span class="status-error">0 aplicaciones</span></td>';
                echo '</tr>';
                echo '</table>';
            }
        }
        
        echo '</div>';
        
        // Cerrar conexión
        $conexion_master->close();
        
        ?>
        
        <div style="margin-top: 30px; padding: 20px; background: #e8f4f8; border-radius: 8px; text-align: center;">
            <p style="color: #2c3e50; margin: 0;">
                <i class="fas fa-info-circle"></i> Esta página verifica que la asignación automática de aplicaciones funcione correctamente.
            </p>
            <p style="color: #2c3e50; margin-top: 10px; font-size: 0.9rem;">
                Última actualización: <strong><?php echo date('d/m/Y H:i:s'); ?></strong>
            </p>
        </div>
    </div>
</body>
</html>
