-- ============================================================
-- AGREGAR PERMISOS DE COSTEO AUTOMÁTICO A LA BD MASTER
-- Crear aplicaciones: Materias Primas y Recetas
-- ============================================================

-- Verificar si ya existen las aplicaciones
INSERT IGNORE INTO `aplicaciones` (`nombre`, `descripcion`, `clave`, `icono`, `estado`) VALUES
('Materias Primas', 'Gestión de materias primas y ingredientes', 'materias_primas', 'fas fa-leaf', 'activo'),
('Recetas', 'Gestión de recetas y cálculo automático de costos', 'recetas', 'fas fa-recipe', 'activo');

SELECT 'Permisos agregados exitosamente' as Resultado;
