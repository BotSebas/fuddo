# Script para limpiar FUDDO - Version segura
Write-Host "=== LIMPIEZA SEGURA DE PROYECTO FUDDO ===" -ForegroundColor Green
Write-Host ""

# 1. ELIMINAR CARPETAS INNECESARIAS
Write-Host "1. Eliminando carpetas de demos y documentacion..." -ForegroundColor Yellow
$carpetasEliminar = @("docs", "pages", "build", ".github")
foreach ($carpeta in $carpetasEliminar) {
    $ruta = Join-Path $PSScriptRoot $carpeta
    if (Test-Path $ruta) {
        Remove-Item -Path $ruta -Recurse -Force
        Write-Host "  OK Eliminado: $carpeta" -ForegroundColor Green
    }
}

# 2. ELIMINAR HTML DE DEMO
Write-Host ""
Write-Host "2. Eliminando archivos HTML de demo..." -ForegroundColor Yellow
$archivosHtml = @("index.html", "index2.html", "index3.html", "dashboard.html", "starter.html", "caja.html", "cocina.html", "iframe.html", "iframe-dark.html", "inventario.html", "login.html", "menu.html", "mesas.html", "pedidos.html")
foreach ($archivo in $archivosHtml) {
    $ruta = Join-Path $PSScriptRoot $archivo
    if (Test-Path $ruta) {
        Remove-Item -Path $ruta -Force
        Write-Host "  OK Eliminado: $archivo" -ForegroundColor Green
    }
}

# 3. ELIMINAR PHP OBSOLETOS
Write-Host ""
Write-Host "3. Eliminando archivos PHP obsoletos..." -ForegroundColor Yellow
$archivosPhp = @("autenticar_old.php", "validar_old.php")
foreach ($archivo in $archivosPhp) {
    $ruta = Join-Path $PSScriptRoot $archivo
    if (Test-Path $ruta) {
        Remove-Item -Path $ruta -Force
        Write-Host "  OK Eliminado: $archivo" -ForegroundColor Green
    }
}

# 4. ELIMINAR CONFIGS
Write-Host ""
Write-Host "4. Eliminando configs de AdminLTE..." -ForegroundColor Yellow
$archivosConfig = @(".babelrc.js", "package.json", "package-lock.json")
foreach ($archivo in $archivosConfig) {
    $ruta = Join-Path $PSScriptRoot $archivo
    if (Test-Path $ruta) {
        Remove-Item -Path $ruta -Force
        Write-Host "  OK Eliminado: $archivo" -ForegroundColor Green
    }
}

# 5. LIMPIAR IMAGENES
Write-Host ""
Write-Host "5. Limpiando imagenes de ejemplo..." -ForegroundColor Yellow
$rutaDistImg = Join-Path $PSScriptRoot "dist\img"
if (Test-Path $rutaDistImg) {
    $imgs = Get-ChildItem -Path $rutaDistImg -File | Where-Object { $_.Name -notlike "*fuddo*" -and $_.Name -notlike "user*" -and $_.Name -notlike "avatar*" -and $_.Name -ne "boxed-bg.jpg" -and $_.Name -ne "boxed-bg.png" }
    foreach ($img in $imgs) {
        Remove-Item -Path $img.FullName -Force
        Write-Host "  OK Eliminado: dist/img/$($img.Name)" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "=== LIMPIEZA COMPLETADA ===" -ForegroundColor Green
Write-Host "Todos los plugins locales mantenidos" -ForegroundColor Cyan
