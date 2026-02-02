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
    `estado` enum('activo','inactivo') DEFAULT 'activo',
    `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

