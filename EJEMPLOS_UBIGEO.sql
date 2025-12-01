-- ========================================
-- EJEMPLOS DE CONSULTAS SQL UBIGEO
-- ========================================

-- ========================================
-- 1. CONSULTAS BÁSICAS
-- ========================================

-- Contar registros por tipo
SELECT 
    (SELECT COUNT(*) FROM ubigeos WHERE codigo_postal LIKE '%0000') as departamentos,
    (SELECT COUNT(*) FROM ubigeos WHERE codigo_postal LIKE '%00' AND codigo_postal NOT LIKE '%0000') as provincias,
    (SELECT COUNT(*) FROM ubigeos WHERE codigo_postal NOT LIKE '%00') as distritos,
    COUNT(*) as total
FROM ubigeos;

-- Ver todos los departamentos
SELECT * FROM ubigeos 
WHERE codigo_postal LIKE '%0000' 
ORDER BY nombre;

-- ========================================
-- 2. CONSULTAS JERÁRQUICAS
-- ========================================

-- Ver estructura completa de un departamento (AMAZONAS)
SELECT 
    CASE 
        WHEN u1.codigo_postal LIKE '%0000' THEN CONCAT('1. ', u1.nombre)
        WHEN u2.codigo_postal LIKE '%0000' THEN CONCAT('  2. ', u1.nombre)
        ELSE CONCAT('    3. ', u1.nombre)
    END as ubicacion,
    u1.codigo_postal,
    u1.id
FROM ubigeos u1
LEFT JOIN ubigeos u2 ON u1.dependencia_id = u2.id
WHERE u1.codigo_postal LIKE '01%'
ORDER BY u1.codigo_postal;

-- Ver un departamento con sus provincias y distritos
SELECT 
    d.nombre as departamento,
    p.nombre as provincia,
    di.nombre as distrito,
    di.codigo_postal as ubigeo
FROM ubigeos d
LEFT JOIN ubigeos p ON p.dependencia_id = d.id
LEFT JOIN ubigeos di ON di.dependencia_id = p.id
WHERE d.codigo_postal = '010000' -- AMAZONAS
ORDER BY p.nombre, di.nombre;

-- ========================================
-- 3. BÚSQUEDAS
-- ========================================

-- Buscar un distrito específico por nombre
SELECT * FROM ubigeos 
WHERE nombre LIKE '%CHACHAPOYAS%' 
AND codigo_postal NOT LIKE '%00';

-- Buscar todos los distritos que contienen "LA"
SELECT 
    codigo_postal,
    nombre
FROM ubigeos 
WHERE nombre LIKE '%LA%' 
AND codigo_postal NOT LIKE '%00'
ORDER BY nombre;

-- Buscar el departamento de un distrito
SELECT 
    d.nombre as departamento,
    p.nombre as provincia,
    di.nombre as distrito
FROM ubigeos di
LEFT JOIN ubigeos p ON di.dependencia_id = p.id
LEFT JOIN ubigeos d ON p.dependencia_id = d.id
WHERE di.nombre = 'CHACHAPOYAS';

-- ========================================
-- 4. ESTADÍSTICAS
-- ========================================

-- Contar distritos por departamento
SELECT 
    d.nombre as departamento,
    COUNT(di.id) as total_distritos
FROM ubigeos d
LEFT JOIN ubigeos p ON p.dependencia_id = d.id
LEFT JOIN ubigeos di ON di.dependencia_id = p.id
WHERE d.codigo_postal LIKE '%0000'
GROUP BY d.id
ORDER BY total_distritos DESC;

-- Contar provincias por departamento
SELECT 
    d.nombre as departamento,
    COUNT(p.id) as total_provincias
FROM ubigeos d
LEFT JOIN ubigeos p ON p.dependencia_id = d.id
WHERE d.codigo_postal LIKE '%0000' 
AND p.codigo_postal LIKE '%00' 
AND p.codigo_postal NOT LIKE '%0000'
GROUP BY d.id
ORDER BY total_provincias DESC;

-- Contar distritos por provincia
SELECT 
    p.nombre as provincia,
    d.nombre as departamento,
    COUNT(di.id) as total_distritos
FROM ubigeos p
LEFT JOIN ubigeos d ON p.dependencia_id = d.id
LEFT JOIN ubigeos di ON di.dependencia_id = p.id
WHERE p.codigo_postal LIKE '%00' 
AND p.codigo_postal NOT LIKE '%0000'
GROUP BY p.id
ORDER BY d.nombre, p.nombre;

-- ========================================
-- 5. RELACIONES CON OTRAS TABLAS
-- ========================================

-- Ver empleados con su ubicación (UBIGEO)
SELECT 
    e.nombre,
    e.apellido,
    CONCAT(d.nombre, ' > ', p.nombre, ' > ', di.nombre) as ubicacion
FROM empleados e
LEFT JOIN ubigeos di ON e.ubigeo_id = di.id
LEFT JOIN ubigeos p ON di.dependencia_id = p.id
LEFT JOIN ubigeos d ON p.dependencia_id = d.id
LIMIT 10;

-- Ver proveedores con su ubicación
SELECT 
    p.nombre as proveedor,
    CONCAT(d.nombre, ' > ', pr.nombre, ' > ', di.nombre) as ubicacion
