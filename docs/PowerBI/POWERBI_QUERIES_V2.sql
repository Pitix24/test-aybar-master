/*
Power BI V2 - ERP Aybar
Estrategia:
1) Vistas dimension: vw_pbi_v2_dim_*
2) Vistas hecho: vw_pbi_v2_fact_*
3) Vistas QA: vw_pbi_v2_qa_* para conciliacion
*/

CREATE OR REPLACE VIEW vw_pbi_v2_dim_fecha AS
WITH RECURSIVE fechas AS (
    SELECT DATE('2020-01-01') AS fecha
    UNION ALL
    SELECT fecha + INTERVAL 1 DAY
    FROM fechas
    WHERE fecha < DATE('2032-12-31')
)
SELECT
    CAST(DATE_FORMAT(fecha, '%Y%m%d') AS UNSIGNED) AS fecha_id,
    fecha,
    YEAR(fecha) AS anio,
    MONTH(fecha) AS mes_numero,
    MONTHNAME(fecha) AS mes_nombre,
    CONCAT(YEAR(fecha), '-', LPAD(MONTH(fecha), 2, '0')) AS anio_mes,
    QUARTER(fecha) AS trimestre,
    WEEK(fecha, 3) AS semana_iso,
    DAY(fecha) AS dia_mes,
    DAYOFWEEK(fecha) AS dia_semana_numero,
    DAYNAME(fecha) AS dia_semana_nombre
FROM fechas;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_usuario AS
SELECT
    u.id,
    u.name,
    u.email,
    u.rol,
    u.activo,
    u.email_verified_at,
    u.password_changed_at,
    u.created_at,
    u.updated_at,
    u.deleted_at
FROM users u;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_region AS
SELECT r.id, r.nombre FROM regions r;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_provincia AS
SELECT p.id, p.region_id, p.nombre FROM provincias p;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_distrito AS
SELECT d.id, d.provincia_id, d.nombre FROM distritos d;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_unidad_negocio AS
SELECT u.id, u.nombre FROM unidad_negocios u;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_proyecto AS
SELECT p.id, p.unidad_negocio_id, p.nombre FROM proyectos p;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_area AS
SELECT a.id, a.nombre FROM areas a;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_canal AS
SELECT c.id, c.nombre FROM canals c;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_tipo_solicitud AS
SELECT t.id, t.nombre FROM tipo_solicituds t;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_prioridad_ticket AS
SELECT p.id, p.nombre FROM prioridad_tickets p;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_estado_ticket AS
SELECT e.id, e.nombre FROM estado_tickets e;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_estado_cita AS
SELECT e.id, e.nombre FROM estado_citas e;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_motivo_cita AS
SELECT m.id, m.nombre FROM motivo_citas m;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_estado_solicitud_evidencia_pago AS
SELECT e.id, e.nombre FROM estado_solicitud_evidencia_pagos e;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_estado_solicitud_digitalizar_letra AS
SELECT e.id, e.nombre FROM estado_solicitud_digitalizar_letras e;

CREATE OR REPLACE VIEW vw_pbi_v2_dim_sede AS
SELECT s.id, s.nombre FROM sedes s;

CREATE OR REPLACE VIEW vw_pbi_v2_fact_clientes AS
SELECT
    c.id,
    c.user_id,
    CAST(DATE_FORMAT(c.created_at, '%Y%m%d') AS UNSIGNED) AS created_fecha_id,
    c.nombre,
    c.email,
    c.dni,
    c.telefono_principal,
    c.telefono_alternativo,
    c.created_at,
    c.updated_at,
    c.deleted_at,
    u.rol AS usuario_rol,
    u.activo AS usuario_activo,
    u.email_verified_at,
    d.id AS direccion_id,
    d.region_id,
    d.provincia_id,
    d.distrito_id
FROM clientes c
LEFT JOIN users u ON u.id = c.user_id
LEFT JOIN direccions d ON d.user_id = c.user_id;

CREATE OR REPLACE VIEW vw_pbi_v2_fact_direcciones AS
SELECT
    d.id,
    d.user_id,
    CAST(DATE_FORMAT(d.created_at, '%Y%m%d') AS UNSIGNED) AS created_fecha_id,
    d.region_id,
    d.provincia_id,
    d.distrito_id,
    d.direccion,
    d.created_at,
    d.updated_at,
    d.deleted_at
FROM direccions d
JOIN users u ON u.id = d.user_id
WHERE u.rol = 'cliente';

CREATE OR REPLACE VIEW vw_pbi_v2_fact_admins AS
SELECT
    u.id,
    CAST(DATE_FORMAT(u.created_at, '%Y%m%d') AS UNSIGNED) AS created_fecha_id,
    u.name,
    u.email,
    u.activo,
    u.created_at,
    u.updated_at,
    u.deleted_at,
    GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ', ') AS roles_nombres,
    COUNT(DISTINCT r.id) AS total_roles
