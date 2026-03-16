<?php
/**
 * Script de Prueba - Verificar configuración de FUDDO
 * 
 * Usar: http://localhost/fuddo/setup/test_setup.php
 */

$tests = [
    'success' => [],
    'warnings' => [],
    'errors' => []
];

// Test 1: Conexión a BD Master
try {
    require_once __DIR__ . '/../includes/conexion_master.php';
    
    if ($conexion_master && !$conexion_master->connect_error) {
        $tests['success'][] = '✓ Conexión a base de datos master exitosa';
    } else {
        $tests['errors'][] = '✗ Error conectando a base de datos master';
    }
} catch (Exception $e) {
    $tests['errors'][] = '✗ Excepción en conexión: ' . $e->getMessage();
}

// Test 2: Tablas de usuarios
try {
    if (isset($conexion_master) && !$conexion_master->connect_error) {
        $result = $conexion_master->query("SHOW TABLES LIKE 'usuarios_master'");
        if ($result && $result->num_rows > 0) {
            $tests['success'][] = '✓ Tabla usuarios_master existe';
        } else {
            $tests['warnings'][] = '⚠ Tabla usuarios_master no existe - ejecuta: setup/init_database.php';
        }
    }
} catch (Exception $e) {
    $tests['errors'][] = '✗ Error verificando tablas: ' . $e->getMessage();
}

// Test 3: PHPMailer
$phpmailerPath = __DIR__ . '/../vendor/autoload.php';
if (file_exists($phpmailerPath)) {
    require_once $phpmailerPath;
    try {
        new PHPMailer\PHPMailer\PHPMailer();
        $tests['success'][] = '✓ PHPMailer instalado correctamente';
    } catch (Exception $e) {
        $tests['errors'][] = '✗ Error con PHPMailer: ' . $e->getMessage();
    }
} else {
    $tests['warnings'][] = '⚠ PHPMailer no instalado - ejecuta: composer install';
}

// Test 4: Configuración de Email
@include_once __DIR__ . '/../config/mail_config.php';
if (defined('MAIL_USERNAME') && MAIL_USERNAME !== 'tu_email@gmail.com') {
    $tests['success'][] = '✓ Configuración de email detectada';
} else {
    $tests['warnings'][] = '⚠ Email no configurado - edita: config/mail_config.php';
}

// Test 5: Archivos PNG y CSS
$files = [
    '../assets/img/logo-fuddohorizontal.png' => 'Logo FUDDO',
    '../assets/img/landing-img1.jpg' => 'Imagen de landing',
    '../registro.php' => 'Formulario de registro',
    '../procesar_registro.php' => 'Procesador de registro',
    '../login.php' => 'Página de login'
];

foreach ($files as $path => $name) {
    $fullPath = __DIR__ . '/' . $path;
    if (file_exists($fullPath)) {
        $tests['success'][] = "✓ $name existe";
    } else {
        $tests['warnings'][] = "⚠ $name no encontrado";
    }
}

// Test 6: Permisos de carpetas
$folders = [
    '../vendor' => 'vendor',
    '../config' => 'config',
    '../sql' => 'sql'
];

foreach ($folders as $path => $name) {
    $fullPath = __DIR__ . '/' . $path;
    if (is_dir($fullPath) && is_writable($fullPath)) {
        $tests['success'][] = "✓ Carpeta $name es escribible";
    } else if (is_dir($fullPath)) {
        $tests['warnings'][] = "⚠ Carpeta $name existe pero no es escribible";
    } else {
        $tests['warnings'][] = "⚠ Carpeta $name no existe";
    }
}

