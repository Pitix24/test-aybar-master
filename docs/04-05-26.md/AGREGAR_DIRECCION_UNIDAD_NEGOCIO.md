# Implementación: Agregar Campo Dirección en Unidad de Negocio

**Fecha:** 4 de mayo de 2026  
**Rama:** `feature/libro-reclamacion`  
**Objetivo:** Agregar y visualizar el campo `direccion` en las vistas Crear, Editar y Ver de Unidad de Negocio

---

## 📋 Resumen Ejecutivo

Se agregará el campo `direccion` (que ya existe en la tabla `unidad_negocios`) a la sección "Información General" de las vistas `Crear`, `Editar` y `Ver` de Unidad de Negocio. El campo será:

- **Editable** en las vistas Crear y Editar
- **Solo lectura** en la vista Ver
- **Validado** como: `nullable|string|max:255`

---

## 🎯 Campo a Implementar

### Dirección de Unidad de Negocio

- **Nombre en BD:** `direccion`
- **Tipo:** `text` / `nullable`
- **Descripción:** Dirección física/postal de la unidad de negocio
- **Ubicación en formulario:** Sección "Información General"
- **Validación:**
    - nullable
    - string
    - max 255 caracteres
- **Interfaz:**
    - Textarea de múltiples líneas en modo Crear/Editar
    - Textarea readonly en modo Ver
    - Mostrar "-" si vacío

---

## 🔧 Archivos a Modificar

### 1. Componente Crear

**Archivo:** `app/Livewire/Erp/Negocio/UnidadNegocio/UnidadNegocioCrear.php`

#### Cambio 1.1: Agregar propiedad pública

**Ubicación:** Después de la línea `public $slin_id = '';`

```php
public $direccion = '';
```

**Contexto completo:**

```php
public $slin_id = '';
public $direccion = '';  // ← AGREGAR ESTA LÍNEA
public $cavali_girador_tipo_documento = '';
```

#### Cambio 1.2: Agregar regla de validación

**Ubicación:** En el método `rules()`, después de `'slin_id' => ...`

```php
'direccion' => 'nullable|string|max:255',
```

**Contexto completo:**

```php
'slin_id' => 'nullable|string|max:50|unique:unidad_negocios,slin_id',
'direccion' => 'nullable|string|max:255',  // ← AGREGAR ESTA LÍNEA
'cavali_girador_tipo_documento' => 'nullable|string|max:50',
```

#### Cambio 1.3: Agregar atributo de validación

**Ubicación:** En el método `validationAttributes()`, después de `'slin_id' => ...`

```php
'direccion' => 'dirección',
```

**Contexto completo:**

```php
'slin_id' => 'SLIN ID',
'direccion' => 'dirección',  // ← AGREGAR ESTA LÍNEA
'cavali_girador_tipo_documento' => 'tipo doc. girador',
```

#### Cambio 1.4: Mapear en método `store()`

**Ubicación:** En el método `store()`, dentro de `UnidadNegocio::create([...])`, después de `'slin_id' => ...`

```php
'direccion' => $this->direccion ?: null,
```

**Contexto completo:**

```php
'ruc' => $this->ruc ?: null,
'slin_id' => $this->slin_id ?: null,
'direccion' => $this->direccion ?: null,  // ← AGREGAR ESTA LÍNEA
'cavali_girador_tipo_documento' => $this->cavali_girador_tipo_documento ?: null,
```

---

### 2. Componente Editar

**Archivo:** `app/Livewire/Erp/Negocio/UnidadNegocio/UnidadNegocioEditar.php`

#### Cambio 2.1: Agregar propiedad pública

**Ubicación:** Después de la línea `public $slin_id = '';`

```php
public $direccion = '';
```

**Contexto completo:**

```php
public $slin_id = '';
public $direccion = '';  // ← AGREGAR ESTA LÍNEA
public $cavali_girador_tipo_documento = '';
```

#### Cambio 2.2: Cargar en método `mount()`

**Ubicación:** En el método `mount()`, después de `$this->slin_id = ...`

```php
$this->direccion = $this->unidad_model->direccion;
```

**Contexto completo:**

```php
$this->ruc = $this->unidad_model->ruc;
$this->slin_id = $this->unidad_model->slin_id;
$this->direccion = $this->unidad_model->direccion;  // ← AGREGAR ESTA LÍNEA
$this->cavali_girador_tipo_documento = $this->unidad_model->cavali_girador_tipo_documento;
```

