<?php
include 'lang/idiomas.php';
include 'includes/url.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad - FUDDO</title>
    <meta name="description" content="Política de Privacidad de FUDDO - Conoce cómo protegemos tus datos">
    
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
        <h1>Política de Privacidad</h1>
        <p class="last-update"><strong>Última actualización:</strong> 25 de marzo de 2026</p>
        
        <p>En FUDDO valoramos tu privacidad y nos comprometemos a proteger la información personal que compartes con nosotros.</p>
        
        <p>Esta Política de Privacidad explica cómo recopilamos, usamos, almacenamos, protegemos y, en ciertos casos, compartimos la información cuando visitas fuddo.co, te comunicas con nosotros o utilizas nuestros servicios.</p>
        
        <p>Al usar nuestro sitio web o plataforma, aceptas esta Política de Privacidad.</p>
        
        <h2>1. Responsable del tratamiento</h2>
        <p>El responsable del tratamiento de los datos personales es:</p>
        <div class="contact-info">
            <p><strong>FUDDO</strong></p>
            <p><strong>Correo:</strong> fuddocol@gmail.com</p>
            <p><strong>Sitio web:</strong> fuddo.co</p>
        </div>
        
        <h2>2. Información que podemos recopilar</h2>
        <p>Podemos recopilar información personal y comercial cuando interactúas con nuestro sitio o servicios, incluyendo:</p>
        
        <h3>a) Información de contacto</h3>
        <ul>
            <li>Nombre y apellidos</li>
            <li>Nombre del negocio o empresa</li>
            <li>Correo electrónico</li>
            <li>Número de teléfono</li>
            <li>Ciudad o país</li>
        </ul>
        
        <h3>b) Información comercial y operativa</h3>
        <ul>
            <li>Tipo de negocio</li>
            <li>Tamaño del establecimiento</li>
            <li>Información sobre el uso del sistema</li>
            <li>Datos relacionados con configuración, operación y soporte</li>
        </ul>
        
        <h3>c) Información técnica</h3>
        <ul>
            <li>Dirección IP</li>
            <li>Tipo de navegador</li>
            <li>Dispositivo utilizado</li>
            <li>Sistema operativo</li>
            <li>Fecha, hora y actividad de navegación</li>
            <li>Cookies y tecnologías similares</li>
        </ul>
        
        <h3>d) Información enviada voluntariamente</h3>
        <ul>
            <li>Mensajes enviados mediante formularios de contacto</li>
            <li>Solicitudes de demostración</li>
            <li>Soporte técnico</li>
            <li>Comentarios o comunicaciones comerciales</li>
        </ul>
        
        <h2>3. Finalidades del tratamiento</h2>
        <p>Usamos la información recopilada para las siguientes finalidades:</p>
        <ul>
            <li>Atender solicitudes de contacto, cotización o demostración</li>
            <li>Crear, administrar o dar soporte a cuentas de usuario</li>
            <li>Prestar, mantener y mejorar nuestros servicios</li>
            <li>Brindar soporte técnico y atención al cliente</li>
            <li>Enviar información comercial, novedades o comunicaciones relacionadas con FUDDO</li>
            <li>Gestionar la relación contractual o precontractual con clientes</li>
            <li>Analizar el uso del sitio y mejorar la experiencia del usuario</li>
            <li>Cumplir obligaciones legales, contractuales o regulatorias</li>
            <li>Proteger la seguridad, estabilidad e integridad de la plataforma</li>
        </ul>
        
        <h2>4. Base legal del tratamiento</h2>
        <p>Tratamos la información personal con fundamento en una o varias de las siguientes bases:</p>
        <ul>
            <li>La autorización otorgada por el titular</li>
            <li>La necesidad de ejecutar una relación contractual o precontractual</li>
            <li>El cumplimiento de obligaciones legales</li>
            <li>El interés legítimo de FUDDO en mejorar y operar sus servicios, siempre respetando los derechos del titular</li>
        </ul>
        
        <h2>5. Datos ingresados por clientes dentro de la plataforma</h2>
        <p>Si un cliente de FUDDO utiliza la plataforma para registrar información de su negocio, empleados, proveedores o clientes finales, dicho cliente será responsable de contar con la autorización o base legal necesaria para el tratamiento de esa información.</p>
        
        <p>En esos casos, FUDDO podrá actuar como encargado del tratamiento o proveedor tecnológico, en la medida en que procese dicha información por cuenta del cliente para la prestación del servicio.</p>
        
        <h2>6. Compartición de información</h2>
        <p><strong>FUDDO no vende datos personales a terceros.</strong></p>
        
        <p>Podremos compartir información únicamente cuando sea necesario para:</p>
        <ul>
            <li>Operar el servicio</li>
            <li>Prestar soporte técnico</li>
            <li>Cumplir obligaciones legales</li>
            <li>Utilizar proveedores tecnológicos o de infraestructura</li>
            <li>Proteger nuestros derechos, usuarios o sistemas</li>
        </ul>
        
        <p>Esto puede incluir proveedores de:</p>
        <ul>
            <li>Hosting o almacenamiento en la nube</li>
            <li>Analítica</li>
            <li>Correo electrónico</li>
            <li>Seguridad</li>
            <li>Mensajería</li>
            <li>Integraciones técnicas</li>
        </ul>
        
        <p>En todos los casos, procuramos trabajar con proveedores que mantengan estándares razonables de seguridad y confidencialidad.</p>
        
        <h2>7. Cookies y tecnologías similares</h2>
        <p>Nuestro sitio puede utilizar cookies y herramientas similares para:</p>
        <ul>
            <li>Recordar preferencias</li>
            <li>Analizar tráfico y comportamiento de navegación</li>
            <li>Mejorar el rendimiento del sitio</li>
            <li>Facilitar ciertas funcionalidades</li>
        </ul>
        <p>El usuario puede configurar su navegador para rechazar o eliminar cookies. Sin embargo, esto podría afectar el correcto funcionamiento de algunas partes del sitio.</p>
        
        <h2>8. Conservación de la información</h2>
        <p>Conservaremos la información personal únicamente durante el tiempo que sea necesario para cumplir las finalidades descritas en esta política, atender obligaciones legales, contractuales, contables, de soporte o resolver controversias.</p>
        
        <p>Cuando la información ya no sea necesaria, podrá ser eliminada, anonimizada o bloqueada conforme a criterios técnicos y legales aplicables.</p>
        
        <h2>9. Seguridad de la información</h2>
        <p>FUDDO adopta medidas razonables de seguridad técnicas, administrativas y organizativas para proteger la información contra acceso no autorizado, pérdida, alteración, uso indebido o divulgación no permitida.</p>
        
        <p>No obstante, ningún sistema es completamente invulnerable, por lo que no puede garantizarse seguridad absoluta en entornos digitales o transmisiones por internet.</p>
        
        <h2>10. Derechos del titular de los datos</h2>
        <p>De conformidad con la legislación aplicable, el titular de los datos personales puede ejercer, entre otros, los siguientes derechos:</p>
        <ul>
            <li>Conocer, actualizar y rectificar sus datos personales</li>
            <li>Solicitar prueba de la autorización otorgada, cuando aplique</li>
            <li>Ser informado sobre el uso dado a sus datos</li>
            <li>Solicitar la supresión de sus datos cuando sea procedente</li>
            <li>Revocar la autorización cuando aplique</li>
            <li>Presentar consultas o reclamos relacionados con el tratamiento de sus datos</li>
        </ul>
        
        <p>Para ejercer estos derechos, puedes escribirnos a: <strong>fuddocol@gmail.com</strong></p>
        
        <h2>11. Menores de edad</h2>
        <p>FUDDO no está dirigido a menores de edad y no recopila intencionalmente datos personales de menores sin la autorización correspondiente de sus representantes legales.</p>
        
        <p>Si detectamos que se ha recopilado información de un menor de forma indebida, podremos eliminarla de manera razonable.</p>
        
        <h2>12. Transferencia nacional o internacional de datos</h2>
        <p>En algunos casos, la información podrá ser almacenada o procesada en infraestructura tecnológica ubicada dentro o fuera de Colombia, especialmente cuando se utilicen servicios en la nube o proveedores internacionales.</p>
        
        <p>Al utilizar FUDDO, el usuario entiende y acepta que dicha información podrá ser tratada bajo estándares de protección razonables y conforme a las necesidades operativas del servicio.</p>
        
        <h2>13. Cambios a esta política</h2>
        <p>FUDDO podrá actualizar esta Política de Privacidad en cualquier momento para reflejar cambios legales, técnicos, operativos o comerciales.</p>
        
        <p>La versión vigente será siempre la publicada en este sitio web, junto con su fecha de última actualización.</p>
        
        <h2>14. Contacto</h2>
        <p>Si tienes preguntas, solicitudes, consultas o reclamos relacionados con esta Política de Privacidad o con el tratamiento de tus datos personales, puedes escribirnos a:</p>
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
