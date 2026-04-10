CREATE OR REPLACE VIEW vw_pbi_dim_fecha AS
WITH RECURSIVE fechas AS (
    SELECT DATE('2020-01-01') AS fecha
    UNION ALL
    SELECT fecha + INTERVAL 1 DAY
    FROM fechas
    WHERE fecha < DATE('2030-12-31')
)
SELECT
    DATE_FORMAT(fecha, '%Y%m%d') AS fecha_id,
    fecha,
    YEAR(fecha) AS anio,
    MONTH(fecha) AS mes_numero,
    MONTHNAME(fecha) AS mes_nombre,
    CONCAT(YEAR(fecha), '-', LPAD(MONTH(fecha), 2, '0')) AS anio_mes,
    QUARTER(fecha) AS trimestre,
    WEEK(fecha, 3) AS semana_iso,
    DAY(fecha) AS dia_mes,
    DAYOFWEEK(fecha) AS dia_semana_numero,
    DAYNAME(fecha) AS dia_semana_nombre,
    CASE
        WHEN DAYOFWEEK(fecha) = 1 THEN 'Domingo'
        WHEN DAYOFWEEK(fecha) = 2 THEN 'Lunes'
        WHEN DAYOFWEEK(fecha) = 3 THEN 'Martes'
        WHEN DAYOFWEEK(fecha) = 4 THEN 'Miercoles'
        WHEN DAYOFWEEK(fecha) = 5 THEN 'Jueves'
        WHEN DAYOFWEEK(fecha) = 6 THEN 'Viernes'
        WHEN DAYOFWEEK(fecha) = 7 THEN 'Sabado'
    END AS dia_semana_es
FROM fechas;

CREATE OR REPLACE VIEW vw_pbi_dim_usuario AS
SELECT
    u.id,
    u.name,
    u.email,
    u.rol,
    u.activo,
    u.email_verified_at,
    u.password_changed_at,
    u.profile_photo_path,
    u.created_at,
    u.updated_at,
    u.deleted_at
FROM users u;

CREATE OR REPLACE VIEW vw_pbi_dim_region AS
SELECT
    r.id,
    r.nombre,
    r.created_at,
    r.updated_at
FROM regions r;

CREATE OR REPLACE VIEW vw_pbi_dim_provincia AS
SELECT
    p.id,
    p.region_id,
    p.nombre,
    p.created_at,
    p.updated_at
FROM provincias p;

CREATE OR REPLACE VIEW vw_pbi_dim_distrito AS
SELECT
    d.id,
    d.provincia_id,
    d.nombre,
    d.created_at,
    d.updated_at
FROM distritos d;

CREATE OR REPLACE VIEW vw_pbi_dim_unidad_negocio AS
SELECT
    u.id,
    u.nombre,
    u.created_at,
    u.updated_at
FROM unidad_negocios u;

CREATE OR REPLACE VIEW vw_pbi_dim_proyecto AS
SELECT
    p.id,
    p.unidad_negocio_id,
    p.nombre,
    p.created_at,
    p.updated_at
FROM proyectos p;

CREATE OR REPLACE VIEW vw_pbi_dim_area AS
SELECT
    a.id,
    a.nombre,
    a.created_at,
    a.updated_at
FROM areas a;

CREATE OR REPLACE VIEW vw_pbi_dim_canal AS
SELECT
    c.id,
    c.nombre,
    c.created_at,
    c.updated_at
FROM canals c;

CREATE OR REPLACE VIEW vw_pbi_dim_tipo_solicitud AS
SELECT
    t.id,
    t.nombre,
    t.created_at,
    t.updated_at
FROM tipo_solicituds t;

CREATE OR REPLACE VIEW vw_pbi_dim_sub_tipo_solicitud AS
SELECT
    s.id,
    s.tipo_solicitud_id,
    s.nombre,
    s.created_at,
    s.updated_at
FROM sub_tipo_solicituds s;

CREATE OR REPLACE VIEW vw_pbi_dim_prioridad_ticket AS
SELECT
    p.id,
    p.nombre,
    p.created_at,
    p.updated_at
FROM prioridad_tickets p;

CREATE OR REPLACE VIEW vw_pbi_dim_estado_ticket AS
SELECT
    e.id,
    e.nombre,
    e.created_at,
    e.updated_at
