-- =====================================================
-- Script para permitir que super-admin funcione sin restaurante asociado
-- =====================================================

USE fuddo_master;

-- Paso 1: Eliminar la restricción de clave foránea actual
ALTER TABLE usuarios_master DROP FOREIGN KEY usuarios_master_ibfk_1;

-- Paso 2: Modificar la columna id_restaurante para permitir NULL
ALTER TABLE usuarios_master MODIFY id_restaurante INT NULL;

-- Paso 3: Agregar nuevamente la clave foránea con ON DELETE SET NULL
ALTER TABLE usuarios_master 
ADD CONSTRAINT fk_usuarios_master_restaurante 
FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE SET NULL;

-- Paso 4: Actualizar el usuario super-admin para no tener restaurante (opcional)
-- Descomenta la siguiente línea si quieres que el super-admin no esté asociado a ningún restaurante
-- UPDATE usuarios_master SET id_restaurante = NULL WHERE rol = 'super-admin';

SELECT 'Script ejecutado exitosamente. Super-admin puede funcionar sin restaurante.' as Resultado;
