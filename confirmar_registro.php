<?php
/**
 * Confirmar y activar registro en demo
 * Archivo: confirmar_registro.php
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activar Cuenta - FUDDO</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/icons/logo-fuddo.ico">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14px;
            opacity: 0.9;
        }

        .content {
            padding: 40px;
            text-align: center;
        }

        .icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .message {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            margin: 20px 0;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }

        .details {
            background: #f0f8f5;
            border-left: 4px solid #27ae60;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            text-align: left;
            font-size: 14px;
        }

        .details strong {
            color: #1e8449;
        }

        .button {
            display: inline-block;
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            color: white;
            padding: 12px 40px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 20px;
            transition: transform 0.3s;
        }

        .button:hover {
            transform: translateY(-2px);
        }

        .footer {
            background: #f9f9f9;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            color: #999;
            font-size: 12px;
        }

        .footer a {
            color: #27ae60;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>FUDDO</h1>
            <p>Activación de Cuenta</p>
        </div>

        <div class="content">
            <?php
            require_once 'includes/conexion_master.php';

            $token = isset($_GET['token']) ? trim($_GET['token']) : '';
            $email = isset($_GET['email']) ? trim($_GET['email']) : '';

            if (empty($token) || empty($email)) {
                ?>
                <div class="icon">❌</div>
                <div class="error">
                    <strong>Error de Activación</strong><br>
                    El enlace de activación es inválido o está incompleto.
                </div>
                <p class="message">Por favor, verifica que hayas hecho clic en el enlace correcto del correo electrónico.</p>
                <a href="login.php" class="button">Ir al Login</a>
                <?php
            } else {
                try {
                    // Validar email
                    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception('Email inválido');
                    }

                    // Buscar usuario por email y token
                    $sql = "SELECT id, nombre, usuario, estado FROM usuarios_master WHERE email = ? AND token_activacion = ? LIMIT 1";
                    $stmt = $conexion_master->prepare($sql);
                    
                    if (!$stmt) {
                        throw new Exception('Error en la base de datos');
                    }

                    $stmt->bind_param('ss', $email, $token);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows === 0) {
                        throw new Exception('Token o email no encontrado');
                    }

                    $usuario = $result->fetch_assoc();
                    $stmt->close();

                    // Si ya está activo
                    if ($usuario['estado'] === 'activo') {
                        ?>
                        <div class="icon">✅</div>
                        <div class="success">
                            <strong>Cuenta Activada</strong><br>
                            Tu cuenta ya estaba activada.
                        </div>
                        <p class="message">
                            <strong>Usuario (Correo):</strong> <?php echo htmlspecialchars($usuario['usuario']); ?><br>
                            <strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?>
                        </p>
                        <a href="login.php" class="button">Ir al Login</a>
                        <?php
                    } else {
                        // Activar cuenta
                        $sqlUpdate = "UPDATE usuarios_master SET estado = 'activo', token_activacion = NULL WHERE id = ?";
                        $stmtUpdate = $conexion_master->prepare($sqlUpdate);
                        
                        if (!$stmtUpdate) {
                            throw new Exception('Error al actualizar la base de datos');
                        }

                        $stmtUpdate->bind_param('i', $usuario['id']);
                        
                        if (!$stmtUpdate->execute()) {
                            throw new Exception('Error al activar la cuenta');
                        }

                        $stmtUpdate->close();

                        // También activar el restaurante asociado
                        $sqlActivateRest = "UPDATE restaurantes SET estado = 'activo' WHERE id IN (SELECT id_restaurante FROM usuarios_master WHERE id = ?)";
                        $stmtActivateRest = $conexion_master->prepare($sqlActivateRest);
                        if ($stmtActivateRest) {
                            $stmtActivateRest->bind_param('i', $usuario['id']);
                            $stmtActivateRest->execute();
                            $stmtActivateRest->close();
                        }

                        error_log("[ACTIVACION EXITOSA] Email: $email | Usuario: " . $usuario['usuario']);
                        ?>
                        <div class="icon">🎉</div>
                        <div class="success">
                            <strong>¡Cuenta Activada Exitosamente!</strong><br>
                            Tu cuenta está lista para usar.
                        </div>
                        <p class="message">¡Gracias por unirte a FUDDO! Tu prueba gratuita de 7 días ya está a tu disposición.</p>
                        
                        <div class="details">
                            <strong>Tu información:</strong><br>
                            📧 Usuario (Correo): <?php echo htmlspecialchars($usuario['usuario']); ?><br>
                            👤 Nombre: <?php echo htmlspecialchars($usuario['nombre']); ?><br>
                            💡 Usa tu correo completo para iniciar sesión
                        </div>

                        <p class="message">Ahora puedes iniciar sesión con tus credenciales.</p>
                        <a href="login.php" class="button">Iniciar Sesión</a>
                        <?php
                    }

                } catch (Exception $e) {
                    error_log("[ACTIVACION ERROR] " . $e->getMessage());
                    ?>
                    <div class="icon">⚠️</div>
                    <div class="error">
                        <strong>Error en la Activación</strong><br>
                        <?php echo htmlspecialchars($e->getMessage()); ?>
                    </div>
                    <p class="message">Si el problema persiste, por favor contacta a soporte.</p>
                    <a href="login.php" class="button">Volver al Login</a>
                    <?php
                }
            }

            if (isset($conexion_master)) {
                @$conexion_master->close();
            }
            ?>
        </div>

        <div class="footer">
            <p style="margin: 0;">¿Necesitas ayuda? <a href="mailto:fuddocol@gmail.com">Contacta a Soporte</a></p>
            <p style="margin: 10px 0 0 0;">© 2026 FUDDO - Sistema Profesional de Gestión de Restaurantes</p>
        </div>
    </div>
</body>
</html>