FROM estado_tickets e;

CREATE OR REPLACE VIEW vw_pbi_dim_estado_cita AS
SELECT
    e.id,
    e.nombre,
    e.created_at,
    e.updated_at
FROM estado_citas e;

CREATE OR REPLACE VIEW vw_pbi_dim_motivo_cita AS
SELECT
    m.id,
    m.nombre,
    m.created_at,
    m.updated_at
FROM motivo_citas m;

CREATE OR REPLACE VIEW vw_pbi_dim_estado_solicitud_evidencia_pago AS
SELECT
    e.id,
    e.nombre,
    e.created_at,
    e.updated_at
FROM estado_solicitud_evidencia_pagos e;

CREATE OR REPLACE VIEW vw_pbi_dim_estado_solicitud_digitalizar_letra AS
SELECT
    e.id,
    e.nombre,
    e.created_at,
    e.updated_at
FROM estado_solicitud_digitalizar_letras e;

CREATE OR REPLACE VIEW vw_pbi_dim_sede AS
SELECT
    s.id,
    s.nombre,
    s.created_at,
    s.updated_at
FROM sedes s;

CREATE OR REPLACE VIEW vw_pbi_fact_clientes AS
SELECT
    c.id,
    c.user_id,
    c.nombre,
    c.email,
    c.dni,
    c.telefono_principal,
    c.telefono_alternativo,
    c.created_at,
    c.updated_at,
    c.deleted_at,
    u.name AS usuario_name,
    u.rol AS usuario_rol,
    u.activo AS usuario_activo,
    u.email_verified_at,
    u.password_changed_at,
    u.profile_photo_path,
    d.id AS direccion_id,
    d.direccion,
    d.direccion_numero,
    d.opcional,
    d.codigo_postal,
    d.referencia,
    d.region_id,
    r.nombre AS region_nombre,
    d.provincia_id,
    p.nombre AS provincia_nombre,
    d.distrito_id,
    di.nombre AS distrito_nombre,
    d.created_at AS direccion_created_at,
    d.updated_at AS direccion_updated_at
FROM clientes c
LEFT JOIN users u ON u.id = c.user_id
LEFT JOIN direccions d ON d.user_id = u.id
LEFT JOIN regions r ON r.id = d.region_id
LEFT JOIN provincias p ON p.id = d.provincia_id
LEFT JOIN distritos di ON di.id = d.distrito_id;

CREATE OR REPLACE VIEW vw_pbi_fact_direcciones AS
SELECT
    d.id,
    d.user_id,
    u.name AS user_name,
    u.email AS user_email,
    u.rol AS user_rol,
    u.activo AS user_activo,
    d.region_id,
    r.nombre AS region_nombre,
    d.provincia_id,
    p.nombre AS provincia_nombre,
    d.distrito_id,
    di.nombre AS distrito_nombre,
    d.direccion,
    d.direccion_numero,
    d.opcional,
    d.codigo_postal,
    d.referencia,
    d.created_at,
    d.updated_at,
    d.deleted_at
FROM direccions d
LEFT JOIN users u ON u.id = d.user_id
LEFT JOIN regions r ON r.id = d.region_id
LEFT JOIN provincias p ON p.id = d.provincia_id
LEFT JOIN distritos di ON di.id = d.distrito_id
WHERE u.rol = 'cliente';

CREATE OR REPLACE VIEW vw_pbi_fact_admins AS
SELECT
    u.id,
    u.name,
    u.email,
    u.rol,
    u.activo,
    u.email_verified_at,
    u.password_changed_at,
    u.profile_photo_path,
    u.created_at,
    u.updated_at,
    u.deleted_at,
    GROUP_CONCAT(DISTINCT r.name ORDER BY r.name SEPARATOR ', ') AS roles_nombres,
    COUNT(DISTINCT r.id) AS total_roles
FROM users u
LEFT JOIN model_has_roles mhr ON mhr.model_id = u.id
LEFT JOIN roles r ON r.id = mhr.role_id
WHERE u.rol = 'admin'
GROUP BY
    u.id,
    u.name,
    u.email,
    u.rol,
    u.activo,
    u.email_verified_at,
    u.password_changed_at,
    u.profile_photo_path,
    u.created_at,
    u.updated_at,
    u.deleted_at;

