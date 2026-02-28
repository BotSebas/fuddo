-- ============================================================
-- CREAR TABLAS DEL SISTEMA DE COSTEO PARA BARROCK
-- Prefijo: fuddo_barrock_
-- ============================================================

-- Tabla de Materias Primas
CREATE TABLE IF NOT EXISTS `fuddo_barrock_materias_primas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_materia_prima` varchar(50) NOT NULL,
    `nombre` varchar(200) NOT NULL,
    `unidad_medida` enum('kg','g','lb','l','ml','und') NOT NULL COMMENT 'Unidad de medida: kg=kilogramo, g=gramo, lb=libra, l=litro, ml=mililitro, und=unidad',
    `cantidad_base_comprada` decimal(10,3) NOT NULL COMMENT 'Cantidad en la unidad de medida original',
    `costo_total_base` decimal(10,2) NOT NULL COMMENT 'Costo total de la cantidad base comprada',
    `costo_por_unidad_minima` decimal(15,6) NOT NULL COMMENT 'Costo en la unidad mínima estándar (g, ml, und)',
    `unidad_minima` varchar(10) NOT NULL COMMENT 'Unidad mínima para cálculos (g, ml, und)',
    `cantidad_en_unidad_minima` decimal(15,3) NOT NULL COMMENT 'Cantidad convertida a la unidad mínima',
    `estado` enum('activo','inactivo') DEFAULT 'activo',
    `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
    `fecha_ultima_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_id_materia_prima` (`id_materia_prima`),
    KEY `idx_unidad_medida` (`unidad_medida`),
    KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Recetas
CREATE TABLE IF NOT EXISTS `fuddo_barrock_recetas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_receta` varchar(50) NOT NULL,
    `nombre_platillo` varchar(200) NOT NULL,
    `descripcion` text,
    `costo_total_receta` decimal(10,2) DEFAULT 0.00 COMMENT 'Costo total calculado de todos los ingredientes',
    `id_producto_asociado` int(11) COMMENT 'FK a la tabla de productos si se crea automáticamente',
    `estado` enum('activo','inactivo') DEFAULT 'activo',
    `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
    `fecha_ultima_actualizacion` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_id_receta` (`id_receta`),
    KEY `idx_estado` (`estado`),
    KEY `idx_producto_asociado` (`id_producto_asociado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Ingredientes de Recetas
CREATE TABLE IF NOT EXISTS `fuddo_barrock_receta_ingredientes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_receta` varchar(50) NOT NULL,
    `id_materia_prima` varchar(50) NOT NULL,
    `cantidad_usada` decimal(10,3) NOT NULL COMMENT 'Cantidad de materia prima usada en la receta',
    `unidad_cantidad` varchar(10) NOT NULL COMMENT 'Unidad en que se mide (g, ml, und)',
    `costo_ingrediente` decimal(10,2) NOT NULL COMMENT 'Costo total del ingrediente en la receta (cantidad x costo unitario)',
    `orden` int(11) DEFAULT 0,
    `nota` text,
    `fecha_creacion` datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_receta` (`id_receta`),
    KEY `idx_materia_prima` (`id_materia_prima`),
    FOREIGN KEY (`id_receta`) REFERENCES `fuddo_barrock_recetas` (`id_receta`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`id_materia_prima`) REFERENCES `fuddo_barrock_materias_primas` (`id_materia_prima`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índice adicional para búsquedas rápidas
CREATE INDEX idx_receta_materia ON `fuddo_barrock_receta_ingredientes` (`id_receta`, `id_materia_prima`);

-- Confirmar
SELECT 'Tablas creadas exitosamente para Barrock' as Resultado;
