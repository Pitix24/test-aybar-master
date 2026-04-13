# Decision Sheet - Ticket Libro Reclamacion (Fase 2)

## Proposito
Alinear decisiones de negocio y tecnica antes de implementar la automatizacion del flujo legal de Libro Reclamaciones.

## Contexto
- `libro_reclamacions` = intake historico (todo lo que llega por web/ERP).
- `ticket_libro_reclamacions` = gestion operativa exclusiva de Legal.
- No hay relacion con la tabla `tickets` de ATC.

## Decisiones Clave a Validar

### 1) Politica de creacion de ticket legal
Opciones:
- A. Manual total
- B. Automatica total
- C. Hibrida

Recomendacion: **C. Hibrida**
- Automatico para `PROCEDE`
- Manual para `NO_PROCEDE` y `PENDIENTE_REVISION`

Decision esperada: [ ] A  [ ] B  [x] C

### 2) Regla minima para PROCEDE
Se requiere al menos:
- Identidad: `numero_documento` o `nombre completo`
- Contacto: `email` o `telefono`
- Contenido: `detalle` o `descripcion`

Decision esperada: [x] Aprobar  [ ] Ajustar
Notas: ______________________________

### 3) Estado inicial del ticket legal
Opciones:
- A. `NUEVO`
- B. `PENDIENTE_REVISION`
- C. `EN_GESTION`

Recomendacion: **A. NUEVO**

Decision esperada: [x] A  [ ] B  [ ] C

### 4) Campos bloqueados en edicion
Bloqueados propuestos:
- `codigo`
- `libro_reclamacion_ticket`
- `created_by`
- `created_at`

Decision esperada: [x] Aprobar  [ ] Ajustar
Notas: ______________________________

### 5) Nota fuente automatica
Campos incluidos:
- Monto reclamado
- Descripcion producto/servicio
- Tipo de solicitud
- Detalle reclamo/queja
- Manzana
- Lote

Decision esperada: [x] Aprobar  [ ] Ajustar

### 6) Deteccion de cliente
Prioridad propuesta:
1. DNI/Documento
2. Email
3. Nombre (coincidencia aproximada)

Si no se detecta:
- `cliente_id = null`
- clasificacion sugerida: `PENDIENTE_REVISION`

Decision esperada: [x] Aprobar  [ ] Ajustar

## Riesgos y Mitigaciones

1. Duplicidad de ticket legal por reintentos.
- Mitigacion: idempotencia por `libro_reclamacion_ticket`.

2. Clasificacion inestable al inicio.
- Mitigacion: registrar motivo y ajustar reglas por version.

3. Falsos positivos en deteccion de cliente.
- Mitigacion: permitir override manual en ERP.

## Criterios de Go/No-Go
Go si se cumple:
1. No impacta tabla `tickets` de ATC.
2. Clasificacion funciona en vacio/parcial/completo.
3. Creacion hibrida valida.
4. No se crean duplicados por intake.
5. Legal puede corregir y enriquecer sin perder trazabilidad.

## Entregables Fase 2
1. Servicio de clasificacion.
2. Servicio/factory de creacion de ticket legal.
3. Listener de procesamiento post-registro de intake.
4. Pruebas de flujo hibrido e idempotencia.

## Aprobaciones
- Lider Legal: __________________  Fecha: __________
- Lider TI: _____________________  Fecha: __________
- PM/Producto: __________________  Fecha: __________
