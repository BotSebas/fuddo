<?php
include 'lang/idiomas.php';
include 'includes/url.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos de Servicio - FUDDO</title>
    <meta name="description" content="Términos de Servicio de FUDDO - Lee nuestras políticas y condiciones de uso">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            color: #1a1a1a;
            line-height: 1.8;
            background: #f5f5f5;
        }
        
        header {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            padding: 1.5rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        header a {
            color: #27ae60;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            margin-top: 2rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        
        h1 {
            color: #27ae60;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        
        .last-update {
            color: #999;
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }
        
        h2 {
            color: #27ae60;
            font-size: 1.5rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        
        h3 {
            color: #1e8449;
            font-size: 1.1rem;
            margin-top: 1.5rem;
            margin-bottom: 0.8rem;
        }
        
        p {
            margin-bottom: 1rem;
            color: #555;
        }
        
        ul, ol {
            margin-left: 2rem;
            margin-bottom: 1rem;
        }
        
        li {
            margin-bottom: 0.5rem;
            color: #555;
        }
        
        strong {
            color: #27ae60;
            font-weight: 600;
        }
        
        a {
            color: #27ae60;
            text-decoration: none;
        }
        
        a:hover {
            text-decoration: underline;
        }
        
        .contact-info {
            background: #f0f8f5;
            border-left: 4px solid #27ae60;
            padding: 1.5rem;
            border-radius: 6px;
            margin: 1.5rem 0;
        }
        
        .contact-info p {
            margin: 0.5rem 0;
        }
        
        footer {
            background: #1a1a1a;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        footer a {
            color: #27ae60;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            h2 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <a href="<?php echo $BASE_URL; ?>">
            <i class="fas fa-arrow-left"></i> Volver a FUDDO
        </a>
    </header>
    
    <!-- Contenido -->
    <div class="container">
        <h1>Términos de Servicio</h1>
        <p class="last-update"><strong>Última actualización:</strong> 25 de marzo de 2026</p>
        
        <p>Bienvenido(a) a FUDDO. Estos Términos de Servicio regulan el acceso, navegación y uso del sitio web fuddo.co y de los servicios, herramientas, funcionalidades y software ofrecidos por FUDDO.</p>
        
        <p>Al acceder, registrarte, solicitar información, contratar o utilizar nuestros servicios, aceptas estos Términos de Servicio. Si no estás de acuerdo con ellos, te pedimos no utilizar la plataforma ni nuestros servicios.</p>
        
        <h2>1. ¿Quiénes somos?</h2>
        <p>FUDDO es una solución tecnológica diseñada para restaurantes, bares y negocios gastronómicos, orientada a facilitar la gestión operativa del establecimiento, incluyendo funciones como toma de pedidos, control de mesas, cocina, inventario, reportes y otras herramientas relacionadas.</p>
        
        <p>Para cualquier contacto puedes escribirnos a:</p>
        <ul>
            <li><strong>Correo:</strong> fuddocol@gmail.com</li>
            <li><strong>Sitio web:</strong> fuddo.co</li>
        </ul>
        
        <h2>2. Aceptación de los términos</h2>
        <p>Al usar FUDDO, el usuario declara que:</p>
        <ul>
            <li>Tiene capacidad legal para aceptar estos términos.</li>
            <li>Utilizará la plataforma de manera lícita y de buena fe.</li>
            <li>Proporcionará información veraz, actualizada y completa cuando sea requerida.</li>
            <li>Es responsable del uso que haga de su cuenta, sus credenciales y su operación dentro del sistema.</li>
        </ul>
        <p>Si el usuario actúa en nombre de una empresa, restaurante, bar o establecimiento comercial, declara que tiene autorización para vincular a dicha organización a estos términos.</p>
        
        <h2>3. Descripción del servicio</h2>
        <p>FUDDO ofrece una plataforma digital para apoyar la administración y operación de negocios gastronómicos.</p>
        <p>Las funcionalidades pueden incluir, entre otras:</p>
        <ul>
            <li>Gestión de mesas</li>
            <li>Registro y seguimiento de pedidos</li>
            <li>Comunicación con cocina</li>
            <li>Gestión de inventario</li>
            <li>Reportes y estadísticas</li>
            <li>Control operativo del negocio</li>
            <li>Herramientas administrativas complementarias</li>
        </ul>
        <p>FUDDO podrá actualizar, modificar, mejorar, ampliar, suspender o eliminar funcionalidades en cualquier momento, con o sin previo aviso, cuando ello sea necesario por razones técnicas, comerciales, operativas o de seguridad.</p>
        
        <h2>4. Registro y cuentas de usuario</h2>
        <p>Para acceder a ciertas funciones, el usuario podrá requerir una cuenta.</p>
        <p>El usuario se compromete a:</p>
        <ul>
            <li>Mantener la confidencialidad de sus credenciales de acceso.</li>
            <li>No compartir su usuario o contraseña con terceros no autorizados.</li>
            <li>Notificar de inmediato cualquier uso no autorizado o sospechoso de su cuenta.</li>
            <li>Asegurar que la información de registro sea correcta y esté actualizada.</li>
        </ul>
        <p>FUDDO no será responsable por pérdidas o daños derivados del uso indebido de las credenciales del usuario cuando dicho uso sea atribuible a negligencia, descuido o mala administración del acceso.</p>
        
        <h2>5. Uso permitido</h2>
        <p>El usuario se compromete a utilizar FUDDO únicamente para fines legales y relacionados con la operación legítima de su negocio.</p>
        <p>Está prohibido:</p>
        <ul>
            <li>Utilizar la plataforma para actividades ilícitas, fraudulentas o engañosas.</li>
            <li>Intentar acceder sin autorización a sistemas, servidores, bases de datos o cuentas de terceros.</li>
            <li>Interferir con el funcionamiento normal del servicio.</li>
            <li>Copiar, reproducir, revender, sublicenciar o explotar comercialmente el software o cualquier parte de FUDDO sin autorización expresa.</li>
            <li>Introducir virus, malware, scripts maliciosos o cualquier código que afecte la seguridad o estabilidad del sistema.</li>
            <li>Usar el sistema para almacenar o procesar información cuya gestión viole la ley aplicable.</li>
        </ul>
        <p>FUDDO podrá suspender o restringir el acceso cuando detecte usos indebidos, riesgos de seguridad o incumplimientos de estos términos.</p>
        
        <h2>6. Disponibilidad del servicio</h2>
        <p>FUDDO realiza esfuerzos razonables para mantener la plataforma disponible y operativa. Sin embargo, no garantiza que el servicio esté libre de interrupciones, errores, caídas, demoras o fallos técnicos permanentes o temporales.</p>
        <p>El servicio puede verse afectado por:</p>
        <ul>
            <li>Mantenimiento programado o correctivo</li>
            <li>Fallos de conectividad</li>
            <li>Problemas de terceros proveedores</li>
            <li>Actualizaciones técnicas</li>
            <li>Eventos de fuerza mayor o caso fortuito</li>
        </ul>
        <p>FUDDO no será responsable por interrupciones razonables, temporales o ajenas a su control.</p>
        
        <h2>7. Planes, pagos y facturación</h2>
        <p>En caso de que FUDDO ofrezca servicios pagos, suscripciones, licencias o planes comerciales:</p>
        <ul>
            <li>Los precios, características y condiciones serán informados al usuario antes de la contratación.</li>
            <li>El acceso a ciertas funcionalidades podrá depender del plan adquirido.</li>
            <li>Los pagos deberán realizarse en las condiciones, periodicidad y medios establecidos por FUDDO.</li>
            <li>La falta de pago podrá dar lugar a la suspensión temporal o definitiva del servicio.</li>
        </ul>
        <p>Salvo que se indique expresamente lo contrario, los pagos realizados no son reembolsables una vez activado el servicio, excepto en los casos exigidos por la ley aplicable o cuando FUDDO lo autorice expresamente.</p>
        
        <h2>8. Datos del negocio y responsabilidad del usuario</h2>
        <p>El usuario es responsable de la información que registra o administra dentro de la plataforma, incluyendo, entre otros:</p>
        <ul>
            <li>Productos</li>
            <li>Precios</li>
            <li>Inventarios</li>
            <li>Pedidos</li>
            <li>Datos operativos</li>
            <li>Información de clientes, empleados o proveedores</li>
        </ul>
        <p>El usuario garantiza que cuenta con la autorización y base legal necesaria para cargar, usar y tratar dicha información dentro de FUDDO.</p>
        <p>FUDDO actúa como proveedor tecnológico y no asume responsabilidad por errores operativos, decisiones comerciales, configuraciones incorrectas, pérdidas derivadas de datos ingresados erróneamente o uso inadecuado de la plataforma.</p>
        
        <h2>9. Propiedad intelectual</h2>
        <p>Todos los derechos sobre el sitio web, software, interfaz, diseño, marca, logotipos, estructura, textos, funcionalidades, código, contenido visual y elementos asociados a FUDDO son propiedad de FUDDO o de sus respectivos titulares, y están protegidos por las normas de propiedad intelectual aplicables.</p>
        <p>El uso del servicio no transfiere al usuario ningún derecho de propiedad sobre la plataforma, salvo el derecho limitado, revocable, no exclusivo e intransferible de usarla conforme a estos términos.</p>
        <p>Está prohibido:</p>
        <ul>
            <li>Copiar o replicar el software</li>
            <li>Descompilar o realizar ingeniería inversa</li>
            <li>Modificar o crear obras derivadas</li>
            <li>Usar la marca FUDDO sin autorización</li>
        </ul>
        
        <h2>10. Privacidad y tratamiento de datos</h2>
        <p>El tratamiento de los datos personales se rige por nuestra <a href="<?php echo $BASE_URL; ?>politica-privacidad.php">Política de Privacidad</a>, la cual hace parte integral de estos Términos de Servicio.</p>
        <p>Al usar FUDDO, el usuario reconoce haber leído y aceptado dicha política.</p>
        
        <h2>11. Integraciones y servicios de terceros</h2>
        <p>FUDDO podrá integrarse o interactuar con herramientas, servicios o plataformas de terceros, tales como:</p>
        <ul>
            <li>Pasarelas de pago</li>
            <li>Herramientas de mensajería</li>
            <li>Servicios en la nube</li>
            <li>Analítica</li>
            <li>Software administrativo o contable</li>
        </ul>
        <p>El uso de servicios de terceros puede estar sujeto a sus propios términos, condiciones y políticas. FUDDO no controla ni asume responsabilidad por el funcionamiento, disponibilidad, exactitud o legalidad de dichos servicios externos.</p>
        
        <h2>12. Limitación de responsabilidad</h2>
        <p>En la máxima medida permitida por la ley, FUDDO no será responsable por daños directos, indirectos, incidentales, especiales, consecuenciales o lucro cesante derivados de:</p>
        <ul>
            <li>Uso o imposibilidad de uso del servicio</li>
            <li>Errores de configuración o digitación por parte del usuario</li>
            <li>Interrupciones o fallos técnicos</li>
            <li>Pérdida de datos causada por terceros o por factores ajenos al control razonable de FUDDO</li>
            <li>Decisiones comerciales tomadas por el usuario con base en la información de la plataforma</li>
        </ul>
        <p>El usuario entiende que el uso del servicio se realiza bajo su propia responsabilidad operativa y comercial.</p>
        
        <h2>13. Suspensión o cancelación</h2>
        <p>FUDDO podrá suspender, restringir o cancelar el acceso a la plataforma, temporal o definitivamente, cuando:</p>
        <ul>
            <li>Exista incumplimiento de estos términos</li>
            <li>Se detecte actividad sospechosa o fraudulenta</li>
            <li>Haya uso indebido del sistema</li>
            <li>Se incumplan obligaciones de pago</li>
            <li>Sea necesario proteger la seguridad, estabilidad o integridad del servicio</li>
        </ul>
        <p>El usuario también podrá dejar de usar la plataforma en cualquier momento, sin perjuicio de las obligaciones pendientes que existan.</p>
        
        <h2>14. Modificaciones de los términos</h2>
        <p>FUDDO podrá actualizar estos Términos de Servicio en cualquier momento. Cuando ello ocurra, se publicará la versión actualizada en este sitio web con su respectiva fecha de modificación.</p>
        <p>El uso continuado del servicio después de la publicación de cambios constituye aceptación de los nuevos términos.</p>
        
        <h2>15. Ley aplicable y jurisdicción</h2>
        <p>Estos Términos de Servicio se regirán por las leyes de la República de Colombia.</p>
        <p>Cualquier controversia relacionada con el uso del sitio o los servicios de FUDDO será resuelta conforme a la legislación colombiana y, cuando aplique, ante las autoridades o jueces competentes de Colombia.</p>
        
        <h2>16. Contacto</h2>
        <p>Si tienes preguntas sobre estos Términos de Servicio, puedes contactarnos en:</p>
        <div class="contact-info">
            <p><strong>FUDDO</strong></p>
            <p><strong>Correo:</strong> fuddocol@gmail.com</p>
            <p><strong>Sitio web:</strong> fuddo.co</p>
        </div>
    </div>
    
    <!-- Footer -->
    <footer>
        <p>&copy; 2026 FUDDO - Sistema Profesional de Gestión de Restaurantes. Todos los derechos reservados.</p>
    </footer>
</body>
</html>
