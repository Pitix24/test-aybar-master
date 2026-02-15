# Estándar de Desarrollo para Módulos CRUD (Livewire + ERP)

Este documento define la estructura, lógica y componentes obligatorios para todos los módulos del sistema Aybar ERP, con el fin de mantener coherencia visual, técnica y de seguridad en todo el proyecto.

## 1. Estructura de Archivos
Cada módulo debe estar organizado en su propio directorio dentro de `app/Livewire/Erp/` y seguir esta nomenclatura:

- `NombreLista.php`: Vista de tabla con filtros, búsqueda y paginación.
- `NombreCrear.php`: Formulario para el registro de nuevos datos.
- `NombreEditar.php`: Formulario para modificar datos existentes y acción de eliminar.
- `NombreVer.php`: Pantalla de detalle con todos los campos en modo de solo lectura.

## 2. Configuración de Rutas y Permisos
Las rutas deben definirse en archivos específicos por módulo dentro de `routes/erp/`. Cada acción debe estar protegida por un permiso específico.

### Bloque de Comentarios de Permisos
Al final de cada archivo de rutas, se debe incluir el inventario de permisos bajo la convención `recurso.accion`:

```php
/*
ROL
1. rol.navegacion (Permite ver el ítem en el menú)
2. rol.ver (Acceso a la lista y al detalle individual)
3. rol.crear
4. rol.editar
5. rol.eliminar
6. rol.exportar-filtro
7. rol.exportar-todo
*/
```

## 3. Estándar de Componentes (PHP)

Todos los componentes de página deben usar las siguientes directivas y métodos:

### Atributos de Clase
```php
#[Lazy] // Carga asíncrona para mejorar la experiencia inicial
#[Layout('layouts.erp.layout-erp')] // Uso del layout principal del ERP
#[Title('Título de la Página')] // Definición del título de la pestaña del navegador
```

### Autorización de Acciones
Se debe usar `$this->authorize()` en cada método que realice cambios.
```php
public function store() {
    $this->authorize('rol.crear');
    // ... lógica de guardado
}
```

### Estados de Carga (Placeholder)
Es obligatorio para el funcionamiento de `#[Lazy]`.
```php
public function placeholder() {
    return <<<'HTML'
    <x-placeholder />
    HTML;
}
```

### Feedback y Alertas
Notificar al usuario mediante el despacho del evento `alertaLivewire`.
```php
$this->dispatch('alertaLivewire', [
    'type' => 'success', // success, error, warning, info
    'title' => 'Título Mensaje',
    'text' => 'Descripción detallada de la acción.'
]);
```

### Registro de Errores (Logging)
Cada módulo debe tener su propio canal de log configurado en `config/logging.php`. Se debe usar el canal específico (`Log::channel('nombre_modulo')->error`) dentro de los bloques `catch`, incluyendo un prefijo descriptivo (ej: `[ROL]`, `[PERMISO]`) para facilitar la trazabilidad fuera del log general.

```php
} catch (\Exception $e) {
    Log::channel('nombre_modulo')->error("[MODULO] Error en acción X: " . $e->getMessage(), [
        'usuario_id' => auth()->id(),
        'datos' => $this->all(),
        'trace' => $e->getTraceAsString()
    ]);
    // ... dispatch alerta
}
```

## 4. Estándar de Vistas (Blade)

### Feedback Visual de Carga
Incluir el overlay de carga al inicio de la vista, apuntando a los métodos clave.
```html
<x-loading-overlay 
    wire:loading 
    wire:target="update, eliminarRolOn" 
    message="Procesando..." 
/>
```

### Validación de Formularios
Uso de la clase `input-error` y visualización de mensajes de error.
```html
<div class="g_margin_bottom_10">
    <label for="name">Nombre</label>
    <input type="text" id="name" wire:model.blur="name" class="@error('name') input-error @enderror">
    @error('name') 
        <p class="mensaje_error">{{ $message }}</p> 
    @enderror
</div>
```

### Control de Visibilidad (Blade @can)
Todos los botones que ejecuten acciones deben estar envueltos en directivas `@can`.
```html
@can('rol.editar')
    <button type="submit" class="g_boton guardar">Actualizar</button>
@endcan
```

## 5. Exportación a Excel
Los módulos deben incluir dos opciones de exportación utilizando una única clase `Export` parametrizada:

1.  **Exportar Filtrados**: Respeta la búsqueda actual, filtros personalizados y la paginación de la vista. (Permiso: `recurso.exportar-filtro`).
2.  **Exportar Todo**: Ignora búsqueda y paginación, pero debe filtrar por rango de fechas (Desde/Hasta). (Permiso: `recurso.exportar-todo`).

---
*Este estándar es la base para garantizar la rapidez en el desarrollo y la calidad del código en Aybar ERP.*
