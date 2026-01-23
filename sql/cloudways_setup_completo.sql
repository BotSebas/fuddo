-- CONFIGURACIÓN COMPLETA PARA CLOUDWAYS (UNA SOLA BASE DE DATOS)
-- Ejecutar este archivo en la base de datos mgacgdnjkg

-- =====================================================
-- PARTE 1: TABLAS DEL SISTEMA MASTER
-- =====================================================

-- Tabla de restaurantes
CREATE TABLE IF NOT EXISTS restaurantes (
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
CREATE TABLE IF NOT EXISTS usuarios_master (
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

-- Tabla de aplicaciones/módulos disponibles
CREATE TABLE IF NOT EXISTS aplicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(50),
    orden INT DEFAULT 0,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de permisos de aplicaciones por restaurante
CREATE TABLE IF NOT EXISTS restaurante_aplicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_restaurante INT NOT NULL,
    id_aplicacion INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_aplicacion) REFERENCES aplicaciones(id) ON DELETE CASCADE,
    UNIQUE KEY unique_restaurante_aplicacion (id_restaurante, id_aplicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de reportes disponibles
CREATE TABLE IF NOT EXISTS reportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clave VARCHAR(50) UNIQUE NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    archivo VARCHAR(100) NOT NULL,
    icono VARCHAR(50),
    orden INT DEFAULT 0,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de permisos de reportes por restaurante
CREATE TABLE IF NOT EXISTS restaurante_reportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_restaurante INT NOT NULL,
    id_reporte INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_reporte) REFERENCES reportes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_restaurante_reporte (id_restaurante, id_reporte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PARTE 2: TABLAS OPERATIVAS (COMPARTIDAS POR TODOS LOS RESTAURANTES)
-- =====================================================

-- Mesas (con id_restaurante)
CREATE TABLE IF NOT EXISTS mesas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_restaurante INT NOT NULL,
    id_mesa VARCHAR(50) NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    ubicacion VARCHAR(50) DEFAULT NULL,
    estado ENUM('libre','ocupada','reservada','inactiva') DEFAULT 'libre',
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_mesa_restaurante (id_restaurante, id_mesa),
    INDEX idx_restaurante (id_restaurante)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Productos (con id_restaurante)
CREATE TABLE IF NOT EXISTS productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_restaurante INT NOT NULL,
    id_producto VARCHAR(50) NOT NULL,
    nombre_producto VARCHAR(150) NOT NULL,
    valor_sin_iva DECIMAL(10,2) NOT NULL,
    valor_con_iva DECIMAL(10,2) NOT NULL,
    inventario INT NOT NULL DEFAULT 0,
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_producto_restaurante (id_restaurante, id_producto),
    INDEX idx_restaurante (id_restaurante)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Servicios (con id_restaurante)
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_restaurante INT NOT NULL,
    id_servicio VARCHAR(50) NOT NULL,
    id_mesa VARCHAR(50) NOT NULL,
    id_producto VARCHAR(150) NOT NULL,
    cantidad INT NOT NULL,
    valor_unitario DECIMAL(10,2) NOT NULL,
    valor_total DECIMAL(10,2) NOT NULL,
    fecha_servicio DATE NOT NULL,
    hora_servicio TIME NOT NULL,
    estado ENUM('activo','finalizado','cancelado') DEFAULT 'activo',
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE CASCADE,
    INDEX idx_restaurante (id_restaurante),
    INDEX idx_servicio (id_servicio),
    INDEX idx_fecha (fecha_servicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Servicios Total (con id_restaurante)
CREATE TABLE IF NOT EXISTS servicios_total (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_restaurante INT NOT NULL,
    id_servicio VARCHAR(50) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    metodo_pago ENUM('efectivo','llave','nequi','daviplata','tarjeta') NOT NULL DEFAULT 'efectivo',
    fecha_servicio DATE NOT NULL,
    hora_cierre_servicio TIME NOT NULL,
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE CASCADE,
    INDEX idx_restaurante (id_restaurante),
    INDEX idx_servicio (id_servicio),
    INDEX idx_fecha (fecha_servicio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PARTE 3: DATOS INICIALES
-- =====================================================

-- Insertar usuario super-admin (CAMBIAR PASSWORD)
INSERT INTO usuarios_master (usuario, password, nombre, email, rol, estado) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador FUDDO', 'admin@fuddo.com', 'super-admin', 'activo')
ON DUPLICATE KEY UPDATE usuario=usuario;

-- Insertar aplicaciones base
INSERT INTO aplicaciones (clave, nombre, descripcion, icono, orden) VALUES
('mesas', 'Mesas', 'Gestión de mesas y servicios del restaurante', 'fas fa-utensils', 1),
('productos', 'Productos', 'Administración de productos e inventario', 'fas fa-box', 2),
('cocina', 'Cocina', 'Vista de pedidos para cocina', 'fas fa-fire', 3),
('reportes', 'Reportes', 'Reportes y estadísticas de ventas', 'fas fa-chart-bar', 4),
('pedidos', 'Pedidos', 'Gestión de pedidos', 'fas fa-shopping-cart', 5)
ON DUPLICATE KEY UPDATE clave=clave;

-- Insertar reportes base
INSERT INTO reportes (clave, nombre, descripcion, archivo, icono, orden) VALUES
('cierre_caja', 'Cierre de Caja', 'Reporte detallado de ventas por día, semana o mes con desglose por método de pago y productos', 'cierre_caja.php', 'fas fa-cash-register', 1),
('inventario_valorizado', 'Inventario Valorizado', 'Valorización del inventario con alertas de stock bajo, crítico y sin existencias', 'inventario_valorizado.php', 'fas fa-boxes', 2)
ON DUPLICATE KEY UPDATE clave=clave;

-- =====================================================
-- CONFIGURACIÓN COMPLETADA
-- =====================================================

SELECT 'Base de datos configurada exitosamente para Cloudways!' as Mensaje;
