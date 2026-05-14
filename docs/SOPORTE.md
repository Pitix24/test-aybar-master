# Módulo Soporte

## Resumen

Documento que describe las modificaciones y artefactos creados para el módulo **Soporte**.

## Contenido añadido / modificado

### Migraciones:

- `database/migrations/2026_05_07_120100_create_tipo_soportes_table.php`
- `database/migrations/2026_05_07_120200_create_prioridad_soportes_table.php`
- `database/migrations/2026_05_07_120300_create_estado_soportes_table.php`
- `database/migrations/2026_05_07_120400_create_soportes_table.php`
- `database/migrations/2026_05_12_120500_create_soporte_archivos_table.php` (Nueva - Archivos adjuntos)
- `database/migrations/2026_05_12_120600_add_soporte_archivo_permissions.php` (Nueva - Permisos)

### Modelos (app/Models/Erp/Soporte):

- `TipoSoporte.php`
- `PrioridadSoporte.php`
- `EstadoSoporte.php`
- `Soporte.php` (actualizado con relación de archivos)
- `SoporteArchivo.php` (nuevo)

### Livewire (app/Livewire/Erp/Soporte):

**Gestión de Tickets:**

- `SoporteLista.php` — lista de tickets
- `SoporteCrear.php` — formulario de creación
- `SoporteVer.php` — vista detalle
- `SoporteEditar.php` — edición de ticket

**Gestión de Archivos Adjuntos:**

- `SoporteArchivo.php` (nuevo) — componente para manejar archivos adjuntos

**Submódulos TipoSoporte:**

- `TipoSoporte/TipoSoporteLista.php`
- `TipoSoporte/TipoSoporteCrear.php`
- `TipoSoporte/TipoSoporteVer.php`
- `TipoSoporte/TipoSoporteEditar.php`

**Submódulos PrioridadSoporte:**

- `PrioridadSoporte/PrioridadSoporteLista.php`
- `PrioridadSoporte/PrioridadSoporteCrear.php`
- `PrioridadSoporte/PrioridadSoporteVer.php`
- `PrioridadSoporte/PrioridadSoporteEditar.php`

**Submódulos EstadoSoporte:**

- `EstadoSoporte/EstadoSoporteLista.php`
- `EstadoSoporte/EstadoSoporteCrear.php`
- `EstadoSoporte/EstadoSoporteVer.php`
- `EstadoSoporte/EstadoSoporteEditar.php`

### Vistas (resources/views/livewire/erp/soporte):

- `soporte-crear.blade.php`
- `soporte-ver.blade.php` (actualizada con componente de archivos)
- `soporte-editar.blade.php` (actualizada con componente de archivos)
- `soporte-lista.blade.php`
- `soporte-archivo.blade.php` (nueva - vista del componente de archivos)

Submódulos:

- `tipo-soporte/tipo-soporte-*.blade.php`
- `prioridad-soporte/prioridad-soporte-*.blade.php`
- `estado-soporte/estado-soporte-*.blade.php`

### Rutas:

- `routes/erp/soporte.php`

### Seeders:

- `database/seeders/TipoSoporteSeeder.php`
- `database/seeders/PrioridadSoporteSeeder.php`
- `database/seeders/EstadoSoporteSeeder.php`

## Permisos y middleware

Las rutas usan permisos para controlar el acceso. Permisos principales:

### Navegación y Visualización:

- `modulo-soporte.ver` — acceso al módulo
- `soporte.navegacion` — navegación dentro del módulo
- `soporte.vista-lista`, `soporte.vista-ver`, `soporte.vista-crear`, `soporte.vista-editar` — acceso a vistas específicas
- `soporte.supervisor` — administración de catálogos (tipo, prioridad, estado)

### Gestión de Archivos (Nuevos):

- `soporte.accion-agregar-archivo` — permitir subir/adjuntar archivos
- `soporte.accion-ver-archivo` — permitir descargar/ver archivos
- `soporte.accion-eliminar-archivo` — permitir eliminar archivos

### Policy (Nueva - Mayo 14, 2026):

Se ha implementado `SoportePolicy` (`app/Policies/SoportePolicy.php`) para validación explícita sin bypaseo de super-admin.

**Métodos disponibles:**

- `viewAny(User $user)` — validar acceso a listar soportes
- `view(User $user, Soporte $soporte)` — validar acceso a ver un soporte
- `create(User $user)` — validar acceso a crear soporte
- `update(User $user, Soporte $soporte)` — validar acceso a editar soporte
- `delete(User $user, Soporte $soporte)` — validar acceso a eliminar soporte
- `attachFile(User $user, Soporte $soporte)` — validar acceso a adjuntar archivos
- `viewFiles(User $user, Soporte $soporte)` — validar acceso a ver archivos
- `deleteFile(User $user, Soporte $soporte)` — validar acceso a eliminar archivos
- `manageCatalogues(User $user)` — validar acceso a gestionar catálogos

**Características:**

- ✅ No aplica bypass de super-admin: todos deben tener permiso explícito
- ✅ Integrada en componentes Livewire (SoporteLista, SoporteVer, SoporteCrear, SoporteEditar)
- ✅ Usar con `$this->authorize('método', $modelo)` en componentes
- ✅ Registrada en `AuthServiceProvider`

