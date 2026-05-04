# Plan: Menor de Edad y Representante Legal

**Fecha:** 4 de mayo de 2026  
**Rama:** `feature/libro-reclamacion`  
**Módulo:** Libro de Reclamaciones  
**Versión:** 1.0

---

## Objetivo General

Agregar a la plataforma digital (web) y al backoffice (ERP) la capacidad de registrar y gestionar casos donde el reclamante es **menor de edad**, activando un bloque condicional que captura los **datos del padre o representante legal** (nombre y apellidos).

El flujo permitirá:

- Marcar si el cliente es menor de edad en el formulario web
- Mostrar un bloque condicional para capturar datos del representante legal
- Permitir visualización, creación y edición de estos datos en el ERP
- Persistir todos los datos en la tabla `libro_reclamacions` de la base de datos

---

## Alcance Confirmado

| Aspecto                            | Decisión                                             |
| ---------------------------------- | ---------------------------------------------------- |
| **Campos del Representante Legal** | Nombre y apellidos únicamente                        |
| **Captura de Datos**               | En la plataforma web                                 |
| **Edición de Datos**               | Permitida en el ERP (creación y edición)             |
| **Visualización en ERP**           | Lectura en la vista detalle                          |
| **Almacenamiento**                 | Tabla `libro_reclamacions` (no en `unidad_negocios`) |
| **Indicador Visual**               | Badge/columna en lista de ERP (opcional)             |

---

## Arquitectura de Cambios

### 1. Base de Datos (Migración)

**Archivo:** `database/migrations/2026_02_16_211526_create_libro_reclamacions_table.php`

**Campos a agregar:**

```
- es_cliente_menor (boolean, default: false, nullable: false)
- representante_legal_nombre (string, 255, nullable: true)
- representante_legal_apellido (string, 255, nullable: true)
```

**Consideraciones:**

- Los campos son nullable para permitir valores vacíos en registros de mayores de edad
- El flag `es_cliente_menor` tiene default `false` para compatibilidad con registros existentes
- La migración se ejecutará de forma segura sin romper datos presentes

### 2. Modelo de Datos

**Archivo:** `app/Models/LibroReclamacion/LibroReclamacion.php`

**Cambios requeridos:**

- Agregar campos nuevos al array `$fillable`
- Agregar casts para `es_cliente_menor` como booleano
- Mantener los campos de representante legal como string

```php
protected $fillable = [
    // ... campos existentes ...
    'es_cliente_menor',
    'representante_legal_nombre',
    'representante_legal_apellido',
];

protected $casts = [
    // ... casts existentes ...
    'es_cliente_menor' => 'boolean',
];
```

---

## Flujo Web (Plataforma Digital)

### 3. Componente Livewire Web

**Archivo:** `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`

**Cambios requeridos:**

#### 3.1 Agregar propiedades públicas

```php
public $es_cliente_menor = false;
public $representante_legal_nombre = '';
public $representante_legal_apellido = '';
```

#### 3.2 Extender reglas de validación

```php
protected function rules()
{
    return [
        // ... reglas existentes ...
        'es_cliente_menor' => 'nullable|boolean',
        'representante_legal_nombre' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
        'representante_legal_apellido' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
    ];
}
```

#### 3.3 Agregar atributos de validación

```php
public function validationAttributes()
{
    return [
        // ... atributos existentes ...
        'es_cliente_menor' => 'indicador de menor de edad',
        'representante_legal_nombre' => 'nombre del representante legal',
        'representante_legal_apellido' => 'apellido del representante legal',
    ];
}
```

#### 3.4 Persistir datos en el guardado

En el método `enviar()`, dentro del array de `LibroReclamacion::create()`, incluir:

```php
'es_cliente_menor' => (bool) $this->es_cliente_menor,
'representante_legal_nombre' => $this->textoNullable($this->representante_legal_nombre),
'representante_legal_apellido' => $this->textoNullable($this->representante_legal_apellido),
```

### 4. Vista Blade Web

**Archivo:** `resources/views/livewire/web/libro-reclamacion/libro-reclamacion-livewire.blade.php`

**Ubicación:** Después de los campos de datos personales del reclamante (nombre, apellidos, teléfono, email, dirección)

**Bloque a insertar:**

