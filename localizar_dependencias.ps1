# Script para localizar todas las dependencias externas de CDN
# Descarga los recursos necesarios y actualiza las rutas en los archivos

Write-Host "=== LOCALIZADOR DE DEPENDENCIAS FUDDO ===" -ForegroundColor Green
Write-Host ""

# Crear carpeta para recursos descargados si no existe
$rutaAssets = Join-Path $PSScriptRoot "assets"
$rutaVendor = Join-Path $rutaAssets "vendor"

if (!(Test-Path $rutaVendor)) {
    New-Item -ItemType Directory -Path $rutaVendor -Force | Out-Null
}

# 1. DESCARGAR SWEETALERT2 (usado en usuarios y restaurantes)
Write-Host "1. Descargando SweetAlert2..." -ForegroundColor Yellow

$sweetalertPath = Join-Path $rutaVendor "sweetalert2"
if (!(Test-Path $sweetalertPath)) {
    New-Item -ItemType Directory -Path $sweetalertPath -Force | Out-Null
    
    try {
        # Descargar CSS
        $sweetalertCssUrl = "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css"
        $sweetalertCssFile = Join-Path $sweetalertPath "sweetalert2.min.css"
        Invoke-WebRequest -Uri $sweetalertCssUrl -OutFile $sweetalertCssFile -UseBasicParsing
        Write-Host "  ✓ Descargado: sweetalert2.min.css" -ForegroundColor Green
        
        # Descargar JS
        $sweetalertJsUrl = "https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"
        $sweetalertJsFile = Join-Path $sweetalertPath "sweetalert2.all.min.js"
        Invoke-WebRequest -Uri $sweetalertJsUrl -OutFile $sweetalertJsFile -UseBasicParsing
        Write-Host "  ✓ Descargado: sweetalert2.all.min.js" -ForegroundColor Green
    } catch {
        Write-Host "  ✗ Error descargando SweetAlert2: $_" -ForegroundColor Red
    }
} else {
    Write-Host "  ✓ SweetAlert2 ya existe localmente" -ForegroundColor Cyan
}

# 2. DESCARGAR LINEARICONS (usado en index.php)
Write-Host ""
Write-Host "2. Descargando Linearicons..." -ForegroundColor Yellow

$lineariconPath = Join-Path $rutaVendor "linearicons"
if (!(Test-Path $lineariconPath)) {
    New-Item -ItemType Directory -Path $lineariconPath -Force | Out-Null
    
    try {
        $lineariconUrl = "https://cdn.linearicons.com/free/1.0.0/icon-font.min.css"
        $lineariconFile = Join-Path $lineariconPath "linearicons.min.css"
        Invoke-WebRequest -Uri $lineariconUrl -OutFile $lineariconFile -UseBasicParsing
        Write-Host "  ✓ Descargado: linearicons.min.css" -ForegroundColor Green
        
        # Nota: Las fuentes también necesitan descargarse manualmente
        Write-Host "  ⚠ Las fuentes de Linearicons deben descargarse del sitio oficial" -ForegroundColor Yellow
    } catch {
        Write-Host "  ✗ Error descargando Linearicons: $_" -ForegroundColor Red
    }
} else {
    Write-Host "  ✓ Linearicons ya existe localmente" -ForegroundColor Cyan
}

# 3. VERIFICAR PLUGINS LOCALES EXISTENTES
Write-Host ""
Write-Host "3. Verificando plugins locales..." -ForegroundColor Yellow

$pluginsCheck = @{
    "Bootstrap" = "plugins\bootstrap\css\bootstrap.min.css"
    "jQuery" = "plugins\jquery\jquery.min.js"
    "FontAwesome" = "plugins\fontawesome-free\css\all.min.css"
}

foreach ($plugin in $pluginsCheck.GetEnumerator()) {
    $rutaPlugin = Join-Path $PSScriptRoot $plugin.Value
    if (Test-Path $rutaPlugin) {
        Write-Host "  ✓ $($plugin.Key) - Local OK" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $($plugin.Key) - NO ENCONTRADO" -ForegroundColor Red
    }
}

# 4. MOSTRAR ARCHIVOS QUE NECESITAN ACTUALIZACIÓN
Write-Host ""
Write-Host "=== ARCHIVOS QUE NECESITAN ACTUALIZACIÓN MANUAL ===" -ForegroundColor Cyan
Write-Host ""

Write-Host "1. includes/menu.php" -ForegroundColor White
Write-Host "   Reemplazar:" -ForegroundColor Yellow
Write-Host '   <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">'
Write-Host '   <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">'
Write-Host "   Por:" -ForegroundColor Green
Write-Host '   <!-- Fonts locales o eliminar si no son críticas -->'
Write-Host ""

Write-Host "2. index.php (Landing Page)" -ForegroundColor White
Write-Host "   Reemplazar:" -ForegroundColor Yellow
Write-Host '   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">'
Write-Host "   Por:" -ForegroundColor Green
Write-Host '   <link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">'
Write-Host ""
Write-Host "   Reemplazar:" -ForegroundColor Yellow
Write-Host '   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">'
Write-Host "   Por:" -ForegroundColor Green
Write-Host '   <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">'
Write-Host ""
Write-Host "   Reemplazar:" -ForegroundColor Yellow
Write-Host '   <link rel="stylesheet" href="https://cdn.linearicons.com/free/1.0.0/icon-font.min.css">'
Write-Host "   Por:" -ForegroundColor Green
Write-Host '   <link rel="stylesheet" href="assets/vendor/linearicons/linearicons.min.css">'
Write-Host ""

Write-Host "3. usuarios/usuarios.php y restaurantes/restaurantes.php" -ForegroundColor White
Write-Host "   Reemplazar:" -ForegroundColor Yellow
Write-Host '   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>'
Write-Host "   Por:" -ForegroundColor Green
Write-Host '   <script src="<?php echo $BASE_URL; ?>assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>'
Write-Host ""

Write-Host "=== RESUMEN ===" -ForegroundColor Green
Write-Host ""
Write-Host "Recursos descargados:" -ForegroundColor Cyan
Write-Host "  ✓ assets/vendor/sweetalert2/" -ForegroundColor Green
Write-Host "  ✓ assets/vendor/linearicons/" -ForegroundColor Green
Write-Host ""
Write-Host "Próximos pasos:" -ForegroundColor Yellow
Write-Host "  1. Actualizar manualmente las rutas en los archivos listados arriba"
Write-Host "  2. (Opcional) Descargar fuentes de Google Fonts si se necesitan offline"
Write-Host "  3. (Opcional) Descargar Ionicons si se usan"
Write-Host ""
Write-Host "¿Quieres que actualice automáticamente los archivos? (S/N)" -ForegroundColor Cyan