FROM users u
LEFT JOIN model_has_roles mhr ON mhr.model_id = u.id
LEFT JOIN roles r ON r.id = mhr.role_id
WHERE u.rol = 'admin'
GROUP BY u.id, created_fecha_id, u.name, u.email, u.activo, u.created_at, u.updated_at, u.deleted_at;

CREATE OR REPLACE VIEW vw_pbi_v2_fact_solicitud_evidencia_pago AS
SELECT
    sep.id,
    sep.unidad_negocio_id,
    sep.proyecto_id,
    sep.cliente_id,
    sep.gestor_id,
    sep.estado_solicitud_evidencia_pago_id,
    CAST(DATE_FORMAT(sep.created_at, '%Y%m%d') AS UNSIGNED) AS created_fecha_id,
    CAST(DATE_FORMAT(sep.fecha_validacion, '%Y%m%d') AS UNSIGNED) AS validacion_fecha_id,
    sep.monto_operacion,
    sep.slin_monto,
    sep.slin_penalidad,
    sep.resuelto_manual,
    sep.created_at,
    sep.updated_at,
    sep.deleted_at,
    (SELECT COUNT(*) FROM evidencia_pagos ep WHERE ep.solicitud_evidencia_pago_id = sep.id AND ep.deleted_at IS NULL) AS total_evidencias,
    (SELECT COUNT(*) FROM solicitud_evidencia_pago_emails see WHERE see.solicitud_evidencia_pago_id = sep.id AND see.deleted_at IS NULL) AS total_emails
FROM solicitud_evidencia_pagos sep;

CREATE OR REPLACE VIEW vw_pbi_v2_fact_solicitud_evidencia_pago_emails AS
SELECT
    see.id,
    see.solicitud_evidencia_pago_id,
    see.emisor_id,
    see.receptor_id,
    CAST(DATE_FORMAT(see.enviado_at, '%Y%m%d') AS UNSIGNED) AS enviado_fecha_id,
    see.enviado_at,
    see.created_at,
    see.updated_at,
    see.deleted_at
FROM solicitud_evidencia_pago_emails see;

CREATE OR REPLACE VIEW vw_pbi_v2_fact_evidencia_pago AS
SELECT
    ep.id,
    ep.solicitud_evidencia_pago_id,
    sep.unidad_negocio_id,
    sep.proyecto_id,
    ep.estado_solicitud_evidencia_pago_id,
    CAST(DATE_FORMAT(ep.created_at, '%Y%m%d') AS UNSIGNED) AS created_fecha_id,
    CAST(DATE_FORMAT(ep.fecha, '%Y%m%d') AS UNSIGNED) AS operacion_fecha_id,
    ep.banco,
    ep.extension,
    ep.monto,
    ep.slin_respuesta,
    ep.created_at,
    ep.updated_at,
    ep.deleted_at
FROM evidencia_pagos ep
LEFT JOIN solicitud_evidencia_pagos sep ON sep.id = ep.solicitud_evidencia_pago_id;

CREATE OR REPLACE VIEW vw_pbi_v2_fact_tickets AS
SELECT
    t.id,
    t.unidad_negocio_id,
    t.proyecto_id,
    t.cliente_id,
    t.gestor_id,
    t.area_id,
    t.canal_id,
    t.tipo_solicitud_id,
    t.prioridad_ticket_id,
    t.estado_ticket_id,
    CAST(DATE_FORMAT(t.created_at, '%Y%m%d') AS UNSIGNED) AS created_fecha_id,
    CAST(DATE_FORMAT(t.fecha_validacion, '%Y%m%d') AS UNSIGNED) AS cierre_fecha_id,
    t.created_at,
    t.updated_at,
    t.deleted_at,
    (SELECT COUNT(*) FROM ticket_derivados td WHERE td.ticket_id = t.id AND td.deleted_at IS NULL) AS total_derivaciones
FROM tickets t;

CREATE OR REPLACE VIEW vw_pbi_v2_fact_ticket_derivados AS
SELECT
    td.id,
    td.ticket_id,
    td.de_area_id,
    td.a_area_id,
    td.usuario_deriva_id,
    td.usuario_recibe_id,
    CAST(DATE_FORMAT(td.created_at, '%Y%m%d') AS UNSIGNED) AS created_fecha_id,
    td.created_at,
    td.updated_at,
    td.deleted_at
FROM ticket_derivados td;

