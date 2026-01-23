-- Sistema de permisos de reportes por restaurante
USE fuddo_master;

-- Tabla de reportes disponibles en el sistema
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

-- Tabla de relación restaurante-reportes (permisos)
CREATE TABLE IF NOT EXISTS restaurante_reportes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_restaurante INT NOT NULL,
    id_reporte INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id) ON DELETE CASCADE,
    FOREIGN KEY (id_reporte) REFERENCES reportes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_restaurante_reporte (id_restaurante, id_reporte)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar reportes base del sistema
INSERT INTO reportes (clave, nombre, descripcion, archivo, icono, orden) VALUES
('cierre_caja', 'Cierre de Caja', 'Reporte detallado de ventas por día, semana o mes con desglose por método de pago y productos', 'cierre_caja.php', 'fas fa-cash-register', 1),
('inventario_valorizado', 'Inventario Valorizado', 'Valorización del inventario con alertas de stock bajo, crítico y sin existencias', 'inventario_valorizado.php', 'fas fa-boxes', 2);

SELECT 'Sistema de permisos de reportes creado exitosamente!' as Mensaje;
