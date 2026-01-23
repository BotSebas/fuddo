-- Esquema base para cada restaurante
-- Este archivo se ejecuta al crear un nuevo restaurante

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Tabla de mesas
CREATE TABLE IF NOT EXISTS `mesas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_mesa` varchar(50) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `ubicacion` varchar(50) DEFAULT NULL,
  `estado` enum('libre','ocupada','reservada','inactiva') DEFAULT 'libre',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla de productos
CREATE TABLE IF NOT EXISTS `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_producto` varchar(50) NOT NULL,
  `nombre_producto` varchar(150) NOT NULL,
  `valor_sin_iva` decimal(10,2) NOT NULL,
  `valor_con_iva` decimal(10,2) NOT NULL,
  `inventario` int(11) NOT NULL DEFAULT 0,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla de servicios
CREATE TABLE IF NOT EXISTS `servicios` (
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla de servicios_total
CREATE TABLE IF NOT EXISTS `servicios_total` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_servicio` varchar(50) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `metodo_pago` enum('efectivo','llave','nequi','daviplata','tarjeta') NOT NULL DEFAULT 'efectivo',
  `fecha_servicio` date NOT NULL,
  `hora_cierre_servicio` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos iniciales de ejemplo
INSERT INTO `mesas` (`id`, `id_mesa`, `nombre`, `ubicacion`, `estado`) VALUES
(1, 'ME-1', 'mesa 1', 'interior', 'libre'),
(2, 'ME-2', 'mesa 2', 'interior', 'libre');
