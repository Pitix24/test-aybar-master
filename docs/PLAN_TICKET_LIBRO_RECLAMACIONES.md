# Plan: Ticketing de Libro Reclamaciones

Crear el soporte de generación de tickets de atención por Unidad de Negocio usando la Unidad derivada del Proyecto, con correlativos independientes por empresa y un piso configurable para AYBAR CORP. S.A.C. La base recomendada es un contador transaccional por `unidad_negocio_id`, validación única en BD y un flujo único compartido por Web/ERP para evitar divergencias.

## Steps

1. Confirmar el contrato de datos actual en `libro_reclamacions`: `unidad_negocio_id` se conserva como FK interna, `proyecto_id` es la entrada pública, `ticket` sigue siendo la PK técnica y `numero_reclamo` + `codigo_ticket` serán los campos de negocio. Esto define qué se persiste y qué se calcula.
2. Diseñar la migración de soporte de numeración por empresa: crear o ajustar una tabla de contadores por `unidad_negocio_id` con un `ultimo_numero` atómico, índice único por empresa y columnas para auditoría. Mantener la tabla de reclamos como fuente de verdad del ticket emitido.
3. Crear el modelo de ticketing para Libro de Reclamaciones: o bien un modelo dedicado para el contador, o extender `LibroReclamacion` con un método generador único que reserve el número dentro de una transacción. La recomendación es centralizar la lógica en un solo punto para que Web y ERP usen el mismo generador.
4. Implementar la excepción AYBAR CORP. S.A.C.: dejar el arranque en 0 para todas las empresas y exponer un único punto de configuración para AYBAR con valor inicial distinto cuando se defina el número real de continuidad. La configuración debe tener valor por defecto seguro y no romper instalaciones nuevas.
5. Conectar el generador con los Livewire de Libro de Reclamaciones: al escoger el Proyecto, resolver `unidad_negocio_id` desde la relación del Proyecto, generar el ticket antes de persistir y guardar `unidad_negocio_id`, `proyecto_id`, `numero_reclamo` y `codigo_ticket` en la misma operación.
6. Ajustar la vista de confirmación para mostrar el ticket emitido y la razón social asociada al proyecto, sin exponer `ruc` ni `dirección`. Mantener la UI limpia y consistente con la lógica de BD.
7. Verificar concurrencia y separación por empresa: pruebas con dos unidades distintas, varios envíos seguidos y un caso simultáneo para comprobar que no se repiten correlativos ni códigos.

## Relevant files

- `database/migrations/2026_02_16_211526_create_libro_reclamacions_table.php` - esquema base de `libro_reclamacions` y campos ya existentes.
- `app/Models/LibroReclamacion.php` - punto central para persistencia y generación de ticket.
- `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php` - flujo público que resuelve el proyecto y guarda el reclamo.
- `app/Livewire/Erp/LibroReclamacion/LibroReclamacionLivewire.php` - flujo ERP que debe quedar alineado con el mismo generador.
- `config/libro_reclamacion.php` - configuración del inicio especial de AYBAR y opciones del módulo.
- `resources/views/livewire/web/libro-reclamacion/libro-reclamacion-livewire.blade.php` - formulario y confirmación visual del ticket.

## Verification

1. Crear un reclamo de prueba para dos empresas distintas y validar que cada una arranca en su propio correlativo independiente.
2. Ejecutar varios registros consecutivos para la misma empresa y confirmar que el número aumenta sin saltos ni duplicados.
3. Forzar dos envíos casi simultáneos para la misma empresa y validar que la BD no permite repetir `unidad_negocio_id` + `numero_reclamo`.
4. Revisar que AYBAR CORP. S.A.C. respete el piso configurado cuando se active y que el valor por defecto siga siendo 0 para instalaciones nuevas.
5. Consultar `libro_reclamacions` y validar que cada fila guarda `unidad_negocio_id`, `proyecto_id`, `numero_reclamo`, `codigo_ticket` y `estado` correctamente.

## Decisions

- El contador debe vivir por `unidad_negocio_id`, no por nombre de empresa, para evitar errores por cambios de razón social.
- `libro_reclamacions` sigue siendo la tabla principal del reclamo; no se duplica la fuente de verdad del ticket en otra tabla salvo que se requiera auditoría extra.
- El alcance de esta fase excluye correo y notificaciones; eso va después.

## Further Considerations

1. Confirmar si el “modelo de Tickets” debe ser una tabla nueva separada o solo un modelo de contador interno; la recomendación es usar contador interno para no duplicar estado.
2. Definir el número de arranque oficial de AYBAR CORP. S.A.C. antes de pasar a producción.
3. Si se necesita trazabilidad adicional, agregar una tabla de histórico de tickets emitidos para auditoría.