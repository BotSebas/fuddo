<!DOCTYPE html>
<html lang="es">
<head>
<?php include_once 'lang/idiomas.php'; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Prueba Gratis 7 Días | FUDDO</title>
    <meta name="description" content="Crea tu cuenta y comienza tu prueba gratuita de 7 días sin tarjeta de crédito">
    
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

        /* Form Group */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        label .required {
            color: #e74c3c;
        }

        input[type="text"],
        input[type="email"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 0.95rem 1rem;
            border: 1.5px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            transition: all 0.3s;
            background: #fafafa;
            color: #1a1a1a;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="tel"]:focus,
        select:focus {
            outline: none;
            border-color: #27ae60;
            background: white;
            box-shadow: 0 0 0 3px rgba(39, 174, 96, 0.1);
        }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        input[type="tel"]::placeholder {
            color: #999;
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.2em;
            padding-right: 2.5rem;
        }

        /* Form Row for Two Columns */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 640px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 1.5rem;
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

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 1.1rem;
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.05rem;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Footer Link */
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f0f0f0;
        }

        .form-footer p {
            font-size: 0.95rem;
            color: #666;
        }

        .form-footer a {
            color: #27ae60;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .form-footer a:hover {
            color: #1e8449;
            text-decoration: underline;
        }

        /* Error Message */
        .error-message {
            color: #e74c3c;
            font-size: 0.85rem;
            margin-top: 0.4rem;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        input.error,
        select.error {
            border-color: #e74c3c;
            background: #feebee;
        }

        /* Success Message */
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: none;
            border-left: 4px solid #28a745;
        }

        .success-message.show {
            display: block;
        }
        /* Floating Error Alert */
        #errorMessage {
            position: fixed;
            top: 20px;
            right: 20px;
            max-width: 400px;
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(500px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .error-message-content {
            background: #fff;
            border-left: 4px solid #e74c3c;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .error-message-icon {
            color: #e74c3c;
            font-size: 1.3rem;
            flex-shrink: 0;
            margin-top: 0.1rem;
        }

        .error-message-text {
            flex: 1;
            color: #333;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .error-message-close {
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0;
            margin-left: 0.5rem;
            transition: color 0.2s;
            flex-shrink: 0;
        }

        .error-message-close:hover {
            color: #e74c3c;
        }

        /* Responsive */
        @media (max-width: 480px) {
            #errorMessage {
                right: 10px;
                left: 10px;
                max-width: none;
            }
        }
        /* Loading State */
        .loading-spinner {
            display: none;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-right: 0.5rem;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .btn-submit.loading {
            pointer-events: none;
        }

        .btn-submit.loading .loading-spinner {
            display: inline-block;
        }

        .btn-submit.loading .btn-text {
            opacity: 0.7;
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
                    <i class="fas fa-gift"></i> Prueba Gratis 7 Días
                </div>
                <h1>Crea tu Cuenta</h1>
                <p>Estas a un paso de hacer crecer tu negocio</p>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p>
                    <i class="fas fa-check-circle"></i>
                    <span>Sin tarjeta de crédito requerida</span>
                </p>
            </div>

            <!-- Success Message -->
            <div class="success-message" id="successMessage">
                <i class="fas fa-check-circle"></i> ¡Cuenta creada exitosamente! Redirigiendo...
            </div>

            <!-- Error Message -->
            <div class="error-message" id="errorMessage" style="display: none;">
                <div class="error-message-content">
                    <div class="error-message-icon">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="error-message-text">
                        <span id="errorMessageText"></span>
                    </div>
                    <button type="button" class="error-message-close" onclick="document.getElementById('errorMessage').style.display = 'none';">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <!-- Registration Form -->
            <form id="registroForm" method="POST" action="procesar_registro.php" novalidate>
                <!-- Nombre del Usuario -->
                <div class="form-group">
                    <label for="nombre">Nombre Completo <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="nombre" 
                        name="nombre" 
                        placeholder="Ej: Juan Pérez"
                        required
                    >
                    <div class="error-message" id="errorNombre"></div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Correo Electrónico <span class="required">*</span></label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="Ej: juan@ejemplo.com"
                        required
                    >
                    <div class="error-message" id="errorEmail"></div>
                </div>

                <!-- Teléfono -->
                <div class="form-group">
                    <label for="telefono">Teléfono <span class="required">*</span></label>
                    <input 
                        type="tel" 
                        id="telefono" 
                        name="telefono" 
                        placeholder="Ej: 3101234567"
                        required
                    >
                    <div class="error-message" id="errorTelefono"></div>
                </div>

                <!-- Nombre del Negocio / Organización -->
                <div class="form-group">
                    <label for="nombreNegocio">Nombre del Negocio <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="nombreNegocio" 
                        name="nombreNegocio" 
                        placeholder="Ej: El Buen Sazón"
                        required
                    >
                    <div class="error-message" id="errorNombreNegocio"></div>
                </div>

                <!-- Tipo de Negocio -->
                <div class="form-group">
                    <label for="tipoNegocio">Tipo de Negocio <span class="required">*</span></label>
                    <select id="tipoNegocio" name="tipoNegocio" required>
                        <option value="">-- Selecciona tu tipo de negocio --</option>
                        <option value="restaurante">Restaurante</option>
                        <option value="bar">Bar</option>
                        <option value="minimarket">Minimarket</option>
                        <option value="restaurante-bar">Restaurante Bar</option>
                        <option value="otro">Otro</option>
                    </select>
                    <div class="error-message" id="errorTipoNegocio"></div>
                </div>

                <!-- Campo para especificar otro tipo de negocio (oculto por defecto) -->
                <div class="form-group" id="tipoNegocioOtroContainer" style="display: none;">
                    <label for="tipoNegocioOtro">Especifica tu tipo de negocio <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="tipoNegocioOtro" 
                        name="tipoNegocioOtro" 
                        placeholder="Ej: Farmacia, Boutique, Cafetería, etc."
                    >
                    <div class="error-message" id="errorTipoNegocioOtro"></div>
                </div>

                <!-- Terms Agreement -->
                <div class="form-group" style="margin-bottom: 0;">
                    <label style="display: flex; gap: 0.75rem; font-weight: 500; cursor: pointer; margin-bottom: 0;">
                        <input 
                            type="checkbox" 
                            id="terminos" 
                            name="terminos" 
                            required
                            style="width: auto; cursor: pointer; margin-top: 0.15rem;"
                        >
                        <span>Acepto los <a href="terminos-servicio.php" target="_blank" style="color: #27ae60; text-decoration: none;">Términos de Servicio</a> y la <a href="politica-privacidad.php" target="_blank" style="color: #27ae60; text-decoration: none;">Política de Privacidad</a></span>
                    </label>
                    <div class="error-message" id="errorTerminos"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <span class="loading-spinner"></span>
                    <span class="btn-text">Crear Cuenta Gratis</span>
                </button>

                <!-- Footer Link -->
                <div class="form-footer">
                    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Form Validation
        const form = document.getElementById('registroForm');
        const btnSubmit = document.getElementById('btnSubmit');
        const successMessage = document.getElementById('successMessage');

        // Validation Rules
        const validations = {
            nombre: {
                field: document.getElementById('nombre'),
                errorElement: document.getElementById('errorNombre'),
                validate: function(value) {
                    if (!value.trim()) return 'El nombre es requerido';
                    if (value.trim().length < 3) return 'El nombre debe tener al menos 3 caracteres';
                    if (!/^[a-zA-Z\s]+$/.test(value)) return 'El nombre solo puede contener letras';
                    return null;
                }
            },
            email: {
                field: document.getElementById('email'),
                errorElement: document.getElementById('errorEmail'),
                validate: function(value) {
                    if (!value.trim()) return 'El email es requerido';
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) return 'Por favor ingresa un email válido';
                    return null;
                }
            },
            telefono: {
                field: document.getElementById('telefono'),
                errorElement: document.getElementById('errorTelefono'),
                validate: function(value) {
                    if (!value.trim()) return 'El teléfono es requerido';
                    if (!/^\d{7,}$/.test(value.replace(/[^\d]/g, ''))) return 'El teléfono debe tener al menos 7 dígitos';
                    return null;
                }
            },
            nombreNegocio: {
                field: document.getElementById('nombreNegocio'),
                errorElement: document.getElementById('errorNombreNegocio'),
                validate: function(value) {
                    if (!value.trim()) return 'El nombre del negocio es requerido';
                    if (value.trim().length < 3) return 'El nombre debe tener al menos 3 caracteres';
                    return null;
                }
            },
            tipoNegocio: {
                field: document.getElementById('tipoNegocio'),
                errorElement: document.getElementById('errorTipoNegocio'),
                validate: function(value) {
                    if (!value) return 'Debes seleccionar un tipo de negocio';
                    return null;
                }
            },
            tipoNegocioOtro: {
                field: document.getElementById('tipoNegocioOtro'),
                errorElement: document.getElementById('errorTipoNegocioOtro'),
                validate: function(value) {
                    // Solo validar si el campo está visible
                    const container = document.getElementById('tipoNegocioOtroContainer');
                    if (container.style.display === 'none') {
                        return null;
                    }
                    if (!value.trim()) return 'Por favor especifica tu tipo de negocio';
                    if (value.trim().length < 3) return 'Debe tener al menos 3 caracteres';
                    return null;
                }
            },
            terminos: {
                field: document.getElementById('terminos'),
                errorElement: document.getElementById('errorTerminos'),
                validate: function(value) {
                    if (!value) return 'Debes aceptar los términos de servicio';
                    return null;
                }
            }
        };

        // Validate Single Field
        function validateField(fieldName) {
            const validation = validations[fieldName];
            const value = validation.field.type === 'checkbox' ? validation.field.checked : validation.field.value;
            const error = validation.validate(value);

            if (error) {
                validation.errorElement.textContent = error;
                validation.errorElement.classList.add('show');
                validation.field.classList.add('error');
                return false;
            } else {
                validation.errorElement.classList.remove('show');
                validation.field.classList.remove('error');
                return true;
            }
        }

        // Real-time validation on blur
        Object.keys(validations).forEach(fieldName => {
            const field = validations[fieldName].field;
            field.addEventListener('blur', () => validateField(fieldName));
            field.addEventListener('change', () => validateField(fieldName));
        });

        // Mostrar/ocultar campo para especificar otro tipo de negocio
        document.getElementById('tipoNegocio').addEventListener('change', function() {
            const container = document.getElementById('tipoNegocioOtroContainer');
            const field = document.getElementById('tipoNegocioOtro');
            
            if (this.value === 'otro') {
                container.style.display = 'block';
                field.focus();
            } else {
                container.style.display = 'none';
                field.value = '';
                field.classList.remove('error');
                document.getElementById('errorTipoNegocioOtro').classList.remove('show');
            }
        });

        // Form Submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate all fields
            let isValid = true;
            Object.keys(validations).forEach(fieldName => {
                if (!validateField(fieldName)) {
                    isValid = false;
                }
            });

            // Validar tipoNegocio cuando sea 'otro'
            if (document.getElementById('tipoNegocio').value === 'otro') {
                if (!validateField('tipoNegocioOtro')) {
                    isValid = false;
                }
            }

            if (!isValid) {
                // Scroll to first error
                const firstError = form.querySelector('.error-message.show');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            // Show loading state
            btnSubmit.classList.add('loading');
            btnSubmit.disabled = true;

            // Prepare FormData for submission
            const formDataObj = new FormData(form);

            // Send to backend using Fetch API
            fetch('procesar_registro.php', {
                method: 'POST',
                body: formDataObj
            })
            .then(response => {
                // Leer la respuesta como texto primero, sin verificar OK
                return response.text().then(text => {
                    return {
                        ok: response.ok,
                        status: response.status,
                        text: text
                    };
                });
            })
            .then(result => {
                // Intentar parsear como JSON
                try {
                    const data = JSON.parse(result.text);
                    // Si no fue OK pero tenemos JSON con mensaje, propagar el error con el mensaje
                    if (!result.ok && !data.success) {
                        const errorMsg = data.message || `HTTP error! status: ${result.status}`;
                        throw new Error(errorMsg);
                    }
                    return data;
                } catch (e) {
                    // Si el error es nuestro con mensaje, relanzarlo
                    if (e instanceof Error && e.message !== result.text) {
                        throw e;
                    }
                    console.error('Respuesta no es JSON válido:', result.text);
                    throw new Error('Respuesta inválida del servidor: ' + result.text.substring(0, 100));
                }
            })
            .then(data => {
                btnSubmit.classList.remove('loading');
                btnSubmit.disabled = false;

                if (data.success) {
                    // Show success message
                    successMessage.classList.add('show');
                    
                    // Reset form
                    form.reset();
                    
                    // Redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'verificar_email.php?email=' + encodeURIComponent(data.data.email);
                    }, 2000);
                } else {
                    // Show error message
                    let errorMsg = data.message || 'Error al registrarse. Por favor intenta de nuevo.';
                    
                    // If it's an email already exists error, show it in the email field
                    if (errorMsg.includes('correo')) {
                        document.getElementById('errorEmail').textContent = errorMsg;
                        document.getElementById('errorEmail').classList.add('show');
                        document.getElementById('email').classList.add('error');
                        return;
                    }
                    
                    // For other errors, show in floating error message
                    const errorMessageDiv = document.getElementById('errorMessage');
                    document.getElementById('errorMessageText').textContent = errorMsg;
                    errorMessageDiv.style.display = 'block';
                    
                    // Auto-close after 6 seconds
                    setTimeout(() => {
                        errorMessageDiv.style.display = 'none';
                    }, 6000);
                }
            })
            .catch(error => {
                btnSubmit.classList.remove('loading');
                btnSubmit.disabled = false;
                console.error('Error completo:', error);
                
                // Show error in floating error message
                const errorMessageDiv = document.getElementById('errorMessage');
                document.getElementById('errorMessageText').textContent = error.message || 'Error al conectar con el servidor';
                errorMessageDiv.style.display = 'block';
                
                // Auto-close after 6 seconds
                setTimeout(() => {
                    errorMessageDiv.style.display = 'none';
                }, 6000);
            });
        });

        // Clear error when user starts typing
        Object.keys(validations).forEach(fieldName => {
            const field = validations[fieldName].field;
            field.addEventListener('input', function() {
                if (field.classList.contains('error')) {
                    field.classList.remove('error');
                    validations[fieldName].errorElement.classList.remove('show');
                }
                // Also hide general error message when user starts typing
                document.getElementById('errorMessage').style.display = 'none';
            });
        });
    </script>
</body>
</html>
