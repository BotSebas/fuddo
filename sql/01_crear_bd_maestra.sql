-- PASO 1: Crear base de datos maestra
CREATE DATABASE IF NOT EXISTS fuddo_master CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE fuddo_master;

-- Tabla de restaurantes
CREATE TABLE restaurantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    identificador VARCHAR(50) UNIQUE NOT NULL,
    nombre_bd VARCHAR(64) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    email VARCHAR(100),
    estado ENUM('activo', 'inactivo', 'suspendido') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATE NULL,
    plan ENUM('basico', 'premium', 'enterprise') DEFAULT 'basico',
    ultimo_acceso TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios maestros
CREATE TABLE usuarios_master (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    foto VARCHAR(255) NULL,
    id_restaurante INT NULL,
    rol ENUM('super-admin', 'admin-restaurante') DEFAULT 'admin-restaurante',
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ultimo_acceso TIMESTAMP NULL,
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE SET NULL,
    INDEX idx_usuario (usuario),
    INDEX idx_restaurante (id_restaurante)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- IMPORTANTE: Cambiar 'tupassword123' por tu contraseña deseada
-- Ejecutar en consola PHP para generar el hash:
-- echo password_hash('tupassword123', PASSWORD_DEFAULT);

-- Insertar primer restaurante (tu BD actual)
INSERT INTO restaurantes (nombre, identificador, nombre_bd, contacto, email, estado) 
VALUES ('Restaurante Principal', 'principal', 'fuddo_herenciaargentina', 'Admin', 'admin@fuddo.co', 'activo');

-- Insertar super-admin (REEMPLAZA el hash con el generado arriba)
INSERT INTO usuarios_master (usuario, password, nombre, email, id_restaurante, rol, estado) 
VALUES (
    'admin', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Administrador FUDDO', 
    'admin@fuddo.co', 
    1, 
    'super-admin', 
    'activo'
);

SELECT 'Base de datos maestra creada exitosamente!' as Mensaje;
