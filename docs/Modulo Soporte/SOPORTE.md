# Módulo Soporte

## Resumen

Documento que describe las modificaciones y artefactos creados para el módulo **Soporte**.

## Contenido añadido / modificado

- Migraciones:
    - database/migrations/2026_05_07_120100_create_tipo_soportes_table.php
    - database/migrations/2026_05_07_120200_create_prioridad_soportes_table.php
    - database/migrations/2026_05_07_120300_create_estado_soportes_table.php
    - database/migrations/2026_05_07_120400_create_soportes_table.php

- Modelos (app/Models/Erp/Soporte):
    - TipoSoporte.php
    - PrioridadSoporte.php
    - EstadoSoporte.php
    - Soporte.php

- Livewire (app/Livewire/Erp/Soporte):
    - SoporteLista.php — lista de tickets
    - SoporteCrear.php — formulario de creación
    - SoporteVer.php — vista detalle
    - SoporteEditar.php — edición de ticket

    Submódulos TipoSoporte:
    - TipoSoporte/TipoSoporteLista.php
    - TipoSoporte/TipoSoporteCrear.php
    - TipoSoporte/TipoSoporteVer.php
    - TipoSoporte/TipoSoporteEditar.php

    Submódulos PrioridadSoporte:
    - PrioridadSoporte/PrioridadSoporteLista.php
    - PrioridadSoporte/PrioridadSoporteCrear.php
    - PrioridadSoporte/PrioridadSoporteVer.php
    - PrioridadSoporte/PrioridadSoporteEditar.php

    Submódulos EstadoSoporte:
    - EstadoSoporte/EstadoSoporteLista.php
    - EstadoSoporte/EstadoSoporteCrear.php
    - EstadoSoporte/EstadoSoporteVer.php
    - EstadoSoporte/EstadoSoporteEditar.php

- Rutas:
    - routes/erp/soporte.php

- Seeders:
    - database/seeders/TipoSoporteSeeder.php
    - database/seeders/PrioridadSoporteSeeder.php
    - database/seeders/EstadoSoporteSeeder.php

## Permisos y middleware

Las rutas usan permisos para controlar el acceso. Permisos usados (ejemplos observados):

- `modulo-soporte.ver`
- `soporte.navegacion`
- `soporte.vista-lista`, `soporte.vista-ver`, `soporte.vista-crear`, `soporte.vista-editar`
- `soporte.supervisor` (para administración de tipos, prioridades y estados)

Estos permisos deben existir en la tabla de permisos y asignarse a los roles correspondientes (por ejemplo `soporte-tecnico`, `supervisor`).

## Flujo y relaciones principales

- La tabla `soportes` contiene referencias a `tipo_soportes`, `prioridad_soportes` y `estado_soportes`.
- Livewire proporciona las vistas para CRUD de tickets y para gestionar catálogos (tipo/prioridad/estado).

## Cómo probar localmente

1. Ejecutar migraciones:

```bash
php artisan migrate
```

2. Ejecutar seeders (si existen):

```bash
php artisan db:seed --class=TipoSoporteSeeder
php artisan db:seed --class=PrioridadSoporteSeeder
php artisan db:seed --class=EstadoSoporteSeeder
```

3. Ver rutas en el navegador (autenticado con usuario que tenga permisos):

- `/soporte` — listado de tickets
- `/soporte/crear` — crear ticket

4. Revisar en la administración de roles/permiso que los permisos listados estén asignados.

## Notas de implementación y consideraciones

- Asegurar validaciones en `SoporteCrear` y `SoporteEditar` para campos obligatorios.
- Revisar los seeders para nombres estándar de estado (`EN_PROGRESO`, `RESUELTO`, `CERRADO`).
- Añadir pruebas de integración que cubran creación de ticket y transición de estados si se desea robustez.

## Archivos referenciados (para revisión rápida)

- [Rutas de Soporte](routes/erp/soporte.php)
- Migraciones: [database/migrations](database/migrations)
- Livewire: [app/Livewire/Erp/Soporte](app/Livewire/Erp/Soporte)
- Modelos: [app/Models/Erp/Soporte](app/Models/Erp/Soporte)
- Seeders: [database/seeders](database/seeders)
