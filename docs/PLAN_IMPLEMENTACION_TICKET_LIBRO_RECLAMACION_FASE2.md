# Plan de Implementacion - Ticket Libro Reclamacion (Fase 2)

## Objetivo
Implementar la automatizacion y preclasificacion desde libro_reclamacions hacia ticket_libro_reclamacions, manteniendo flujo separado de ATC y dejando control final al equipo legal.

## Alcance Funcional Fase 2
1. Clasificacion automatica de intake:
- PROCEDE
- NO_PROCEDE
- PENDIENTE_REVISION

2. Modo hibrido de creacion de ticket legal:
- Automatico para casos PROCEDE
- Manual para casos NO_PROCEDE o PENDIENTE_REVISION

3. Generacion de nota_fuente automatica con data clave del formulario web:
- Monto reclamado
- Descripcion del producto o servicio
- Tipo de solicitud
- Detalle del reclamo o queja
- Manzana
- Lote

4. Base para autodeteccion de cliente y contexto (sin forzar bloqueo):
- Busqueda por DNI o nombre
- Sugerencia de proyecto, manzana y lote

## Reglas de Clasificacion (propuesta inicial)
### Regla minima de identidad/contacto/contenido
Se considera PROCEDE si cumple:
- Identidad: numero_documento o nombre completo
- Contacto: email o telefono
- Contenido: detalle o descripcion

Se considera NO_PROCEDE si no cumple minimos.

Se considera PENDIENTE_REVISION si:
- Cumple parcialmente
- Hay informacion ambigua para consolidar cliente/proyecto

## Comportamiento del Flujo
### Flujo A - Automatico (PROCEDE)
1. Se registra en libro_reclamacions (ya existente).
2. Se evalua clasificacion.
3. Si clasificacion es PROCEDE, se crea registro en ticket_libro_reclamacions con:
- estado_legal = NUEVO
- clasificacion = PROCEDE
- nota_fuente completa
- libro_reclamacion_ticket apuntando al origen

### Flujo B - Manual Legal (NO_PROCEDE / PENDIENTE_REVISION)
1. Se registra en libro_reclamacions.
2. Se guarda clasificacion preliminar en intake.
3. No se crea ticket legal automaticamente.
4. Legal revisa intake y decide crear ticket manual desde ERP.

## Cambios Tecnicos Propuestos
### 1) Servicios de Dominio
Crear servicio de preclasificacion:
- app/Services/LibroReclamacion/LibroReclamacionClasificacionService.php
Responsabilidades:
- evaluar reglas de procede/no procede/pendiente
- devolver resultado estructurado con motivos

Crear servicio de orquestacion:
- app/Services/LibroReclamacion/TicketLibroReclamacionFactoryService.php
Responsabilidades:
- construir nota_fuente
- mapear data de libro_reclamacions a ticket_libro_reclamacions
- crear ticket legal cuando corresponda

### 2) Hook de Integracion
Agregar listener adicional al evento de registro de libro:
- Evento actual: LibroReclamacionRegistrado
- Listener nuevo sugerido:
  - app/Listeners/LibroReclamacion/CrearTicketLegalDesdeLibro.php

Nota:
Este listener no toca tabla tickets de ATC. Solo trabaja con ticket_libro_reclamacions.

### 3) Ajustes en Modelo de Intake (si aplica)
Si se desea trazabilidad de clasificacion en intake, agregar columnas en libro_reclamacions:
- clasificacion_preliminar (enum)
- clasificacion_motivo (text nullable)
- evaluado_at (datetime nullable)

Esto permite auditar porque un caso fue NO_PROCEDE o PENDIENTE_REVISION.

### 4) Reglas de Deduccion de Cliente
Prioridad sugerida:
1. Buscar por numero_documento
2. Si no hay, buscar por email
3. Si no hay, buscar por coincidencia de nombre
4. Si no se detecta cliente, dejar cliente_id null y marcar para revision

### 5) Nota Fuente Estandar
Formato sugerido:
- Monto reclamado: {valor}
- Descripcion del producto o servicio: {valor}
- Tipo de solicitud: {valor}
- Detalle del reclamo o queja: {valor}
- Manzana: {valor}
- Lote: {valor}

Con fallback N/D para campos vacios.

## Permisos y Seguridad
No se agregan nuevos permisos de fase 1.
La creacion automatica debe ejecutarse en backend sin exponer accion manual al cliente.
El equipo legal sigue siendo el unico con acceso de gestion en ERP.

## Archivos a Crear / Modificar (Plan)
Crear:
- app/Services/LibroReclamacion/LibroReclamacionClasificacionService.php
- app/Services/LibroReclamacion/TicketLibroReclamacionFactoryService.php
- app/Listeners/LibroReclamacion/CrearTicketLegalDesdeLibro.php

Modificar:
- app/Providers/EventServiceProvider.php
- app/Listeners/LibroReclamacion/EnviarCorreosLibroReclamacion.php (solo si se requiere orden de ejecucion)
- app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php (solo si se necesita ajuste en payload del evento)
- app/Models/LibroReclamacion/LibroReclamacion.php (si se agregan columnas de trazabilidad)
- database/migrations (si se agrega clasificacion_preliminar en intake)

## Riesgos a Controlar
1. Duplicidad de ticket legal por reintentos de eventos.
Mitigacion:
- verificar existencia previa por libro_reclamacion_ticket antes de crear.

2. Clasificacion demasiado estricta o demasiado laxa.
Mitigacion:
- versionar reglas en servicio y registrar motivos.

3. Inconsistencia entre intake y ticket legal.
Mitigacion:
- no editar intake automaticamente tras crear ticket legal, salvo columnas de trazabilidad.

## Checklist de Pruebas Fase 2
1. Intake vacio:
- libro_reclamacions se registra
- clasificacion: NO_PROCEDE o PENDIENTE_REVISION segun regla
- no crea ticket legal automatico

2. Intake parcial con contacto pero sin contenido:
- clasificacion PENDIENTE_REVISION
- no crea ticket legal automatico

3. Intake completo:
- clasificacion PROCEDE
- crea ticket_libro_reclamacions automatico
- estado_legal NUEVO
- nota_fuente completa

4. Validar idempotencia:
- mismo intake no crea dos tickets legales

5. Validar independencia ATC:
- no se crean filas en tickets
- no impacta rutas ATC

## Criterios de Aprobacion de Fase 2
1. Clasificacion automatica funciona en 3 escenarios base.
2. Creacion hibrida se cumple sin afectar intake historico.
3. No existe dependencia con tabla tickets de ATC.
4. Nota fuente queda consistente para gestion legal.

## Siguiente Paso (Fase 3)
- UI de bandeja de intake pendiente para Legal.
- Boton de conversion manual intake -> ticket legal.
- Motor de sugerencias de cliente y lotes con mayor precision.
