<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MigracionAybarCorp2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function () {

            /* --- MODULO USUARIO --- */
            $this->command->info('Migrando Módulo Usuario...');

            DB::statement("
                INSERT INTO aybar.users (
                    id, name, email, email_verified_at, password, must_change_password, 
                    password_changed_at, profile_photo_path, rol, politica_uno, 
                    politica_dos, activo, remember_token, created_at, updated_at, deleted_at
                )
                SELECT u.id, u.name, u.email, u.email_verified_at, u.password, u.must_change_password,
                    u.password_changed_at, u.profile_photo_path, u.rol, u.politica_uno,
                    u.politica_dos, u.activo, u.remember_token, u.created_at, u.updated_at, u.deleted_at
                FROM aybarcorp2.users u
                LEFT JOIN aybar.users au ON au.id = u.id
                WHERE au.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.model_has_roles (role_id, model_type, model_id)
                SELECT 1, 'App\\Models\\User', u.id
                FROM aybar.users u
                WHERE u.rol = 'admin'
                    AND NOT EXISTS (
                        SELECT 1 FROM aybar.model_has_roles m 
                        WHERE m.model_id = u.id AND m.role_id = 1 AND m.model_type = 'App\\Models\\User'
                    )
            ");

            DB::statement("
                INSERT INTO aybar.clientes (
                    id, user_id, nombre, email, dni, telefono_principal, 
                    telefono_alternativo, created_at, updated_at, deleted_at
                )
                SELECT c.id, c.user_id, c.nombre, c.email, c.dni, c.telefono_principal,
                    c.telefono_alternativo, c.created_at, c.updated_at, c.deleted_at
                FROM aybarcorp2.clientes c
                INNER JOIN aybarcorp2.users u ON u.id = c.user_id
                LEFT JOIN aybar.clientes ac ON ac.id = c.id
                WHERE u.rol = 'cliente' AND ac.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.direccions (
                    id, user_id, region_id, provincia_id, distrito_id, direccion, 
                    direccion_numero, opcional, codigo_postal, referencia, created_at, updated_at
                )
                SELECT d.id, d.user_id, d.region_id, d.provincia_id, d.distrito_id, d.direccion,
                    d.direccion_numero, d.opcional, d.codigo_postal, d.instrucciones AS referencia,
                    d.created_at, d.updated_at
                FROM aybarcorp2.direccions d
                INNER JOIN aybarcorp2.users u ON u.id = d.user_id
                LEFT JOIN aybar.direccions ad ON ad.id = d.id
                WHERE u.rol = 'cliente' AND ad.id IS NULL
            ");

            //MODULO NEGOCIO
            /*$this->command->info('Migrando Módulo Negocio...');

            DB::statement("
                INSERT INTO aybar.unidad_negocios (
                    id, nombre, razon_social, ruc, slin_id, cavali_girador_tipo_documento,
                    cavali_girador_documento, cavali_girador_nombre, cavali_girador_apellido,
                    cavali_girador_email, cavali_girador_telefono, created_at, updated_at, deleted_at
                )
                SELECT id, nombre, razon_social, ruc, slin_id, cavali_girador_tipo_documento,
                    cavali_girador_documento, cavali_girador_nombre, cavali_girador_apellido,
                    cavali_girador_email, cavali_girador_telefono, created_at, updated_at, deleted_at
                FROM aybarcorp2.unidad_negocios
            ");

            DB::statement("
                INSERT INTO aybar.grupo_proyectos (id, nombre, activo, created_at, updated_at, deleted_at)
                SELECT id, nombre, activo, created_at, updated_at, deleted_at FROM aybarcorp2.grupo_proyectos
            ");

            DB::statement("
                INSERT INTO aybar.proyectos (
                    id, unidad_negocio_id, grupo_proyecto_id, nombre, slin_id, activo, 
                    created_at, updated_at, deleted_at
                )
                SELECT p.id, p.unidad_negocio_id, p.grupo_proyecto_id, p.nombre, p.slin_id, p.activo,
                    p.created_at, p.updated_at, p.deleted_at
                FROM aybarcorp2.proyectos p
                LEFT JOIN aybar.proyectos ap ON ap.id = p.id
                WHERE ap.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.areas (
                    id, nombre, email_buzon, color, icono, activo, created_at, updated_at, deleted_at
                )
                SELECT a.id, a.nombre, a.email_buzon, a.color, a.icono, a.activo, 
                    a.created_at, a.updated_at, a.deleted_at
                FROM aybarcorp2.areas a
                LEFT JOIN aybar.areas aa ON aa.id = a.id
                WHERE aa.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.area_user (id, area_id, user_id, is_principal, created_at, updated_at)
                SELECT au.id, au.area_id, au.user_id, au.is_principal, au.created_at, au.updated_at
                FROM aybarcorp2.area_user au
                LEFT JOIN aybar.area_user a2 ON a2.id = au.id
                WHERE a2.id IS NULL
            ");

            //MODULO ATC
            $this->command->info('Migrando Módulo ATC...');

            DB::statement("
                INSERT INTO aybar.tipo_solicituds (id, nombre, tiempo_solucion, activo, created_at, updated_at, deleted_at)
                SELECT ts.id, ts.nombre, ts.tiempo_solucion, ts.activo, ts.created_at, ts.updated_at, ts.deleted_at
                FROM aybarcorp2.tipo_solicituds ts
                LEFT JOIN aybar.tipo_solicituds ats ON ats.id = ts.id
                WHERE ats.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.sub_tipo_solicituds (
                    id, tipo_solicitud_id, nombre, tiempo_solucion, activo, created_at, updated_at, deleted_at
                )
                SELECT sts.id, sts.tipo_solicitud_id, sts.nombre, sts.tiempo_solucion, sts.activo,
                    sts.created_at, sts.updated_at, sts.deleted_at
                FROM aybarcorp2.sub_tipo_solicituds sts
                LEFT JOIN aybar.sub_tipo_solicituds asts ON asts.id = sts.id
                WHERE asts.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.area_tipo_solicitud (id, area_id, tipo_solicitud_id, created_at, updated_at)
                SELECT ats.id, ats.area_id, ats.tipo_solicitud_id, ats.created_at, ats.updated_at
                FROM aybarcorp2.area_tipo_solicitud ats
                LEFT JOIN aybar.area_tipo_solicitud a2 ON a2.id = ats.id
                WHERE a2.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.estado_tickets (id, nombre, color, icono, activo, created_at, updated_at, deleted_at)
                SELECT etc.id, etc.nombre, etc.color, etc.icono, etc.activo, etc.created_at, etc.updated_at, etc.deleted_at
                FROM aybarcorp2.estado_tickets etc
                LEFT JOIN aybar.estado_tickets aet ON aet.id = etc.id
                WHERE aet.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.prioridad_tickets (
                    id, nombre, tiempo_permitido, color, icono, activo, created_at, updated_at, deleted_at
                )
                SELECT ptc.id, ptc.nombre, ptc.tiempo_permitido, ptc.color, ptc.icono, ptc.activo,
                    ptc.created_at, ptc.updated_at, ptc.deleted_at
                FROM aybarcorp2.prioridad_tickets ptc
                LEFT JOIN aybar.prioridad_tickets pta ON pta.id = ptc.id
                WHERE pta.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.canals (id, nombre, activo, created_at, updated_at, deleted_at)
                SELECT c2.id, c2.nombre, c2.activo, c2.created_at, c2.updated_at, c2.deleted_at
                FROM aybarcorp2.canals c2
                LEFT JOIN aybar.canals c1 ON c1.id = c2.id
                WHERE c1.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.tickets (
                    id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, area_id, 
                    ticket_padre_id, tipo_solicitud_id, sub_tipo_solicitud_id, canal_id, 
                    estado_ticket_id, prioridad_ticket_id, asunto_inicial, descripcion_inicial, 
                    lotes, asunto_respuesta, descripcion_respuesta, dni, nombres, email, 
                    celular, direccion, origen, usuario_valida_id, fecha_validacion, 
                    created_by, updated_by, deleted_by, created_at, updated_at, deleted_at
                )
                SELECT t.id, t.unidad_negocio_id, t.proyecto_id, t.cliente_id, t.gestor_id, t.area_id,
                    t.ticket_padre_id, t.tipo_solicitud_id, t.sub_tipo_solicitud_id, t.canal_id,
                    t.estado_ticket_id, t.prioridad_ticket_id, t.asunto_inicial, t.descripcion_inicial,
                    t.lotes, t.asunto_respuesta, t.descripcion_respuesta, t.dni, t.nombres, t.email,
                    t.celular, t.direccion, t.origen, t.usuario_valida_id, t.fecha_validacion,
                    t.created_by, t.updated_by, t.deleted_by, t.created_at, t.updated_at, t.deleted_at
                FROM aybarcorp2.tickets t
                LEFT JOIN aybar.tickets at ON at.id = t.id
                WHERE at.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.ticket_archivos (
                    id, archivable_type, archivable_id, user_id, nombre_original, 
                    path, url, titulo, descripcion, extension, size, mime_type, 
                    created_at, updated_at, deleted_at
                )
                SELECT a.id, a.archivable_type, a.archivable_id, 1 AS user_id, 
                    COALESCE(a.titulo, a.path) AS nombre_original, a.path, a.url, a.titulo, 
                    a.descripcion, COALESCE(a.extension, '') AS extension, 0 AS size, 
                    'application/octet-stream' AS mime_type, a.created_at, a.updated_at, a.deleted_at
                FROM aybarcorp2.archivos a
            ");

            DB::statement("
                INSERT INTO aybar.ticket_historials (id, ticket_id, user_id, accion, detalle, created_at, updated_at)
                SELECT th.id, th.ticket_id, th.user_id, th.accion, th.detalle, th.created_at, th.updated_at
                FROM aybarcorp2.ticket_historials th
                LEFT JOIN aybar.ticket_historials ath ON ath.id = th.id
                WHERE th.deleted_at IS NULL AND ath.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.ticket_derivados (
                    id, ticket_id, de_area_id, a_area_id, usuario_deriva_id, 
                    usuario_recibe_id, motivo, created_at, updated_at, deleted_at
                )
                SELECT td.id, td.ticket_id, td.de_area_id, td.a_area_id, td.usuario_deriva_id,
                    td.usuario_recibe_id, td.motivo, td.created_at, td.updated_at, td.deleted_at
                FROM aybarcorp2.ticket_derivados td
                LEFT JOIN aybar.ticket_derivados atd ON atd.id = td.id
                WHERE atd.id IS NULL
            ");

            //MODULO CITAS
            $this->command->info('Migrando Módulo Citas...');

            DB::statement("DROP TABLE IF EXISTS aybar.clientes_2");
            DB::statement("CREATE TABLE aybar.clientes_2 LIKE aybarcorp2.clientes_2");
            DB::statement("INSERT INTO aybar.clientes_2 SELECT * FROM aybarcorp2.clientes_2");

            DB::statement("
                INSERT INTO aybar.motivo_citas (id, nombre, activo, created_at, updated_at, deleted_at)
                SELECT mc.id, mc.nombre, mc.activo, mc.created_at, mc.updated_at, mc.deleted_at
                FROM aybarcorp2.motivo_citas mc
                LEFT JOIN aybar.motivo_citas mc2 ON mc2.id = mc.id
                WHERE mc2.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.estado_citas (id, nombre, color, icono, activo, created_at, updated_at, deleted_at)
                SELECT ec.id, ec.nombre, ec.color, ec.icono, ec.activo, ec.created_at, ec.updated_at, ec.deleted_at
                FROM aybarcorp2.estado_citas ec
                LEFT JOIN aybar.estado_citas ec2 ON ec2.id = ec.id
                WHERE ec2.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.sedes (id, nombre, direccion, activo, created_at, updated_at, deleted_at)
                SELECT s.id, s.nombre, s.direccion, s.activo, s.created_at, s.updated_at, s.deleted_at
                FROM aybarcorp2.sedes s
                LEFT JOIN aybar.sedes sc ON sc.id = s.id
                WHERE sc.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.citas (
                    id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, area_id, 
                    ticket_id, usuario_crea_id, sede_id, motivo_cita_id, estado_cita_id, 
                    fecha_inicio, fecha_fin, fecha_cierre, asunto_solicitud, descripcion_solicitud, 
                    asunto_respuesta, descripcion_respuesta, dni, nombres, origen, 
                    usuario_valida_id, fecha_validacion, created_by, updated_by, deleted_by, 
                    created_at, updated_at, deleted_at
                )
                SELECT c.id, c.unidad_negocio_id, c.proyecto_id, c.cliente_id, c.gestor_id, c.area_id,
                    c.ticket_id, c.usuario_crea_id, c.sede_id, c.motivo_cita_id, c.estado_cita_id,
                    c.fecha_inicio, c.fecha_fin, c.fecha_cierre, c.asunto_solicitud, c.descripcion_solicitud,
                    c.asunto_respuesta, c.descripcion_respuesta, c.dni, c.nombres, c.origen,
                    c.usuario_valida_id, c.fecha_validacion, c.created_by, c.updated_by, c.deleted_by,
                    c.created_at, c.updated_at, c.deleted_at
                FROM aybarcorp2.citas c
                LEFT JOIN aybar.citas cc ON cc.id = c.id
                WHERE cc.id IS NULL
            ");

            //MODULO BACKOFFICE
            $this->command->info('Migrando Módulo Backoffice...');

            DB::statement("
                INSERT INTO aybar.estado_evidencia_pagos (id, nombre, color, icono, activo, created_at, updated_at, deleted_at)
                SELECT e2.id, e2.nombre, e2.color, e2.icono, e2.activo, e2.created_at, e2.updated_at, e2.deleted_at
                FROM aybarcorp2.estado_evidencia_pagos e2
                LEFT JOIN aybar.estado_evidencia_pagos e1 ON e1.id = e2.id
                WHERE e1.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.solicitud_evidencia_pagos (
                    id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, 
                    estado_evidencia_pago_id, lote_completo, codigo_cuota, razon_social, 
                    nombre_proyecto, etapa, manzana, lote, codigo_cliente, numero_cuota, 
                    transaccion_id, fecha_operacion, fecha_vencimiento, monto_operacion, 
                    slin_monto, slin_penalidad, slin_numero_operacion, comprobante, 
                    ticket, slin_asbanc, slin_evidencia, resuelto_manual, observacion, 
                    usuario_valida_id, fecha_validacion, created_by, updated_by, 
                    deleted_by, created_at, updated_at, deleted_at
                )
                SELECT s2.id, s2.unidad_negocio_id, s2.proyecto_id, s2.cliente_id, s2.gestor_id,
                    s2.estado_evidencia_pago_id, s2.lote_completo, s2.codigo_cuota, s2.razon_social,
                    s2.nombre_proyecto, s2.etapa, s2.manzana, s2.lote, s2.codigo_cliente, s2.numero_cuota,
                    s2.transaccion_id, s2.fecha_operacion, s2.fecha_vencimiento, s2.monto_operacion,
                    s2.slin_monto, s2.slin_penalidad, s2.slin_numero_operacion, s2.comprobante,
                    s2.ticket, s2.slin_asbanc, s2.slin_evidencia, s2.resuelto_manual, s2.observacion,
                    s2.usuario_valida_id, 
                    CASE WHEN CAST(s2.fecha_validacion AS CHAR) = '0000-00-00 00:00:00' THEN NULL ELSE s2.fecha_validacion END,
                    s2.created_by, s2.updated_by, s2.deleted_by, s2.created_at, s2.updated_at, s2.deleted_at
                FROM aybarcorp2.solicitud_evidencia_pagos s2
                LEFT JOIN aybar.solicitud_evidencia_pagos s1 ON s1.id = s2.id
                WHERE s1.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.evidencia_pagos (
                    id, solicitud_evidencia_pago_id, estado_evidencia_pago_id, path, 
                    url, extension, numero_operacion, banco, monto, fecha, es_reenvio, 
                    slin_respuesta, observacion, created_at, updated_at, deleted_at
                )
                SELECT e2.id, e2.solicitud_evidencia_pago_id, e2.estado_evidencia_pago_id, e2.path,
                    e2.url, e2.extension, e2.numero_operacion, e2.banco, e2.monto, e2.fecha,
                    e2.es_reenvio, e2.slin_respuesta, e2.observacion, e2.created_at, e2.updated_at, e2.deleted_at
                FROM aybarcorp2.evidencia_pagos e2
                LEFT JOIN aybar.evidencia_pagos e1 ON e1.id = e2.id
                INNER JOIN aybar.solicitud_evidencia_pagos s ON s.id = e2.solicitud_evidencia_pago_id
                WHERE e1.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.solicitud_evidencia_pago_emails (
                    id, solicitud_evidencia_pago_id, mensaje, enviado_at, created_at, updated_at
                )
                SELECT c2.id, c2.solicitud_evidencia_pago_id, c2.mensaje, c2.enviado_at, c2.created_at, c2.updated_at
                FROM aybarcorp2.correo_evidencia_pagos c2
                LEFT JOIN aybar.solicitud_evidencia_pago_emails c1 ON c1.id = c2.id
                INNER JOIN aybar.solicitud_evidencia_pagos s ON s.id = c2.solicitud_evidencia_pago_id
                WHERE c1.id IS NULL
            ");

            DB::statement("
                INSERT INTO aybar.evidencia_pago_antiguos (
                    id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, imagen_url, 
                    operacion_numero, operacion_hora, `union`, cuota_fija, monto, pago_de, 
                    codigo_cuenta, nombre_archivo, moneda, medio_pago, fecha_deposito, 
                    observacion, estado_evidencia_pago_id, estado_registro, dni_cliente, 
                    codigo_cliente, nombres_cliente, razon_social, proyecto_nombre, 
                    etapa, lote, numero_cuota, gestor, fecha_registro, usuario_valida_id, 
                    validador, fecha_validacion, created_by, updated_by, deleted_by, created_at, updated_at
                )
                SELECT e2.id, e2.unidad_negocio_id, e2.proyecto_id, e2.cliente_id, e2.gestor_id, e2.imagen_url,
                    e2.operacion_numero, e2.operacion_hora, e2.`union`, e2.cuota_fija, e2.monto, e2.pago_de,
                    e2.codigo_cuenta, e2.nombre_archivo, e2.moneda, e2.medio_pago, e2.fecha_deposito,
                    e2.observacion, e2.estado_evidencia_pago_id, e2.estado_registro, e2.dni_cliente,
                    e2.codigo_cliente, e2.nombres_cliente, e2.razon_social, e2.proyecto_nombre,
                    e2.etapa, e2.lote, e2.numero_cuota, e2.gestor, e2.fecha_registro, e2.usuario_valida_id,
                    e2.validador, e2.fecha_validacion, e2.created_by, e2.updated_by, e2.deleted_by, e2.created_at, e2.updated_at
                FROM aybarcorp2.evidencia_pago_antiguos e2
                LEFT JOIN aybar.evidencia_pago_antiguos e1 ON e1.id = e2.id
                WHERE e1.id IS NULL
            ");

            //MODULO LETRAS
            $this->command->info('Migrando Módulo Letras...');

            DB::statement("
                INSERT INTO aybar.estado_solicitud_digitalizar_letras (id, nombre, color, icono, activo, created_at, updated_at)
                VALUES (1, 'OBSERVADO', NULL, NULL, 1, NOW(), NOW()),
                       (2, 'PENDIENTE', NULL, NULL, 1, NOW(), NOW()),
                       (3, 'RECHAZADO', NULL, NULL, 1, NOW(), NOW()),
                       (4, 'APROBADO', NULL, NULL, 1, NOW(), NOW())
                ON DUPLICATE KEY UPDATE nombre = VALUES(nombre)
            ");

            DB::statement("
                INSERT INTO aybar.solicitud_digitalizar_letras (
                    id, unidad_negocio_id, proyecto_id, cliente_id, lote_completo, 
                    codigo_cuota, razon_social, nombre_proyecto, etapa, manzana, 
                    lote, codigo_cliente, numero_cuota, codigo_venta, fecha_vencimiento, 
                    importe_cuota, estado_cavali, created_at, updated_at, deleted_at
                )
                SELECT s2.id, s2.unidad_negocio_id, s2.proyecto_id, s2.cliente_id, s2.lote_completo,
                    s2.codigo_cuota, s2.razon_social, s2.nombre_proyecto, s2.etapa, s2.manzana,
                    s2.lote, s2.codigo_cliente, s2.numero_cuota, s2.codigo_venta, s2.fecha_vencimiento,
                    s2.importe_cuota, s2.estado_cavali, s2.created_at, s2.updated_at, s2.deleted_at
                FROM aybarcorp2.solicitud_digitalizar_letras s2
                LEFT JOIN aybar.solicitud_digitalizar_letras s1 ON s1.id = s2.id
                WHERE s1.id IS NULL
            ");

            $this->command->info('✓ ¡Migración completada con éxito!');*/
        });
    }
}
