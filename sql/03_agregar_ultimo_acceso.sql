-- Verificar si la columna ultimo_acceso existe en usuarios_master
-- Si NO existe, ejecutar el ALTER TABLE

USE fuddo_master;

-- Agregar columna ultimo_acceso si no existe
ALTER TABLE usuarios_master 
ADD COLUMN IF NOT EXISTS ultimo_acceso TIMESTAMP NULL DEFAULT NULL;

-- Verificar la estructura
DESCRIBE usuarios_master;
