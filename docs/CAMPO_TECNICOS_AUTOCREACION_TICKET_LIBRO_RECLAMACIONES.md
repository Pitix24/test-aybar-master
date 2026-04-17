# Campos Tecnicos para Autocreacion de Ticket - Libro Reclamaciones

Fecha: 17-04-2026  
Estado: Borrador funcional para implementacion con decisiones cerradas

## Proposito

Este documento define que campos tecnicos se autogeneraran al crear el Ticket automatico desde el formulario web de Libro de Reclamaciones. El objetivo es dejar un contrato claro antes de implementar la orquestacion Web -> Ticket -> Libro.

## Contexto funcional

Flujo acordado:

Cliente envia formulario web -> Correo cliente -> Correo legal -> Crear Ticket -> Crear Libro Reclamacion vinculado al Ticket.

El Ticket automatico sera el contenedor operativo para el equipo legal. Libro Reclamaciones quedara como registro legal vinculado por `ticket_id`.

## Campos tecnicos que se autocrearan en Ticket

### 1. Identidad operativa del Ticket

| Campo Ticket | Origen | Regla | Estado |
|---|---|---|---|
| `area_id` | Fijo tecnico | Area Legal (id 3) | Confirmado |
| `canal_id` | Catalogo existente | Canal Formulario Web | Confirmado |
| `estado_ticket_id` | Catalogo estados | Estado inicial Nuevo | Confirmado |
| `prioridad_ticket_id` | Catalogo prioridades | Prioridad Alta (id 3) | Confirmado |
| `ticket_padre_id` | Nulo | Solo aplica si existe derivacion manual futura | Nulo por defecto |
| `created_by` | Sistema / web | `null` | Confirmado |
| `gestor_id` | Regla de asignacion | Usuario legal por defecto o nulo segun politica | Pendiente de cerrar |

### 2. Datos del solicitante que se copiaran al Ticket

| Campo Ticket | Origen web | Regla |
|---|---|---|
| `dni` | `numero_documento` | Se copia el documento ingresado en el formulario |
| `nombres` | `nombre` + `apellido_paterno` + `apellido_materno` | Se concatena el nombre completo |
| `email` | `email` | Se copia si existe |
| `celular` | `telefono` | Se copia si existe |
| `direccion` | `domicilio` | Se copia si existe |

### 3. Datos del caso que se autogeneraran

| Campo Ticket | Origen web | Regla |
|---|---|---|
| `asunto_inicial` | `tipo_pedido` + `numero_documento` | Formato fijo: `RECLAMO/QUEJA - DNI del Cliente` |
| `descripcion_inicial` | `detalle` + `pedido` | Formato compuesto: `Cliente detalla...` + `Cliente pide...` incluyendo solo las partes no vacias |
| `tipo_solicitud_id` | Catalogo Ticket | `28` o el catalogo con nombre `LIBRO DE RECLAMACIONES` |
| `sub_tipo_solicitud_id` | `tipo_pedido` | Se tomaran los subtipos vinculados a `LIBRO DE RECLAMACIONES` y al seleccionado en el formulario web |
| `unidad_negocio_id` | `proyecto_id` -> relacion | Se resuelve por la unidad del proyecto |
| `proyecto_id` | `proyecto_id` | Se conserva el proyecto elegido en el formulario |

### 4. Campos de trazabilidad y control

| Campo Ticket | Origen | Regla |
|---|---|---|
| `origen` | Sistema | Se marcará como origen web del libro |
| `lotes` | Formulario web | Se copiaran si el formulario trae lotes asociados |
| `leido` | Sistema | `false` por defecto |
| `prioridad` | Sistema | Se resuelve por defecto del flujo web |
| `estado` | Sistema | Estado inicial del flujo legal |

## Campos que NO deben depender de captura manual ERP

Estos datos no deben solicitarse como creación manual en ERP para el nuevo flujo:

1. `area_id`.
2. `canal_id`.
3. `estado_ticket_id` inicial.
4. `prioridad_ticket_id` inicial.
5. `ticket_padre_id`.
6. `created_by` del flujo automatico.

## Campos que se pueden mantener editables despues de creado el Ticket

1. `gestor_id`.
2. `asunto_inicial` si el negocio decide permitir ajuste.
3. `descripcion_inicial` si el negocio decide permitir ajuste.
4. `prioridad_ticket_id` si legal necesita reasignacion.

## Mapeo minimo recomendado desde el formulario web

| Formulario web | Ticket | Observacion |
|---|---|---|
| `proyecto_id` | `proyecto_id` | Entrada principal del flujo |
| `numero_documento` | `dni` | Documento del solicitante |
| `nombre` + apellidos | `nombres` | Nombre completo |
| `email` | `email` | Opcional |
| `telefono` | `celular` | Opcional |
| `domicilio` | `direccion` | Opcional |
| `tipo_pedido` | `tipo_solicitud_id` / `sub_tipo_solicitud_id` | Requiere mapeo de catalogo |
| `detalle` | `descripcion_inicial` | Se incluye en el bloque `Cliente detalla lo siguiente...` |
| `pedido` | `descripcion_inicial` | Se incluye en el bloque `Cliente pide lo siguiente...` |
| `lotes` | `lotes` | Si el formulario los aporta |

## Reglas funcionales acordadas

1. El Ticket automatico debe nacer en Area Legal.
2. El Ticket automatico debe usar el canal de Formulario Web.
3. El Ticket debe crearse dentro de transaccion.
4. El Libro Reclamaciones se crea despues y queda vinculado por `ticket_id`.
5. Si falla Ticket o Libro, no debe quedar ningun registro intermedio.
6. La configuracion debe permitir rollback por flag sin borrar codigo.
7. `tipo_solicitud_id` queda fijado en `28` o en el catalogo con nombre `LIBRO DE RECLAMACIONES`.
8. `sub_tipo_solicitud_id` debe resolver el subtipo vinculado al catalogo `LIBRO DE RECLAMACIONES` y coincidir con el seleccionado en el Form Web.
9. `prioridad_ticket_id` queda fijado en `3` (ALTA).
10. `created_by` del flujo automatizado sera `null`.
11. `pedido` se guarda en Libro y tambien en Ticket (dentro de `descripcion_inicial`).
12. `asunto_inicial` se arma predefinido como `RECLAMO/QUEJA - DNI del Cliente`.
13. `descripcion_inicial` se arma desde `detalle` + `pedido` con texto guiado. Si `detalle` o `pedido` no existen, su sección se salta a la otra.

## Campos que siguen pendientes de cerrar antes de codificar

1. Regla exacta de `gestor_id` inicial: asignado, nulo o autoseleccionado.

## Criterio de cierre de esta fase

La fase queda lista cuando exista una tabla cerrada de mapeo y el componente web pueda crear el Ticket con todos los campos tecnicos definidos sin depender de entrada manual ERP.