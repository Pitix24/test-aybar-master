# Plan de Implementacion - Ticket Libro Reclamacion (Fase 1)

## Objetivo
Implementar un modulo ERP separado para gestion legal de reclamos, sin relacion con la tabla `tickets` de ATC.

## Alcance Fase 1
1. Crear tabla operativa `ticket_libro_reclamacions`.
2. Crear modelo con auditoria (`created_by`, `updated_by`, `deleted_by`) y `softDeletes`.
3. Crear CRUD ERP base: lista, ver, crear, editar y eliminar logico.
4. Aplicar permisos dedicados al modulo legal.
5. Mantener `libro_reclamacions` como intake historico de origen.

## Estructura de Datos
Tabla nueva: `ticket_libro_reclamacions`

Campos principales:
- `id`
- `codigo` (unico)
- `libro_reclamacion_ticket` (nullable, referencia a `libro_reclamacions.ticket`)
- `unidad_negocio_id` (nullable)
- `proyecto_id` (nullable)
- `cliente_id` (nullable)
- `gestor_id` (nullable)
- `estado_legal` (`NUEVO|EN_GESTION|OBSERVADO|RESUELTO|NO_PROCEDE|CERRADO`)
- `clasificacion` (`PROCEDE|NO_PROCEDE|PENDIENTE_REVISION`)
- `nota_fuente` (text nullable)
- `observaciones_internas` (text nullable)
- `assigned_at` (datetime nullable)
- `created_by`, `updated_by`, `deleted_by` (nullable)
- `created_at`, `updated_at`, `deleted_at`

## Reglas Iniciales
- Estado inicial por defecto: `NUEVO`.
- Clasificacion por defecto: `PENDIENTE_REVISION`.
- Campos bloqueados en editar:
  - `codigo`
  - `libro_reclamacion_ticket`
  - `created_by`
  - `created_at`
- `assigned_at` se actualiza cuando cambia `gestor_id`.

## Permisos del Modulo
- `modulo-libro-reclamacion.ver`
- `ticket-libro-reclamacion.navegacion`
- `ticket-libro-reclamacion.lista`
- `ticket-libro-reclamacion.ver`
- `ticket-libro-reclamacion.crear`
- `ticket-libro-reclamacion.editar`
- `ticket-libro-reclamacion.eliminar`
- `ticket-libro-reclamacion.exportar-filtro`
- `ticket-libro-reclamacion.exportar-todo`

## Rutas ERP
Archivo: `routes/erp/libro-reclamacion.php`

Endpoints:
- `GET /erp/libro-reclamacion` -> Lista
- `GET /erp/libro-reclamacion/ver/{id}` -> Ver
- `GET /erp/libro-reclamacion/crear` -> Crear
- `GET /erp/libro-reclamacion/editar/{id}` -> Editar

## Componentes Livewire
- `LibroReclamacionLista`
- `LibroReclamacionVer`
- `LibroReclamacionCrear`
- `LibroReclamacionEditar`

## Checklist de Verificacion
1. Ejecutar migracion y validar existencia de tabla nueva.
2. Ingresar al listado ERP y confirmar carga de filtros.
3. Crear registro manual desde ERP.
4. Editar registro y cambiar gestor; validar `assigned_at`.
5. Eliminar logicamente y validar `deleted_at` y `deleted_by`.
6. Confirmar que este modulo no aparece ni depende del modulo ATC Tickets.

## Comandos de soporte
- Migrar:
  - `php artisan migrate --force`
- Refrescar permisos en entorno:
  - `php artisan db:seed --class=RolesYPermisosSeeder`

## Fase 2 (Proxima)
- Clasificacion automatica de intake web (`PROCEDE`, `NO_PROCEDE`, `PENDIENTE_REVISION`).
- Autodeteccion de cliente por DNI/nombre.
- Sugerencia de proyecto, manzana y lote para agilizar registro legal.