## Flujo y relaciones principales

### Estructura de Datos:

- La tabla `soportes` contiene referencias a `tipo_soportes`, `prioridad_soportes` y `estado_soportes`
- La tabla `soporte_archivos` usa relación polimórfica (similar a `ticket_archivos`) para adjuntar archivos a tickets
- El campo `archivable_type` y `archivable_id` permiten extender en el futuro para otros tipos de entidades

### Flujo de Archivos:

1. **Creador del Ticket**: Puede adjuntar archivos al crear o editar un ticket de soporte
2. **Gestor Asignado**: Puede adjuntar archivos de confirmación o resolución
3. **Vista Lectura**: Los archivos se visualizan en la vista detalle sin permitir edición (si se pasa `soloLectura=true`)

## Cómo probar localmente

### 1. Ejecutar migraciones:

```bash
php artisan migrate
```

### 2. Ejecutar seeders (si existen):

```bash
php artisan db:seed --class=TipoSoporteSeeder
php artisan db:seed --class=PrioridadSoporteSeeder
php artisan db:seed --class=EstadoSoporteSeeder
```

### 3. Asignar permisos a roles:

En la administración de roles/permisos, asignar:

- `soporte.accion-agregar-archivo`
- `soporte.accion-ver-archivo`
- `soporte.accion-eliminar-archivo`

A los roles correspondientes (ej: `soporte-tecnico`, `supervisor`, `gestor`).

### 4. Probar en el navegador (autenticado con usuario que tenga permisos):

- `/soporte` — listado de tickets
- `/soporte/crear` — crear ticket
- `/soporte/editar/{id}` — editar y adjuntar archivos
- `/soporte/ver/{id}` — ver ticket con archivos

## Notas de implementación y consideraciones

### Archivos Adjuntos:

- **Almacenamiento**: Los archivos se guardan en `storage/app/public/soportes/{soporte_id}/`
- **Tipos soportados**: PDF, DOCX, XLSX, PPTX, JPG, JPEG, PNG
- **Tamaño máximo**: 51 MB por archivo
- **Soft Deletes**: Los archivos borrados se marcan con soft delete, pudiendo ser recuperados si es necesario

### Validaciones:

- Asegurar que `SoporteCrear` y `SoporteEditar` validen correctamente los campos obligatorios
- El componente `SoporteArchivo` valida automáticamente extensiones y tamaños de archivo
- Los permisos deben estar asignados antes de permitir acciones

### Estados de Soporte:

- `ABIERTO` — estado inicial
- `EN_PROGRESO` — ticket en proceso
- `RESUELTO` — ticket resuelto
- `CERRADO` — ticket cerrado definitivamente

### Funcionalidades Futuras:

- Integrar mensajes/comentarios en el soporte (similar a TicketMensaje)
- Historial de cambios (auditoria)
- Notificaciones automáticas al asignar o cambiar estado
- Carga masiva de archivos

## Archivos referenciados (para revisión rápida)

- [Rutas de Soporte](routes/erp/soporte.php)
- [Policy de Soporte](app/Policies/SoportePolicy.php) (Nueva - Autorización sin bypass)
- [AuthServiceProvider](app/Providers/AuthServiceProvider.php) (Registro de policies)
- [Componente SoporteArchivo](app/Livewire/Erp/Soporte/SoporteArchivo.php)
- [Vista SoporteArchivo](resources/views/livewire/erp/soporte/soporte-archivo.blade.php)
- [Modelo SoporteArchivo](app/Models/Erp/Soporte/SoporteArchivo.php)
- [Migraciones Soporte](database/migrations) (filtrar por 2026_05_07, 2026_05_12, 2026_05_13)
- [Livewire Soporte](app/Livewire/Erp/Soporte)
- [Modelos Soporte](app/Models/Erp/Soporte)
- [Seeders Soporte](database/seeders)

---

## Cambios Recientes

### Mayo 12, 2026:

✅ **Sistema de Archivos Adjuntos Implementado**

- Nueva tabla `soporte_archivos` con relación polimórfica
- Componente Livewire `SoporteArchivo` para manejar carga y eliminación
- Integración en vistas de Editar y Ver Soporte
- Permisos específicos para gestión de archivos
- Soporte para múltiples tipos de archivo (PDF, Office, imágenes)

### Mayo 13, 2026:

✅ **Campo de Observaciones/Notas Agregado**

- Nueva columna `observaciones` en tabla `soportes`
- Visible solo en vistas de Editar y Ver
- Bloque destacado para seguimiento interno del caso
- Soporte para texto de hasta 2000 caracteres

### Mayo 14, 2026:

✅ **Policy de Autorización Implementada (Sin Bypass de Super-admin)**

- Nueva `SoportePolicy` con validaciones explícitas de permiso
- Integrada en componentes Livewire (SoporteLista, SoporteVer, SoporteCrear, SoporteEditar)
- Super-admin debe tener permisos explícitos para acceder a soportes
- Métodos para validar: listar, ver, crear, editar, eliminar, adjuntar archivos
- Registrada en `AuthServiceProvider`

---

Si tienes dudas o necesitas mejoras adicionales:

- Agregar pruebas unitarias o de integración
- Implementar historial de carga de archivos
- Crear reportes con análisis de archivos
- Extender el sistema para otros módulos
