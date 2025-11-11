-- ============================================
-- DATOS PARA TIPOS DE ACTIVIDAD CON SUB-ACTIVIDADES
-- ============================================

USE gestion_operativa;

-- ACTIVIDADES PRINCIPALES

-- 1. CORTE DE SUMINISTRO
INSERT INTO tipos_actividad (nombre, dependencia_id, estado) VALUES
('CORTE', NULL, TRUE);
SET @corte_id = LAST_INSERT_ID();

-- Sub-actividades de CORTE
INSERT INTO tipos_actividad (nombre, dependencia_id, estado) VALUES
('Corte por No Pago', @corte_id, TRUE),
('Corte por Orden Judicial', @corte_id, TRUE),
('Corte Preventivo', @corte_id, TRUE);

-- 2. RECONEXIONES
INSERT INTO tipos_actividad (nombre, dependencia_id, estado) VALUES
('RECONEXION', NULL, TRUE);
SET @reconexion_id = LAST_INSERT_ID();

-- Sub-actividades de RECONEXION
INSERT INTO tipos_actividad (nombre, dependencia_id, estado) VALUES
('Reconexión por Pago', @reconexion_id, TRUE),
('Reconexión por Resolución Judicial', @reconexion_id, TRUE);

-- 3. SUMO-B (Servicio de Uso Medidor Observado - Baja Tensión)
INSERT INTO tipos_actividad (nombre, dependencia_id, estado) VALUES
('SUMO-B', NULL, TRUE);
SET @sumo_b_id = LAST_INSERT_ID();

-- Sub-actividades de SUMO-B
INSERT INTO tipos_actividad (nombre, dependencia_id, estado) VALUES
('Sumo-B: Cambio de Medidor', @sumo_b_id, TRUE),
('Sumo-B: Instalación Nueva', @sumo_b_id, TRUE),
('Sumo-B: Revisión Técnica', @sumo_b_id, TRUE);

-- 4. INSPECCIONES (Adicional útil)
INSERT INTO tipos_actividad (nombre, dependencia_id, estado) VALUES
('INSPECCION', NULL, TRUE);
SET @inspeccion_id = LAST_INSERT_ID();

-- Sub-actividades de INSPECCION
INSERT INTO tipos_actividad (nombre, dependencia_id, estado) VALUES
('Inspección Técnica', @inspeccion_id, TRUE),
('Inspección por Anomalía', @inspeccion_id, TRUE);

-- ============================================
-- VERIFICAR DATOS INSERTADOS
-- ============================================

SELECT 
    id,
    nombre,
    dependencia_id,
    CASE WHEN dependencia_id IS NULL THEN 'PRINCIPAL' ELSE 'SUB-ACTIVIDAD' END AS tipo,
    estado,
    created_at
FROM tipos_actividad
ORDER BY dependencia_id, id;

-- ============================================
-- ESTADÍSTICAS
-- ============================================

SELECT 
    COUNT(*) as total_actividades,
    SUM(CASE WHEN dependencia_id IS NULL THEN 1 ELSE 0 END) as actividades_principales,
    SUM(CASE WHEN dependencia_id IS NOT NULL THEN 1 ELSE 0 END) as sub_actividades
FROM tipos_actividad;
