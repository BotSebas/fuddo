<!DOCTYPE html>
<html lang="es">
<head>
<?php include_once 'lang/idiomas.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifica tu Correo - FUDDO</title>
    <meta name="description" content="Verifica tu correo para completar el registro">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/icons/logo-fuddo.ico">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #1a1a1a;
            line-height: 1.6;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
            min-height: 100vh;
        }

        /* Header Navigation */
        header {
            position: sticky;
            top: 0;
            right: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(225, 225, 225, 0.5);
            z-index: 1000;
            padding: 1rem 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 700;
            font-size: 1.5rem;
            color: #27ae60;
            text-decoration: none;
        }

        .logo img {
            height: 40px;
            width: auto;
        }

        .header-right {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .btn-login {
            background: transparent;
            color: #1a1a1a;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-login:hover {
            color: #27ae60;
        }

        /* Main Container */
        .container {
            max-width: 500px;
            margin: 0px auto 40px;
            padding: 2rem;
        }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: 16px;
            padding: 3rem 2.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid #f0f0f0;
        }

        .form-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .form-header h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            color: #1a1a1a;
            font-weight: 700;
        }

        .form-header p {
            font-size: 1rem;
            color: #666;
            margin-bottom: 1.5rem;
        }

        .trial-badge {
            display: inline-block;
            background: #e8f8f0;
            color: #27ae60;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        /* Icon */
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 30px auto;
            animation: pulse 2s infinite;
        }

        .icon-wrapper i {
            font-size: 40px;
            color: white;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        /* Info Box */
        .info-box {
            background: #f0f8f5;
            border-left: 4px solid #27ae60;
            padding: 1.2rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .info-box p {
            font-size: 0.9rem;
            color: #27ae60;
            margin: 0;
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .info-box i {
            flex-shrink: 0;
            margin-top: 0.2rem;
        }

        /* Email Highlight */
        .email-highlight {
            background: #f0f5ff;
            color: #27ae60;
            padding: 12px 16px;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            margin: 20px 0;
            word-break: break-all;
            border-left: 4px solid #27ae60;
            font-size: 0.95rem;
        }

        /* Steps */
        .steps {
            background: #fafafa;
            border-radius: 8px;
            padding: 30px;
            margin: 30px 0;
            text-align: left;
        }

        .step {
            display: flex;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .step:last-child {
            margin-bottom: 0;
        }

        .step-number {
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
            margin-right: 15px;
            font-size: 0.85rem;
        }

        .step-content h3 {
            color: #1a1a1a;
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .step-content p {
            color: #666;
            font-size: 0.85rem;
            margin: 0;
        }

        /* Buttons */
        .buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-direction: column;
        }

        .btn {
            padding: 1.1rem 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #27ae60;
            border: 2px solid #27ae60;
            box-shadow: none;
        }

        .btn-secondary:hover {
            background: #f0f8f5;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.2);
        }

        /* Help Text */
        .help-text {
            color: #999;
            font-size: 0.85rem;
            margin-top: 20px;
            text-align: center;
        }

        .help-text a {
            color: #27ae60;
            text-decoration: none;
            font-weight: 600;
        }

        .help-text a:hover {
            color: #1e8449;
            text-decoration: underline;
        }

        /* Spam Warning */
        .spam-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
            padding: 1rem;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 0.9rem;
            display: flex;
            gap: 0.75rem;
        }

        .spam-warning i {
            flex-shrink: 0;
            margin-top: 0.2rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                padding: 0.75rem 1rem;
            }

            .container {
                margin: 0px auto 20px;
                padding: 1rem;
            }

            .form-card {
                padding: 2rem 1.5rem;
            }

            .form-header h1 {
                font-size: 1.5rem;
            }

            .icon-wrapper {
                width: 70px;
                height: 70px;
            }

            .icon-wrapper i {
                font-size: 35px;
            }

            .steps {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <a href="index.php" class="logo">
                <img src="assets/img/logo-fuddohorizontal.png" alt="FUDDO" style="height: 40px; width: auto;">
            </a>
            <div class="header-right">
                <a href="index.php" style="color: #666; text-decoration: none; font-weight: 500;">Volver</a>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="container">
        <!-- Form Card -->
        <div class="form-card">
            <div class="form-header">
                <div class="trial-badge">
                    <i class="fas fa-envelope"></i> Verifica tu Correo
                </div>
                <h1>¡Casi Listo!</h1>
                <p>Hemos enviado un enlace de activación a tu bandeja de entrada</p>
            </div>

            <div class="icon-wrapper">
                <i class="fas fa-envelope"></i>
            </div>

            <div class="email-highlight">
                <?php 
                    $email = isset($_GET['email']) ? htmlspecialchars($_GET['email']) : 'tu correo';
                    echo $email;
                ?>
            </div>

            <div class="info-box">
                <p>
                    <i class="fas fa-exclamation-circle"></i>
                    Si no ves el correo, revisa tu carpeta de <strong>Spam</strong> o <strong>Promociones</strong>
                </p>
            </div>

            <div class="buttons">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i>
                    Volver al Inicio
                </a>
            </div>
        </div>
    </div>
</body>
</html>
