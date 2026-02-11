<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateCorporateDataCommand extends Command
{
    protected $signature = 'app:migrate-corporate-data {--source=aybarcorp} {--source2=aybarcorp2}';
    protected $description = 'Migra datos desde las bases de datos corporativas antiguas hacia aybar';

    public function handle()
    {
        $source = $this->option('source');
        $source2 = $this->option('source2');

        $this->info("🚀 Iniciando migración de datos corporativos...");
        $this->info("Origen 1: $source | Origen 2: $source2");

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::statement("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';");

        $tasks = [
            'Usuarios' => fn() => $this->migrateUsers($source),
            'Clientes' => fn() => $this->migrateClientes($source),
            'Direcciones' => fn() => $this->migrateDirecciones($source),
            'Unidades de Negocio' => fn() => $this->migrateUnidadNegocios($source2),
            'Grupos de Proyectos' => fn() => $this->migrateGrupoProyectos($source2),
            'Proyectos' => fn() => $this->migrateProyectos($source),
            'Áreas y Categorías' => fn() => $this->migrateAreas($source, $source2),
            'Tickets' => fn() => $this->migrateTickets($source2),
            'Archivos de Tickets' => fn() => $this->migrateTicketArchivos($source2),
            'Historial y Derivados' => fn() => $this->migrateTicketExtras($source2),
            'Citas' => fn() => $this->migrateCitas($source),
            'Estructura de Pagos' => fn() => $this->migratePagosEstructura($source2),
            'Solicitudes de Pago' => fn() => $this->migratePagosSolicitudes($source2),
            'Evidencia Antigua (Stock)' => fn() => $this->migrateEvidenciaAntigua($source2),
            'Cavali y Letras' => fn() => $this->migrateCavali($source2),
        ];

        foreach ($tasks as $name => $task) {
            $this->output->write("<fg=yellow>Migrando $name...</> ");
            try {
                $task();
                $this->output->writeln("<fg=green> [OK]</>");
            } catch (\Exception $e) {
                $this->output->writeln("<fg=red> [ERROR]</>");
                $this->error("   ➜ " . $e->getMessage());
                if (!$this->confirm("¿Desea continuar con la siguiente tabla?", true)) {
                    break;
                }
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->info("=========================================");
        $this->info("✅ Proceso de migración finalizado.");
        return 0;
    }

    private function migrateUsers($source)
    {
        DB::statement("INSERT INTO aybar.users (id, name, email, email_verified_at, password, must_change_password, password_changed_at, profile_photo_path, rol, politica_uno, politica_dos, activo, remember_token, created_at, updated_at, deleted_at)
            SELECT u.id, u.name, u.email, u.email_verified_at, u.password, u.must_change_password, u.password_changed_at, u.profile_photo_path, u.rol, u.politica_uno, u.politica_dos, u.activo, u.remember_token, u.created_at, u.updated_at, u.deleted_at
            FROM {$source}.users u LEFT JOIN aybar.users au ON au.email = u.email WHERE au.id IS NULL");

        DB::statement("INSERT INTO aybar.model_has_roles (role_id, model_type, model_id)
            SELECT 1, 'App\\\\Models\\\\User', u.id FROM aybar.users u WHERE u.rol = 'admin'
            AND NOT EXISTS (SELECT 1 FROM aybar.model_has_roles m WHERE m.model_id = u.id AND m.role_id = 1 AND m.model_type = 'App\\\\Models\\\\User')");
    }

    private function migrateClientes($source)
    {
        DB::statement("INSERT INTO aybar.clientes (id, user_id, nombre, email, dni, telefono_principal, telefono_alternativo, created_at, updated_at, deleted_at)
            SELECT c.id, c.user_id, c.nombre, c.email, c.dni, c.telefono_principal, c.telefono_alternativo, c.created_at, c.updated_at, c.deleted_at
            FROM {$source}.clientes c INNER JOIN aybar.users u ON u.id = c.user_id LEFT JOIN aybar.clientes ac ON ac.id = c.id WHERE u.rol = 'cliente' AND ac.id IS NULL");
    }

    private function migrateDirecciones($source)
    {
        DB::statement("INSERT INTO aybar.direccions (user_id, region_id, provincia_id, distrito_id, direccion, direccion_numero, opcional, codigo_postal, referencia, created_at, updated_at)
            SELECT d.user_id, d.region_id, d.provincia_id, d.distrito_id, d.direccion, d.direccion_numero, d.opcional, d.codigo_postal, d.instrucciones, d.created_at, d.updated_at
            FROM {$source}.direccions d INNER JOIN aybar.users u ON u.id = d.user_id LEFT JOIN aybar.direccions ad ON ad.user_id = d.user_id WHERE u.rol = 'cliente' AND ad.user_id IS NULL");
    }

    private function migrateUnidadNegocios($source)
    {
        DB::statement("INSERT INTO aybar.unidad_negocios (id, nombre, razon_social, ruc, slin_id, cavali_girador_tipo_documento, cavali_girador_documento, cavali_girador_nombre, cavali_girador_apellido, cavali_girador_email, cavali_girador_telefono, created_at, updated_at, deleted_at)
            SELECT id, nombre, razon_social, ruc, slin_id, cavali_girador_tipo_documento, cavali_girador_documento, cavali_girador_nombre, cavali_girador_apellido, cavali_girador_email, cavali_girador_telefono, created_at, updated_at, deleted_at
            FROM {$source}.unidad_negocios s LEFT JOIN aybar.unidad_negocios a ON a.id = s.id WHERE a.id IS NULL");
    }

    private function migrateGrupoProyectos($source)
    {
        DB::statement("INSERT INTO aybar.grupo_proyectos (id, nombre, activo, created_at, updated_at, deleted_at)
            SELECT id, nombre, activo, created_at, updated_at, deleted_at FROM {$source}.grupo_proyectos s LEFT JOIN aybar.grupo_proyectos a ON a.id = s.id WHERE a.id IS NULL");
    }

    private function migrateProyectos($source)
    {
        DB::statement("INSERT INTO aybar.proyectos (id, unidad_negocio_id, grupo_proyecto_id, nombre, slin_id, activo, created_at, updated_at, deleted_at)
            SELECT id, unidad_negocio_id, grupo_proyecto_id, nombre, slin_id, activo, created_at, updated_at, deleted_at FROM {$source}.proyectos s LEFT JOIN aybar.proyectos a ON a.id = s.id WHERE a.id IS NULL");
    }

    private function migrateAreas($source, $source2)
    {
        DB::statement("INSERT INTO aybar.areas (id, nombre, email_buzon, color, icono, activo, created_at, updated_at, deleted_at)
            SELECT id, nombre, email_buzon, color, icono, activo, created_at, updated_at, deleted_at FROM {$source}.areas s LEFT JOIN aybar.areas a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.area_user (id, area_id, user_id, is_principal, created_at, updated_at)
            SELECT id, area_id, user_id, is_principal, created_at, updated_at FROM {$source2}.area_user s LEFT JOIN aybar.area_user a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.tipo_solicituds (id, nombre, tiempo_solucion, activo, created_at, updated_at, deleted_at)
            SELECT id, nombre, tiempo_solucion, activo, created_at, updated_at, deleted_at FROM {$source2}.tipo_solicituds s LEFT JOIN aybar.tipo_solicituds a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.sub_tipo_solicituds (id, tipo_solicitud_id, nombre, tiempo_solucion, activo, created_at, updated_at, deleted_at)
            SELECT id, tipo_solicitud_id, nombre, tiempo_solucion, activo, created_at, updated_at, deleted_at FROM {$source2}.sub_tipo_solicituds s LEFT JOIN aybar.sub_tipo_solicituds a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.area_tipo_solicitud (id, area_id, tipo_solicitud_id, created_at, updated_at)
            SELECT id, area_id, tipo_solicitud_id, created_at, updated_at FROM {$source}.area_tipo_solicitud s LEFT JOIN aybar.area_tipo_solicitud a ON a.id = s.id WHERE a.id IS NULL");
    }

    private function migrateTickets($source)
    {
        DB::statement("INSERT INTO aybar.estado_tickets (id, nombre, color, icono, activo, created_at, updated_at, deleted_at) SELECT id, nombre, color, icono, activo, created_at, updated_at, deleted_at FROM {$source}.estado_tickets s LEFT JOIN aybar.estado_tickets a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.prioridad_tickets (id, nombre, tiempo_permitido, color, icono, activo, created_at, updated_at, deleted_at) SELECT id, nombre, tiempo_permitido, color, icono, activo, created_at, updated_at, deleted_at FROM {$source}.prioridad_tickets s LEFT JOIN aybar.prioridad_tickets a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.canals (id, nombre, activo, created_at, updated_at, deleted_at) SELECT id, nombre, activo, created_at, updated_at, deleted_at FROM {$source}.canals s LEFT JOIN aybar.canals a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.tickets (id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, area_id, ticket_padre_id, tipo_solicitud_id, sub_tipo_solicitud_id, canal_id, estado_ticket_id, prioridad_ticket_id, asunto_inicial, descripcion_inicial, lotes, asunto_respuesta, descripcion_respuesta, dni, nombres, email, celular, direccion, origen, usuario_valida_id, fecha_validacion, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
            SELECT id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, area_id, ticket_padre_id, tipo_solicitud_id, sub_tipo_solicitud_id, canal_id, estado_ticket_id, prioridad_ticket_id, asunto_inicial, descripcion_inicial, lotes, asunto_respuesta, descripcion_respuesta, dni, nombres, email, celular, direccion, origen, usuario_valida_id, fecha_validacion, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at
            FROM {$source}.tickets s LEFT JOIN aybar.tickets a ON a.id = s.id WHERE a.id IS NULL");
    }

    private function migrateTicketArchivos($source)
    {
        DB::statement("INSERT INTO aybar.ticket_archivos (id, archivable_type, archivable_id, user_id, nombre_original, path, url, titulo, descripcion, extension, size, mime_type, created_at, updated_at, deleted_at)
            SELECT a.id, a.archivable_type, a.archivable_id, 1, COALESCE(a.titulo, a.path), a.path, a.url, a.titulo, a.descripcion, COALESCE(a.extension, ''), 0, 'application/octet-stream', a.created_at, a.updated_at, a.deleted_at
            FROM {$source}.archivos a LEFT JOIN aybar.ticket_archivos ta ON ta.id = a.id WHERE ta.id IS NULL");
    }

    private function migrateTicketExtras($source)
    {
        DB::statement("INSERT INTO aybar.ticket_historials (id, ticket_id, user_id, accion, detalle, created_at, updated_at) SELECT id, ticket_id, user_id, accion, detalle, created_at, updated_at FROM {$source}.ticket_historials s LEFT JOIN aybar.ticket_historials a ON a.id = s.id WHERE a.id IS NULL AND s.deleted_at IS NULL");
        DB::statement("INSERT INTO aybar.ticket_derivados (id, ticket_id, de_area_id, a_area_id, usuario_deriva_id, usuario_recibe_id, motivo, created_at, updated_at, deleted_at) SELECT id, ticket_id, de_area_id, a_area_id, usuario_deriva_id, usuario_recibe_id, motivo, created_at, updated_at, deleted_at FROM {$source}.ticket_derivados s LEFT JOIN aybar.ticket_derivados a ON a.id = s.id WHERE a.id IS NULL");
    }

    private function migrateCitas($source)
    {
        DB::statement("INSERT INTO aybar.motivo_citas SELECT * FROM {$source}.motivo_citas m WHERE NOT EXISTS(SELECT 1 FROM aybar.motivo_citas WHERE id=m.id)");
        DB::statement("INSERT INTO aybar.estado_citas SELECT * FROM {$source}.estado_citas m WHERE NOT EXISTS(SELECT 1 FROM aybar.estado_citas WHERE id=m.id)");
        DB::statement("INSERT INTO aybar.sedes SELECT * FROM {$source}.sedes m WHERE NOT EXISTS(SELECT 1 FROM aybar.sedes WHERE id=m.id)");
        DB::statement("INSERT INTO aybar.citas (id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, area_id, ticket_id, usuario_crea_id, sede_id, motivo_cita_id, estado_cita_id, fecha_inicio, fecha_fin, fecha_cierre, asunto_solicitud, descripcion_solicitud, asunto_respuesta, descripcion_respuesta, dni, nombres, origen, usuario_valida_id, fecha_validacion, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
            SELECT id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, area_id, ticket_id, usuario_crea_id, sede_id, motivo_cita_id, estado_cita_id, fecha_inicio, fecha_fin, fecha_cierre, asunto_solicitud, descripcion_solicitud, asunto_respuesta, descripcion_respuesta, dni, nombres, origen, usuario_valida_id, fecha_validacion, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at
            FROM {$source}.citas s LEFT JOIN aybar.citas a ON a.id = s.id WHERE a.id IS NULL");
    }

    private function migratePagosEstructura($source)
    {
        DB::statement("INSERT INTO aybar.estado_solicitud_evidencia_pagos (id, nombre, color, icono, activo, created_at, updated_at, deleted_at) SELECT id, nombre, color, icono, activo, created_at, updated_at, deleted_at FROM {$source}.estado_evidencia_pagos s LEFT JOIN aybar.estado_solicitud_evidencia_pagos a ON a.id = s.id WHERE a.id IS NULL");
    }

    private function migratePagosSolicitudes($source)
    {
        DB::statement("INSERT INTO aybar.solicitud_evidencia_pagos (id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, estado_evidencia_pago_id, lote_completo, codigo_cuota, razon_social, nombre_proyecto, etapa, manzana, lote, codigo_cliente, numero_cuota, transaccion_id, fecha_operacion, fecha_vencimiento, monto_operacion, slin_monto, slin_penalidad, slin_numero_operacion, comprobante, ticket, slin_asbanc, slin_evidencia, resuelto_manual, observacion, usuario_valida_id, fecha_validacion, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at)
            SELECT id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, estado_evidencia_pago_id, lote_completo, codigo_cuota, razon_social, nombre_proyecto, etapa, manzana, lote, codigo_cliente, numero_cuota, transaccion_id, fecha_operacion, fecha_vencimiento, monto_operacion, slin_monto, slin_penalidad, slin_numero_operacion, comprobante, ticket, slin_asbanc, slin_evidencia, resuelto_manual, observacion, usuario_valida_id, CASE WHEN CAST(fecha_validacion AS CHAR) = '0000-00-00 00:00:00' THEN NULL ELSE fecha_validacion END, created_by, updated_by, deleted_by, created_at, updated_at, deleted_at
            FROM {$source}.solicitud_evidencia_pagos s LEFT JOIN aybar.solicitud_evidencia_pagos a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.evidencia_pagos (id, solicitud_evidencia_pago_id, estado_evidencia_pago_id, path, url, extension, numero_operacion, banco, monto, fecha, es_reenvio, slin_respuesta, observacion, created_at, updated_at, deleted_at)
            SELECT e.id, e.solicitud_evidencia_pago_id, e.estado_evidencia_pago_id, e.path, e.url, e.extension, e.numero_operacion, e.banco, e.monto, e.fecha, e.es_reenvio, e.slin_respuesta, e.observacion, e.created_at, e.updated_at, e.deleted_at
            FROM {$source}.evidencia_pagos e INNER JOIN aybar.solicitud_evidencia_pagos s ON s.id = e.solicitud_evidencia_pago_id LEFT JOIN aybar.evidencia_pagos a ON a.id = e.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.solicitud_evidencia_pago_emails (id, solicitud_evidencia_pago_id, mensaje, enviado_at, created_at, updated_at)
            SELECT c.id, c.solicitud_evidencia_pago_id, c.mensaje, c.enviado_at, c.created_at, c.updated_at FROM {$source}.correo_evidencia_pagos c INNER JOIN aybar.solicitud_evidencia_pagos s ON s.id = c.solicitud_evidencia_pago_id LEFT JOIN aybar.solicitud_evidencia_pago_emails a ON a.id = c.id WHERE a.id IS NULL");
    }

    private function migrateEvidenciaAntigua($source)
    {
        DB::statement("INSERT INTO aybar.evidencia_pago_antiguos (id, unidad_negocio_id, proyecto_id, cliente_id, gestor_id, imagen_url, operacion_numero, operacion_hora, `union`, cuota_fija, monto, pago_de, codigo_cuenta, nombre_archivo, moneda, medio_pago, fecha_deposito, observacion, estado_solicitud_evidencia_pago_id, estado_registro, dni_cliente, codigo_cliente, nombres_cliente, razon_social, proyecto_nombre, etapa, lote, numero_cuota, gestor, fecha_registro, usuario_valida_id, validador, fecha_validacion, created_by, updated_by, deleted_by, created_at, updated_at)
            SELECT e.id, e.unidad_negocio_id, e.proyecto_id, e.cliente_id, e.gestor_id, e.imagen_url, e.operacion_numero, e.operacion_hora, e.`union`, e.cuota_fija, e.monto, e.pago_de, e.codigo_cuenta, e.nombre_archivo, e.moneda, e.medio_pago, e.fecha_deposito, e.observacion, e.estado_evidencia_pago_id, e.estado_registro, e.dni_cliente, e.codigo_cliente, e.nombres_cliente, e.razon_social, e.proyecto_nombre, e.etapa, e.lote, e.numero_cuota, e.gestor, e.fecha_registro, e.usuario_valida_id, e.validador, e.fecha_validacion, e.created_by, e.updated_by, e.deleted_by, e.created_at, e.updated_at
            FROM {$source}.evidencia_pago_antiguos e LEFT JOIN aybar.evidencia_pago_antiguos a ON a.id = e.id WHERE a.id IS NULL");
    }

    private function migrateCavali($source)
    {
        DB::statement("INSERT INTO aybar.estado_solicitud_digitalizar_letras (id, nombre, color, icono, activo, created_at, updated_at, deleted_at) SELECT id, nombre, color, icono, activo, created_at, updated_at, deleted_at FROM {$source}.estado_solicitud_digitalizar_letras s LEFT JOIN aybar.estado_solicitud_digitalizar_letras a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.solicitud_digitalizar_letras (id, unidad_negocio_id, proyecto_id, cliente_id, lote_completo, codigo_cuota, razon_social, nombre_proyecto, etapa, manzana, lote, codigo_cliente, numero_cuota, codigo_venta, fecha_vencimiento, importe_cuota, estado_solicitud_digitalizar_letra_id, created_at, updated_at, deleted_at)
            SELECT id, unidad_negocio_id, proyecto_id, cliente_id, lote_completo, codigo_cuota, razon_social, nombre_proyecto, etapa, manzana, lote, codigo_cliente, numero_cuota, codigo_venta, fecha_vencimiento, importe_cuota, 1, created_at, updated_at, deleted_at
            FROM {$source}.solicitud_digitalizar_letras s LEFT JOIN aybar.solicitud_digitalizar_letras a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.envios_cavali (id, fecha_corte, unidad_negocio_id, estado_solicitud_digitalizar_letra_id, enviado_at, archivo_zip, created_at, updated_at)
            SELECT id, fecha_corte, unidad_negocio_id, 1, enviado_at, archivo_zip, created_at, updated_at FROM {$source}.envios_cavali s LEFT JOIN aybar.envios_cavali a ON a.id = s.id WHERE a.id IS NULL");
        DB::statement("INSERT INTO aybar.envio_cavali_solicitud (envios_cavali_id, solicitud_digitalizar_letras_id, created_at, updated_at)
            SELECT envios_cavali_id, solicitud_digitalizar_letras_id, created_at, updated_at FROM {$source}.envio_cavali_solicitud s LEFT JOIN aybar.envio_cavali_solicitud a ON a.envios_cavali_id = s.envios_cavali_id AND a.solicitud_digitalizar_letras_id = s.solicitud_digitalizar_letras_id WHERE a.envios_cavali_id IS NULL");
    }
}