#### Cambio 2.3: Agregar regla de validación

**Ubicación:** En el método `rules()`, después de `'slin_id' => ...`

```php
'direccion' => 'nullable|string|max:255',
```

**Contexto completo:**

```php
'slin_id' => 'nullable|string|max:50|unique:unidad_negocios,slin_id,' . $this->unidad_model->id,
'direccion' => 'nullable|string|max:255',  // ← AGREGAR ESTA LÍNEA
'cavali_girador_tipo_documento' => 'nullable|string|max:50',
```

#### Cambio 2.4: Agregar atributo de validación

**Ubicación:** En el método `validationAttributes()`, después de `'slin_id' => ...`

```php
'direccion' => 'dirección',
```

**Contexto completo:**

```php
'slin_id' => 'SLIN ID',
'direccion' => 'dirección',  // ← AGREGAR ESTA LÍNEA
'cavali_girador_tipo_documento' => 'tipo doc. girador',
```

#### Cambio 2.5: Mapear en método `update()`

**Ubicación:** En el método `update()`, dentro de `$this->unidad_model->update([...])`, después de `'slin_id' => ...`

```php
'direccion' => $this->direccion ?: null,
```

**Contexto completo:**

```php
'ruc' => $this->ruc ?: null,
'slin_id' => $this->slin_id ?: null,
'direccion' => $this->direccion ?: null,  // ← AGREGAR ESTA LÍNEA
'cavali_girador_tipo_documento' => $this->cavali_girador_tipo_documento ?: null,
```

---

### 3. Vista Crear

**Archivo:** `resources/views/livewire/erp/negocio/unidad-negocio/unidad-negocio-crear.blade.php`

#### Cambio 3.1: Agregar textarea de dirección

**Ubicación:** En la pestaña "Información General", después del bloque de RUC (después de la línea `</div>` que cierra la fila de RUC)

```blade
<div class="g_fila">
    <div class="g_margin_bottom_10 g_columna_12">
        <label for="direccion">
            Dirección
        </label>
        <textarea id="direccion" wire:model.blur="direccion"
            class="@error('direccion') input-error @enderror" placeholder="Ej: Av. Principal 123, Piso 5, Lima"></textarea>
        @error('direccion')
            <p class="mensaje_error">{{ $message }}</p>
        @enderror
    </div>
</div>
```

**Contexto de ubicación (buscar en la vista):**

```blade
<div class="g_fila">
    <div class="g_margin_bottom_10 g_columna_6">
        <label for="ruc">
            RUC
        </label>
        <input type="text" id="ruc" wire:model.blur="ruc"
            class="@error('ruc') input-error @enderror" autocomplete="off" maxlength="11">
        @error('ruc')
            <p class="mensaje_error">{{ $message }}</p>
        @enderror
    </div>
</div>
<!-- ← INSERTAR AQUÍ EL BLOQUE DE DIRECCIÓN -->
```

---

### 4. Vista Editar

**Archivo:** `resources/views/livewire/erp/negocio/unidad-negocio/unidad-negocio-editar.blade.php`

#### Cambio 4.1: Agregar textarea de dirección

**Ubicación:** En la pestaña "Información General", después del bloque de RUC (idéntico al de Crear)

```blade
<div class="g_fila">
    <div class="g_margin_bottom_10 g_columna_12">
        <label for="direccion">
            Dirección
        </label>
        <textarea id="direccion" wire:model.blur="direccion"
            class="@error('direccion') input-error @enderror" placeholder="Ej: Av. Principal 123, Piso 5, Lima"></textarea>
        @error('direccion')
            <p class="mensaje_error">{{ $message }}</p>
        @enderror
    </div>
</div>
```

**Contexto de ubicación (buscar en la vista):**

```blade
<div class="g_fila">
    <div class="g_margin_bottom_10 g_columna_6">
        <label for="ruc">
            RUC
        </label>
        <input type="text" id="ruc" wire:model.blur="ruc"
            class="@error('ruc') input-error @enderror" autocomplete="off" maxlength="11">
        @error('ruc')
            <p class="mensaje_error">{{ $message }}</p>
        @enderror
    </div>
</div>
<!-- ← INSERTAR AQUÍ EL BLOQUE DE DIRECCIÓN -->
```

---

### 5. Vista Ver

