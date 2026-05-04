# Implementación de Nuevos Campos en Libro de Reclamaciones

**Fecha:** 4 de mayo de 2026  
**Rama:** `feature/libro-reclamacion`  
**Objetivo:** Agregar dirección de Unidad de Negocio y código de ticket gestionables en ERP

---

## 📋 Resumen Ejecutivo

Se agregarán dos nuevos campos al módulo de Libro de Reclamaciones que permitirán a los gestores del ERP:

1. **Dirección de la Unidad de Negocio** - Campo editable en crear/editar y visible en detalle
2. **Código de Ticket** - Campo con limitación de 3 caracteres, editable en crear/editar y visible en detalle

Ambos campos estarán disponibles en las vistas: **Crear**, **Editar** y **Ver**.

---

## 🎯 Campos a Implementar

### 1. Dirección de Unidad de Negocio

- **Nombre en BD:** `direccion_unidad_negocio`
- **Tipo:** `text` / `nullable`
- **Descripción:** Dirección completa de la sede/unidad de negocio donde se tramita la reclamación
- **Ubicación en formulario:** Sección "1.- Identificación del Proveedor"
- **Validación:**
    - nullable
    - string
    - max 255 caracteres
- **Interfaz:** Textarea de múltiples líneas en modo edición; lectura en modo Ver

### 2. Código de Ticket

- **Nombre en BD:** `codigo_ticket` (ya existe, se optimiza validación)
- **Tipo:** `string(20)` / `nullable`
- **Descripción:** Código único de 3 caracteres que identifica el ticket
- **Ubicación en formulario:** Sección "1.- Identificación del Proveedor" (junto a Manzana/Lote)
- **Validación:**
    - nullable
    - string
    - max 3 caracteres
- **Interfaz:** Input con `maxlength="3"` en modo edición; lectura en modo Ver

---

## 🔧 Archivos a Modificar

### Base de Datos

- ✅ `database/migrations/2026_05_04_130000_add_direccion_unidad_negocio_to_libro_reclamacions_table.php` **(NUEVO)**

### Modelo Eloquent

- ✅ `app/Models/LibroReclamacion/LibroReclamacion.php`
    - Agregar campo a `$fillable`

### Componentes Livewire

- ✅ `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`
    - Agregar propiedades públicas
    - Agregar reglas de validación
    - Mapear en método `store()`

- ✅ `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`
    - Agregar propiedades públicas
    - Cargar datos en `mount()`
    - Agregar reglas de validación
    - Mapear en método `update()`

### Vistas Blade

- ✅ `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-form.blade.php`
    - Agregar inputs para ambos campos
    - Soportar modo lectura con `@if($viewMode)`

---

## 📝 Pasos de Implementación

### Paso 1: Crear Migración

**Archivo:** `database/migrations/2026_05_04_130000_add_direccion_unidad_negocio_to_libro_reclamacions_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('libro_reclamacions', function (Blueprint $table) {
            $table->text('direccion_unidad_negocio')->nullable()->after('proyecto_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('libro_reclamacions', function (Blueprint $table) {
            $table->dropColumn('direccion_unidad_negocio');
        });
    }
};
```

### Paso 2: Actualizar Modelo

**Archivo:** `app/Models/LibroReclamacion/LibroReclamacion.php`

En el arreglo `$fillable`, agregar:

```php
'direccion_unidad_negocio',
```

El campo ya existe en fillable: `'codigo_ticket'` (verificar).

### Paso 3: Actualizar Componente Crear

**Archivo:** `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`

#### 3.1 Agregar propiedades públicas (después de `$proyecto_id`):

```php
public $direccion_unidad_negocio = '';
// codigo_ticket ya debe existir
```

#### 3.2 En método `rules()`, agregar validaciones:

```php
'direccion_unidad_negocio' => 'nullable|string|max:255',
'codigo_ticket' => 'nullable|string|max:3',
```

#### 3.3 En método `store()`, mapear campos antes de `create()`:

```php
$this->ticket_model->create([
    // ... otros campos ...
    'direccion_unidad_negocio' => trim($this->direccion_unidad_negocio) ?: null,
    'codigo_ticket' => trim($this->codigo_ticket) ?: null,
    // ... resto de campos ...
]);
```

### Paso 4: Actualizar Componente Editar

**Archivo:** `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`

#### 4.1 Agregar propiedades públicas (después de `$proyecto_id`):

```php
public $direccion_unidad_negocio = '';
// codigo_ticket ya debe existir
```

#### 4.2 En método `mount()`, cargar datos:

```php
$this->direccion_unidad_negocio = $this->ticket_model->direccion_unidad_negocio ?? '';
// codigo_ticket debería estar ya mapeado
```

#### 4.3 En método `rules()`, agregar validaciones (igual a Crear):

