-- PASO 6: Sincronizar usuario en BD local
-- Ejecutar este SQL en phpMyAdmin

USE fuddo_herenciaargentina;

-- Verificar si el usuario admin ya existe
SELECT * FROM usuarios WHERE usuario = 'admin';

-- Si NO existe, ejecutar el INSERT:
INSERT INTO usuarios (usuario, password, nombre, email, rol, estado) 
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Administrador FUDDO',
    'admin@fuddo.co',
    'fuddo-admin',
    'activo'
)
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    rol = 'fuddo-admin',
    estado = 'activo';

-- Verificar que se creó correctamente
SELECT id, usuario, nombre, rol, estado FROM usuarios WHERE usuario = 'admin';
