@echo off
echo ============================================
echo SETUP LOCAL CLOUDWAYS - FUDDO POS
echo ============================================
echo.
echo Este script va a:
echo 1. Crear BD mgacgdnjkg
echo 2. Crear usuario mgacgdnjkg con password HPESTrrt4t
echo 3. Importar estructura de tablas maestras
echo 4. Insertar datos iniciales (super-admin)
echo.
pause

echo.
echo [1/3] Creando base de datos y usuario...
mysql -u root -e "source C:\xampp\htdocs\fuddo\sql\00_setup_local_cloudways.sql"

if %errorlevel% neq 0 (
    echo ERROR: No se pudo crear la base de datos
    pause
    exit /b 1
)

echo.
echo [2/3] Importando estructura de tablas maestras...
mysql -u mgacgdnjkg -pHPESTrrt4t mgacgdnjkg < C:\xampp\htdocs\fuddo\sql\cloudways_master_setup.sql

if %errorlevel% neq 0 (
    echo ERROR: No se pudo importar las tablas
    pause
    exit /b 1
)

echo.
echo [3/3] Verificando instalacion...
mysql -u mgacgdnjkg -pHPESTrrt4t mgacgdnjkg -e "SHOW TABLES; SELECT COUNT(*) as 'Total Restaurantes' FROM restaurantes; SELECT COUNT(*) as 'Total Usuarios' FROM usuarios_master; SELECT COUNT(*) as 'Total Aplicaciones' FROM aplicaciones;"

echo.
echo ============================================
echo INSTALACION COMPLETADA!
echo ============================================
echo.
echo Base de datos: mgacgdnjkg
echo Usuario: mgacgdnjkg
echo Password: HPESTrrt4t
echo.
echo Super-admin creado:
echo   Usuario: admin
echo   Password: admin123
echo.
echo Ahora puedes acceder a:
echo   http://localhost/fuddo/login.php
echo.
pause
