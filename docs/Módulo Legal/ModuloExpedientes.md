# Plan: Módulo Expedientes (Sistema INDECOPI)

Crear un módulo completo para gestionar Expedientes INDECOPI con recepción automática de correos, asignación de gestores, generación de Tickets Hijos desde plantillas, y notificaciones automáticas. Basado en la arquitectura de Libro_Reclamaciones pero más robusto.

---

## FASES

### FASE 1: Base de Datos & Modelos ✅ (Parcialmente completada)

1. Completar migration expedientes con campos:

- `unidad_negocio_id` -> Razón Social (Empresa): Completado según el correo remitente.
- `codigo_expediente` -> Código de Expediente: “Ejem:447-2025/CC2”
- `cuerpo` -> Cuerpo del correo con link incluidos
- `gestor_id` -> Gestor encargado de la Gestión Interna del Expediente
- `responsable_id` -> Gestor encargado de la Gestión Legal del Expediente
- `origen` -> Origen: Correo Indecopi o Migracion (Por defecto Indecopi, para los creados automaticamente)
- `tipo_expediente_id`-> Expediente Nuevo o Notificación
- `alerta` -> JSON + Auditoría
- `ticket_id` -> Una vez generado el Expediente, este deberá generar un Ticket Asociado.
- `fecha_notificacion`-> Fecha de Notificación: Se coloca la fecha de llegada de la notificación
- timestamps: `created_at`, `updated_at`, `deleted_at`
- auditoría: `created_by`, `updated_by`, `deleted_by`

2. Crear Modelo `Expediente` con relaciones: `unidadNegocio()`, `gestor()`, `ticket()`

### FASE 2: Recepción Automática de Correos (Job + Service)

1. **Job** `ProcessExpedienteEmail`: Recibe correo, extrae código (regex: `NNN-YYYY/XX2`), mapea unidad por email remitente, verifica si expediente existe
2. **Service** `ProcessadorCorreoExpediente`: Métodos para extraer código, mapear unidad, validar duplicados, extraer enlaces
3. **Config** `config/expedientes.php`: Mapeo de 6 emails TSMP Google → unidades de negocio
4. **Listener** notifica al gestor asignado cuando se crea expediente (incompleto = notificación especial)

### FASE 3: Módulo ERP - 4 Componentes Livewire (paralelo con FASE 2)

1. **ExpedienteLista**: Tabla con filtros (estado, tipo, unidad, gestor) + KPIs + búsqueda
2. **ExpedienteVer**: Detalle readonly, timeline de alertas, botón para crear tickets hijos
3. **ExpedienteCrear**: Formulario para entrada manual (Indecopi/Migración)
4. **ExpedienteEditar**: Cambiar código si incompleto, reasignar gestor, cambiar estado, agregar notas

### FASE 4: Tickets Hijos & Plantillas (depende de FASE 1)

1. Completar migration TemplateTicketHijo:

- Deberá de tener los campos iguales para el Formulario al crear un Ticket Hijo/Asociado, en espera. De esta manera, se heredarán ciertos campos del Ticket ASOCIADO al Expediente, para crear el Hijo de manera rápida. EX: Solicitar un Estado de Cuenta a Backoffice es algo repetitivo, por lo que tener Listo una Plantilla que me permita directamente Crear un Ticket Hijo para el Ticket ASOCIADO al Expediente agilizaría el trabajo del Gestor.

2. Crear Modelo `TemplateTicketHijo` para gestionar plantillas de subtareas
3. **GestionPlantillasTicketHijo**: CRUD de plantillas por expediente (nombre, área, canal, prioridad, orden)
4. **GeneradorTicketsHijos**: Service que crea N tickets desde plantilla al hacer clic en "Crear Tickets"
5. **Relación many-to-many**: Tabla pivot `expediente_ticket`

### FASE 5: Notificaciones Mejoradas (depende de FASE 1)

1. **Job** `NotificarExpedienteVencimientoCercano`: Corre cada 15 min, busca vencimientos próximos
2. **Job** `NotificarExpedienteVencido`: Marca estado EN_ESPERA, notificación escalada
3. **Listener** `ActualizarAlertasSegunEstado`: Recalcula vencimientos al cambiar estado

---

## Archivos Clave a Crear/Modificar

### Migraciones

- `database/migrations/2026_05_18_155616_create_expedientes_table.php.php` — _actualizar con campos adicionales_
- `database/migrations/2026_05_18_XXXXXX_create_template_ticket_hijos_table.php`
- `database/migrations/2026_05_18_XXXXXX_create_expediente_ticket_pivot_table.php`

### Modelos

- `app/Models/Expediente/Expediente.php`
- `app/Models/Expediente/TemplateTicketHijo.php`

### Services

- `app/Services/Expediente/ProcessadorCorreoExpediente.php`
- `app/Services/Expediente/GeneradorTicketsHijos.php`

### Jobs

- `app/Jobs/Expediente/ProcessExpedienteEmail.php`
- `app/Jobs/Expediente/NotificarExpedienteVencimientoCercano.php`
- `app/Jobs/Expediente/NotificarExpedienteVencido.php`

### Listeners

