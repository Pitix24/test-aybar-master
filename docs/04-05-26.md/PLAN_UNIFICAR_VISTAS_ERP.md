# Plan: Unificar vistas ERP — estilo formulario web

Resumen

Reemplazar las vistas ERP actuales de Libro de Reclamaciones (Crear / Editar / Ver) por una sola vista de formulario con secciones ordenadas como en el formulario web:

1. Identificación del Proveedor
2. Información del consumidor reclamante
3. Identificación del bien contratado
4. Detalle de la reclamación

Objetivo: facilitar la visualización y edición para el Supervisor de Reclamaciones, manteniendo la lógica de validación y persistencia existente (Livewire).

Alcance

- Crear una plantilla unificada `libro-reclamacion-form.blade.php` bajo `resources/views/livewire/erp/libro-reclamacion/`.
- Reutilizar validaciones y bindings actuales (`wire:model.live`) desde los componentes Livewire.
- Adaptar los componentes ERP (`Crear`, `Editar`, `Ver`) para usar la nueva plantilla (modo crear/editar/solo-lectura).
- Extraer partials Blade reutilizables si se repite código.

Pasos detallados

1. Extraer/definir fragmentos (optional)
    - Identificar bloques repetidos (secciones, alertas, filas de inputs) y mover a `partials/` si procede.

2. Implementar plantilla unificada
    - Crear `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-form.blade.php` con las 4 secciones.
    - Soportar parámetros: `$ticket_model` (cuando aplique), `$modo` o flags `isEdit`, `isView`.
    - Usar inputs con `disabled` en modo `view`.

3. Ajustar componentes Livewire
    - `LibroReclamacionCrear`: renderizar la plantilla en modo crear, mantener validaciones y `store()` actual.
    - `LibroReclamacionEditar`: montar valores desde `$ticket_model` y renderizar en modo editar; `update()` debe mapear campos nuevos.
    - `LibroReclamacionVer` (o la ruta/ver existente): renderizar en modo lectura.

4. Manejo de campo "Menor de edad" y representante legal
    - Mantener `required_if` para los campos del representante.
    - Mostrar/ocultar el bloque condicional según el flag.

5. Pruebas manuales
    - Escenarios mínimos: crear reclamo normal, crear reclamo con menor + representante, editar y ver.
    - Comprobar permisos de `ver`/`editar`.

6. QA y despliegue a staging
    - Ajustes menores tras feedback del supervisor.

Verificación y comandos útiles

- Limpiar caches:

```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

- Ejecutar migraciones (si aplica):

```bash
php artisan migrate
```

Checklist de aceptación

- [ ] La vista `Crear` muestra el formulario unificado y crea reclamos correctamente.
- [ ] La vista `Editar` carga y actualiza los valores existentes.
- [ ] La vista `Ver` muestra campos deshabilitados con N/D cuando corresponde.
- [ ] El bloque de representante legal aparece y valida correctamente cuando se marca "menor de edad".
- [ ] Permisos de ERP no se ven alterados.

Estimación de esfuerzo (aprox.)

- Diseño + partials: 1–2 horas
- Implementación plantilla + componentes: 2–4 horas
- Pruebas + ajustes: 1–2 horas
- Despliegue a staging + validación: 30–60 minutos

Notas

- Ya existe un plan en memoria de sesión con más detalle (`/memories/session/plan.md`).
- Si quieres, comienzo implementando la plantilla y adaptando `Crear` primero. Indica si procedo ahora.