// Cerrar conexión
if (isset($conexion_master)) {
    $conexion_master->close();
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Configuración - FUDDO</title>
    <style>
        body {
            font-family: 'Inter', Arial, sans-serif;
            background: linear-gradient(135deg, #27ae60, #1e8449);
            min-height: 100vh;
            padding: 20px;
            margin: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        h1 {
            color: #27ae60;
            text-align: center;
            margin-top: 0;
        }
        
        .section {
            margin: 30px 0;
        }
        
        h2 {
            color: #333;
            border-bottom: 2px solid #27ae60;
            padding-bottom: 10px;
            font-size: 1.1em;
        }
        
        .item {
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            font-size: 0.95em;
        }
        
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .summary {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .summary strong {
            font-size: 1.1em;
        }
        
        .button-group {
            text-align: center;
            margin-top: 30px;
        }
        
        button, a {
            display: inline-block;
            padding: 12px 30px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin: 0 10px;
        }
        
        button:hover, a:hover {
            background: #1e8449;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            margin: 20px 0;
        }
        
        .status-ok {
            background: #d4edda;
            color: #155724;
        }
        
        .status-alert {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-error {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificación de Configuración - FUDDO</h1>
        
        <?php
        $hasErrors = !empty($tests['errors']);
        $hasWarnings = !empty($tests['warnings']);
        $statusClass = $hasErrors ? 'status-error' : ($hasWarnings ? 'status-alert' : 'status-ok');
        $statusText = $hasErrors ? 'Hay errores' : ($hasWarnings ? 'Hay advertencias' : 'Todo está bien');
        ?>
        
        <div class="status-badge <?php echo $statusClass; ?>">
            <?php echo $statusText; ?>
        </div>
        
        <div class="summary">
            <strong>✓ Aprobadas:</strong> <?php echo count($tests['success']); ?> |
            <strong>⚠ Advertencias:</strong> <?php echo count($tests['warnings']); ?> |
            <strong>✗ Errores:</strong> <?php echo count($tests['errors']); ?>
        </div>
        
        <?php if (!empty($tests['success'])): ?>
            <div class="section">
                <h2>✓ Verificaciones Exitosas</h2>
                <?php foreach ($tests['success'] as $msg): ?>
                    <div class="item success"><?php echo htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($tests['warnings'])): ?>
            <div class="section">
                <h2>⚠ Advertencias</h2>
                <?php foreach ($tests['warnings'] as $msg): ?>
                    <div class="item warning"><?php echo htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($tests['errors'])): ?>
            <div class="section">
                <h2>✗ Errores</h2>
                <?php foreach ($tests['errors'] as $msg): ?>
                    <div class="item error"><?php echo htmlspecialchars($msg); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="button-group">
            <a href="init_database.php">Inicializar BD</a>
            <a href="../registro.php">Ir a Registro</a>
            <a href="../index.php">Volver a FUDDO</a>
        </div>
        
        <hr style="margin-top: 40px; border: none; border-top: 1px solid #eee;">
        
        <h2>📋 Checklist de Configuración</h2>
        <ol>
            <li><strong>✓ Instalar Dependencias:</strong> 
                <?php
                if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
                    echo '<span style="color: #27ae60;">✓ Hecho</span>';
                } else {
                    echo '<span style="color: #ff9800;">→ Ejecutar: composer install</span>';
                }
                ?>
            </li>
            <li><strong>✓ Crear Tablas:</strong> 
                <?php
                $dbExists = false;
                if (isset($conexion_master) && !$conexion_master->connect_error) {
                    $result = $conexion_master->query("SELECT 1 FROM usuarios_master LIMIT 1");
                    $dbExists = $result !== false;
                }
                if ($dbExists) {
                    echo '<span style="color: #27ae60;">✓ Hecho</span>';
                } else {
                    echo '<span style="color: #ff9800;">→ <a href="init_database.php">Ejecutar Setup</a></span>';
                }
                ?>
            </li>
            <li><strong>✓ Configurar Email:</strong> 
                <?php
                if (defined('MAIL_USERNAME') && MAIL_USERNAME !== 'tu_email@gmail.com') {
                    echo '<span style="color: #27ae60;">✓ Hecho</span>';
                } else {
                    echo '<span style="color: #ff9800;">→ Editar config/mail_config.php</span>';
                }
                ?>
            </li>
            <li><strong>✓ Probar Registro:</strong> 
                <a href="../registro.php" style="display: inline; color: #27ae60; text-decoration: none;">→ Ir a Registro</a>
            </li>
        </ol>
    </div>
</body>
</html>