```blade
<!-- Indicador de menor de edad -->
<div class="g_margin_bottom_10">
    <label>¿Es el reclamante menor de edad? <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
    <div style="display: flex; gap: 1rem;">
        <label>
            <input type="radio" wire:model.live="es_cliente_menor" :value="false"> No
        </label>
        <label>
            <input type="radio" wire:model.live="es_cliente_menor" :value="true"> Sí, es menor de edad
        </label>
    </div>
</div>

<!-- Bloque condicional: Datos del representante legal -->
@if ($es_cliente_menor)
    <div class="g_margin_top_20 g_alerta warning">
        <i class="fa-solid fa-exclamation-triangle"></i>
        <strong>Datos del Padre, Madre o Representante Legal Requerido</strong>
    </div>

    <div class="g_fila">
        <div class="g_columna_6 g_margin_bottom_10">
            <label>Nombre del representante legal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model.blur="representante_legal_nombre"
                class="@error('representante_legal_nombre') input-error @enderror"
                placeholder="Ej: Juan">
            @error('representante_legal_nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_columna_6 g_margin_bottom_10">
            <label>Apellido del representante legal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model.blur="representante_legal_apellido"
                class="@error('representante_legal_apellido') input-error @enderror"
                placeholder="Ej: Pérez García">
            @error('representante_legal_apellido') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>
    </div>
@endif
```

---

## Flujo ERP (Backoffice)

### 5. Componente Livewire: Crear

**Archivo:** `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`

**Cambios requeridos:**

#### 5.1 Agregar propiedades públicas

```php
public $es_cliente_menor = false;
public $representante_legal_nombre = '';
public $representante_legal_apellido = '';
```

#### 5.2 Extender reglas de validación (similar al web)

```php
protected function rules(): array
{
    return [
        // ... reglas existentes ...
        'es_cliente_menor' => 'nullable|boolean',
        'representante_legal_nombre' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
        'representante_legal_apellido' => 'required_if:es_cliente_menor,true|string|max:255|nullable',
    ];
}
```

#### 5.3 Agregar atributos de validación

```php
public function validationAttributes(): array
{
    return [
        // ... atributos existentes ...
        'es_cliente_menor' => 'indicador de menor de edad',
        'representante_legal_nombre' => 'nombre del representante legal',
        'representante_legal_apellido' => 'apellido del representante legal',
    ];
}
```

#### 5.4 Agregar validación en `updated()`

```php
public function updated($propertyName): void
{
    // ... lógica existente ...

    if (in_array($propertyName, [
        // ... propiedades existentes ...
        'es_cliente_menor',
        'representante_legal_nombre',
        'representante_legal_apellido',
    ], true)) {
        $this->validateOnly($propertyName);
    }
}
```

#### 5.5 En el método `store()`, persistir los nuevos campos

```php
$reclamo = LibroReclamacion::create([
    // ... campos existentes ...
    'es_cliente_menor' => (bool) $this->es_cliente_menor,
    'representante_legal_nombre' => trim($this->representante_legal_nombre) ?: null,
    'representante_legal_apellido' => trim($this->representante_legal_apellido) ?: null,
]);
```

### 6. Componente Livewire: Editar

**Archivo:** `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`

**Cambios requeridos:**

#### 6.1 Agregar propiedades públicas (idénticas a Crear)

```php
public $es_cliente_menor = false;
public $representante_legal_nombre = '';
public $representante_legal_apellido = '';
```

#### 6.2 En el método `mount()`, cargar los valores existentes

```php
public function mount($id): void
{
    // ... código existente de carga del ticket ...

    // Cargar datos de menor de edad y representante legal
    $this->es_cliente_menor = $this->ticket_model->es_cliente_menor ?? false;
    $this->representante_legal_nombre = $this->ticket_model->representante_legal_nombre ?? '';
    $this->representante_legal_apellido = $this->ticket_model->representante_legal_apellido ?? '';
}
```

#### 6.3 Extender validaciones y atributos (idénticas a Crear)

Aplicar los mismos cambios que en `rules()`, `validationAttributes()` y `updated()`.

#### 6.4 En el método `update()`, persistir los nuevos campos

```php
$this->ticket_model->update([
    // ... campos existentes ...
    'es_cliente_menor' => (bool) $this->es_cliente_menor,
    'representante_legal_nombre' => trim($this->representante_legal_nombre) ?: null,
    'representante_legal_apellido' => trim($this->representante_legal_apellido) ?: null,
]);
```

