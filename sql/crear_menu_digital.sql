-- Script para agregar el módulo de Menú Digital
USE mgacgdnjkg;

-- 1. Agregar aplicación de Menú Digital
INSERT INTO aplicaciones (clave, nombre, descripcion, icono, orden) 
VALUES ('menu_digital', 'Menú Digital', 'Crear y gestionar el menú digital público del restaurante', 'fas fa-qrcode', 6)
ON DUPLICATE KEY UPDATE 
    nombre = 'Menú Digital',
    descripcion = 'Crear y gestionar el menú digital público del restaurante',
    icono = 'fas fa-qrcode',
    orden = 6;

SELECT 'Aplicación Menú Digital agregada exitosamente!' as Mensaje;
