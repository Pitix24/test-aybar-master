# Plan: Agregar `codigo` a UnidadNegocio y asociar Unidad de Negocio en Tickets

## Resumen

Este documento especifica los cambios para:

- Agregar un campo `codigo` (3 caracteres, mayúsculas) a la tabla `unidad_negocios` y hacerlo editable desde el ERP.
- Permitir que los gestores definan manualmente el `codigo` y autogenerarlo cuando quede vacío.
- Asociar una `UnidadNegocio` a los `Ticket` del sistema (guardar `unidad_negocio_id` en la tabla de tickets), y exponer ese selector en el formulario del módulo Ticket en el ERP.

## Preguntas de diseño (responder antes de implementar)

1. Reglas del campo `codigo` en `unidad_negocios`:
    - ¿Debe ser obligatorio o opcional? (recomendado: obligatorio y único si ustedes quieren control estricto).
    - ¿Debe validarse siempre como 3 caracteres alfabéticos en mayúscula? (sí — validar servidor y cliente)
    - ¿Autogeneración?: sí, si queda vacío se generará un código de 3 letras mayúsculas.

2. Módulo Ticket:
    - ¿Crear un módulo `Ticket` nuevo dentro de `ERP` o reutilizar el módulo existente `Atc/Ticket`? (recomendado: reutilizar `app/Livewire/Erp/Atc/Ticket` si ya existe flujo de tickets)
    - ¿`unidad_negocio_id` debe ser requerido al crear un ticket, o opcional (nullable)?

## Cambios técnicos propuestos

1. Migración: `add_codigo_to_unidad_negocios`
    - Añadir columna `codigo` CHAR(3) NULLABLE o NOT NULL según decisión.
    - Índice único: `unique('unidad_negocios','codigo')`.
    - Script ejemplo (Laravel):

```php
Schema::table('unidad_negocios', function (Blueprint $table) {
    $table->string('codigo', 3)->nullable()->after('id');
    $table->unique('codigo');
});
```

2. Migración: `add_unidad_negocio_id_to_tickets`
    - Añadir columna `unidad_negocio_id` unsignedBigInteger nullable, FK a `unidad_negocios(id)`.

```php
Schema::table('tickets', function (Blueprint $table) {
    $table->foreignId('unidad_negocio_id')->nullable()->constrained('unidad_negocios');
});
```

3. Modelo `UnidadNegocio`
    - Añadir `codigo` a `$fillable`.
    - Añadir autogeneración cuando el código quede vacío.

4. Livewire `UnidadNegocioCrear` / `UnidadNegocioEditar` / `UnidadNegocioVer`
    - Añadir propiedad `$codigo`.
    - Reglas: `'codigo' => 'nullable|string|size:3|regex:/^[A-Z]{3}$/|unique:unidad_negocios,codigo'`
    - En vistas: agregar input `codigo` (mayúsculas) y autogenerarlo si se deja vacío.

5. Ticket (formulario)
    - En el formulario de creación/edición de tickets, mostrar el `codigo_ticket` calculado a partir de la unidad seleccionada.
    - Si no hay unidad seleccionada, mostrar `NUL` como placeholder.
    - El componente Ticket (crear/editar) debe mantener la propiedad `$codigo` sincronizada con la unidad elegida.

6. Rutas / Permisos
    - Si se crea un nuevo módulo Ticket, agregar archivo `routes/erp/ticket.php` y enlaces en menú ERP.
    - Si se reutiliza `Atc/Ticket`, simplemente modificar vistas y permisos existentes.
    - `app/Services/LibroReclamacion/LibroReclamacionNumeroService.php` debe tomar `unidad_negocios.codigo` como origen del `codigo_ticket`.

7. Tests y pasos finales
    - Ejecutar migraciones: `php artisan migrate`
    - Validar sintaxis: `php -l` en archivos modificados
    - Limpiar cachés: `php artisan view:clear && php artisan cache:clear && php artisan config:clear && php artisan optimize:clear`
    - Probar flujo UI: Crear/Editar UnidadNegocio con `codigo`, Crear ticket y seleccionar UnidadNegocio; verificar que el `codigo_ticket` cambie en vivo y que sin unidad muestre `NUL`.

## Archivos a modificar (lista preliminar)

- Nueva migración: `database/migrations/YYYY_MM_DD_add_codigo_to_unidad_negocios.php`
- Nueva migración: `database/migrations/YYYY_MM_DD_add_unidad_negocio_id_to_tickets.php`
- `app/Models/UnidadNegocio.php` (añadir `$fillable` y helpers)
- `app/Livewire/Erp/Negocio/UnidadNegocio/UnidadNegocioCrear.php` (propiedad y reglas)
- `app/Livewire/Erp/Negocio/UnidadNegocio/UnidadNegocioEditar.php`
- `resources/views/livewire/erp/negocio/unidad-negocio/*.blade.php` (añadir input/textarea para `codigo` si aplica)
- `app/Livewire/Erp/Atc/Ticket/*` (componentes Crear/Editar/Ver para incluir select de UnidadNegocio)
- Rutas: `routes/erp/atc.php` o `routes/erp/ticket.php` según decisión

## Plan de trabajo (alto nivel)

1. Confirmar decisiones de diseño (responder preguntas).
2. Crear migraciones y ejecutarlas en entorno local.
3. Implementar cambios en modelos y Livewire de UnidadNegocio.
4. Implementar integración en formulario Ticket.
5. Ajustar vistas, permisos y rutas.
6. Ejecutar pruebas manuales y correcciones.
7. Documentar en `docs/` y cerrar tickets.

## Próximo paso inmediato

Responder las dos preguntas en la sección "Preguntas de diseño". Una vez respondidas, generaré las migraciones y empezaré con la implementación.
