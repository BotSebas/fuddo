-- =============================================
-- SCRIPT DE VERIFICACIÓN - SISTEMA FUDDO
-- Ejecutar para verificar estado de la BD
-- =============================================

USE mgacgdnjkg;

-- 1. INFORMACIÓN GENERAL
SELECT '=== INFORMACIÓN GENERAL ===' as '';
SELECT DATABASE() as 'Base de Datos Actual';
SELECT VERSION() as 'Versión MySQL';
SELECT NOW() as 'Fecha/Hora Actual';

-- 2. TABLAS MAESTRAS
SELECT '\n=== TABLAS MAESTRAS ===' as '';
SHOW TABLES LIKE '%restaurante%';
SHOW TABLES LIKE '%usuario%';
SHOW TABLES LIKE '%aplicacion%';
SHOW TABLES LIKE '%reporte%';

-- 3. ESTADÍSTICAS
SELECT '\n=== ESTADÍSTICAS ===' as '';
SELECT COUNT(*) as 'Total Restaurantes' FROM restaurantes;
SELECT COUNT(*) as 'Total Usuarios' FROM usuarios_master;
SELECT COUNT(*) as 'Total Aplicaciones' FROM aplicaciones;
SELECT COUNT(*) as 'Total Reportes' FROM reportes;

-- 4. RESTAURANTES REGISTRADOS
SELECT '\n=== RESTAURANTES ===' as '';
SELECT id, nombre, identificador, nombre_bd, estado, fecha_creacion 
FROM restaurantes 
ORDER BY fecha_creacion DESC;

-- 5. USUARIOS REGISTRADOS
SELECT '\n=== USUARIOS ===' as '';
SELECT id, usuario, nombre, email, rol, id_restaurante, estado 
FROM usuarios_master 
ORDER BY id;

-- 6. TABLAS CON PREFIJOS (por restaurante)
SELECT '\n=== TABLAS CON PREFIJOS ===' as '';
SHOW TABLES LIKE 'fuddo_%';

-- 7. PERMISOS ASIGNADOS
SELECT '\n=== PERMISOS DE APLICACIONES ===' as '';
SELECT r.nombre as 'Restaurante', a.nombre as 'Aplicación', ra.tiene_acceso as 'Acceso'
FROM restaurante_aplicaciones ra
INNER JOIN restaurantes r ON ra.id_restaurante = r.id
INNER JOIN aplicaciones a ON ra.id_aplicacion = a.id
ORDER BY r.nombre, a.orden;

SELECT '\n=== PERMISOS DE REPORTES ===' as '';
SELECT r.nombre as 'Restaurante', rep.nombre as 'Reporte', rr.tiene_acceso as 'Acceso'
FROM restaurante_reportes rr
INNER JOIN restaurantes r ON rr.id_restaurante = r.id
INNER JOIN reportes rep ON rr.id_reporte = rep.id
ORDER BY r.nombre, rep.nombre;

-- 8. VERIFICAR SI HAY DATOS DE PRUEBA
SELECT '\n=== DATOS DE PRUEBA (si existen) ===' as '';

-- Contar mesas por restaurante
SELECT 
    SUBSTRING_INDEX(SUBSTRING_INDEX(table_name, '_', 2), '_', -1) as 'Identificador',
    table_name as 'Tabla Mesas',
    (SELECT COUNT(*) FROM information_schema.tables t2 
     WHERE t2.table_schema = 'mgacgdnjkg' 
     AND t2.table_name = t.table_name) as 'Existe'
FROM information_schema.tables t
WHERE table_schema = 'mgacgdnjkg' 
AND table_name LIKE 'fuddo_%_mesas'
ORDER BY table_name;

-- 9. RESUMEN FINAL
SELECT '\n=== RESUMEN ===' as '';
SELECT 
    (SELECT COUNT(*) FROM restaurantes) as 'Restaurantes',
    (SELECT COUNT(*) FROM usuarios_master) as 'Usuarios',
    (SELECT COUNT(*) FROM aplicaciones) as 'Aplicaciones',
    (SELECT COUNT(*) FROM reportes) as 'Reportes',
    (SELECT COUNT(*) FROM information_schema.tables 
     WHERE table_schema = 'mgacgdnjkg' 
     AND table_name LIKE 'fuddo_%') as 'Tablas con Prefijo';

SELECT '=== VERIFICACIÓN COMPLETADA ===' as '';
