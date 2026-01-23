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