### 7. Vista Blade: Crear

**Archivo:** `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-crear.blade.php`

**Ubicación:** Tab "Cliente", después de los campos `cliente_direccion`

**Bloque a insertar:** (idéntico al formulario web, con clases ERP ajustadas)

```blade
<!-- Indicador de menor de edad -->
<div class="g_margin_bottom_10">
    <label>¿Es el reclamante menor de edad?</label>
    <div style="display: flex; gap: 1rem;">
        <label>
            <input type="radio" wire:model.live="es_cliente_menor" :value="false"> No
        </label>
        <label>
            <input type="radio" wire:model.live="es_cliente_menor" :value="true"> Sí
        </label>
    </div>
</div>

<!-- Bloque condicional: Datos del representante legal -->
@if ($es_cliente_menor)
    <div class="g_margin_top_20 g_alerta warning">
        <i class="fa-solid fa-alert"></i>
        <strong>Representante Legal Requerido (Cliente Menor de Edad)</strong>
    </div>

    <div class="g_fila">
        <div class="g_columna_6 g_margin_bottom_10">
            <label>Nombre del representante legal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model.blur="representante_legal_nombre"
                class="@error('representante_legal_nombre') input-error @enderror">
            @error('representante_legal_nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_columna_6 g_margin_bottom_10">
            <label>Apellido del representante legal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model.blur="representante_legal_apellido"
                class="@error('representante_legal_apellido') input-error @enderror">
            @error('representante_legal_apellido') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>
    </div>
@endif
```

### 8. Vista Blade: Editar

**Archivo:** `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-editar.blade.php`

**Ubicación:** Tab "Cliente", después de los campos existentes del cliente

**Bloque a insertar:** (idéntico al crear, con cierto ajuste visual de lectura)

```blade
<!-- Indicador de menor de edad -->
<div class="g_margin_bottom_10">
    <label>¿Es el reclamante menor de edad?</label>
    <div style="display: flex; gap: 1rem;">
        <label>
            <input type="radio" wire:model.live="es_cliente_menor" :value="false"> No
        </label>
        <label>
            <input type="radio" wire:model.live="es_cliente_menor" :value="true"> Sí
        </label>
    </div>
</div>

<!-- Bloque condicional: Datos del representante legal -->
@if ($es_cliente_menor)
    <div class="g_margin_top_20 g_alerta warning">
        <i class="fa-solid fa-alert"></i>
        <strong>Representante Legal Requerido (Cliente Menor de Edad)</strong>
    </div>

    <div class="g_fila">
        <div class="g_columna_6 g_margin_bottom_10">
            <label>Nombre del representante legal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model.blur="representante_legal_nombre"
                class="@error('representante_legal_nombre') input-error @enderror">
            @error('representante_legal_nombre') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>

        <div class="g_columna_6 g_margin_bottom_10">
            <label>Apellido del representante legal <span class="obligatorio"><i class="fa-solid fa-asterisk"></i></span></label>
            <input type="text" wire:model.blur="representante_legal_apellido"
                class="@error('representante_legal_apellido') input-error @enderror">
            @error('representante_legal_apellido') <p class="mensaje_error">{{ $message }}</p> @enderror
        </div>
    </div>
@endif
```

### 9. Vista Blade: Ver

**Archivo:** `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php`

**Ubicación:** Tab "Cliente", después de la sección de datos del cliente (antes del cierre del tab)

**Bloque a insertar:** (solo lectura, sin campos editables)

```blade
<!-- Bloque de Menor de Edad y Representante Legal (Solo lectura) -->
@if ($ticket->es_cliente_menor)
    <div class="g_margin_top_20 g_alerta info">
        <i class="fa-solid fa-exclamation-triangle"></i>
        <strong>Cliente Menor de Edad - Representante Legal Registrado</strong>
    </div>

    <div class="g_fila">
        <div class="g_columna_6 g_margin_bottom_10">
            <label>Nombre del representante legal</label>
            <input type="text" value="{{ $ticket->representante_legal_nombre ?: 'N/D' }}" disabled>
        </div>

        <div class="g_columna_6 g_margin_bottom_10">
            <label>Apellido del representante legal</label>
            <input type="text" value="{{ $ticket->representante_legal_apellido ?: 'N/D' }}" disabled>
        </div>
    </div>
@endif
```

