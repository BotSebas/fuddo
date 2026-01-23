<?php include_once 'lang/idiomas.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $landing_title; ?></title>
    <meta name="description" content="<?php echo $landing_description; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/icons/logo-fuddo.ico">

    <!-- External CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700|Josefin+Sans:300,400,700" rel="stylesheet">

    <!-- CSS Inline -->
    <style>
        /* Base Styles */
        body {
            font-family: 'Open Sans', sans-serif;
            color: #6c757d;
            line-height: 1.8;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Josefin Sans', sans-serif;
        }

        /* Primary Color */
        .btn-primary {
            background-color: #27ae60 !important;
            border-color: #27ae60 !important;
        }

        .btn-primary:hover {
            background-color: #229954 !important;
            border-color: #229954 !important;
        }

        /* Side Navigation */
        .sidenav {
            height: 100%;
            width: 0;
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            background-color: #27ae60;
            overflow-x: hidden;
            transition: 0.3s;
            padding-top: 60px;
        }

        .sidenav a {
            padding: 8px 8px 8px 32px;
            text-decoration: none;
            font-size: 25px;
            color: white;
            display: block;
            transition: 0.3s;
        }

        .sidenav #side-nav-close {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
            color: white;
        }

        .sidenav-content {
            padding: 20px 32px;
            color: white;
        }

        .sidenav-content p {
            color: white;
            font-size: 16px;
            padding-left: 0;
        }

        .fs-16 {
            font-size: 16px;
        }
        
        .fs-16 a {
            font-size: 18px;
        }

        .primary-color {
            color: white;
        }

        #canvas-overlay {
            position: fixed;
            display: none;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0,0,0,0.5);
            z-index: 9998;
        }

        /* Boxed Page */
        .boxed-page {
            max-width: 100%;
            margin: 0 auto;
        }

        .static-layout {
            background: white;
        }

        /* Navigation */
        #navbar-header {
            background: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .navbar-brand {
            padding: 0;
        }

        .navbar-brand img {
            max-height: 50px;
        }

        .navbar-brand-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .navbar .nav-link {
            color: #333;
            font-weight: 500;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .navbar .nav-link:hover {
            color: #27ae60;
        }

        .btn-login-nav {
            background: #27ae60;
            color: white !important;
            border-radius: 25px;
            padding: 10px 25px !important;
        }

        .btn-login-nav:hover {
            background: #229954;
        }

        .only-desktop {
            display: block;
        }

        .only-mobile {
            display: none;
        }

        @media (max-width: 991px) {
            .only-desktop {
                display: none;
            }
            .only-mobile {
                display: flex !important;
            }
            .navbar-brand-center {
                position: relative;
                left: 0;
                transform: none;
            }
        }

        /* Hero */
        .hero {
            background: linear-gradient(rgba(39, 174, 96, 0.05), rgba(255, 255, 255, 1));
            padding: 60px 0 40px;
        }

        .hero h1 {
            font-weight: 700;
            color: #333;
            margin-bottom: 30px;
        }

        .hero p.lead {
            font-size: 1.2rem;
            color: #6c757d;
        }

        .hero-info {
            margin-top: 30px;
        }

        .hero-info li {
            padding: 20px;
            flex: 1;
        }

        .hero-info li .lnr {
            font-size: 3rem;
            color: #27ae60;
            margin-bottom: 15px;
            display: block;
        }

        .hero-info h5 {
            font-size: 1rem;
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .hero-info .border-right {
            border-right: 1px solid #dee2e6;
        }

        .hero-right img {
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        /* Buttons */
        .btn-shadow {
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.3);
            border-radius: 50px;
            padding: 15px 40px;
            font-weight: 600;
        }

        .btn-shadow:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(39, 174, 96, 0.4);
        }

        /* Sections */
        .section-padding {
            padding: 50px 0;
        }

        .bg-grey {
            background: #f8f9fa;
        }

        .bg-white {
            background: white;
        }

        /* Section Headings */
        .heading-section .subheading {
            color: #27ae60;
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 15px;
            display: block;
        }

        .heading-section h2 {
            font-size: 2.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
        }

        /* Welcome Section */
        .img-bg {
            min-height: 500px;
            background-size: cover;
            background-position: center;
            border-radius: 10px;
        }

        .img-2 {
            background-position: center;
        }

        .section-content {
            position: relative;
        }

        .img-cover {
            object-fit: cover;
            height: 150px;
            width: 100%;
            border-radius: 10px;
        }

        .thumb-menu {
            display: block;
            text-decoration: none;
            transition: all 0.3s;
        }

        .thumb-menu:hover {
            transform: translateY(-5px);
        }

        .thumb-menu h6 {
            margin-top: 15px;
            color: #333;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Special Dishes */
        .special-number {
            font-size: 6rem;
            font-weight: 700;
            color: rgba(0,0,0,0.05);
            margin-bottom: 20px;
            line-height: 1;
        }

        .dishes-text h3 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            line-height: 1.3;
        }

        .dishes-text h3 span {
            color: #27ae60;
            font-weight: 300;
        }

        .dishes-text .btn-primary {
            background: #27ae60;
            border: none;
            padding: 15px 40px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .dishes-text .btn-primary:hover {
            background: #229954;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(39, 174, 96, 0.3);
        }

        /* Menu Section */
        .heading-menu h3 {
            font-weight: 700;
            color: #333;
            padding-bottom: 20px;
            border-bottom: 3px solid #27ae60;
            display: inline-block;
        }

        .menu-wrap {
            margin-bottom: 25px;
        }

        .menus {
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .menus:last-child {
            border-bottom: none;
        }

        .menu-img {
            width: 90px;
            height: 90px;
            margin-right: 25px;
            flex-shrink: 0;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .menu-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .text-wrap h4 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .text-wrap p {
            color: #6c757d;
            margin: 0;
            font-size: 0.95rem;
        }

        /* Reservation */
        .bg-fixed {
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
        }

        .overlay {
            position: relative;
        }

        .overlay::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.7);
        }

        #gtco-reservation .section-content {
            position: relative;
            z-index: 1;
            border-radius: 10px;
        }

        #gtco-reservation .form-control {
            border-radius: 5px;
            padding: 15px;
            border: 1px solid #dee2e6;
        }

        #gtco-reservation .form-control:focus {
            border-color: #27ae60;
            box-shadow: 0 0 0 0.2rem rgba(39, 174, 96, 0.25);
        }

        /* Footer */
        .mastfoot {
            background: white;
            border-top: 1px solid #e9ecef;
        }

        .footer-logo {
            margin-bottom: 25px;
        }

        .footer-widget h4 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
        }

        .footer-widget p {
            color: #6c757d;
            line-height: 1.8;
        }

        .open-hours {
            margin: 0;
            padding: 0;
        }

        .open-hours li {
            padding: 10px 0;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .nav-mastfoot {
            margin-top: 20px;
        }

        .nav-mastfoot .nav-link {
            color: #6c757d;
            font-size: 1.5rem;
            padding: 0 15px 0 0;
        }

        .nav-mastfoot .nav-link:hover {
            color: #27ae60;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .hero {
                padding: 50px 0 30px;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .section-padding {
                padding: 40px 0;
            }

            .heading-section h2 {
                font-size: 2rem;
            }

            .special-number {
                font-size: 4rem;
            }

            .dishes-text h3 {
                font-size: 2rem;
            }

            .img-bg {
                min-height: 300px;
                margin-bottom: 30px;
            }
        }

        @media (max-width: 767px) {
            .hero-info {
                margin-top: 30px;
            }

            .hero-info li {
                padding: 15px 10px;
            }

            .hero-info li .lnr {
                font-size: 2.5rem;
            }

            .hero-info h5 {
                font-size: 0.9rem;
            }
        }
    </style>

</head>
<body data-spy="scroll" data-target="#navbar" class="static-layout">
<div id="side-nav" class="sidenav">
	<a href="javascript:void(0)" id="side-nav-close">&times;</a>
	
	<div class="sidenav-content">
		<p style="padding-left: 0;">
			<?php echo $landing_sidenav_descripcion; ?>
		</p>
		<p style="padding-left: 0;">
			<span class="fs-16 primary-color"><a href="https://wa.me/573123268932" target="_blank" style="color: white; text-decoration: none;padding-left: 0;">+57 312 326 8932</a></span>
		</p>
		<p style="padding-left: 0;">fuddocol@gmail.com</p>
	</div>
</div>

<div id="canvas-overlay"></div>
<div class="boxed-page">
<nav id="navbar-header" class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand navbar-brand-center d-flex align-items-center p-0 only-mobile" href="/">
            <img src="assets/img/logo-fuddohorizontal.png" alt="FUDDO">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="lnr lnr-menu"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-between" id="navbarSupportedContent">
            <ul class="navbar-nav d-flex justify-content-between">
                <li class="nav-item only-desktop">
                    <a class="nav-link" id="side-nav-open" href="#">
                        <span class="lnr lnr-menu"></span>
                    </a>
                </li>
                <div class="d-flex flex-lg-row flex-column">
                    <li class="nav-item active">
                        <a class="nav-link" href="/"><?php echo $landing_nav_inicio; ?> <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#gtco-welcome"><?php echo $landing_nav_nosotros; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#gtco-special-dishes"><?php echo $landing_nav_beneficios; ?></a>
                    </li>
                </div>
            </ul>
            
            <ul class="navbar-nav d-flex justify-content-between">
                <div class="d-flex flex-lg-row flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="#gtco-menu"><?php echo $landing_nav_caracteristicas; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#gtco-reservation"><?php echo $landing_nav_contacto; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-login-nav" href="login.php"><?php echo $landing_nav_login; ?></a>
                    </li>
                </div>
            </ul>
        </div>
    </div>
</nav>

<div class="hero">
  <div class="container">
	<div class="row d-flex align-items-center">
		<div class="col-lg-6 hero-left">
		    <h1 class="display-4 mb-5"><?php echo $landing_hero_title; ?><br><?php echo $landing_hero_subtitle; ?></h1>
		    <p class="lead mb-5"><?php echo $landing_hero_description; ?></p>
		    <div class="mb-2">
		    	<a class="btn btn-primary btn-shadow btn-lg" href="login.php" role="button">
		    		<i class="fas fa-sign-in-alt mr-2"></i><?php echo $landing_hero_btn; ?>
		    	</a>
		    </div>
		   
		    <ul class="hero-info list-unstyled d-flex text-center mb-0">
		    	<li class="border-right">
		    		<span class="lnr lnr-rocket"></span>
		    		<h5>
		    			<?php echo $landing_hero_rapido; ?>
		    		</h5>
		    	</li>
		    	<li class="border-right">
		    		<span class="lnr lnr-eye"></span>
		    		<h5>
		    			<?php echo $landing_hero_claro; ?>
		    		</h5>
		    	</li>
		    	<li class="">
		    		<span class="lnr lnr-clock"></span>
		    		<h5>
		    			<?php echo $landing_hero_pensado; ?>
		    		</h5>
		    	</li>
		    </ul>

	    </div>
	    <div class="col-lg-6 hero-right">
	    	<img class="img-fluid" src="assets/img/landing-img1.jpg" alt="Sistema POS">
	    </div>
	</div>
  </div>
</div>

<!-- Welcome Section -->
<section id="gtco-welcome" class="bg-white section-padding">
    <div class="container">
        <div class="section-content">
            <div class="row">
                <div class="col-sm-5 img-bg d-flex shadow align-items-center justify-content-center justify-content-md-end img-2" style="background-image: url(assets/img/landing-img2.jpg);">
                    
                </div>
                <div class="col-sm-7 py-5 pl-md-0 pl-4">
                    <div class="heading-section pl-lg-5 ml-md-5">
                        <span class="subheading">
                            <?php echo $landing_welcome_span; ?>
                        </span>
                        <h2>
                            <?php echo $landing_welcome_title; ?>
                        </h2>
                    </div>
                    <div class="pl-lg-5 ml-md-5">
                        <p><?php echo $landing_welcome_p1; ?></p>
                        <p><?php echo $landing_welcome_p2; ?></p>
                        <p><strong><?php echo $landing_welcome_p3; ?></strong></p>
                        <h3 class="mt-5"><?php echo $landing_welcome_subtitle; ?></h3>
                        <div class="row">
                            <div class="col-4">
                                <a href="#" class="thumb-menu">
                                    <img class="img-fluid img-cover" src="assets/img/landing-img4.jpg" alt="Dashboard"/>
                                    <h6><?php echo $landing_welcome_dashboard; ?></h6>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="thumb-menu">
                                    <img class="img-fluid img-cover" src="assets/img/landing-img5.jpg" alt="Reportes"/>
                                    <h6><?php echo $landing_welcome_reportes; ?></h6>
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="#" class="thumb-menu">
                                    <img class="img-fluid img-cover" src="assets/img/landing-img3.jpg" alt="Inventario"/>
                                    <h6><?php echo $landing_welcome_inventario; ?></h6>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End of Welcome Section -->

<!-- Special Dishes Section -->
<section id="gtco-special-dishes" class="bg-grey section-padding">
    <div class="container">
        <div class="section-content">
            <div class="heading-section text-center">
                <span class="subheading">
                    <?php echo $landing_special_span; ?>
                </span>
                <h2>
                    <?php echo $landing_special_title; ?>
                </h2>
            </div>
            <div class="row mt-5">
                <div class="col-lg-5 col-md-6 align-self-center py-5">
                    <h2 class="special-number">01.</h2>
                    <div class="dishes-text">
                        <h3><span><?php echo $landing_special_01_title; ?></span><br><?php echo $landing_special_01_subtitle; ?></h3>
                        <p class="pt-3"><?php echo $landing_special_01_desc; ?></p>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-2 col-md-6 align-self-center mt-4 mt-md-0">
                    <img src="assets/img/landing-img2.jpg" alt="Toma de Pedidos" class="img-fluid shadow w-100">
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-5 col-md-6 align-self-center order-2 order-md-1 mt-4 mt-md-0">
                    <img src="assets/img/landing-img4.jpg" alt="Control Total" class="img-fluid shadow w-100">
                </div>
                <div class="col-lg-5 offset-lg-2 col-md-6 align-self-center order-1 order-md-2 py-5">
                    <h2 class="special-number">02.</h2>
                    <div class="dishes-text">
                        <h3><span><?php echo $landing_special_02_title; ?></span><br><?php echo $landing_special_02_subtitle; ?></h3>
                        <p class="pt-3"><?php echo $landing_special_02_desc; ?></p>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-lg-5 col-md-6 align-self-center py-5">
                    <h2 class="special-number">03.</h2>
                    <div class="dishes-text">
                        <h3><span><?php echo $landing_special_03_title; ?></span><br><?php echo $landing_special_03_subtitle; ?></h3>
                        <p class="pt-3"><?php echo $landing_special_03_desc; ?></p>
                    </div>
                </div>
                <div class="col-lg-5 offset-lg-2 col-md-6 align-self-center mt-4 mt-md-0">
                    <img src="assets/img/landing-img6.jpg" alt="Datos en Tiempo Real" class="img-fluid shadow w-100">
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End of Special Dishes Section -->

<!-- Menu Section -->
<section id="gtco-menu" class="section-padding">
    <div class="container">
        <div class="section-content">
            <div class="row mb-5">
                <div class="col-md-12">
                    <div class="heading-section text-center">
                        <span class="subheading">
                            <?php echo $landing_menu_span; ?>
                        </span>
                        <h2>
                            <?php echo $landing_menu_title; ?>
                        </h2>
                    </div>  
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 menu-wrap">
                    <div class="heading-menu">
                        <h3 class="text-center mb-5"><?php echo $landing_menu_operaciones; ?></h3>
                    </div>
                    <div class="menus d-flex align-items-center">
                        <div class="menu-img rounded-circle">
                            <i class="fas fa-utensils fa-2x" style="color: #27ae60;"></i>
                        </div>
                        <div class="text-wrap">
                            <div class="row align-items-start">
                                <div class="col-12">
                                    <h4><?php echo $landing_menu_gestion_mesas; ?></h4>
                                </div>
                            </div>
                            <p><?php echo $landing_menu_gestion_mesas_desc; ?></p>
                        </div>
                    </div>
                    <div class="menus d-flex align-items-center">
                        <div class="menu-img rounded-circle">
                            <i class="fas fa-fire fa-2x" style="color: #27ae60;"></i>
                        </div>
                        <div class="text-wrap">
                            <div class="row align-items-start">
                                <div class="col-12">
                                    <h4><?php echo $landing_menu_cocina; ?></h4>
                                </div>
                            </div>
                            <p><?php echo $landing_menu_cocina_desc; ?></p>
                        </div>
                    </div>
                    <div class="menus d-flex align-items-center">
                        <div class="menu-img rounded-circle">
                            <i class="fas fa-receipt fa-2x" style="color: #27ae60;"></i>
                        </div>
                        <div class="text-wrap">
                            <div class="row align-items-start">
                                <div class="col-12">
                                    <h4><?php echo $landing_menu_pedidos; ?></h4>
                                </div>
                            </div>
                            <p><?php echo $landing_menu_pedidos_desc; ?></p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 menu-wrap">
                    <div class="heading-menu">
                        <h3 class="text-center mb-5"><?php echo $landing_menu_gestion; ?></h3>
                    </div>
                    <div class="menus d-flex align-items-center">
                        <div class="menu-img rounded-circle">
                            <i class="fas fa-box fa-2x" style="color: #27ae60;"></i>
                        </div>
                        <div class="text-wrap">
                            <div class="row align-items-start">
                                <div class="col-12">
                                    <h4><?php echo $landing_menu_inventario; ?></h4>
                                </div>
                            </div>
                            <p><?php echo $landing_menu_inventario_desc; ?></p>
                        </div>
                    </div>
                    <div class="menus d-flex align-items-center">
                        <div class="menu-img rounded-circle">
                            <i class="fas fa-chart-line fa-2x" style="color: #27ae60;"></i>
                        </div>
                        <div class="text-wrap">
                            <div class="row align-items-start">
                                <div class="col-12">
                                    <h4><?php echo $landing_menu_reportes; ?></h4>
                                </div>
                            </div>
                            <p><?php echo $landing_menu_reportes_desc; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End of Menu Section -->

<!-- Reservation Section -->
<section id="gtco-reservation" class="bg-fixed bg-white section-padding overlay" style="background-image: url(assets/img/bg-login.jpg);">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="section-content bg-white p-5 shadow">
                    <div class="heading-section text-center">
                        <span class="subheading">
                            <?php echo $landing_contact_span; ?>
                        </span>
                        <h2>
                            <?php echo $landing_contact_title; ?>
                        </h2>
                    </div>
                    <form method="post" name="contact-us" action="">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <input type="text" class="form-control" id="name" name="name" placeholder="<?php echo $landing_contact_nombre; ?>">
                            </div>
                            <div class="col-md-12 form-group">
                                <input type="text" class="form-control" id="email" name="email" placeholder="<?php echo $landing_contact_email; ?>">
                            </div>
                            <div class="col-md-12 form-group">
                                <input type="number" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="<?php echo $landing_contact_telefono; ?>">
                            </div>
                            <div class="col-md-12 form-group">
                                <input type="text" class="form-control" id="company" name="company" placeholder="<?php echo $landing_contact_restaurante; ?>">
                            </div>
                 
                            <div class="col-md-12 form-group">
                                <textarea class="form-control" id="message" name="message" rows="6" placeholder="<?php echo $landing_contact_mensaje; ?>"></textarea>
                            </div>
                            <div class="col-md-12 text-center">
                                <button class="btn btn-primary btn-shadow btn-lg" type="submit" name="submit">
                                	<i class="fas fa-paper-plane mr-2"></i><?php echo $landing_contact_btn; ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-6 offset-lg-1 d-flex align-items-center">
                <div class="text-white">
                    <h2 class="text-white mb-4"><?php echo $landing_why_title; ?></h2>
                    <div class="d-flex mb-4">
                        <div class="mr-3">
                            <i class="fas fa-check-circle fa-2x" style="color: #27ae60;"></i>
                        </div>
                        <div>
                            <h5 class="text-white"><?php echo $landing_why_facil; ?></h5>
                            <p><?php echo $landing_why_facil_desc; ?></p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="mr-3">
                            <i class="fas fa-check-circle fa-2x" style="color: #27ae60;"></i>
                        </div>
                        <div>
                            <h5 class="text-white"><?php echo $landing_why_soporte; ?></h5>
                            <p><?php echo $landing_why_soporte_desc; ?></p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="mr-3">
                            <i class="fas fa-check-circle fa-2x" style="color: #27ae60;"></i>
                        </div>
                        <div>
                            <h5 class="text-white"><?php echo $landing_why_nube; ?></h5>
                            <p><?php echo $landing_why_nube_desc; ?></p>
                        </div>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="mr-3">
                            <i class="fas fa-check-circle fa-2x" style="color: #27ae60;"></i>
                        </div>
                        <div>
                            <h5 class="text-white"><?php echo $landing_why_costos; ?></h5>
                            <p><?php echo $landing_why_costos_desc; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End of Reservation Section -->

<footer class="mastfoot pb-5 bg-white section-padding pb-0">
    <div class="inner container">
         <div class="row">
         	<div class="col-lg-6">
         		<div class="footer-widget pr-lg-5 pr-0">
         			<img src="assets/img/logo-fuddohorizontal.png" class="img-fluid footer-logo mb-3" style="max-height: 60px;">
	         		<p><?php echo $landing_footer_desc; ?></p>
	         		<nav class="nav nav-mastfoot justify-content-start">
		                <a class="nav-link" href="https://www.instagram.com/fuddo.col/" target="_blank" rel="noopener noreferrer">
		                	<i class="fab fa-instagram"></i>
		                </a>
		            </nav>
         		</div>
         		
         	</div>

         	<div class="col-lg-6">
         		<div class="footer-widget pl-lg-5 pl-0">
         			<h4><?php echo $landing_footer_contacto; ?></h4>
	         		<p>
	         			<i class="fas fa-envelope mr-2"></i> fuddocol@gmail.com<br>
	         			<i class="fab fa-whatsapp mr-2"></i> <a href="https://wa.me/573123268932" target="_blank" style="color: inherit; text-decoration: none;">+57 312 326 8932</a>
	         		</p>
	         		<p class="mt-4">
	         			<a href="login.php" class="btn btn-primary btn-block">
	         				<i class="fas fa-sign-in-alt mr-2"></i><?php echo $landing_footer_btn; ?>
	         			</a>
	         		</p>
         		</div>
         		
         	</div>
         	<div class="col-md-12 d-flex align-items-center">
         		<p class="mx-auto text-center mb-0">
         			<?php echo $landing_footer_copyright; ?> <?php echo date('Y'); ?>. <?php echo $landing_footer_derechos; ?>
         		</p>
         	</div>
            
        </div>
    </div>
</footer>

</div>
</div>

	<!-- External JS -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

	<!-- Main JS -->
	<script>
	    // Side Navigation
	    $('#side-nav-open').on('click', function(e) {
	        e.preventDefault();
	        $('#side-nav').css('width', '300px');
	        $('#canvas-overlay').fadeIn();
	    });

	    $('#side-nav-close, #canvas-overlay').on('click', function() {
	        $('#side-nav').css('width', '0');
	        $('#canvas-overlay').fadeOut();
	    });

	    // Smooth Scroll
	    $('a[href^="#"]').on('click', function(e) {
	        var target = $(this.getAttribute('href'));
	        if (target.length) {
	            e.preventDefault();
	            $('html, body').stop().animate({
	                scrollTop: target.offset().top - 80
	            }, 1000);
	        }
	    });

	    // Navbar scroll effect
	    $(window).on('scroll', function() {
	        if ($(window).scrollTop() > 50) {
	            $('#navbar-header').css({
	                'box-shadow': '0 5px 20px rgba(0,0,0,0.2)',
	                'padding': '0.5rem 0'
	            });
	        } else {
	            $('#navbar-header').css({
	                'box-shadow': '0 2px 10px rgba(0,0,0,0.1)',
	                'padding': '1rem 0'
	            });
	        }
	    });

	    // Mobile menu close
	    $('.navbar-nav>li>a').on('click', function() {
	        $('.navbar-collapse').collapse('hide');
	    });

	    // Form validation
	    $('form[name="contact-us"]').on('submit', function(e) {
	        e.preventDefault();
	        
	        var name = $('#name').val();
	        var email = $('#email').val();
	        var phone = $('#phoneNumber').val();
	        
	        if (!name || !email || !phone) {
	            alert('<?php echo $landing_form_completa; ?>');
	            return false;
	        }
	        
	        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	        if (!emailPattern.test(email)) {
	            alert('<?php echo $landing_form_email_invalido; ?>');
	            return false;
	        }
	        
	        alert('<?php echo $landing_form_gracias; ?>');
	        this.reset();
	    });
	</script>
</body>
</html>
