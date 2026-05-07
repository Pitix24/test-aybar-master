# Plan: Soporte Autoadministrable por Gestores

## Resumen

Este documento define la ejecución para convertir el módulo Soporte en un sistema autoadministrable por gestores, replicando el patrón de Ticket para los catálogos:

- `tipo-soporte`
- `prioridad-soporte`
- `estado-soporte`

El objetivo es reemplazar enums hardcodeados por tablas maestras administrables desde ERP, con CRUD completo (Lista, Crear, Editar, Ver), incluyendo configuración de `color` e `icono`.

## Alcance

Incluye:

- Nuevas tablas de catálogos para Soporte.
- Modelos y relaciones Eloquent.
- CRUD Livewire + Blade para los tres catálogos.
- Rutas, permisos y menú ERP.
- Refactor de `SoporteCrear`, `SoporteEditar`, `SoporteVer`, `SoporteLista` para consumir catálogos desde BD.
- Migración de datos de enums actuales a FKs (2 etapas).

No incluye por ahora:

- Nuevas capacidades funcionales fuera de alcance (SLA avanzado, derivación, chat de soporte, etc.).

## Decisiones Aprobadas

1. Convención de nombres de módulos/permisos:
    - `tipo-soporte`
    - `prioridad-soporte`
    - `estado-soporte`
2. Campos visuales: mismo formato que Ticket (`color` HEX + clase de icono Font Awesome).
3. Estrategia de migración: 2 etapas (segura).

## Estrategia Técnica (2 Etapas)

### Etapa A: Convivencia y Backfill (sin romper operación)

1. Crear tablas maestras:
    - `tipo_soportes`
    - `prioridad_soportes`
    - `estado_soportes`
2. Cargar seeders iniciales con valores actuales del código.
3. Agregar columnas FK nullable en `soportes`:
    - `tipo_soporte_id`
    - `prioridad_soporte_id`
    - `estado_soporte_id`
4. Ejecutar backfill enum -> FK.
5. Actualizar aplicación para consumir FKs y relaciones.

### Etapa B: Consolidación

1. Validar cobertura 100% del backfill (sin nulos).
2. Convertir FKs a NOT NULL.
3. Remover columnas enum antiguas de `soportes`.
4. Retirar helpers hardcodeados del modelo (`tipos()`, `prioridades()`, `estados()`).

## Modelo de Datos Propuesto

### Tabla `tipo_soportes`

- `id`
- `nombre` (unique)
- `color` (HEX)
- `icono` (FA class)
- `activo` (bool, default true)
- `created_by`, `updated_by`, `deleted_by` (si se mantiene patrón ERP)
- `timestamps`, `softDeletes`

### Tabla `prioridad_soportes`

- `id`
- `nombre` (unique)
- `color` (HEX)
- `icono` (FA class)
- `activo` (bool, default true)
- `created_by`, `updated_by`, `deleted_by`
- `timestamps`, `softDeletes`

### Tabla `estado_soportes`

- `id`
- `nombre` (unique)
- `color` (HEX)
- `icono` (FA class)
- `activo` (bool, default true)
- `created_by`, `updated_by`, `deleted_by`
- `timestamps`, `softDeletes`

### Cambios en `soportes`

Agregar FKs:

- `tipo_soporte_id` -> `tipo_soportes.id`
- `prioridad_soporte_id` -> `prioridad_soportes.id`
- `estado_soporte_id` -> `estado_soportes.id`

## Fases de Implementación

### Fase 1 - Infraestructura de Catálogos

- Migraciones de `tipo_soportes`, `prioridad_soportes`, `estado_soportes`.
- Modelos Eloquent y relaciones base.
- Seeders iniciales para catálogos.

### Fase 2 - CRUD de Catálogos (igual a Ticket)

Por cada recurso (`tipo-soporte`, `prioridad-soporte`, `estado-soporte`):

- Livewire: `Lista`, `Crear`, `Editar`, `Ver`.
- Blades equivalentes al patrón Ticket.
- Soporte visual de `color` e `icono`.

### Fase 3 - Integración con Soporte Operativo

- Refactor de componentes Soporte:
    - `SoporteCrear`
    - `SoporteEditar`
    - `SoporteVer`
    - `SoporteLista`
- Validaciones `exists` sobre catálogos.
- Selects y badges basados en relaciones BD.

### Fase 4 - Seguridad y Navegación

- Rutas ERP para los 3 catálogos.
- Permisos por recurso (navegación, vistas, acciones).
- Menú principal ERP con accesos a listas y crear.

