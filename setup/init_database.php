<?php
/**
 * Script de Inicialización - FUDDO
 * 
 * Ejecuta este script una vez para crear las tablas necesarias
 * en la base de datos usuarios_master
 * 
 * Usar: http://localhost/fuddo/setup/init_database.php
 */

// Evitar acceso directo - solo si está en setup o local
$allowSetup = true;

// Obtener conexión a base de datos master
require_once __DIR__ . '/../includes/conexion_master.php';

if (!$conexion_master) {
    die(json_encode([
        'success' => false,
        'message' => 'Error: No se puede conectar a la base de datos master'
    ]));
}

// Leer archivo SQL
$sqlFile = __DIR__ . '/../sql/crear_usuarios_master.sql';

if (!file_exists($sqlFile)) {
    die(json_encode([
        'success' => false,
        'message' => 'Error: No se encuentra el archivo de esquema SQL'
    ]));
}

$sqlContent = file_get_contents($sqlFile);

// Dividir en queries individuales
$queries = explode(';', $sqlContent);

$resultados = [];
$errores = [];

foreach ($queries as $query) {
    $query = trim($query);
    
    // Saltar comentarios y queries vacías
    if (empty($query) || strpos($query, '--') === 0) {
        continue;
    }
    
    // Ejecutar query
    if (!$conexion_master->query($query)) {
        $errores[] = [
            'query' => substr($query, 0, 50) . '...',
            'error' => $conexion_master->error
        ];
    } else {
        $resultados[] = substr($query, 0, 50) . '... ✓';
    }
}

$conexion_master->close();

// Respuesta
$response = [
    'success' => empty($errores),
    'tablas_creadas' => count($resultados),
    'errores_count' => count($errores),
    'detalles' => [
        'creadas' => $resultados,
        'errores' => $errores
    ],
    'mensaje' => empty($errores) 
        ? 'Base de datos inicializada correctamente' 
        : 'Se completó con algunos errores (posiblemente las tablas ya existen)'
];

// Si es AJAX, responder JSON
if (!empty($_GET['json'])) {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Si no, mostrar HTML
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicialización de Base de Datos - FUDDO</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, #27ae60, #1e8449);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            margin: 0;
        }
        
        .container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        h1 {
            color: #27ae60;
            margin-top: 0;
            text-align: center;
        }
        
        .status {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 600;
        }
        
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .status.warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            max-height: 300px;
            overflow-y: auto;
        }
        
        .details ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .details li {
            margin: 8px 0;
            font-size: 0.9em;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        
        .details li.error {
            color: #e74c3c;
        }
        
        .details li.success {
            color: #27ae60;
        }
        
        .button-group {
            text-align: center;
            margin-top: 30px;
        }
        
        a {
            display: inline-block;
            padding: 12px 30px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s;
            margin: 0 10px;
        }
        
        a:hover {
            background: #1e8449;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Inicialización de FUDDO</h1>
        
        <div class="status <?php echo $response['success'] ? 'success' : 'warning'; ?>">
            <?php echo htmlspecialchars($response['mensaje']); ?>
        </div>
        
        <div>
            <strong>📊 Tablas creadas:</strong> <?php echo $response['tablas_creadas']; ?>/13
            <br>
            <strong>⚠️ Errores:</strong> <?php echo $response['errores_count']; ?>
        </div>
        
        <?php if (!empty($response['detalles']['creadas'])): ?>
            <h3>✓ Tablas Creadas:</h3>
            <div class="details">
                <ul>
                    <?php foreach ($response['detalles']['creadas'] as $item): ?>
                        <li class="success"><?php echo htmlspecialchars($item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($response['detalles']['errores'])): ?>
            <h3>⚠️ Errores (Normalmente significa que las tablas ya existen):</h3>
            <div class="details">
                <ul>
                    <?php foreach ($response['detalles']['errores'] as $error): ?>
                        <li class="error">
                            <strong>Query:</strong> <?php echo htmlspecialchars($error['query']); ?>
                            <br>
                            <strong>Error:</strong> <?php echo htmlspecialchars($error['error']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <div class="button-group">
            <a href="../index.php">← Volver a FUDDO</a>
            <a href="../registro.php">Ir al Registro →</a>
        </div>
        
        <hr style="margin-top: 30px; border: none; border-top: 1px solid #eee;">
        
        <h3>📝 Próximos Pasos:</h3>
        <ol>
            <li>Configura el email en <code>config/mail_config.php</code></li>
            <li>Prueba el registro en <a href="../registro.php" style="display: inline;">registro.php</a></li>
            <li>Verifica los logs de error si hay problemas de email</li>
        </ol>
    </div>
</body>
</html>
<?php
?>
