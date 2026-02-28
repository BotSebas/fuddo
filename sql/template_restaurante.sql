-- TEMPLATE PARA CREAR TABLAS DE UN RESTAURANTE
-- Este archivo será usado por crear_restaurante.php
-- Reemplazar {PREFIX} con fuddo_{identificador}_

-- Tabla de mesas
CREATE TABLE IF NOT EXISTS `{PREFIX}mesas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_mesa` varchar(50) NOT NULL,
    `nombre` varchar(50) NOT NULL,
    `ubicacion` varchar(50) DEFAULT NULL,
    `estado` enum('libre','ocupada','reservada','inactiva') DEFAULT 'libre',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_id_mesa` (`id_mesa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS `{PREFIX}productos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_producto` varchar(50) NOT NULL,
    `nombre_producto` varchar(150) NOT NULL,
    `costo_producto` decimal(10,2) NOT NULL DEFAULT 0.00,
    `valor_sin_iva` decimal(10,2) NOT NULL,
    `valor_con_iva` decimal(10,2) NOT NULL,
    `inventario` int(11) NOT NULL DEFAULT 0,
    `minimo_inventario` int(11) NOT NULL DEFAULT 2,
    `estado` enum('activo','inactivo') DEFAULT 'activo',
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_id_producto` (`id_producto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de servicios
CREATE TABLE IF NOT EXISTS `{PREFIX}servicios` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_servicio` varchar(50) NOT NULL,
    `id_mesa` varchar(50) NOT NULL,
    `id_producto` varchar(150) NOT NULL,
    `cantidad` int(11) NOT NULL,
    `valor_unitario` decimal(10,2) NOT NULL,
    `valor_total` decimal(10,2) NOT NULL,
    `fecha_servicio` date NOT NULL,
    `hora_servicio` time NOT NULL,
    `estado` enum('activo','finalizado','cancelado') DEFAULT 'activo',
    PRIMARY KEY (`id`),
    KEY `idx_servicio` (`id_servicio`),
    KEY `idx_fecha` (`fecha_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de servicios totales
CREATE TABLE IF NOT EXISTS `{PREFIX}servicios_total` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_servicio` varchar(50) NOT NULL,
    `total` decimal(10,2) NOT NULL,
    `metodo_pago` enum('efectivo','llave','nequi','daviplata','tarjeta') NOT NULL DEFAULT 'efectivo',
    `fecha_servicio` date NOT NULL,
    `hora_cierre_servicio` time NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_servicio` (`id_servicio`),
    KEY `idx_fecha` (`fecha_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de comandas (productos de comandas)
CREATE TABLE IF NOT EXISTS `{PREFIX}comandas` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_comanda` varchar(50) NOT NULL,
    `id_producto` varchar(150) NOT NULL,
    `cantidad` int(11) NOT NULL,
    `valor_unitario` decimal(10,2) NOT NULL,
    `valor_total` decimal(10,2) NOT NULL,
    `fecha_servicio` date NOT NULL,
    `hora_servicio` time NOT NULL,
    `estado` enum('activo','finalizado','cancelado') DEFAULT 'activo',
    PRIMARY KEY (`id`),
    KEY `idx_comanda` (`id_comanda`),
    KEY `idx_fecha` (`fecha_servicio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de comandas_total (totales de comandas cerradas)
CREATE TABLE IF NOT EXISTS `{PREFIX}comandas_total` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_comanda` varchar(50) NOT NULL,
    `total` decimal(10,2) NOT NULL,
    `metodo_pago` enum('efectivo','llave','nequi','daviplata','tarjeta') NOT NULL DEFAULT 'efectivo',
    `fecha_comanda` date NOT NULL,
    `hora_cierre_comanda` time NOT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_comanda` (`id_comanda`),
    KEY `idx_fecha` (`fecha_comanda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de menú digital (bloques/secciones del menú público)
CREATE TABLE IF NOT EXISTS `{PREFIX}menu_digital` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `titulo_seccion` varchar(100) NOT NULL,
    `productos_ids` text NOT NULL COMMENT 'IDs de productos separados por comas',
    `orden` int(11) NOT NULL DEFAULT 0,
    `color_tema` varchar(20) DEFAULT 'verde' COMMENT 'Tema de color del menú público',
    `modo_oscuro` TINYINT(1) DEFAULT 0 COMMENT 'Modo oscuro (0=claro, 1=oscuro)',
    `logo_menu` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del archivo del logo del menú',
    `facebook` VARCHAR(255) DEFAULT NULL,
    `instagram` VARCHAR(255) DEFAULT NULL,
    `tiktok` VARCHAR(255) DEFAULT NULL,
    `youtube` VARCHAR(255) DEFAULT NULL,
    `whatsapp` VARCHAR(20) DEFAULT NULL,
    `estado` enum('activo','inactivo') DEFAULT 'activo',
    `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Materias Primas
CREATE TABLE IF NOT EXISTS `{PREFIX}materias_primas` (
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
CREATE TABLE IF NOT EXISTS `{PREFIX}recetas` (
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
CREATE TABLE IF NOT EXISTS `{PREFIX}receta_ingredientes` (
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
    FOREIGN KEY (`id_receta`) REFERENCES `{PREFIX}recetas` (`id_receta`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`id_materia_prima`) REFERENCES `{PREFIX}materias_primas` (`id_materia_prima`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índice adicional para búsquedas rápidas
CREATE INDEX idx_receta_materia ON `{PREFIX}receta_ingredientes` (`id_receta`, `id_materia_prima`);