### 10. Vista Blade: Lista (Opcional)

**Archivo:** `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-lista.blade.php`

**Ubicación:** Tabla, después de la columna "Estado" o "Clasificación"

**Bloque a insertar:** (indicador visual de si es menor de edad)

```blade
<th class="g_celda_centro" style="width: 80px;">Menor</th>

<!-- En la fila de datos: -->
<td class="g_celda_centro">
    @if ($item->es_cliente_menor)
        <span class="g_badge danger"><i class="fa-solid fa-triangle-exclamation"></i> Menor</span>
    @else
        <span class="g_badge light">Mayor</span>
    @endif
</td>
```

---

## Secuencia de Implementación

| Orden | Paso                                            | Archivo(s)                                                                            | Prioridad   |
| ----- | ----------------------------------------------- | ------------------------------------------------------------------------------------- | ----------- |
| 1     | Crear migración                                 | `database/migrations/...`                                                             | **CRÍTICA** |
| 2     | Actualizar modelo                               | `app/Models/LibroReclamacion/LibroReclamacion.php`                                    | **CRÍTICA** |
| 3     | Componente web (propiedades, reglas, atributos) | `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`                      | ALTA        |
| 4     | Vista web (bloque condicional)                  | `resources/views/livewire/web/libro-reclamacion/libro-reclamacion-livewire.blade.php` | ALTA        |
| 5     | Persistencia web (create)                       | `app/Livewire/Web/LibroReclamacion/LibroReclamacionLivewire.php`                      | ALTA        |
| 6     | Componente ERP Crear                            | `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`                         | ALTA        |
| 7     | Vista ERP Crear                                 | `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-crear.blade.php`    | ALTA        |
| 8     | Componente ERP Editar                           | `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`                        | ALTA        |
| 9     | Vista ERP Editar                                | `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-editar.blade.php`   | ALTA        |
| 10    | Vista ERP Ver                                   | `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php`      | ALTA        |
| 11    | Vista ERP Lista (indicador)                     | `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-lista.blade.php`    | MEDIA       |
| 12    | Pruebas y validación                            | Suite de pruebas del módulo                                                           | MEDIA       |

---

## Validación de Cambios

### Checklist Pre-Migración

- [ ] Branch correcta: `feature/libro-reclamacion`
- [ ] Sin cambios pendientes en la migración que conflicten con registros existentes
- [ ] Default values seguros (false para booleanos, null para strings)

### Checklist Post-Implementación

- [ ] Migración ejecutada sin errores: `php artisan migrate`
- [ ] Modelo refleja los cambios en `$fillable` y `$casts`
- [ ] Crear reclamo **sin** menor de edad: el bloque no aparece, guardado exitoso
- [ ] Crear reclamo **con** menor de edad: bloque aparece, validación funciona, guardado exitoso
- [ ] Editar reclamo de menor de edad: carga valores, permitir cambios, guardado exitoso
- [ ] Ver reclamo de menor de edad en ERP: datos mostrados correctamente
- [ ] Ver reclamo de mayor de edad en ERP: no aparece bloque de representante legal
- [ ] Lista ERP: indicador visual activo para menores de edad (si está implementado)

### Checklist de Regresión

- [ ] Flujo web sin cambios de edad: funciona como antes
- [ ] Flujo ERP sin cambios de edad: funciona como antes
- [ ] Compatibilidad con registros históricos (no rompen)
- [ ] No hay errores en logs de Livewire
- [ ] No hay errores en validaciones condicionales

---

## Pruebas Funcionales Recomendadas

### Test 1: Alta de Reclamo Web (Mayor de Edad)

```
1. Ir a formulario web de libro de reclamaciones
2. Seleccionar "No" en ¿Es menor de edad?
3. No debe aparecer bloque de representante legal
4. Llenar resto del formulario y enviar
5. Verificar en BD que es_cliente_menor = false
```

### Test 2: Alta de Reclamo Web (Menor de Edad)