**Archivo:** `resources/views/livewire/erp/negocio/unidad-negocio/unidad-negocio-ver.blade.php`

#### Cambio 5.1: Agregar campo de dirección (solo lectura)

**Ubicación:** En la pestaña "Información General", después del bloque de RUC

```blade
<div class="g_fila">
    <div class="g_margin_bottom_10 g_columna_12">
        <label>Dirección</label>
        <textarea readonly disabled>{{ $unidad_model->direccion ?? '-' }}</textarea>
    </div>
</div>
```

**Contexto de ubicación (buscar en la vista):**

```blade
<div class="g_fila">
    <div class="g_margin_bottom_10 g_columna_6">
        <label>RUC</label>
        <input type="text" value="{{ $unidad_model->ruc ?? '-' }}" readonly disabled>
    </div>
</div>
<!-- ← INSERTAR AQUÍ EL BLOQUE DE DIRECCIÓN -->
```

---

## ✅ Validaciones Implementadas

| Campo       | Regla                         | Mensaje               |
| ----------- | ----------------------------- | --------------------- |
| `direccion` | nullable \| string \| max:255 | Máximo 255 caracteres |

---

## 🚀 Pasos de Ejecución

1. **Editar Componente Crear** (`UnidadNegocioCrear.php`)
    - Agregar propiedad `$direccion = ''`
    - Agregar regla: `'direccion' => 'nullable|string|max:255'`
    - Agregar atributo: `'direccion' => 'dirección'`
    - Mapear en `store()`: `'direccion' => $this->direccion ?: null`

2. **Editar Componente Editar** (`UnidadNegocioEditar.php`)
    - Agregar propiedad `$direccion = ''`
    - Cargar en `mount()`: `$this->direccion = $this->unidad_model->direccion`
    - Agregar regla: `'direccion' => 'nullable|string|max:255'`
    - Agregar atributo: `'direccion' => 'dirección'`
    - Mapear en `update()`: `'direccion' => $this->direccion ?: null`

3. **Editar Vista Crear** (`unidad-negocio-crear.blade.php`)
    - Insertar textarea con `wire:model.blur="direccion"` después del bloque RUC

4. **Editar Vista Editar** (`unidad-negocio-editar.blade.php`)
    - Insertar textarea con `wire:model.blur="direccion"` después del bloque RUC (idéntico a Crear)

5. **Editar Vista Ver** (`unidad-negocio-ver.blade.php`)
    - Insertar textarea readonly con `{{ $unidad_model->direccion ?? '-' }}` después del bloque RUC

### Ejecutar Validaciones y Limpiar Caché

```bash
php -l app/Livewire/Erp/Negocio/UnidadNegocio/UnidadNegocioCrear.php
php -l app/Livewire/Erp/Negocio/UnidadNegocio/UnidadNegocioEditar.php
php -l resources/views/livewire/erp/negocio/unidad-negocio/unidad-negocio-crear.blade.php
php -l resources/views/livewire/erp/negocio/unidad-negocio/unidad-negocio-editar.blade.php
php -l resources/views/livewire/erp/negocio/unidad-negocio/unidad-negocio-ver.blade.php
php artisan view:clear
php artisan cache:clear
```

---

## 📊 Resultado Esperado

### En Crear:

- Textarea editable para Dirección
- Validación aplicada en tiempo real
- Al guardar, se persiste correctamente

### En Editar:

- Textarea precargado con dirección existente
- Editable y con validación
- Al guardar, se actualiza correctamente

### En Ver:

- Textarea readonly mostrando la dirección
- Si está vacía, muestra "-"
- No es editable

---

## 📌 Notas Importantes

- El campo `direccion` ya existe en la tabla `unidad_negocios` (verificado en BD)
- El campo es **opcional** (nullable)
- Se aplica `trim()` (automático en Laravel) antes de guardar
- Si está vacío, se guarda como `null`
- No hay cambios en el modelo `UnidadNegocio` (solo componentes y vistas)
- No se requieren migraciones (campo ya existe)

---

## 🔗 Referencia de Cambios

- **Rama:** `feature/libro-reclamacion`
- **Contexto:** Mejorar UI de gestión de Unidad de Negocio mostrando dirección
- **Documento:** `docs/AGREGAR_DIRECCION_UNIDAD_NEGOCIO.md`

---

**Estado:** Listo para implementación  
**Revisado:** 4 de mayo de 2026