- `app/Listeners/Expediente/EnviarNotificacionesExpediente.php`
- `app/Listeners/Expediente/ActualizarAlertasSegunEstado.php`

### Livewire Components

- `app/Livewire/Erp/Expediente/ExpedienteLista.php`
- `app/Livewire/Erp/Expediente/ExpedienteVer.php`
- `app/Livewire/Erp/Expediente/ExpedienteCrear.php`
- `app/Livewire/Erp/Expediente/ExpedienteEditar.php`
- `app/Livewire/Erp/Expediente/GestionPlantillasTicketHijo.php`

### Config & Rutas

- `config/expedientes.php` — Mapeo emails a unidades
- `routes/erp/expediente.php`

---

## Patrones a Reutilizar

- `app/Models/LibroReclamacion/LibroReclamacion.php` — Estructura de relaciones
- `app/Livewire/Erp/LibroReclamacion/` — Componentes como template
- `app/Services/LibroReclamacion/LibroReclamacionNumeroService.php` — Patrón de generación de números
- `routes/erp/legal.php` — Estructura consolidada de rutas legales

---

## Contexto de Requisitos

### Tipos de Creación

- **RECEPCION POR CORREO**: Automático, origen INDECOPI
- **CREACION MANUAL**: Manual, origen INDECOPI o MIGRACION

### Código de Expediente

- Formato: `NNN-YYYY/XX2` (ej: 447-2025/CC2)
- Se extrae del correo remitente
- Si no se identifica → "NO IDENTIFICADO" + notificación interna

### Datos Presentados en Expediente

- Razón Social (Empresa): Del correo remitente
- Fecha de Notificación: Fecha de llegada del correo
- Código de Expediente: Extraído o "NO IDENTIFICADO"
- Cuerpo del correo con enlaces
- Tarea: "Completar y Validar" (editable)
- Responsable: Gestor de Reclamos (asignable)
- Tiempo de atención: 4 horas (temporizador)
- Origen: Indecopi (para correo) o Indecopi/Migración (para manual)
- Tipo de registro: Depende del estado
- Alerta: Campo completado por eventos del sistema

### Tipos de Registro

- **EXPEDIENTE_NUEVO**: Nuevo expediente identificado
- **NOTIFICACION**: Actualización de expediente existente
- **EXPEDIENTE_INCOMPLETO**: Código no identificado, datos parciales

### Transiciones de Tipo Registro

- EXPEDIENTE_INCOMPLETO → EXPEDIENTE_NUEVO (al editar y validar código)
- EXPEDIENTE_INCOMPLETO → NOTIFICACION (si ya existe en el sistema)

### Estados del Expediente

- NUEVO: Recién creado
- EN_PROCESO: En evaluación/gestión
- EN_ESPERA: Esperando resolución de otras áreas (tickets hijos)
- GESTIONADO: Gestor en espera de confirmación/parte
- CERRADO: Resuelto

### Unidad de Negocio (por email remitente)

- notificacionesindecopi@aybarsac.com → Aybar
- notificacionesindecopilotes@aybarsac.com → Lotes
- notificacionesindecopiinvestment@aybarsac.com → Investment
- notificacionesindecopivivanorte@aybarsac.com → Viva Norte
- notificacionesindecopipontevedra@aybarsac.com → Pontevedra
- notificacionesindecopicomexlat@aybarsac.com → Comex Lat

### Tickets Hijos

- Son subtareas con estados propios
- Creados desde Plantillas de Tickets Hijos
- N tickets por expediente
- Plantilla define: Nombre, descripción, área, canal, prioridad, subtipo, orden

### Notificaciones Mejoradas

- Temporizador de 4 horas con alertas automáticas
- Alerta cuando faltan 30 minutos para vencer
- Alerta cuando ya venció
- Sistema centralizado de notificaciones (mejorar existente, no duplicar)

---

## Verification Steps

1. ✅ Migration corre sin errores
2. ✅ Modelos creables desde Tinker
3. ✅ Rutas `/erp/expedientes` accesibles (permiso `expediente.gestor`)
4. ✅ Email de prueba procesa correctamente y crea expediente
5. ✅ Sistema de notificaciones envía alertas
6. ✅ Generador de tickets hijos crea N tickets desde plantilla

---

## Decisiones Arquitectónicas

- ✅ Reutilizar patrones de Libro_Reclamaciones (consistencia)
- ✅ Job async para correos (no bloquea UI)
- ✅ Relación many-to-many Expediente ↔ Ticket (N tickets por expediente)
- ✅ Campo `tipo_origen` diferencia RECEPCION vs MANUAL + `origen` para INDECOPI/MIGRACION
- ✅ Auditoría completa + soft deletes
- ✅ Estado EN_ESPERA para esperas de otras áreas

---

## Scope

### Incluido

- 5 fases de implementación (DB → Modelos → Recepción → Gestión → Notificaciones)
- Módulo de Plantillas de Tickets Hijos
- Integración con sistema de tickets existente
- Roles y permisos específicos
- Sistema de alertas mejorado

### Excluido (out of scope)

- Integración con Gmail API directa (usar interface genérica)
- Workflows automáticos avanzados
- Portal web público (solo ERP interno)
