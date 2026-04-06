-- Script para agregar soporte a Factura Electrónica
-- Este script agrega el campo correo_factura_electronica a las tablas servicios_total y comandas_total
-- para todas las organizaciones existentes
-- 
-- INSTRUCCIONES DE USO:
-- 1. Abre phpMyAdmin o tu cliente MySQL
-- 2. Selecciona la base de datos de tu restaurante (ej: fuddo_restaurante_1)
-- 3. Ve a la pestaña "SQL"
-- 4. Copia y pega el comando ALTER TABLE que corresponda a tu tabla
-- 5. Ejecuta el comando
--
-- NOTA: Reemplaza "restaurante_1" con el prefijo específico de tu organización
-- ( ej: servicios_total → restaurante_1_servicios_total )

-- Para la tabla servicios_total (MESAS)
ALTER TABLE `restaurante_1_servicios_total` 
ADD COLUMN `correo_factura_electronica` varchar(100) DEFAULT NULL 
AFTER `hora_cierre_servicio`;

-- Para la tabla comandas_total (COMANDAS)
ALTER TABLE `restaurante_1_comandas_total` 
ADD COLUMN `correo_factura_electronica` varchar(100) DEFAULT NULL 
AFTER `hora_cierre_comanda`;

-- ============================================================================
-- EJEMPLOS PARA DIFERENTES ORGANIZACIONES:
-- ============================================================================

-- Organización "restaurante_2":
-- ALTER TABLE `restaurante_2_servicios_total` ADD COLUMN `correo_factura_electronica` varchar(100) DEFAULT NULL AFTER `hora_cierre_servicio`;
-- ALTER TABLE `restaurante_2_comandas_total` ADD COLUMN `correo_factura_electronica` varchar(100) DEFAULT NULL AFTER `hora_cierre_comanda`;

-- Organización "cliente_3":
-- ALTER TABLE `cliente_3_servicios_total` ADD COLUMN `correo_factura_electronica` varchar(100) DEFAULT NULL AFTER `hora_cierre_servicio`;
-- ALTER TABLE `cliente_3_comandas_total` ADD COLUMN `correo_factura_electronica` varchar(100) DEFAULT NULL AFTER `hora_cierre_comanda`;

-- ============================================================================
-- VALIDACIÓN:
-- Después de ejecutar los comandos, puedes verificar que se agregó correctamente
-- con estos comandos SELECT:
-- ============================================================================

-- Para verificar servicios_total:
-- DESCRIBE `restaurante_1_servicios_total`;

-- Para verificar comandas_total:
-- DESCRIBE `restaurante_1_comandas_total`;

-- Si ves la columna `correo_factura_electronica` en ambas tablas, ¡todo está correcto!
