-- Agregar columna costo_producto a la tabla productos
-- Esta columna almacena el costo del producto para cálculo de ganancias

ALTER TABLE `productos` ADD COLUMN `costo_producto` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `nombre_producto`;

-- Si la tabla productos ya existe en restaurantes (en la BD maestra o multi-tenant), ejecutar también:
-- ALTER TABLE productos ADD COLUMN `costo_producto` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `nombre_producto`;
