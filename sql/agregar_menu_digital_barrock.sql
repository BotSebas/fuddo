-- Script para agregar tabla menu_digital a restaurante existente 'barrock'
USE mgacgdnjkg;

CREATE TABLE IF NOT EXISTS `fuddo_barrock_menu_digital` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `titulo_seccion` varchar(100) NOT NULL,
    `productos_ids` text NOT NULL COMMENT 'IDs de productos separados por comas',
    `orden` int(11) NOT NULL DEFAULT 0,
    `estado` enum('activo','inactivo') DEFAULT 'activo',
    `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
    `fecha_actualizacion` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SELECT 'Tabla menu_digital creada para restaurante barrock' as Mensaje;
