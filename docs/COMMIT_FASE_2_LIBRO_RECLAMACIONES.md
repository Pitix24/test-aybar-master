# Commit Fase 2 - Creacion transaccional Ticket + Libro vinculado

Fecha: 17-04-2026

## Mensaje de commit sugerido

`feat(libro-reclamacion): crear ticket automatico y vincular libro por ticket_id en flujo web`

## Alcance incluido

1. Se implemento la creacion del Ticket automatico dentro de la misma transaccion del formulario web.
2. Se implemento el enlace de `libro_reclamacions.ticket_id` hacia el Ticket recien creado.
3. Se mantuvo rollback transaccional: si falla Ticket o Libro, no persiste ninguno.
4. Se agrego flag de control para rollback operativo:
   - `LIBRO_RECLAMACION_TICKET_AUTOCREACION_HABILITADO`
5. Se reforzo la resolucion de IDs de catalogo para Ticket con fallback seguro.
6. Se aplico ajuste de canal para resolver `canal_id = 4` (Libro Reclamacion).
7. Se aplico migracion correctiva para asegurar existencia de `ticket_id` en `libro_reclamacions` en entornos desfasados.
8. Se aplico hardening visual en Lista de Tickets para registros historicos con `canal_id` nulo.

## Archivos tocados en esta fase

1. `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`
2. `app/Models/LibroReclamacion/LibroReclamacion.php`
3. `config/libro_reclamacion.php`
4. `database/migrations/2026_04_17_160100_add_missing_ticket_id_to_libro_reclamacions_table.php`
5. `resources/views/livewire/erp/atc/ticket/ticket-lista.blade.php`

## Checklist de verificacion rapida

1. Enviar formulario web y confirmar que se crea un Ticket nuevo.
2. Confirmar que el Libro nuevo guarda `ticket_id` apuntando al Ticket creado.
3. Confirmar que `asunto_inicial` y `descripcion_inicial` respetan el contrato de mapeo.
4. Confirmar que el Ticket generado desde web guarda `canal_id = 4` (Libro Reclamacion).
5. Simular error en creacion de Ticket o Libro y validar rollback sin registros huerfanos.
6. Confirmar que al desactivar `LIBRO_RECLAMACION_TICKET_AUTOCREACION_HABILITADO=false` el Libro sigue creando sin Ticket.

## Notas operativas

1. Esta fase no cambia aun la secuencia de envio de correos del listener.
2. La definicion de `gestor_id` inicial queda pendiente de negocio para fase siguiente.
3. Si el entorno tiene historicos con `canal_id` nulo, la Lista de Tickets no debe romper; mostrara `-` en Canal.