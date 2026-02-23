-- ========================================================
-- SINCRONIZACIÓN DE ESTADOS DE MEDIDORES
-- Versión: 1.0
-- Fecha: 23 de Febrero, 2026
-- ========================================================
-- Estado: 1 = Disponible (no asignado a suministro)
-- Estado: 2 = Asignado (tiene suministro)
-- ========================================================

-- 1. MARCAR TODOS LOS MEDIDORES COMO DISPONIBLES (estado = 1)
UPDATE medidors SET estado = 1;

-- 2. MARCAR LOS MEDIDORES ASIGNADOS A SUMINISTROS (estado = 2)
UPDATE medidors 
SET estado = 2 
WHERE id IN (
    SELECT medidor_id 
    FROM suministros 
    WHERE medidor_id IS NOT NULL
);

-- ========================================================
-- VERIFICACIÓN DE RESULTADOS
-- ========================================================

-- Ver cantidad de medidores por estado
SELECT 
    CASE 
        WHEN estado = 1 THEN 'Disponible'
        WHEN estado = 2 THEN 'Asignado'
        ELSE 'Desconocido'
    END as estado_nombre,
    COUNT(*) as cantidad
FROM medidors
GROUP BY estado
ORDER BY estado;

-- Ver medidores disponibles
SELECT id, serie, modelo, marca, estado FROM medidors WHERE estado = 1 ORDER BY serie;

-- Ver medidores asignados (con información del suministro)
SELECT 
    m.id,
    m.serie,
    m.modelo,
    m.marca,
    m.estado,
    s.codigo as suministro_codigo,
    s.nombre as suministro_nombre,
    s.direccion
FROM medidors m
LEFT JOIN suministros s ON m.id = s.medidor_id
WHERE m.estado = 2
ORDER BY m.serie;

-- Verificar si hay medidores con suministro pero estado = 1 (inconsistencia)
SELECT 
    m.id,
    m.serie,
    m.estado,
    COUNT(s.id) as cantidad_suministros
FROM medidors m
LEFT JOIN suministros s ON m.id = s.medidor_id
WHERE m.estado = 1 AND s.id IS NOT NULL
GROUP BY m.id, m.serie, m.estado;

-- Verificar suministros sin medidor
SELECT id, codigo, nombre, medidor_id FROM suministros WHERE medidor_id IS NULL;