CREATE OR REPLACE VIEW vw_pbi_fact_solicitud_evidencia_pago AS
SELECT
    sep.id,
    sep.unidad_negocio_id,
    un.nombre AS unidad_negocio_nombre,
    sep.proyecto_id,
    p.nombre AS proyecto_nombre,
    sep.cliente_id,
    uc.name AS cliente_name,
    sep.gestor_id,
    ug.name AS gestor_name,
    sep.adjuntado_por_id,
    ua.name AS adjuntado_por_name,
    sep.estado_solicitud_evidencia_pago_id,
    est.nombre AS estado_nombre,
    sep.razon_social,
    sep.nombre_proyecto,
    sep.etapa,
    sep.manzana,
    sep.lote,
    sep.codigo_cliente,
    sep.codigo_cuota,
    sep.numero_cuota,
    sep.transaccion_id,
    sep.fecha_operacion,
    sep.fecha_vencimiento,
    sep.monto_operacion,
    sep.slin_monto,
    sep.slin_penalidad,
    sep.slin_numero_operacion,
    sep.comprobante,
    sep.ticket,
    sep.lote_completo,
    sep.slin_asbanc,
    sep.slin_evidencia,
    sep.resuelto_manual,
    sep.dni,
    sep.nombres,
    sep.origen,
    sep.usuario_valida_id,
    uv.name AS usuario_valida_name,
    sep.fecha_validacion,
    sep.created_at,
    sep.updated_at,
    sep.deleted_at,
    (SELECT COUNT(*) FROM evidencia_pagos ep WHERE ep.solicitud_evidencia_pago_id = sep.id AND ep.deleted_at IS NULL) AS total_evidencias,
    (SELECT COUNT(*) FROM solicitud_evidencia_pago_emails see WHERE see.solicitud_evidencia_pago_id = sep.id AND see.deleted_at IS NULL) AS total_emails
FROM solicitud_evidencia_pagos sep
LEFT JOIN unidad_negocios un ON un.id = sep.unidad_negocio_id
LEFT JOIN proyectos p ON p.id = sep.proyecto_id
LEFT JOIN users uc ON uc.id = sep.cliente_id
LEFT JOIN users ug ON ug.id = sep.gestor_id
LEFT JOIN users ua ON ua.id = sep.adjuntado_por_id
LEFT JOIN users uv ON uv.id = sep.usuario_valida_id
LEFT JOIN estado_solicitud_evidencia_pagos est ON est.id = sep.estado_solicitud_evidencia_pago_id;

CREATE OR REPLACE VIEW vw_pbi_fact_solicitud_evidencia_pago_emails AS
SELECT
    see.id,
    see.solicitud_evidencia_pago_id,
    sep.unidad_negocio_id,
    un.nombre AS unidad_negocio_nombre,
    sep.proyecto_id,
    p.nombre AS proyecto_nombre,
    see.emisor_id,
    em.name AS emisor_name,
    see.receptor_id,
    re.name AS receptor_name,
    see.asunto,
    see.mensaje,
    see.enviado_at,
    see.created_at,
    see.updated_at,
    see.deleted_at
FROM solicitud_evidencia_pago_emails see
LEFT JOIN solicitud_evidencia_pagos sep ON sep.id = see.solicitud_evidencia_pago_id
LEFT JOIN unidad_negocios un ON un.id = sep.unidad_negocio_id
LEFT JOIN proyectos p ON p.id = sep.proyecto_id
LEFT JOIN users em ON em.id = see.emisor_id
LEFT JOIN users re ON re.id = see.receptor_id;

CREATE OR REPLACE VIEW vw_pbi_fact_evidencia_pago AS
SELECT
    ep.id,
    ep.solicitud_evidencia_pago_id,
    sep.unidad_negocio_id,
    un.nombre AS unidad_negocio_nombre,
    sep.proyecto_id,
    p.nombre AS proyecto_nombre,
    ep.estado_solicitud_evidencia_pago_id,
    est.nombre AS estado_nombre,
    ep.path,
    ep.url,
    ep.extension,
    ep.numero_operacion,
    ep.banco,
    ep.monto,
    ep.fecha,
    ep.es_reenvio,
    ep.slin_respuesta,
    ep.observacion,
    ep.created_at,
    ep.updated_at,
    ep.deleted_at
