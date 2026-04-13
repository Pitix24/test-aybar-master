# Decisiones Funcionales: Libro Reclamacion ERP

Fecha: 13-04-2026
Estado: Base de implementacion

## Objetivo

Dejar definido el comportamiento funcional del modulo Libro Reclamacion en ERP para que el equipo pueda implementarlo y documentarlo sin ambiguedades.

## Contexto

El modulo ya quedo consolidado sobre una sola entidad operativa de Libro Reclamacion. A partir de este punto, los ajustes solicitados son de comportamiento funcional, orden visual, trazabilidad y campos de soporte.

## Decisiones acordadas

### 1. Origen del registro

- Cuando el registro se crea desde ERP, el origen debe mostrarse como `ERP - Registro Interno`.
- Cuando el registro proviene del formulario web, se conserva el origen web.
- El texto `Formulario web` no debe aparecer como origen en registros creados manualmente desde ERP.
- En Ver, el origen debe verse de forma explicita como campo propio para evitar ambiguedad con la nota fuente.

### 2. Nota fuente

- En ERP, la nota fuente debe quedar vacia por defecto.
- La nota fuente debe reservarse para la informacion capturada desde el formulario web.
- Si el registro fue creado desde ERP, no debe forzarse contenido de origen web dentro de la nota fuente.

### 3. Orden de la seccion de detalle

En la vista de detalle, el orden deseado es:

1. Observaciones internas
2. Titulo y fecha de nota
3. Nota fuente

Esto se hace para que primero se vea lo que el Equipo Legal deja como observacion operativa, luego la metadata de registro, y finalmente la fuente recopilada desde web.

### 4. Estado Legal y Estado Interno

- Estado Legal sigue siendo el estado normativo/operativo visible del registro.
- Estado Interno fue retirado de la UI de Crear y Editar para evitar duplicidad semantica.
- Mientras no exista una definicion de negocio distinta, se trabajara solo con Estado Legal.

### 5. Subtipo

- Debe existir un Subtipo para clasificar el registro, por ejemplo: Reclamo, Queja, etc.
- En ERP debe ser editable.
- Si el registro proviene del formulario web, el subtipo puede venir prellenado desde alli.
- Si no existe valor web, el ERP debe permitir escogerlo manualmente.
- En Crear y Editar, el Subtipo se muestra junto a Estado Legal en la seccion de informacion general.

### 6. Auditoria

- En la seccion Auditoria debe mostrarse el usuario vinculado al registro, ademas de las fechas.
- Deben mostrarse, como minimo, creador y actualizador.
- Si aplica eliminacion logica, tambien debe mostrarse el usuario eliminador.

### 7. Historial de asignaciones

- Debe existir una tabla visible en Ver para el historial de gestores asignados.
- La tabla debe registrar, como minimo:
  - Gestor anterior
  - Gestor nuevo
  - Usuario que hizo el cambio
  - Fecha y hora
- Este historial debe comportarse de forma equivalente al patron de Ticket.

## Impacto funcional por pantalla

### Crear

- Mostrar origen correcto del registro.
- Permitir registrar sin forzar nota fuente de web.
- Mantener subtipo editable y visible junto a Estado Legal.
- No mostrar Estado Interno en la UI.

### Editar

- Mantener la misma logica de origen y nota fuente.
- Permitir ajuste manual de subtipo.
- No mostrar Estado Interno en la UI.
- Mantener historial de asignacion consistente.

### Ver

- Reordenar la informacion segun la regla acordada.
- Mostrar auditoria con usuario vinculado.
- Mostrar tabla de historial de gestores asignados.
- No mostrar Estado Interno.

## Archivos de referencia

- [app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php](../app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php)
- [app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php](../app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php)
- [app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php](../app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php)
- [resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-crear.blade.php](../resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-crear.blade.php)
- [resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-editar.blade.php](../resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-editar.blade.php)
- [resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php](../resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php)
- [app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php](../app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php)
- [app/Models/LibroReclamacion/LibroReclamacion.php](../app/Models/LibroReclamacion/LibroReclamacion.php)

## Orden sugerido de implementacion

1. Ajustar origen y nota fuente.
2. Reordenar la vista de detalle.
3. Ocultar Estado Interno.
4. Incorporar Subtipo editable y prellenado.
5. Expandir Auditoria.
6. Agregar tabla de historial de asignaciones.
7. Cerrar con validacion tecnica y documentacion final.

## Nota final

Este documento reemplaza como referencia funcional al plan anterior de DNI/paridad para todo lo relacionado con la nueva iteracion de UX y trazabilidad del modulo.

## Estado de implementacion

- FASE 1 iniciada y aplicada en origen ERP: los registros internos ya resuelven el titulo de nota como `ERP - Registro Interno`.
- En Ver, el origen se resuelve por canal para no mostrar `Formulario web` en registros creados desde ERP.
- En Editar, el titulo de nota resuelto se vuelve a persistir al guardar.
- La nota fuente queda vacia para el canal ERP a nivel de UI, dejando el texto web solo para registros realmente originados en formulario web.
