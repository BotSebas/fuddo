-- Agregar campo logo_menu a la tabla menu_digital de todos los restaurantes

-- Para barrock
ALTER TABLE fuddo_barrock_menu_digital 
ADD COLUMN logo_menu VARCHAR(255) DEFAULT NULL AFTER modo_oscuro;

-- Para otros restaurantes, reemplazar 'identificador' con el identificador real
-- ALTER TABLE fuddo_identificador_menu_digital 
-- ADD COLUMN logo_menu VARCHAR(255) DEFAULT NULL AFTER modo_oscuro;
