# Commit Fase 0 - Bloqueo de Creacion ERP

Fecha: 17-04-2026

## Mensaje de commit sugerido

`feat(libro-reclamacion): deshabilitar creacion ERP y ocultar acceso a crear`

## Alcance incluido

1. Se agrego el flag `libro_reclamacion.crear_erp_habilitado` con valor por defecto deshabilitado.
2. Se bloqueo el registro de la ruta `/erp/libro-reclamacion/crear` cuando el flag esta apagado.
3. Se oculto el boton Crear en la lista del ERP cuando la creacion esta deshabilitada.
4. Se agrego guard en el componente Livewire de Crear para bloquear acceso por rutas antiguas o cacheadas.

## Checklist de verificacion rapida

1. Abrir la lista ERP de Libro y confirmar que el boton Crear no aparece.
2. Intentar entrar por URL directa a `/erp/libro-reclamacion/crear` y confirmar que responde 404.
3. Verificar que `/erp/libro-reclamacion`, `/ver/{id}` y `/editar/{id}` siguen funcionando.
4. Confirmar que el formulario web sigue accesible y sin cambios de comportamiento.
5. Revisar que no existan errores de sintaxis en los archivos modificados.

## Nota operativa

Si en el futuro se necesita reactivar temporalmente la creacion desde ERP, basta con cambiar el flag:

`LIBRO_RECLAMACION_CREAR_ERP_HABILITADO=true`

Luego limpiar cache de config si aplica.

## Mini-commit complementario (hotfix)

- Commit: `0f83a1f`
- Mensaje: `fix(menu-erp): evitar error por rutas no definidas en enlaces dinamicos`
- Archivo: `resources/views/layouts/erp/menu-erp.blade.php`
- Motivo: evitar `RouteNotFoundException` cuando existan items de menu con rutas deshabilitadas o no registradas.