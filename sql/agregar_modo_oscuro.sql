-- Agregar campo modo_oscuro a tabla menu_digital
USE mgacgdnjkg;

ALTER TABLE `fuddo_barrock_menu_digital` 
ADD COLUMN `modo_oscuro` TINYINT(1) DEFAULT 0 COMMENT 'Modo oscuro (0=claro, 1=oscuro)' 
AFTER `color_tema`;

SELECT 'Campo modo_oscuro agregado exitosamente' as Mensaje;
