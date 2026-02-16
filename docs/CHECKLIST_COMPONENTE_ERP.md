# Checklist de Estándares para Componentes Livewire ERP

Este documento detalla los requisitos técnicos y de diseño que deben cumplir todos los componentes Livewire dentro del sistema ERP.

## 1. Estructura de la Clase (PHP)

### 1.1. Carga Perezosa y Marcador de Posición
Si el componente usa el layout principal del ERP, debe implementar la carga perezosa (`#[Lazy]`) y el método `placeholder()`.

```php
#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class MiComponente extends Component
{
    // ...

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
```

### 1.2. Manejo de Errores y Logging
Cada componente debe registrar sus errores en un canal de log específico relacionado con su módulo.

```php
// Ejemplo para el módulo de proyectos
Log::channel('proyecto')->error("[PROYECTO] Error en acción X: " . $e->getMessage(), [
    'usuario_id' => auth()->id(),
    'datos' => $this->all(),
    'trace' => $e->getTraceAsString()
]);
```

### 1.3. Validación de Formularios
La validación debe capturar la `ValidationException` para despachar una alerta genérica al usuario antes de lanzar la excepción original.

```php
public function store()
{
    try {
        $this->validate();
    } catch (ValidationException $e) {
        $this->dispatch('alertaLivewire', [
            'type' => 'warning',
            'title' => 'Advertencia',
            'text' => 'Verifique los errores de los campos resaltados.'
        ]);
        throw $e;
    }
    
    // ... lógica concurrente
}
```

### 1.4. Autorización y Transacciones
Todas las funciones que impacten la base de datos (`store`, `update`, `destroy`, `quitar`, `agregar`, etc.) deben:
1. Validar permisos con `$this->authorize()`.
2. Usar transacciones de base de datos.

```php
public function store()
{
    $this->authorize('modulo.crear');
    
    // ... validación ...

    try {
        DB::beginTransaction();
        
        // ... creación/edición ...

        DB::commit();
        
        // ... feedback y redirección ...
        
    } catch (\Exception $e) {
        DB::rollBack();
        Log::channel('canal_modulo')->error(...);
        // ... dispatch error alert ...
    }
}
```

---

## 2. Estructura de la Vista (Blade)

### 2.1. Indicadores de Carga
Toda acción que procese datos debe tener un overlay de carga vinculado.

```blade
<x-loading-overlay wire:loading wire:target="store" message="Procesando..." />
```

### 2.2. Clasificación de Errores en Inputs
Los campos de entrada deben resaltar visualmente cuando hay un error de validación.

```blade
<div class="g_columna_12">
    <label>Nombre del Campo</label>
    <select wire:model.live="campo_id" class="@error('campo_id') input-error @enderror">
        <option value="">Seleccione...</option>
        {{-- ... --}}
    </select>
    @error('campo_id') <span class="g_error">{{ $message }}</span> @enderror
</div>
```

### 2.3. Restricciones de UI
- **NO USAR `placeholder`** en los inputs. La etiqueta (label) es suficiente o usar una opción por defecto para los selects.
- Los botones o enlaces que ejecutan acciones en la DB deben estar protegidos por `@can`.

```blade
@can('modulo.crear')
    <button wire:click="store" class="g_boton guardar">
        <i class="fa-solid fa-save"></i> Guardar
    </button>
@endcan
```

---

*Referencia base: `app/Livewire/Erp/Negocio/Proyecto/ProyectoCrear.php`*