FROM evidencia_pagos ep
LEFT JOIN solicitud_evidencia_pagos sep ON sep.id = ep.solicitud_evidencia_pago_id
LEFT JOIN unidad_negocios un ON un.id = sep.unidad_negocio_id
LEFT JOIN proyectos p ON p.id = sep.proyecto_id
LEFT JOIN estado_solicitud_evidencia_pagos est ON est.id = ep.estado_solicitud_evidencia_pago_id;

CREATE OR REPLACE VIEW vw_pbi_fact_tickets AS
SELECT
    t.id,
    t.unidad_negocio_id,
    un.nombre AS unidad_negocio_nombre,
    t.proyecto_id,
    p.nombre AS proyecto_nombre,
    t.cliente_id,
    uc.name AS cliente_name,
    t.area_id,
    a.nombre AS area_nombre,
    t.ticket_padre_id,
    t.tipo_solicitud_id,
    ts.nombre AS tipo_solicitud_nombre,
    t.sub_tipo_solicitud_id,
    sts.nombre AS sub_tipo_solicitud_nombre,
    t.canal_id,
    c.nombre AS canal_nombre,
    t.estado_ticket_id,
    est.nombre AS estado_nombre,
    t.prioridad_ticket_id,
    pr.nombre AS prioridad_nombre,
    t.gestor_id,
    ug.name AS gestor_name,
    t.asunto_inicial,
    t.descripcion_inicial,
    t.lotes,
    t.asunto_respuesta,
    t.descripcion_respuesta,
    t.dni,
    t.nombres,
    t.email,
    t.celular,
    t.direccion,
    t.origen,
    t.usuario_valida_id,
    uv.name AS usuario_valida_name,
    t.fecha_validacion,
    t.created_at,
    t.updated_at,
    t.deleted_at,
    (SELECT COUNT(*) FROM ticket_derivados td WHERE td.ticket_id = t.id AND td.deleted_at IS NULL) AS total_derivaciones,
    (SELECT COUNT(*) FROM ticket_mensajes tm WHERE tm.ticket_id = t.id AND tm.deleted_at IS NULL) AS total_mensajes,
    (SELECT COUNT(*) FROM ticket_archivos ta WHERE ta.archivable_id = t.id AND ta.archivable_type = 'App\\Models\\Ticket' AND ta.deleted_at IS NULL) AS total_archivos
FROM tickets t
LEFT JOIN unidad_negocios un ON un.id = t.unidad_negocio_id
LEFT JOIN proyectos p ON p.id = t.proyecto_id
LEFT JOIN users uc ON uc.id = t.cliente_id
LEFT JOIN areas a ON a.id = t.area_id
LEFT JOIN tipo_solicituds ts ON ts.id = t.tipo_solicitud_id
LEFT JOIN sub_tipo_solicituds sts ON sts.id = t.sub_tipo_solicitud_id
LEFT JOIN canals c ON c.id = t.canal_id
LEFT JOIN estado_tickets est ON est.id = t.estado_ticket_id
LEFT JOIN prioridad_tickets pr ON pr.id = t.prioridad_ticket_id
LEFT JOIN users ug ON ug.id = t.gestor_id
LEFT JOIN users uv ON uv.id = t.usuario_valida_id;

CREATE OR REPLACE VIEW vw_pbi_fact_ticket_derivados AS
SELECT
    td.id,
    td.ticket_id,
    t.unidad_negocio_id,
    un.nombre AS unidad_negocio_nombre,
    t.proyecto_id,
    p.nombre AS proyecto_nombre,
    td.de_area_id,
    da.nombre AS de_area_nombre,
    td.a_area_id,
    aa.nombre AS a_area_nombre,
    td.usuario_deriva_id,
    ud.name AS usuario_deriva_name,
    td.usuario_recibe_id,
    ur.name AS usuario_recibe_name,
    td.motivo,
    td.created_at,
    td.updated_at,
    td.deleted_at
FROM ticket_derivados td
LEFT JOIN tickets t ON t.id = td.ticket_id
LEFT JOIN unidad_negocios un ON un.id = t.unidad_negocio_id
LEFT JOIN proyectos p ON p.id = t.proyecto_id
LEFT JOIN areas da ON da.id = td.de_area_id
LEFT JOIN areas aa ON aa.id = td.a_area_id
LEFT JOIN users ud ON ud.id = td.usuario_deriva_id
LEFT JOIN users ur ON ur.id = td.usuario_recibe_id;