### Fase 5 - Consolidación Final (Etapa B)

- Endurecer FKs a obligatorias.
- Retirar enums y código hardcodeado legado.
- Limpieza de cachés y verificación final.

## Seed Inicial (a ejecutar al final de Fase 1)

### Tipos Soporte

- BUG
- MEJORA
- IMPLEMENTACION
- CONSULTA

### Prioridades Soporte

- BAJA
- MEDIA
- ALTA
- CRITICA

### Estados Soporte

- ABIERTO
- EN_PROGRESO
- EN_REVISION
- RESUELTO
- CERRADO

## Riesgos y Mitigaciones

1. Riesgo: ruptura por cambio string -> FK.
    - Mitigación: Etapa A con compatibilidad temporal y backfill validado.
2. Riesgo: vistas con N+1 queries.
    - Mitigación: eager loading (`with`) en listas/ver.
3. Riesgo: accesos sin permisos nuevos.
    - Mitigación: incluir permisos y asignación de roles en mismo lote.
4. Riesgo: inconsistencias visuales.
    - Mitigación: clonar estructura de Ticket y validar diseño con checklist UI.

## Checklist de Ejecución

- [x] Crear migraciones de catálogos Soporte.
- [x] Crear modelos de catálogos Soporte.
- [x] Crear seeders de catálogos Soporte.
- [x] Crear Livewire + Blade de `tipo-soporte` (lista/crear/editar/ver).
- [x] Crear Livewire + Blade de `prioridad-soporte` (lista/crear/editar/ver).
- [x] Crear Livewire + Blade de `estado-soporte` (lista/crear/editar/ver).
- [x] Registrar rutas ERP de catálogos.
- [ ] Registrar permisos de catálogos y asignarlos a roles.
- [ ] Incorporar accesos en menú principal ERP.
- [ ] Agregar FKs nullable en `soportes` y backfill.
- [ ] Refactor completo de `Soporte*` para usar relaciones de catálogo.
- [ ] Validar UI y flujo completo (crear, editar, ver, listar).
- [ ] Consolidación final: FKs obligatorias + retirar enums.
- [ ] Limpiar cache y validar rutas/vistas.

## Validación Técnica Final

Comandos de verificación al cierre de cada fase:

```bash
php artisan migrate
php artisan db:seed
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan optimize:clear
php artisan route:list | Select-String "soporte" -Context 1,1
```

## Bitácora de Avance

### 2026-05-07

- Plan aprobado por usuario para ejecutar migración a catálogos autoadministrables.
- Se define implementación en 2 etapas para minimizar riesgo.
- Se acuerda mantener formato de `color` e `icono` igual a Ticket.
- Fase 1 completada:
    - Migraciones creadas y ejecutadas: `tipo_soportes`, `prioridad_soportes`, `estado_soportes`.
    - Modelos creados: `TipoSoporte`, `PrioridadSoporte`, `EstadoSoporte`.
    - Seeders creados y ejecutados: `TipoSoporteSeeder`, `PrioridadSoporteSeeder`, `EstadoSoporteSeeder`.
    - Seeders registrados en `DatabaseSeeder`.
    - Validación de conteo tras seeding: tipo=4, prioridad=4, estado=5.
- Fase 2 completada:
    - 12 componentes Livewire creados (4 × 3 catálogos):
        - `TipoSoporteLista`, `TipoSoporteCrear`, `TipoSoporteEditar`, `TipoSoporteVer`
        - `PrioridadSoporteLista`, `PrioridadSoporteCrear`, `PrioridadSoporteEditar`, `PrioridadSoporteVer`
        - `EstadoSoporteLista`, `EstadoSoporteCrear`, `EstadoSoporteEditar`, `EstadoSoporteVer`
    - 12 vistas Blade creadas con formularios e iconografía completa.
    - Rutas ERP registradas con naming: `erp.tipo-soporte.*`, `erp.prioridad-soporte.*`, `erp.estado-soporte.*`.
    - Patrón de componentes exacto a `EstadoTicket` (Lista con paginación, Crear/Editar con validación, Ver con detalles).
    - Componentes incluyen: autorización, validación con `rules()`, transacciones BD, logging, alertas `alertaLivewire`.
    - Vistas con CSS grid `g_*` consistent con módulo Soporte existente, soportan color/icono preview.
    - Rutas cacheadas y validadas: `php artisan route:list` muestra todas las rutas (12 nuevas).
    - Modelos testean carga exitosa via `tinker`.
