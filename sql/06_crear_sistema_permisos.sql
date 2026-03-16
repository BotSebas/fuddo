-- Sistema de permisos de aplicaciones por restaurante
USE fuddo_master;

-- Tabla de aplicaciones/m├│dulos disponibles en el sistema
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

-- Tabla de relaci├│n restaurante-aplicaciones (permisos)
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
('mesas', 'Mesas', 'Gesti├│n de mesas y servicios del restaurante', 'fas fa-utensils', 1),
('productos', 'Productos', 'Administraci├│n de productos e inventario', 'fas fa-box', 2),
('cocina', 'Cocina', 'Vista de pedidos para cocina', 'fas fa-fire', 3),
('reportes', 'Reportes', 'Reportes y estad├¡sticas de ventas', 'fas fa-chart-bar', 4),
('pedidos', 'Pedidos', 'Gesti├│n de pedidos', 'fas fa-shopping-cart', 5);

SELECT 'Sistema de permisos creado exitosamente!' as Mensaje;
