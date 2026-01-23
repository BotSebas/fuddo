-- Sistema de permisos de aplicaciones por restaurante
USE fuddo_master;

-- Tabla de aplicaciones/módulos disponibles en el sistema
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

-- Tabla de relación restaurante-aplicaciones (permisos)
CREATE TABLE IF NOT EXISTS restaurante_aplicaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_restaurante INT NOT NULL,
    id_aplicacion INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_aplicacion) REFERENCES aplicaciones(id) ON DELETE CASCADE,
    UNIQUE KEY unique_restaurante_aplicacion (id_restaurante, id_aplicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar aplicaciones base del sistema
INSERT INTO aplicaciones (clave, nombre, descripcion, icono, orden) VALUES
('mesas', 'Mesas', 'Gestión de mesas y servicios del restaurante', 'fas fa-utensils', 1),
('productos', 'Productos', 'Administración de productos e inventario', 'fas fa-box', 2),
('cocina', 'Cocina', 'Vista de pedidos para cocina', 'fas fa-fire', 3),
('reportes', 'Reportes', 'Reportes y estadísticas de ventas', 'fas fa-chart-bar', 4),
('pedidos', 'Pedidos', 'Gestión de pedidos', 'fas fa-shopping-cart', 5);

SELECT 'Sistema de permisos creado exitosamente!' as Mensaje;
