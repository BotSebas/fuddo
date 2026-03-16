<?php include_once 'lang/idiomas.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($landing_title) ? $landing_title : 'FUDDO - Sistema POS para Restaurantes'; ?></title>
    <meta name="description" content="<?php echo isset($landing_description) ? $landing_description : 'FUDDO es el sistema POS completo para restaurantes'; ?>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700&display=swap" rel="stylesheet">
    
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
        }

        /* Header Navigation */
        header {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(225, 225, 225, 0.5);
            z-index: 1000;
            padding: 1rem 2rem;
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

        nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: #1a1a1a;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #27ae60;
        }

        .btn-header {
            background: #27ae60;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-header:hover {
            background: #229954;
        }

        /* Hero Section */
        .hero {
            margin-top: 80px;
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%);
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
        }

        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }

        .hero-text h1 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 3.5rem;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            color: #1a1a1a;
        }

        .hero-text h1 .highlight {
            color: #27ae60;
        }

        .hero-text p {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background: #27ae60;
            color: white;
            padding: 1rem 2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #229954;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(39, 174, 96, 0.2);
        }

        .btn-secondary {
            background: white;
            color: #27ae60;
            padding: 1rem 2rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 1rem;
            border: 2px solid #27ae60;
            transition: all 0.3s;
            cursor: pointer;
            display: inline-block;
        }

        .btn-secondary:hover {
            background: #27ae60;
            color: white;
        }

        .hero-badge {
            display: inline-block;
            background: #e8f8f0;
            color: #27ae60;
            padding: 0.75rem 1.5rem;
            border-radius: 20px;
            font-weight: 600;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .hero-image {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        /* Features Section */
        .features {
            padding: 4rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-header h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #1a1a1a;
        }

        .section-header p {
            font-size: 1.1rem;
            color: #666;
            max-width: 600px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border-color: #27ae60;
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: #e8f8f0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: #27ae60;
        }

        .feature-card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #1a1a1a;
        }

        .feature-card p {
            color: #666;
            line-height: 1.8;
        }

        /* Pricing Section */
        .pricing {
            padding: 4rem 2rem;
            background: #f9f9f9;
        }

        .pricing-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
            justify-items: center;
        }

        .price-card {
            background: white;
            border-radius: 12px;
            padding: 2.5rem;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
            position: relative;
            display: flex;
            flex-direction: column;
            width: 450px;
        }

        .price-card.featured {
            border-color: #27ae60;
            box-shadow: 0 10px 40px rgba(39, 174, 96, 0.15);
            transform: scale(1.02);
        }

        .price-card.featured .badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: #27ae60;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .price-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #1a1a1a;
        }

        .price-card .price {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 3rem;
            color: #27ae60;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .price-card .period {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .price-card .annual-price {
            background: #e8f8f0;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #27ae60;
            font-weight: 600;
        }

        .price-card ul {
            list-style: none;
            margin-bottom: 2rem;
            flex-grow: 1;
        }

        .price-card li {
            padding: 0.75rem 0;
            color: #666;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .price-card li:last-child {
            border-bottom: none;
        }

        .price-card li i {
            color: #27ae60;
            font-size: 1.1rem;
        }

        .price-card .btn-price {
            width: 100%;
            padding: 1rem;
            border-radius: 6px;
            border: none;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .price-card .btn-price.btn-primary {
            background: #27ae60;
            color: white;
        }

        .price-card .btn-price.btn-primary:hover {
            background: #229954;
        }

        .price-card .btn-price.btn-secondary {
            background: white;
            color: #27ae60;
            border: 2px solid #27ae60;
        }

        .price-card .btn-price.btn-secondary:hover {
            background: #e8f8f0;
        }

        /* Trial Section */
        .trial {
            padding: 4rem 2rem;
            background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);
            color: white;
        }

        .trial-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .trial-container h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 2.8rem;
            margin-bottom: 1rem;
        }

        .trial-container p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            line-height: 1.8;
        }

        .trial-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .trial-feature {
            background: rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .trial-feature i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .trial-feature h4 {
            margin-bottom: 0.5rem;
        }

        .btn-trial {
            background: white;
            color: #27ae60;
            padding: 1rem 2.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-block;
            margin-top: 1rem;
        }

        .btn-trial:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        /* Testimonials Section */
        .testimonials {
            padding: 4rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .testimonial-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            border: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
        }

        .stars {
            color: #ffc107;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .testimonial-text {
            color: #666;
            margin-bottom: 1.5rem;
            flex-grow: 1;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .testimonial-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #27ae60;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
        }

        .testimonial-info h4 {
            margin-bottom: 0.25rem;
            color: #1a1a1a;
        }

        .testimonial-info p {
            color: #999;
            font-size: 0.9rem;
        }

        /* FAQ Section */
        .faq {
            padding: 4rem 2rem;
            background: #f9f9f9;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-item {
            background: white;
            border-radius: 12px;
            margin-bottom: 1rem;
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }

        .faq-question {
            padding: 1.5rem;
            background: white;
            border: none;
            width: 100%;
            text-align: left;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            color: #1a1a1a;
        }

        .faq-question:hover {
            background: #f5f5f5;
        }

        .faq-question.active {
            background: #f9f9f9;
        }

        .faq-icon {
            transition: transform 0.3s;
            color: #27ae60;
        }

        .faq-question.active .faq-icon {
            transform: rotate(180deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s;
            background: #fafafa;
        }

        .faq-answer.active {
            max-height: 500px;
        }

        .faq-answer p {
            padding: 1.5rem;
            color: #666;
            line-height: 1.8;
        }

        /* Footer */
        footer {
            background: #1a1a1a;
            color: white;
            padding: 3rem 2rem;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .footer-section h4 {
            color: #27ae60;
            margin-bottom: 1rem;
            font-weight: 700;
        }

        .footer-section a {
            display: block;
            color: #aaa;
            text-decoration: none;
            margin-bottom: 0.75rem;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: #27ae60;
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 2rem;
            text-align: center;
            color: #aaa;
        }

        /* Responsive */
        @media (max-width: 768px) {
            header {
                padding: 1rem;
            }

            nav {
                display: none;
            }

            .hero-content {
                grid-template-columns: 1fr;
            }

            .hero-text h1 {
                font-size: 2rem;
            }

            .section-header h2 {
                font-size: 1.8rem;
            }

            .price-card.featured {
                transform: scale(1);
            }

            .trial-container h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-container">
            <a href="#" class="logo">
                <img src="assets/img/logo-fuddohorizontal.png" alt="FUDDO" style="height: 45px; width: auto;">
            </a>
            <nav>
                <a href="#features"><?php echo $landing_nav_caracteristicas; ?></a>
                <a href="#pricing"><?php echo isset($landing_mod_pricing_title) ? 'Precios' : 'Precios'; ?></a>
                <a href="#faq"><?php echo isset($landing_mod_faq_title) ? 'Preguntas' : 'Preguntas'; ?></a>
                <a href="login.php"><?php echo $landing_nav_login; ?></a>
                <button class="btn-header" onclick="scrollToTrial()">Demo Gratis</button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <div class="hero-badge">
                    <?php echo $landing_mod_hero_badge; ?>
                </div>
                <h1><?php echo $landing_mod_hero_title; ?></h1>
                <p><?php echo $landing_mod_hero_description; ?></p>
                <div class="hero-buttons">
                    <button class="btn-primary" onclick="scrollToTrial()">
                        <i class="fas fa-play-circle"></i> <?php echo $landing_mod_hero_btn_trial; ?>
                    </button>
                    <a href="#features" class="btn-secondary">
                        <i class="fas fa-arrow-down"></i> <?php echo $landing_mod_hero_btn_learn; ?>
                    </a>
                </div>
                <p style="color: #27ae60; font-weight: 600; margin-top: 1.5rem;">
                    <i class="fas fa-check-circle"></i> <?php echo $landing_mod_hero_no_card; ?>
                </p>
            </div>
            <div class="hero-image">
                <img src="assets/img/landing-img1.jpg" alt="FUDDO Sistema de Gestión" style="width: 100%; height: auto; border-radius: 12px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);">
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-header">
            <h2><?php echo $landing_mod_features_title; ?></h2>
            <p><?php echo $landing_mod_features_subtitle; ?></p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chair"></i>
                </div>
                <h3><?php echo $landing_mod_feature_mesas; ?></h3>
                <p>Control visual completo de espacios de venta. Seguimiento en tiempo real del estado de cada sector de tu negocio.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <h3><?php echo $landing_mod_feature_comandas; ?></h3>
                <p>Gestiona órdenes de forma rápida e intuitiva. Integración directa con operaciones para maximizar eficiencia.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-fire"></i>
                </div>
                <h3><?php echo $landing_mod_feature_cocina; ?></h3>
                <p><?php echo $landing_mod_feature_cocina_desc; ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-box"></i>
                </div>
                <h3><?php echo $landing_mod_feature_inventario; ?></h3>
                <p><?php echo $landing_mod_feature_inventario_desc; ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3><?php echo $landing_mod_feature_reportes; ?></h3>
                <p><?php echo $landing_mod_feature_reportes_desc; ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?php echo $landing_mod_feature_usuarios; ?></h3>
                <p><?php echo $landing_mod_feature_usuarios_desc; ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3><?php echo $landing_mod_feature_costeo; ?></h3>
                <p><?php echo $landing_mod_feature_costeo_desc; ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h3><?php echo $landing_mod_feature_menu_digital; ?></h3>
                <p><?php echo $landing_mod_feature_menu_digital_desc; ?></p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-cloud"></i>
                </div>
                <h3><?php echo $landing_mod_feature_nube; ?></h3>
                <p><?php echo $landing_mod_feature_nube_desc; ?></p>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="pricing" id="pricing">
        <div class="pricing-container">
            <div class="section-header">
                <h2><?php echo $landing_mod_pricing_title; ?></h2>
                <p><?php echo $landing_mod_pricing_subtitle; ?></p>
            </div>
            
            <div class="pricing-grid">
                <div class="price-card featured">
                    <div class="badge"><?php echo $landing_mod_pricing_badge; ?></div>
                    <h3><?php echo $landing_mod_pricing_plan_name; ?></h3>
                    
                    <div style="background: #f0f8f5; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #27ae60;">
                        <div style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; font-weight: 600;">Cuota Mensual / Anual</div>
                        <div class="price">$60.000</div>
                        <div class="period"><?php echo $landing_mod_pricing_monthly; ?></div>
                        <div class="annual-price" style="margin-top: 1rem;">
                            <i class="fas fa-tag"></i> <?php echo $landing_mod_pricing_annual; ?>
                        </div>
                    </div>

                    <div style="background: #fff3cd; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; border-left: 4px solid #ff9800;">
                        <div style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; font-weight: 600;">⚙️ Integración Inicial (Valor Único)</div>
                        <div style="font-size: 1.8rem; color: #ff9800; font-weight: 700; margin-bottom: 0.5rem;">$600.000</div>
                        <p style="font-size: 0.9rem; color: #666; margin: 0;">Incluye setup completo de tu instancia, configuración profesional y capacitación del equipo</p>
                    </div>

                    <div style="margin-bottom: 2rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
                        <p style="font-size: 0.95rem; color: #27ae60; font-weight: 600; margin-bottom: 1rem;"><i class="fas fa-check-circle"></i> Incluye:</p>
                        <ul>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_mesas; ?></li>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_comandas; ?></li>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_cocina; ?></li>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_inventario; ?></li>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_reportes; ?></li>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_costeo; ?></li>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_menu; ?></li>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_usuarios; ?></li>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_soporte; ?></li>
                            <li><i class="fas fa-check"></i> <?php echo $landing_mod_pricing_feature_updates; ?></li>
                        </ul>
                    </div>
                    <button class="btn-price btn-primary" onclick="scrollToTrial()" style="width: 100%; padding: 1.2rem; font-size: 1.1rem;">
                        <i class="fas fa-play"></i> <?php echo $landing_mod_pricing_btn; ?>
                    </button>
                </div>
            </div>

            <div style="text-align: center; margin-top: 3rem;">
                <p style="color: #666; font-size: 1.1rem; margin-bottom: 1rem;">
                    <i class="fas fa-question-circle" style="color: #27ae60;"></i> 
                    <?php echo $landing_mod_pricing_contact_question; ?>
                </p>
                <a href="mailto:contacto@fuddo.com" style="color: #27ae60; font-weight: 600; text-decoration: none;">
                    <?php echo $landing_mod_pricing_contact_link; ?>
                </a>
            </div>
        </div>
    </section>

    <!-- Trial Section -->
    <section class="trial" id="trial">
        <div class="trial-container">
            <h2><i class="fas fa-gift"></i> <?php echo $landing_mod_trial_title; ?></h2>
            <p><?php echo $landing_mod_trial_subtitle; ?></p>
            
            <div class="trial-features">
                <div class="trial-feature">
                    <i class="fas fa-lock-open"></i>
                    <h4><?php echo $landing_mod_trial_feature1_title; ?></h4>
                    <p><?php echo $landing_mod_trial_feature1_desc; ?></p>
                </div>
                <div class="trial-feature">
                    <i class="fas fa-headset"></i>
                    <h4><?php echo $landing_mod_trial_feature2_title; ?></h4>
                    <p><?php echo $landing_mod_trial_feature2_desc; ?></p>
                </div>
                <div class="trial-feature">
                    <i class="fas fa-sync"></i>
                    <h4><?php echo $landing_mod_trial_feature3_title; ?></h4>
                    <p><?php echo $landing_mod_trial_feature3_desc; ?></p>
                </div>
            </div>

            <button class="btn-trial" onclick="goToRegister()">
                <i class="fas fa-rocket"></i> <?php echo $landing_mod_trial_btn; ?>
            </button>
        </div>
    </section>

    <!-- Success Stories / Testimonials -->
    <section class="testimonials">
        <div class="section-header">
            <h2><?php echo $landing_mod_testimonials_title; ?></h2>
            <p><?php echo $landing_mod_testimonials_subtitle; ?></p>
        </div>
        
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text"><?php echo $landing_mod_testimonial1_text; ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">CM</div>
                    <div class="testimonial-info">
                        <h4><?php echo $landing_mod_testimonial1_author; ?></h4>
                        <p><?php echo $landing_mod_testimonial1_company; ?></p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text"><?php echo $landing_mod_testimonial2_text; ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">AR</div>
                    <div class="testimonial-info">
                        <h4><?php echo $landing_mod_testimonial2_author; ?></h4>
                        <p><?php echo $landing_mod_testimonial2_company; ?></p>
                    </div>
                </div>
            </div>

            <div class="testimonial-card">
                <div class="stars">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="testimonial-text"><?php echo $landing_mod_testimonial3_text; ?></p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">JP</div>
                    <div class="testimonial-info">
                        <h4><?php echo $landing_mod_testimonial3_author; ?></h4>
                        <p><?php echo $landing_mod_testimonial3_company; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq" id="faq">
        <div class="faq-container">
            <div class="section-header" style="margin-bottom: 2rem;">
                <h2><?php echo $landing_mod_faq_title; ?></h2>
                <p><?php echo $landing_mod_faq_subtitle; ?></p>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(event)">
                    <span><?php echo $landing_mod_faq1_q; ?></span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <p><?php echo $landing_mod_faq1_a; ?></p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(event)">
                    <span><?php echo $landing_mod_faq2_q; ?></span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <p><?php echo $landing_mod_faq2_a; ?></p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(event)">
                    <span><?php echo $landing_mod_faq3_q; ?></span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <p><?php echo $landing_mod_faq3_a; ?></p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(event)">
                    <span><?php echo $landing_mod_faq4_q; ?></span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <p><?php echo $landing_mod_faq4_a; ?></p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(event)">
                    <span><?php echo $landing_mod_faq5_q; ?></span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <p><?php echo $landing_mod_faq5_a; ?></p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(event)">
                    <span><?php echo $landing_mod_faq6_q; ?></span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <p><?php echo $landing_mod_faq6_a; ?></p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(event)">
                    <span><?php echo $landing_mod_faq7_q; ?></span>
                    <i class="fas fa-chevron-down faq-icon"></i>
                </button>
                <div class="faq-answer">
                    <p><?php echo $landing_mod_faq7_a; ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Final -->
    <section style="padding: 4rem 2rem; background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%); color: white; text-align: center;">
        <div style="max-width: 800px; margin: 0 auto;">
            <h2 style="font-family: 'Plus Jakarta Sans', sans-serif; font-size: 2.5rem; margin-bottom: 1rem;">Toma el control de tu restaurante hoy</h2>
            <p style="font-size: 1.1rem; margin-bottom: 2rem; opacity: 0.95;">Únete a cientos de restaurantes que ya están creciendo con FUDDO. Comienza tu prueba gratis de 7 días ahora.</p>
            <button class="btn-trial" onclick="goToRegister()">
                <i class="fas fa-rocket"></i> Crear Cuenta Gratis
            </button>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>FUDDO</h4>
                <p style="color: #aaa;"><?php echo $landing_mod_footer_tagline; ?></p>
            </div>
            <div class="footer-section">
                <h4><?php echo $landing_mod_footer_product; ?></h4>
                <a href="#features"><?php echo $landing_mod_footer_features; ?></a>
                <a href="#pricing">Precios</a>
                <a href="#faq"><?php echo $landing_mod_footer_faq; ?></a>
                <a href="#">Seguridad</a>
            </div>
            <div class="footer-section">
                <h4><?php echo $landing_mod_footer_company; ?></h4>
                <a href="#"><?php echo $landing_mod_footer_about; ?></a>
                <a href="#"><?php echo $landing_mod_footer_blog; ?></a>
                <a href="#"><?php echo $landing_mod_footer_contact; ?></a>
                <a href="#">Careers</a>
            </div>
            <div class="footer-section">
                <h4><?php echo $landing_mod_footer_legal; ?></h4>
                <a href="#"><?php echo $landing_mod_footer_terms; ?></a>
                <a href="#"><?php echo $landing_mod_footer_privacy; ?></a>
                <a href="#"><?php echo $landing_mod_footer_cookies; ?></a>
            </div>
        </div>
        <div class="footer-bottom">
            <p><?php echo $landing_mod_footer_copyright; ?></p>
        </div>
    </footer>

    <script>
        function scrollToTrial() {
            document.getElementById('trial').scrollIntoView({ behavior: 'smooth' });
        }

        function goToRegister() {
            // Redirigir a la página de registro
            window.location.href = 'registro.php';
        }

        function toggleFaq(event) {
            const question = event.currentTarget;
            const answer = question.nextElementSibling;
            const icon = question.querySelector('.faq-icon');

            // Cerrar otros elementos abiertos
            document.querySelectorAll('.faq-question.active').forEach(q => {
                if (q !== question) {
                    q.classList.remove('active');
                    q.nextElementSibling.classList.remove('active');
                }
            });

            question.classList.toggle('active');
            answer.classList.toggle('active');
        }

        // Animar elementos al entrar en vista
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeInUp 0.6s ease-out forwards';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card, .price-card, .testimonial-card').forEach(el => {
            observer.observe(el);
        });

        // Agregar animación fade-in
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);

        // Smooth scroll auxiliar
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