CREATE OR REPLACE VIEW vw_pbi_fact_citas AS
SELECT
    c.id,
    c.unidad_negocio_id,
    un.nombre AS unidad_negocio_nombre,
    c.proyecto_id,
    p.nombre AS proyecto_nombre,
    c.cliente_id,
    uc.name AS cliente_name,
    c.area_id,
    a.nombre AS area_nombre,
    c.ticket_id,
    c.usuario_crea_id,
    ucrea.name AS usuario_crea_name,
    c.gestor_id,
    ug.name AS gestor_name,
    c.sede_id,
    s.nombre AS sede_nombre,
    c.motivo_cita_id,
    m.nombre AS motivo_nombre,
    c.estado_cita_id,
    est.nombre AS estado_nombre,
    c.fecha_inicio,
    c.fecha_fin,
    c.fecha_cierre,
    c.asunto_solicitud,
    c.descripcion_solicitud,
    c.asunto_respuesta,
    c.descripcion_respuesta,
    c.dni,
    c.nombres,
    c.origen,
    c.usuario_valida_id,
    uv.name AS usuario_valida_name,
    c.fecha_validacion,
    c.created_at,
    c.updated_at,
    c.deleted_at,
    (SELECT COUNT(*) FROM cita_archivos ca WHERE ca.cita_id = c.id AND ca.deleted_at IS NULL) AS total_archivos,
    (SELECT COUNT(*) FROM cita_emails ce WHERE ce.cita_id = c.id AND ce.deleted_at IS NULL) AS total_emails
FROM citas c
LEFT JOIN unidad_negocios un ON un.id = c.unidad_negocio_id
LEFT JOIN proyectos p ON p.id = c.proyecto_id
LEFT JOIN users uc ON uc.id = c.cliente_id
LEFT JOIN areas a ON a.id = c.area_id
LEFT JOIN users ucrea ON ucrea.id = c.usuario_crea_id
LEFT JOIN users ug ON ug.id = c.gestor_id
LEFT JOIN sedes s ON s.id = c.sede_id
LEFT JOIN motivo_citas m ON m.id = c.motivo_cita_id
LEFT JOIN estado_citas est ON est.id = c.estado_cita_id
LEFT JOIN users uv ON uv.id = c.usuario_valida_id;

CREATE OR REPLACE VIEW vw_pbi_fact_solicitud_digitalizar_letra AS
SELECT
    s.id,
    s.unidad_negocio_id,
    un.nombre AS unidad_negocio_nombre,
    s.proyecto_id,
    p.nombre AS proyecto_nombre,
    s.cliente_id,
    uc.name AS cliente_name,
    s.gestor_id,
    ug.name AS gestor_name,
    s.estado_solicitud_digitalizar_letra_id,
    est.nombre AS estado_nombre,
    s.lote_completo,
    s.codigo_cuota,
    s.razon_social,
    s.nombre_proyecto,
    s.etapa,
    s.manzana,
    s.lote,
    s.codigo_cliente,
    s.numero_cuota,
    s.codigo_venta,
    s.fecha_vencimiento,
    CAST(s.importe_cuota AS DECIMAL(10,2)) AS importe_cuota_decimal,
    s.observacion,
    s.dni,
    s.nombres,
    s.email,
    s.celular,
    s.direccion,
    s.region,
    s.provincia,
    s.distrito,
    s.origen,
    s.usuario_valida_id,
    uv.name AS usuario_valida_name,
    s.fecha_validacion,
    s.created_at,
    s.updated_at,
    s.deleted_at
FROM solicitud_digitalizar_letras s
LEFT JOIN unidad_negocios un ON un.id = s.unidad_negocio_id
LEFT JOIN proyectos p ON p.id = s.proyecto_id
LEFT JOIN users uc ON uc.id = s.cliente_id
LEFT JOIN users ug ON ug.id = s.gestor_id
LEFT JOIN estado_solicitud_digitalizar_letras est ON est.id = s.estado_solicitud_digitalizar_letra_id
LEFT JOIN users uv ON uv.id = s.usuario_valida_id;