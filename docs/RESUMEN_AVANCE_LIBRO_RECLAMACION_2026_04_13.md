# Manual Unico de Cambios - Libro Reclamacion

Fecha: 13-04-2026
Estado: Vigente

## 1. Resultado global del dia

Se consolidaron avances en tres frentes:

1. Consolidacion de arquitectura del modulo en una sola entidad operativa.
2. Ajustes de UX y reglas de negocio en ERP (origen, nota fuente, subtipo, estado).
3. Ordenamiento documental para dejar una base clara de continuidad.

## 2. Arquitectura vigente del modulo

### 2.1 Modelo de datos

- Tabla operativa principal: `libro_reclamacions`.
- Tabla de estados legales: `estado_libro_reclamaciones`.
- Modelo principal: `LibroReclamacion`.
- Modelo de estados: `EstadoLibroReclamacion`.
- Modelo legado retirado del flujo: `TicketLibroReclamacion`.

### 2.2 Enfoque funcional actual

- El reclamo se trabaja desde una sola entidad consolidada.
- El estado legal se gestiona por FK (`estado_libro_reclamaciones_id`).
- El proceso ERP usa Crear, Editar, Lista y Ver sobre el mismo modelo.

## 3. Cambios tecnicos aplicados

### 3.1 Base de datos

Se mantuvieron y validaron las migraciones de consolidacion:

- Creacion de `estado_libro_reclamaciones`.
- Consolidacion de campos de trabajo legal en `libro_reclamacions`.
- Ajustes de compatibilidad para codigo y columnas legacy.

Campos relevantes consolidados en `libro_reclamacions`:

- `codigo`
- `estado_libro_reclamaciones_id`
- `clasificacion`
- `cliente_tipo_documento`
- `cliente_documento`
- `cliente_nombre`
- `cliente_email`
- `cliente_celular`
- `cliente_direccion`
- `asunto`
- `lotes`
- `nota_fuente_titulo`
- `nota_fuente_fecha`
- `tipo_pedido`
- `assigned_at`
- `observaciones_internas`
- `created_by`
- `updated_by`
- `deleted_by`

### 3.2 Modelo

En `LibroReclamacion` se dejaron:

- `fillable` y `casts` alineados al esquema consolidado.
- Relaciones de estado y auditoria (`estadoLibroReclamacion`, `creador`, `actualizador`, `eliminador`).
- Hooks de `booted()` para defaults y compatibilidad legacy.
- Resolucion de origen/nota para distinguir ERP vs Web.

### 3.3 Livewire y vistas ERP

Componentes intervenidos:

- `LibroReclamacionCrear`
- `LibroReclamacionEditar`
- `LibroReclamacionVer`
- `LibroReclamacionLista` (ya alineado al modelo consolidado)

Ajustes principales:

- Persistencia confiable de DNI manual sin depender de Buscar.
- Mensajes de validacion con primer error concreto.
- Estructura de Ver homologada al patron operativo esperado.
- Origen visible y consistente por canal de registro.
- Subtipo incorporado en ERP usando `tipo_pedido`.

## 4. Reglas funcionales vigentes

### 4.1 DNI y cliente potencial

- `buscarCliente` es opcional.
- El DNI/CE/RUC digitado manualmente se sincroniza y persiste.
- `cliente_documento` permanece opcional para cliente potencial.

### 4.2 Origen del registro

- Si el registro nace en ERP, el origen visible es `ERP - Registro Interno`.
- Si el registro proviene del formulario web, conserva origen web.
- Ya no debe mostrarse `Formulario web` como origen de un registro creado manualmente en ERP.

### 4.3 Nota fuente

- En ERP, la nota fuente se considera vacia por defecto.
- La nota fuente se reserva para informacion proveniente del formulario web.
- En Ver, origen y nota fuente quedaron separados para evitar confusiones.

### 4.4 Estado Legal y Estado Interno

- Estado Legal es el estado operativo vigente del modulo.
- Estado Interno fue retirado de la UI de Crear/Editar por redundancia.

### 4.5 Subtipo

- Se usa `tipo_pedido` como Subtipo funcional.
- En ERP es editable en Crear y Editar.
- Se muestra en Ver para lectura.
- Valores actuales habilitados en ERP: `RECLAMO`, `QUEJA`.

## 5. Manual operativo rapido (uso diario)

### 5.1 Crear ticket en ERP

1. Seleccionar Unidad de negocio y Proyecto.
2. Elegir Estado Legal.
3. Elegir Subtipo (`RECLAMO` o `QUEJA`).
4. Completar datos de cliente (DNI opcional).
5. Registrar asunto y observaciones internas.
6. Guardar.

Comportamiento esperado:

- Si no se usa Buscar, igual se guarda el documento digitado.
- El origen queda como `ERP - Registro Interno`.
- La nota fuente no se autocompleta con contenido web.

### 5.2 Editar ticket en ERP

1. Ajustar datos necesarios.
2. Mantener o cambiar Subtipo.
3. Guardar cambios.

Comportamiento esperado:

- El origen interno se conserva correctamente.
- El sistema muestra validaciones claras.

### 5.3 Ver ticket en ERP

Secciones disponibles:

- Informacion general
- Cliente
- Asunto y lotes
- Nota y observaciones
- Auditoria

Comportamiento esperado:

- Origen visible de forma explicita.
- Subtipo visible.
- Nota fuente sin confundir origen del registro.

## 6. Verificacion realizada hoy

Se valido:

- Migraciones y seeder de estados sin fallos.
- Ajustes de componentes y vistas sin errores de sintaxis.
- Flujo consolidado Crear/Editar/Ver en estado estable para la iteracion.

Resultado tecnico reportado en la jornada:

- `get_errors()` sin hallazgos en los archivos modificados.

## 7. Pendientes abiertos

Pendientes funcionales para siguiente iteracion:

1. Expandir auditoria en Ver para mostrar claramente usuarios vinculados (creador/actualizador/eliminador) con el nivel de detalle final requerido.
2. Confirmar implementación con supervisor.
3. Implementar tabla de historial de asignaciones de gestores con:
  - gestor anterior,
  - gestor nuevo,
  - usuario que hizo el cambio,
  - fecha y hora.
4. Ejecutar QA manual de negocio en UI con evidencia final.

## 8. Guia para futuras modificaciones

Para evitar regresiones:

1. Mantener `libro_reclamacions` como fuente unica de verdad del modulo.
2. No reintroducir `Estado Interno` en UI sin definicion funcional formal.
3. Respetar la regla de origen por canal (ERP vs Web).
4. Mantener Subtipo sobre `tipo_pedido` mientras no se apruebe migracion a catalogo externo.
5. Validar cada cambio con chequeo tecnico y prueba manual de Crear/Editar/Ver.

## 9. Estado final del dia

Estado general: estable y documentado.

Lo ya implementado en esta jornada:

- consolidacion operativa vigente,
- paridad funcional del flujo DNI,
- origen ERP correctamente resuelto,
- nota fuente separada del origen,
- subtipo visible/editable en ERP,
- estado interno retirado de UI,
- manual unico disponible para continuidad del trabajo.
