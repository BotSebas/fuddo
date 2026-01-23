# Script PowerShell para hacer backup de archivos originales
# Ejecutar desde la carpeta c:\xampp\htdocs\fuddo

Write-Host "=== BACKUP DE ARCHIVOS ORIGINALES ===" -ForegroundColor Green

# Backup de validar.php
if (Test-Path "validar.php") {
    Copy-Item "validar.php" "validar_original_backup.php"
    Write-Host "✓ Backup: validar.php → validar_original_backup.php" -ForegroundColor Cyan
}

# Backup de conexion.php
if (Test-Path "includes\conexion.php") {
    Copy-Item "includes\conexion.php" "includes\conexion_original_backup.php"
    Write-Host "✓ Backup: conexion.php → conexion_original_backup.php" -ForegroundColor Cyan
}

Write-Host "`n=== BACKUP COMPLETADO ===" -ForegroundColor Green
Write-Host "Los archivos originales están respaldados con sufijo '_original_backup'" -ForegroundColor Yellow
