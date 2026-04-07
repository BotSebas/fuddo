-- Migration: Add visibility column to menu_digital table
-- Date: 2026-04-06
-- Description: Add 'visible' column to control if menu sections are displayed without deleting them

ALTER TABLE menu_digital 
ADD COLUMN visible TINYINT(1) NOT NULL DEFAULT 1 
AFTER estado;

-- Verify column was created
DESCRIBE menu_digital;
