# Commit Fase 4 - Modelo, relacion y consultas optimizadas

Fecha: 17-04-2026

## Mensaje de commit sugerido

`feat(libro-reclamacion): agregar relacion al ticket vinculado y eager loading en lista, ver y editar`

## Alcance incluido

1. Se agrego relacion Eloquent segura desde `LibroReclamacion` hacia `Ticket` usando `ticket_id`.
2. Se registro `ticket_id` con cast entero para lectura consistente.
3. Se ajustaron consultas de Lista, Ver y Editar para cargar la relacion vinculada y reducir consultas repetidas.
4. Se mantuvo compatibilidad con la PK historica `ticket` evitando colision de nombre de relacion.
5. Se conservaron fillables/casts existentes salvo el ajuste necesario para `ticket_id`.

## Archivos tocados en esta fase

1. `app/Models/LibroReclamacion/LibroReclamacion.php`
2. `app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php`
3. `app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php`
4. `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`
5. `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-lista.blade.php`
6. `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php`
7. `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-editar.blade.php`

## Checklist de verificacion rapida

1. Abrir Lista de Libro y confirmar que no se rompen los botones de acceso al Ticket.
2. Verificar que la consulta de Lista carga la relacion `ticketRelacionado` sin errores.
3. Entrar a Ver y Editar y confirmar que el boton `Ver Ticket` sigue funcionando.
4. Confirmar que los registros historicos sin ticket_id siguen mostrando `-` o `Sin vincular`.
5. Validar que no aparezcan errores por colision entre la PK `ticket` y la relacion del Ticket vinculado.

## Notas operativas

1. La relacion se nombro `ticketRelacionado` por la colision con la PK historica `ticket` del modelo `libro_reclamacions`.
2. La fase no elimina campos legacy; solo deja el dominio listo para seguir con pruebas y hardening.
