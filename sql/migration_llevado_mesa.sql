-- Migration: Add 'llevado_mesa' column to track product delivery status
-- Date: 2026-04-06
-- Description: Add column to servicios and comandas tables to track which products have been delivered to the table

-- Add llevado_mesa column to servicios table
ALTER TABLE servicios 
ADD COLUMN llevado_mesa TINYINT(1) NOT NULL DEFAULT 0 
AFTER estado,
ADD INDEX idx_llevado_mesa (llevado_mesa);

-- Add llevado_mesa column to comandas table
ALTER TABLE comandas 
ADD COLUMN llevado_mesa TINYINT(1) NOT NULL DEFAULT 0 
AFTER estado,
ADD INDEX idx_llevado_mesa (llevado_mesa);

-- Verify columns were created
DESCRIBE servicios;
DESCRIBE comandas;
