-- Agregar columna foto a usuarios_master
USE fuddo_master;

ALTER TABLE usuarios_master 
ADD COLUMN foto VARCHAR(255) NULL AFTER email;
