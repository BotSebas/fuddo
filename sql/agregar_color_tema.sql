-- Agregar campo color_tema a tabla menu_digital existente
USE mgacgdnjkg;

ALTER TABLE `fuddo_barrock_menu_digital` 
ADD COLUMN `color_tema` varchar(20) DEFAULT 'verde' COMMENT 'Tema de color del menú público' 
AFTER `orden`;

SELECT 'Campo color_tema agregado exitosamente' as Mensaje;
