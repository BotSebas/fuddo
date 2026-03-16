<?php 
// Redirigir automáticamente al landing moderno o mostrar ambas opciones
// Por ahora, vamos a usar landing.php como la nueva página por defecto
$isNewLanding = true;

if ($isNewLanding) {
    // Redirigir a la nueva landing moderna
    header("Location: landing.php", true, 302);
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elige tu landing - FUDDO</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        h1 { text-align: center; margin-bottom: 40px; color: #27ae60; }
        .options { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px; }
        .option { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
        .option h2 { margin-bottom: 15px; color: #333; }
        .option p { color: #666; margin-bottom: 25px; line-height: 1.6; }
        .option a { display: inline-block; padding: 12px 30px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-weight: 600; transition: background 0.3s; }
        .option a:hover { background: #229954; }
        @media (max-width: 768px) {
            .options { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎯 Elige tu Landing Page</h1>
        <div class="options">
            <div class="option">
                <h2>🚀 Moderna & Pro</h2>
                <p>Nueva landing moderna con diseño profesional, actualizaciones en tiempo real, y conversión optimizada. Perfecta para 2026.</p>
                <a href="landing.php">Ir a Landing Moderna →</a>
            </div>
            <div class="option">
                <h2>📄 Clásica</h2>
                <p>Landing tradicional con el diseño actual de FUDDO. Mantiene todos los elementos originales y estructura probada.</p>
                <a href="index-original.php">Ir a Landing Clásica →</a>
            </div>
        </div>
        <div style="background: white; padding: 30px; border-radius: 12px; margin-top: 30px;">
            <p style="text-align: center; color: #666;">💡 <strong>Recomendación:</strong> Usa la landing moderna por defecto. Ambas se pueden usar simultáneamente.</p>
        </div>
    </div>
</body>
</html>
