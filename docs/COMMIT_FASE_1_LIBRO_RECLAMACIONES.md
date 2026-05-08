# Commit Fase 1 - Contrato de Mapeo Web -> Ticket

Fecha: 17-04-2026

## Mensaje de commit sugerido

`feat(libro-reclamacion): implementar contrato tecnico de mapeo para autogeneracion de ticket`

## Alcance incluido

1. Se agrego en configuracion el bloque `ticket_autocreacion` en `config/libro_reclamacion.php`.
2. Se fijaron valores base del contrato:
   - `area_legal_id = 3`
   - `tipo_solicitud_id = 28`
   - `prioridad_ticket_id = 3`
   - `created_by = null`
3. Se incorporaron plantillas tecnicas para construir:
   - `asunto_inicial`
   - `descripcion_inicial`
4. Se agregaron metodos de resolucion y normalizacion en `LibroReclamacionLivewire` para preparar el payload tecnico del Ticket.
5. Se dejo listo el campo `payload_ticket_autocreacion` en el flujo web como previsualizacion tecnica del contrato para fase 2.

## Archivos tocados en esta fase

1. `config/libro_reclamacion.php`
2. `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`
3. `docs/CAMPO_TECNICOS_AUTOCREACION_TICKET_LIBRO_RECLAMACIONES.md`

## Checklist de verificacion rapida

1. Confirmar que existe `ticket_autocreacion` en config con los IDs acordados.
2. Validar que el formulario web sigue registrando Libro Reclamaciones sin regresion.
3. Verificar que se construye internamente `payload_ticket_autocreacion` con:
   - `asunto_inicial` con formato `RECLAMO/QUEJA - DNI del Cliente`
   - `descripcion_inicial` con bloques condicionales de detalle/pedido
4. Verificar que la resolucion de canal y tipo solicitud funciona por `id` y fallback por `nombre`.
5. Confirmar que no se crea Ticket todavia (eso queda para Fase 2).

## Pendiente para Fase 2

1. Ejecutar persistencia transaccional real: crear Ticket y luego Libro vinculado por `ticket_id`.
2. Definir regla final de `gestor_id` inicial.