```php
'direccion_unidad_negocio' => 'nullable|string|max:255',
'codigo_ticket' => 'nullable|string|max:3',
```

#### 4.4 En método `update()`, mapear campos:

```php
$this->ticket_model->update([
    // ... otros campos ...
    'direccion_unidad_negocio' => trim($this->direccion_unidad_negocio) ?: null,
    'codigo_ticket' => trim($this->codigo_ticket) ?: null,
    // ... resto de campos ...
]);
```

### Paso 5: Actualizar Vista Unificada

**Archivo:** `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-form.blade.php`

#### 5.1 En Sección "1.- Identificación del Proveedor", después del campo Proyecto:

Agregar dirección de Unidad de Negocio:

```blade
<div class="g_columna_12 g_margin_bottom_10">
    <label>Dirección de la Unidad de Negocio</label>
    @if($viewMode)
    <textarea disabled>{{ data_get($ticket, 'direccion_unidad_negocio') ?: 'N/D' }}</textarea>
    @else
    <textarea wire:model.blur="direccion_unidad_negocio" placeholder="Ej: Av. Principal 123, Piso 5"></textarea>
    @endif
</div>
```

#### 5.2 Actualizar fila de Manzana/Lote para agregar Código de Ticket:

Modificar estructura de columnas (cambiar de 3 columnas a 4):

```blade
<div class="g_fila">
    <!-- Manzana -->
    <div class="g_columna_3 g_margin_bottom_10">
        <label>Manzana</label>
        @if($viewMode)
        <input type="text" value="{{ $ticket->manzana ?: 'N/D' }}" disabled>
        @else
        <input type="text" wire:model.blur="manzana">
        @endif
    </div>

    <!-- Lote -->
    <div class="g_columna_3 g_margin_bottom_10">
        <label>Lote</label>
        @if($viewMode)
        <input type="text" value="{{ $ticket->lote ?: 'N/D' }}" disabled>
        @else
        <input type="text" wire:model.blur="lote">
        @endif
    </div>

    <!-- Código Ticket (NUEVO) -->
    <div class="g_columna_3 g_margin_bottom_10">
        <label>Código Ticket (3 carac.)</label>
        @if($viewMode)
        <input type="text" value="{{ $ticket->codigo_ticket ?: 'N/D' }}" disabled maxlength="3">
        @else
        <input type="text" wire:model.blur="codigo_ticket" maxlength="3" placeholder="Ej: ABC">
        @endif
    </div>
</div>
```

---

## ✅ Validaciones Implementadas

| Campo                      | Regla                         | Mensaje               |
| -------------------------- | ----------------------------- | --------------------- |
| `direccion_unidad_negocio` | nullable \| string \| max:255 | Máximo 255 caracteres |
| `codigo_ticket`            | nullable \| string \| max:3   | Máximo 3 caracteres   |

---

## 🚀 Pasos de Ejecución Final

1. **Crear archivo de migración** con contenido del Paso 1
2. **Editar Modelo** para agregar campo a `$fillable`
3. **Editar Componente Crear** - agregar propiedades, validaciones y mapeo
4. **Editar Componente Editar** - agregar propiedades, validaciones, mount y mapeo
5. **Editar Vista Unificada** - agregar inputs con soporte a modo lectura

### Ejecutar Migraciones y Limpiar Caché

```bash
php artisan migrate
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan optimize:clear
```

---

## 📊 Resultado Esperado

### En Crear:

- Dirección de Unidad de Negocio: textarea editable
- Código Ticket: input editable con máx 3 caracteres

### En Editar:

- Ambos campos precargados con valores existentes
- Edición disponible
- Validaciones aplicadas

### En Ver:

- Dirección de Unidad de Negocio: textarea deshabilitada
- Código Ticket: input deshabilitado
- Ambos campos muestran "N/D" si están vacíos

---

## 📌 Notas Importantes

- El campo `codigo_ticket` ya existe en la BD con tipo `string(20)`, se valida a máx 3 caracteres en la aplicación
- La migración agrega `direccion_unidad_negocio` como nuevo campo
- Ambos campos son **opcionales** (nullable)
- Se aplica `trim()` y se convierte a `null` si está vacío al guardar
- Los datos se persisten correctamente en Create, Update y se muestran en View
- La vista unificada soporta modo lectura con `@if($viewMode)`

---

## 🔗 Referencia de Commits

- **Rama:** `feature/libro-reclamacion`
- **Cambios anteriores:** Unificación de vistas (Crear/Editar/Ver) en plantilla única
- **Este documento:** `docs/CAMPOS_NUEVOS_LIBRO_RECLAMACION.md`

---

**Estado:** Listo para implementación  
**Revisado:** 4 de mayo de 2026