CREATE OR REPLACE VIEW vw_pbi_v2_fact_citas AS
SELECT
    c.id,
    c.unidad_negocio_id,
    c.proyecto_id,
    c.cliente_id,
    c.gestor_id,
    c.area_id,
    c.sede_id,
    c.motivo_cita_id,
    c.estado_cita_id,
    CAST(DATE_FORMAT(c.created_at, '%Y%m%d') AS UNSIGNED) AS created_fecha_id,
    CAST(DATE_FORMAT(c.fecha_inicio, '%Y%m%d') AS UNSIGNED) AS inicio_fecha_id,
    CAST(DATE_FORMAT(c.fecha_cierre, '%Y%m%d') AS UNSIGNED) AS cierre_fecha_id,
    c.fecha_inicio,
    c.fecha_fin,
    c.fecha_cierre,
    c.created_at,
    c.updated_at,
    c.deleted_at
FROM citas c;

CREATE OR REPLACE VIEW vw_pbi_v2_fact_solicitud_digitalizar_letra AS
SELECT
    s.id,
    s.unidad_negocio_id,
    s.proyecto_id,
    s.cliente_id,
    s.gestor_id,
    s.estado_solicitud_digitalizar_letra_id,
    CAST(DATE_FORMAT(s.created_at, '%Y%m%d') AS UNSIGNED) AS created_fecha_id,
    CAST(DATE_FORMAT(s.fecha_vencimiento, '%Y%m%d') AS UNSIGNED) AS vencimiento_fecha_id,
    CAST(s.importe_cuota AS DECIMAL(10,2)) AS importe_cuota_decimal,
    s.created_at,
    s.updated_at,
    s.deleted_at
FROM solicitud_digitalizar_letras s;

/* QA: conteos fuente vs vistas */

CREATE OR REPLACE VIEW vw_pbi_v2_qa_conteos AS
SELECT 'clientes' AS entidad, (SELECT COUNT(*) FROM clientes) AS fuente_total, (SELECT COUNT(*) FROM vw_pbi_v2_fact_clientes) AS vista_total
UNION ALL
SELECT 'direccions', (SELECT COUNT(*) FROM direccions), (SELECT COUNT(*) FROM vw_pbi_v2_fact_direcciones)
UNION ALL
SELECT 'admins', (SELECT COUNT(*) FROM users WHERE rol = 'admin'), (SELECT COUNT(*) FROM vw_pbi_v2_fact_admins)
UNION ALL
SELECT 'solicitud_evidencia_pagos', (SELECT COUNT(*) FROM solicitud_evidencia_pagos), (SELECT COUNT(*) FROM vw_pbi_v2_fact_solicitud_evidencia_pago)
UNION ALL
SELECT 'evidencia_pagos', (SELECT COUNT(*) FROM evidencia_pagos), (SELECT COUNT(*) FROM vw_pbi_v2_fact_evidencia_pago)
UNION ALL
SELECT 'tickets', (SELECT COUNT(*) FROM tickets), (SELECT COUNT(*) FROM vw_pbi_v2_fact_tickets)
UNION ALL
SELECT 'ticket_derivados', (SELECT COUNT(*) FROM ticket_derivados), (SELECT COUNT(*) FROM vw_pbi_v2_fact_ticket_derivados)
UNION ALL
SELECT 'citas', (SELECT COUNT(*) FROM citas), (SELECT COUNT(*) FROM vw_pbi_v2_fact_citas)
UNION ALL
SELECT 'solicitud_digitalizar_letras', (SELECT COUNT(*) FROM solicitud_digitalizar_letras), (SELECT COUNT(*) FROM vw_pbi_v2_fact_solicitud_digitalizar_letra);

CREATE OR REPLACE VIEW vw_pbi_v2_qa_conteos_diff AS
SELECT
    entidad,
    fuente_total,
    vista_total,
    (vista_total - fuente_total) AS diferencia
FROM vw_pbi_v2_qa_conteos;

/* QA: control de nulos en llaves */
CREATE OR REPLACE VIEW vw_pbi_v2_qa_null_keys AS
SELECT 'fact_tickets.gestor_id' AS regla, COUNT(*) AS total_nulos FROM vw_pbi_v2_fact_tickets WHERE gestor_id IS NULL
UNION ALL
SELECT 'fact_citas.estado_cita_id', COUNT(*) FROM vw_pbi_v2_fact_citas WHERE estado_cita_id IS NULL
UNION ALL
SELECT 'fact_evidencia_pago.solicitud_evidencia_pago_id', COUNT(*) FROM vw_pbi_v2_fact_evidencia_pago WHERE solicitud_evidencia_pago_id IS NULL
UNION ALL
SELECT 'fact_solicitud_evidencia_pago.estado_id', COUNT(*) FROM vw_pbi_v2_fact_solicitud_evidencia_pago WHERE estado_solicitud_evidencia_pago_id IS NULL
UNION ALL
SELECT 'fact_solicitud_digitalizar_letra.estado_id', COUNT(*) FROM vw_pbi_v2_fact_solicitud_digitalizar_letra WHERE estado_solicitud_digitalizar_letra_id IS NULL;