```
1. Ir a formulario web de libro de reclamaciones
2. Seleccionar "Sí" en ¿Es menor de edad?
3. Debe aparecer bloque con campos de nombre y apellido del representante
4. Dejar campos en blanco e intentar enviar: validación rechaza
5. Llenar nombre y apellido, enviar
6. Verificar en BD que es_cliente_menor = true y campos del representante están poblados
```

### Test 3: Crear en ERP (Menor de Edad)

```
1. En ERP, acceder a Crear Ticket Libro Reclamación
2. Seleccionar "Sí" en ¿Es menor de edad?
3. Ingresar datos del representante legal
4. Guardar
5. Verificar que se creó exitosamente con datos del representante
```

### Test 4: Editar en ERP (Cambiar Estado de Edad)

```
1. Cargar un ticket existente con es_cliente_menor = false
2. Cambiar a "Sí" en ¿Es menor de edad?
3. Rellenar datos del representante legal
4. Guardar
5. Verificar que se actualizó correctamente
6. Cambiar de nuevo a "No"
7. Verificar que los datos del representante se preservan pero no aparecen (opcional: limpiar al cambiar)
```

### Test 5: Ver en ERP (Lectura)

```
1. Acceder a un reclamo con es_cliente_menor = true
2. Ir a tab Cliente en vista Ver
3. Verificar que aparece alerta y datos del representante en lectura
4. Acceder a un reclamo con es_cliente_menor = false
5. No debe aparecer bloque de representante legal
```

---

## Consideraciones Técnicas

### Validación Condicional

- `required_if:es_cliente_menor,true` asegura que los campos del representante son obligatorios solo si es menor
- Si `es_cliente_menor` es false, los campos pueden estar vacíos o null

### Limpieza de Datos

- Se recomienda opcionalmente, cuando se cambia de "Sí" a "No" en `es_cliente_menor`, limpiar los campos del representante legal para evitar datos huérfanos
- Esto puede dejarse para una segunda fase si el equipo lo considera

### Compatibilidad Histórica

- Todos los registros existentes tendrán `es_cliente_menor = false` por el default en la migración
- Los campos `representante_legal_nombre` y `representante_legal_apellido` serán NULL para registros históricos
- Esto no genera ruptura en el flujo actual

### Emails y Notificaciones

- Si los emails del módulo deben incluir información del representante legal, se debe revisar:
    - `app/Mail/LibroReclamacion/LibroReclamacionClienteMail.php`
    - `app/Mail/LibroReclamacion/LibroReclamacionLegalMail.php`
    - Templates correspondientes en `resources/views/emails/libro-reclamacion/`
- Esto puede hacerse en una segunda fase si se lo requiere

---

## Impacto en Otros Módulos

| Módulo/Componente | Impacto | Acción                                                                                   |
| ----------------- | ------- | ---------------------------------------------------------------------------------------- |
| Tickets           | Bajo    | Ninguna (relación es unidireccional)                                                     |
| Eventos/Listeners | Bajo    | `LibroReclamacionRegistrado` dispara con nuevos datos; adaptar listeners si es necesario |
| Reportes          | Bajo    | Si existen reportes de libro de reclamaciones, podrían beneficiarse de filtro por edad   |
| Auditoría         | Ninguno | Los campos son parte del modelo estándar                                                 |

---

## Próximas Fases (Futuros)

1. **Fase 2:** Agregar más datos del representante legal si se requiere (teléfono, correo, tipo y número de documento, relación familiar)
2. **Fase 3:** Integración con flujo de firma digital de representante legal
3. **Fase 4:** Reportes analíticos sobre reclamaciones de menores
4. **Fase 5:** Validación de mayoría de edad contra base de datos de DNI (si disponible)

---

## Historial de Cambios

| Versión | Fecha      | Autor      | Cambio                                                                   |
| ------- | ---------- | ---------- | ------------------------------------------------------------------------ |
| 1.0     | 04-05-2026 | Equipo Dev | Plan inicial creado basado en análisis de flujos y decisiones de alcance |

---

## Contacto y Notas

Para consultas o cambios en el alcance, referirse al análisis técnico previo en:

- `/memories/session/libro-reclamacion-analysis.md`
- `/memories/session/libro-reclamacion-mapeo.md`
- `/memories/session/plan.md`

**Rama de trabajo:** `feature/libro-reclamacion`  
**Repositorio:** Mersmith/aybar  
**Rama base:** master