FROM proveedores p
LEFT JOIN ubigeos di ON p.ubigeo_id = di.id
LEFT JOIN ubigeos pr ON di.dependencia_id = pr.id
LEFT JOIN ubigeos d ON pr.dependencia_id = d.id
LIMIT 10;

-- Ver suministros con su ubicación
SELECT 
    s.numero_suministro,
    CONCAT(d.nombre, ' > ', p.nombre, ' > ', di.nombre) as ubicacion
FROM suministros s
LEFT JOIN ubigeos di ON s.ubigeo_id = di.id
LEFT JOIN ubigeos p ON di.dependencia_id = p.id
LEFT JOIN ubigeos d ON p.dependencia_id = d.id
LIMIT 10;

-- ========================================
-- 6. BÚSQUEDAS AVANZADAS
-- ========================================

-- Obtener UBIGEO completo formateado
SELECT 
    di.id,
    di.codigo_postal as ubigeo,
    CONCAT(d.nombre, ' - ', p.nombre, ' - ', di.nombre) as ubicacion_completa,
    di.estado
FROM ubigeos di
LEFT JOIN ubigeos p ON di.dependencia_id = p.id
LEFT JOIN ubigeos d ON p.dependencia_id = d.id
WHERE di.codigo_postal NOT LIKE '%00'
ORDER BY di.codigo_postal
LIMIT 100;

-- Obtener formato UBIGEO más legible
SELECT 
    di.codigo_postal,
    d.nombre as departamento,
    p.nombre as provincia,
    di.nombre as distrito
FROM ubigeos di
LEFT JOIN ubigeos p ON di.dependencia_id = p.id
LEFT JOIN ubigeos d ON p.dependencia_id = d.id
WHERE di.codigo_postal LIKE '15%'  -- LIMA
ORDER BY di.nombre
LIMIT 50;

-- ========================================
-- 7. MANTENIMIENTO
-- ========================================

-- Ver registros sin padres (huérfanos)
SELECT * FROM ubigeos 
WHERE dependencia_id IS NOT NULL 
AND dependencia_id NOT IN (SELECT id FROM ubigeos);

-- Ver registros inactivos
SELECT 
    codigo_postal,
    nombre,
    estado
FROM ubigeos 
WHERE estado = 0
ORDER BY codigo_postal;

-- Desactivar un distrito
UPDATE ubigeos 
SET estado = 0, updated_at = NOW() 
WHERE codigo_postal = '010101';

-- Reactivar un distrito
UPDATE ubigeos 
SET estado = 1, updated_at = NOW() 
WHERE codigo_postal = '010101';

-- ========================================
-- 8. EXPORTACIÓN
-- ========================================

-- Exportar a CSV (desde MySQL)
SELECT 
    codigo_postal as UBIGEO,
    nombre as DISTRITO,
    estado as ACTIVO,
    dependencia_id as PROVINCIA_ID,
    created_at,
    updated_at
FROM ubigeos 
WHERE codigo_postal NOT LIKE '%00'
INTO OUTFILE '/tmp/ubigeo_distritos.csv'
FIELDS TERMINATED BY ',' 
ENCLOSED BY '"' 
LINES TERMINATED BY '\n';

-- ========================================
-- 9. VALIDACIÓN
-- ========================================

-- Ver códigos duplicados
SELECT 
    codigo_postal,
    COUNT(*) as duplicados
FROM ubigeos 
GROUP BY codigo_postal 
HAVING COUNT(*) > 1;

-- Ver nombres duplicados en el mismo nivel
SELECT 
    d.nombre,
    COUNT(*) as cantidad
FROM ubigeos d
WHERE d.codigo_postal LIKE '%0000'
GROUP BY d.nombre
HAVING COUNT(*) > 1;

-- Verificar integridad de códigos
SELECT 
    codigo_postal,
    nombre,
    CASE 
        WHEN codigo_postal LIKE '%0000' THEN 'Departamento'
        WHEN codigo_postal LIKE '%00' THEN 'Provincia'
        ELSE 'Distrito'
    END as tipo,
    LENGTH(codigo_postal) as longitud
FROM ubigeos
WHERE LENGTH(codigo_postal) != 6
AND codigo_postal NOT LIKE '%00%'
ORDER BY codigo_postal;

-- ========================================
-- 10. REPORTES
-- ========================================

-- Reporte: Población administrativa por nivel
SELECT 
    CASE 
        WHEN codigo_postal LIKE '%0000' THEN 'Departamento'
        WHEN codigo_postal LIKE '%00' THEN 'Provincia'
        ELSE 'Distrito'
    END as nivel,
    COUNT(*) as cantidad
FROM ubigeos
GROUP BY 
    CASE 
        WHEN codigo_postal LIKE '%0000' THEN 'Departamento'
        WHEN codigo_postal LIKE '%00' THEN 'Provincia'
        ELSE 'Distrito'
    END;

-- Reporte: Departamentos con más distritos
SELECT 
    d.nombre as departamento,
    COUNT(DISTINCT p.id) as provincias,
    COUNT(DISTINCT di.id) as distritos
FROM ubigeos d
LEFT JOIN ubigeos p ON p.dependencia_id = d.id AND p.codigo_postal LIKE '%00' AND p.codigo_postal NOT LIKE '%0000'
LEFT JOIN ubigeos di ON di.dependencia_id = p.id AND di.codigo_postal NOT LIKE '%00'
WHERE d.codigo_postal LIKE '%0000'
GROUP BY d.id
ORDER BY distritos DESC
LIMIT 10;
