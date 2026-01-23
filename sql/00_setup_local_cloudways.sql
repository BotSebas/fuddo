-- ============================================
-- SETUP LOCAL PARA SIMULAR CLOUDWAYS
-- Este script crea BD y usuario igual que en Cloudways
-- para poder probar localmente antes del deployment
-- ============================================

-- 1. Crear base de datos
DROP DATABASE IF EXISTS mgacgdnjkg;
CREATE DATABASE mgacgdnjkg CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 2. Crear usuario con las mismas credenciales de Cloudways
DROP USER IF EXISTS 'mgacgdnjkg'@'localhost';
CREATE USER 'mgacgdnjkg'@'localhost' IDENTIFIED BY 'HPESTrrt4t';

-- 3. Otorgar todos los permisos sobre la BD
GRANT ALL PRIVILEGES ON mgacgdnjkg.* TO 'mgacgdnjkg'@'localhost';
FLUSH PRIVILEGES;

-- 4. Usar la base de datos
USE mgacgdnjkg;

-- 5. Mostrar información
SELECT 'Base de datos creada exitosamente!' as Mensaje;
SELECT DATABASE() as 'Base de Datos Actual';
SELECT USER() as 'Usuario Actual';
SHOW TABLES;
