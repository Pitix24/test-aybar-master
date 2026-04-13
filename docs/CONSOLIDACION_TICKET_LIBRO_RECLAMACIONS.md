# Plan de Consolidación: `ticket_libro_reclamacions` → `libro_reclamacions`

**Fecha:** 13 de Abril de 2026  
**Versión:** 1.0  
**Estado:** Aprobado para implementación  
**Decisión:** Consolidar dos tablas en una sola para simplificar el modelo de datos

---

## 1. Resumen Ejecutivo

Tu equipo de desarrollo ha indicado que mantener dos tablas separadas (`libro_reclamacions` y `ticket_libro_reclamacions`) es innecesario, ya que no se diferencian en tantos datos. La propuesta es:

- **Consolidar ambas tablas en una sola: `libro_reclamacions`**
- Agregar todos los campos operacionales de `ticket_libro_reclamacions` a `libro_reclamacions`
- Eliminar la tabla `ticket_libro_reclamacions` y su modelo asociado
- Actualizar todos los componentes, servicios y rutas para usar un único modelo
- **Mantener toda la funcionalidad:** tabs, búsqueda de cliente, selección de lotes, códigos auto-generados, clasificación automática, nota congelada

**Beneficios:**
- ✅ Reducción de complejidad: Una tabla en lugar de dos
- ✅ Menos migraciones y cambios de esquema
- ✅ Simplificación de relaciones y queries
- ✅ Funcionalidad intacta: El comportamiento del usuario final no cambia

---

## 2. Análisis de Estado Actual

### 2.1 Tablas Actuales

#### `libro_reclamacions` (Intake Histórico)
**Propósito:** Registro histórico de todas las reclamaciones ingresadas por web o ERP  
**PK:** `ticket` (bigInt)  
**Campos principales:**
- Identificación: `serie`, `numero_reclamo`, `codigo_ticket`
- Cliente: `nombre`, `apellido_paterno`, `apellido_materno`, `email`, `tipo_documento`, `numero_documento`
- Reclamación: `tipo_bien_contratado`, `monto_reclamado`, `descripcion`, `tipo_pedido`, `manzana`, `lote`
- Control: `leido`, `estado`
- Relaciones: `unidad_negocio_id`, `proyecto_id`, `cliente_id`, `gestor_id`
- Auditoría: Soft deletes

#### `ticket_libro_reclamacions` (Gestión Legal)
**Propósito:** Tabla operacional de gestión legal del equipo legal  
**PK:** `id` (bigInt, autoincrement)  
**Campos principales:**
- Identificación: `codigo` (3-char unique)
- Estado: `estado_legal`, `clasificacion`
- Cliente (persisted): `cliente_tipo_documento`, `cliente_documento`, `cliente_nombre`, `cliente_email`, `cliente_celular`, `cliente_direccion`
- Reclamación: `asunto`, `lotes` (JSON)
- Nota fuente: `nota_fuente` (text), `nota_fuente_titulo`, `nota_fuente_fecha` (datetime)
- Gestión: `gestor_id`, `assigned_at`, `observaciones_internas`
- Auditoría: Soft deletes + audit fields (`created_by`, `updated_by`, `deleted_by`)
- Relación: `libro_reclamacion_ticket` (→ `libro_reclamacions.ticket`, nullable)

### 2.2 Modelos Actuales
- `LibroReclamacion` → `libro_reclamacions`
- `TicketLibroReclamacion` → `ticket_libro_reclamacions`
- `LibroReclamacionContador` → `libro_reclamacion_contadores` (mantener para secuenciales)

### 2.3 Componentes Actuales (ERP)
- `LibroReclamacionCrear` → Crea `TicketLibroReclamacion`
- `LibroReclamacionEditar` → Edita `TicketLibroReclamacion`
- `LibroReclamacionLista` → Lista `TicketLibroReclamacion`
- `LibroReclamacionVer` → Visualiza `TicketLibroReclamacion`

### 2.4 Migraciones Aplicadas
- ✅ `2026_04_13_170000_create_ticket_libro_reclamacions_table.php` (Applied)
- ✅ `2026_04_13_190000_add_codigo_to_unidad_negocios_and_legal_fields_to_ticket_libro_reclamacions.php` (Applied)

---

## 3. Plan de Implementación

### FASE 1: Preparación de Base de Datos

#### 1.1 Crear Migración Consolidadora
**Archivo:** `database/migrations/2026_04_13_200000_consolidate_ticket_libro_reclamacions_into_libro_reclamacions.php`

**Acciones:**
1. Agregar 23 columnas nuevas a tabla `libro_reclamacions`:
   ```
   - codigo (string, 3, unique, nullable) — Código de ticket legal
   - estado_legal (enum: NUEVO|EN_GESTION|OBSERVADO|RESUELTO|NO_PROCEDE|CERRADO, default: NUEVO)
   - clasificacion (enum: PROCEDE|NO_PROCEDE|PENDIENTE_REVISION, default: PENDIENTE_REVISION)
   - cliente_tipo_documento (string, nullable)
   - cliente_documento (string, indexed, nullable)
   - cliente_nombre (string, nullable)
   - cliente_email (string, nullable)
   - cliente_celular (string, nullable)
   - cliente_direccion (text, nullable)
   - asunto (text, nullable)
   - lotes (json, nullable) — Array de lotes seleccionados
   - nota_fuente_titulo (string, nullable)
   - nota_fuente_fecha (datetime, nullable)
   - gestor_id (bigInt, unsigned, nullable, foreign → users.id)
   - assigned_at (datetime, nullable)
   - observaciones_internas (text, nullable)
   - created_by (bigInt, unsigned, nullable, foreign → users.id)
   - updated_by (bigInt, unsigned, nullable, foreign → users.id)
   - deleted_by (bigInt, unsigned, nullable, foreign → users.id)
   - deleted_at (datetime, nullable) — Para soft deletes
   ```

2. Copiar datos: Si `ticket_libro_reclamacions` contiene registros, copiarlos a `libro_reclamacions`:
   ```php
   $tickets = DB::connection('mysql')->table('ticket_libro_reclamacions')
       ->whereNull('deleted_at')
       ->get();
   
   foreach ($tickets as $ticket) {
       DB::table('libro_reclamacions')
           ->where('ticket', $ticket->libro_reclamacion_ticket)
           ->update([
               'codigo' => $ticket->codigo,
               'estado_legal' => $ticket->estado_legal,
               'clasificacion' => $ticket->clasificacion,
               'cliente_tipo_documento' => $ticket->cliente_tipo_documento,
               'cliente_documento' => $ticket->cliente_documento,
               'cliente_nombre' => $ticket->cliente_nombre,
               'cliente_email' => $ticket->cliente_email,
               'cliente_celular' => $ticket->cliente_celular,
               'cliente_direccion' => $ticket->cliente_direccion,
               'asunto' => $ticket->asunto,
               'lotes' => $ticket->lotes,
               'nota_fuente_titulo' => $ticket->nota_fuente_titulo,
               'nota_fuente_fecha' => $ticket->nota_fuente_fecha,
               'gestor_id' => $ticket->gestor_id,
               'assigned_at' => $ticket->assigned_at,
               'observaciones_internas' => $ticket->observaciones_internas,
               'created_by' => $ticket->created_by,
               'updated_by' => $ticket->updated_by,
               'deleted_by' => $ticket->deleted_by,
           ]);
   }
   ```

3. Agregar índices para performance:
   ```php
   Schema::table('libro_reclamacions', function (Blueprint $table) {
       $table->index('codigo');
       $table->index('estado_legal');
       $table->index('clasificacion');
       $table->index('gestor_id');
       $table->index('cliente_documento');
   });
   ```

4. Eliminar tabla `ticket_libro_reclamacions`:
   ```php
   Schema::dropIfExists('ticket_libro_reclamacions');
   ```

#### 1.2 Eliminar Migraciones Previas
- **DELETE:** `database/migrations/2026_04_13_170000_create_ticket_libro_reclamacions_table.php`
- **DELETE:** `database/migrations/2026_04_13_190000_add_codigo_to_unidad_negocios_and_legal_fields_to_ticket_libro_reclamacions.php`

**Por qué:** Estas migraciones ya se ejecutaron y sus cambios están incorporados en la migración consolidadora. Mantenerlas causaría conflictos.

---

### FASE 2: Refactorización de Modelos

#### 2.1 Actualizar `app/Models/LibroReclamacion/LibroReclamacion.php`

**Cambios:**

1. **Extender `$fillable`:**
   ```php
   protected $fillable = [
       // Campos existentes
       'serie', 'numero_reclamo', 'codigo_ticket', 'nombre', 'apellido_paterno', 
       'apellido_materno', 'email', 'tipo_documento', 'numero_documento',
       'tipo_bien_contratado', 'monto_reclamado', 'descripcion', 'tipo_pedido',
       'manzana', 'lote', 'leido', 'estado',
       'unidad_negocio_id', 'proyecto_id', 'cliente_id', 'gestor_id',
       
       // Nuevos campos consolidados
       'codigo', 'estado_legal', 'clasificacion',
       'cliente_tipo_documento', 'cliente_documento', 'cliente_nombre', 
       'cliente_email', 'cliente_celular', 'cliente_direccion',
       'asunto', 'lotes', 'nota_fuente_titulo', 'nota_fuente_fecha',
       'assigned_at', 'observaciones_internas',
       'created_by', 'updated_by', 'deleted_by',
   ];
   ```

2. **Agregar `$casts`:**
   ```php
   protected $casts = [
       'lotes' => 'array',
       'assigned_at' => 'datetime',
       'nota_fuente_fecha' => 'datetime',
       'leido' => 'boolean',
   ];
   ```

3. **Agregar relaciones (`belongsTo`):**
   ```php
   public function gestor()
   {
       return $this->belongsTo(User::class, 'gestor_id');
   }

   public function cliente()
   {
       return $this->belongsTo(User::class, 'cliente_id');
   }

   public function creador()
   {
       return $this->belongsTo(User::class, 'created_by');
   }

   public function actualizador()
   {
       return $this->belongsTo(User::class, 'updated_by');
   }

   public function eliminador()
   {
       return $this->belongsTo(User::class, 'deleted_by');
   }
   ```

4. **Agregar hook para auto-generar `codigo`:**
   ```php
   protected static function booted()
   {
       static::creating(function ($model) {
           if (empty($model->codigo)) {
               $unidad = UnidadNegocio::find($model->unidad_negocio_id);
               if ($unidad && $unidad->codigo) {
                   $codigo = $unidad->codigo;
               } else {
                   // Fallback: generar automáticamente
                   $codigo = UnidadNegocio::generarCodigoSecuencial(
                       ($model->unidad_negocio_id ?? 0) % 17576
                   );
               }
               $model->codigo = $codigo;
               $model->estado_legal = 'NUEVO';
               $model->clasificacion = 'PENDIENTE_REVISION';
           }
       });

       // Mantener hooks existentes de auditoría
       static::creating(function ($model) {
           $model->created_by = auth()->id();
       });

       static::updating(function ($model) {
           $model->updated_by = auth()->id();
       });
   }
   ```

#### 2.2 Eliminar Archivo `TicketLibroReclamacion.php`
- **DELETE:** `app/Models/LibroReclamacion/TicketLibroReclamacion.php`

**Razonamiento:** Todo su contenido se fusiona en `LibroReclamacion`. No es necesario mantener dos modelos.

---

### FASE 3: Validación de Servicios (Sin cambios esperados)

#### 3.1 Verificar `app/Services/LibroReclamacion/LibroReclamacionNumeroService.php`

**Estado:** Los métodos `resolverCodigoUnidad()` y `formatearCodigoTicket()` siguen siendo válidos:
- `resolverCodigoUnidad()`: Lee `$unidad->codigo` — ✅ Sigue funcionando en modelo consolidado
- `formatearCodigoTicket()`: Genera formato `ABC-000001` — ✅ Sin cambios necesarios

**Acción:** Ninguna. El servicio es agnóstico a la tabla; solo consume el modelo.

---

### FASE 4: Refactorización de Componentes Livewire

#### 4.1 Actualizar `app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php`

**Cambios principales:**

1. **Cambiar import:**
   ```php
   // Cambiar:
   use App\Models\LibroReclamacion\TicketLibroReclamacion;
   
   // A:
   use App\Models\LibroReclamacion\LibroReclamacion;
   ```

2. **Cambiar método de crear en `store()`:**
   ```php
   // Cambiar:
   $ticket = TicketLibroReclamacion::create([...]);
   
   // A:
   $ticket = LibroReclamacion::create([...]);
   ```

3. **Mantener todo lo demás:**
   - Propiedades públicas y transientes (dni, lote_id, etc.)
   - Validación de reglas
   - Búsqueda de cliente (`buscarCliente()`)
   - Hidratación de datos (`hidratarClienteDesdeResultado()`)
   - Gestión de lotes (`agregarLote()`, `quitarLote()`)
   - Cálculo de clasificación (`resolverClasificacion()`)
   - Construcción de nota (`construirNotaFuente()`)
   - Toda la lógica UI/UX de tabs, búsqueda y selección

#### 4.2 Actualizar `app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php`

**Cambios principales:**

1. **Cambiar import:**
   ```php
   use App\Models\LibroReclamacion\LibroReclamacion;
   ```

2. **Simplificar mount():**
   ```php
   public function mount($id)
   {
       $this->ticket = LibroReclamacion::findOrFail($id);
       
       // Cargar todos los campos (ahora están en la misma tabla)
       $this->codigo = $this->ticket->codigo;
       $this->estado_legal = $this->ticket->estado_legal;
       $this->clasificacion = $this->ticket->clasificacion;
       // ... resto de campos
   }
   ```

3. **Cambiar método `update()`:**
   ```php
   // Cambiar:
   $this->ticket->update([...]);
   
   // A:
   $this->ticket->update([
       'codigo' => $this->codigo,
       'estado_legal' => $this->estado_legal,
       'clasificacion' => $this->resolverClasificacion(),
       // Nota fuente NO se actualiza (congelada)
       // ... resto de campos
   ]);
   ```

4. **Mantener:**
   - Lógica de validación
   - Métodos de búsqueda de cliente
   - Gestión de lotes
   - Nota fuente congelada (no actualizar)
   - Toda la funcionalidad compartida

#### 4.3 Actualizar `app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php`

**Cambios principales:**

1. **Cambiar query en `render()`:**
   ```php
   // Cambiar:
   $items = TicketLibroReclamacion::query()
   
   // A:
   $items = LibroReclamacion::query()
   ```

2. **Simplificar búsqueda:**
   ```php
   // Cambiar: Queries con joins a ticket_libro_reclamacions
   // A: Queries directas en columnas de libro_reclamacions
   
   ->orWhere('codigo', 'like', "%{$this->search}%")
   ->orWhere('cliente_documento', 'like', "%{$this->search}%")
   ->orWhere('cliente_nombre', 'like', "%{$this->search}%")
   ->orWhere('cliente_email', 'like', "%{$this->search}%")
   ->orWhere('numero_documento', 'like', "%{$this->search}%")
   ```

3. **Mantener:**
   - Filtros por estado_legal, clasificación, gestor
   - Paginación
   - Orden y sorteo
   - Toda la lógica de lista

#### 4.4 Actualizar `app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php`

**Cambios principales:**

1. **Simplificar mount():**
   ```php
   public function mount($id)
   {
       $this->ticket = LibroReclamacion::findOrFail($id);
       
       // Ya no necesita cargar un relacionado; todo está en el mismo modelo
   }
   ```

2. **Mantener:**
   - Visualización de todos los campos
   - Relaciones a usuario gestor, creador, etc.
   - Toda la lógica de display

---

### FASE 5: Actualización de Vistas Blade

#### 5.1 `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-crear.blade.php`

**Cambios:** Mínimos — No cambios esperados
- La vista ya referencia propiedades del componente Livewire
- Las propiedades siguen siendo las mismas (cliente_nombre, asunto, lotes, etc.)
- Las directivas `wire:model`, `wire:click`, `wire:loading` siguen funcionando

#### 5.2 `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-editar.blade.php`

**Cambios:** Mínimos — No cambios esperados
- Misma lógica que crear
- Propiedades y validación ídem

#### 5.3 `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-ver.blade.php`

**Cambios:** Limpiar referencias obsoletas
- Remover referencias a `libro_reclamacion_ticket` si existen
- Verificar que todos los campos nuevos se muestren correctamente
- Asegurar que las relaciones a User (gestor, creador, etc.) funcionen

#### 5.4 `resources/views/livewire/erp/libro-reclamacion/libro-reclamacion-lista.blade.php`

**Cambios:** Mínimos — No cambios esperados
- Ya referencia campos del modelo
- Filtros y búsqueda siguen siendo iguales

---

### FASE 6: Rutas y Permisos

#### 6.1 Verificar `routes/erp/libro-reclamacion.php`

**Estado actual:**
```php
Route::middleware(['auth', 'permission:modulo-libro-reclamacion.ver'])->group(function () {
    Route::get('/libro-reclamacion', [...])->name('libro-reclamacion.lista');
    Route::get('/libro-reclamacion/crear', [...])->name('libro-reclamacion.crear');
    Route::get('/libro-reclamacion/ver/{id}', [...])->name('libro-reclamacion.ver');
    Route::get('/libro-reclamacion/editar/{id}', [...])->name('libro-reclamacion.editar');
    // ...
});
```

**Cambios necesarios:** Ninguno
- Las rutas siguen siendo válidas
- Permisos `modulo-libro-reclamacion.*` cubren todas las operaciones
- Componentes responden al mismo nombre de ruta

---

### FASE 7: Cleanup y Validación

#### 7.1 Buscar y Eliminar Referencias Huérfanas

**Acciones:**
1. Search codebase para `TicketLibroReclamacion`:
   ```bash
   grep -r "TicketLibroReclamacion" --include="*.php" app/ routes/ config/ database/
   ```
   
2. Reemplazar/eliminar todas las referencias encontradas
3. Verificar que cero resultados en búsqueda final

#### 7.2 Actualizar Documentación

**Archivos a revisar/actualizar:**
- `docs/DECISION_SHEET_TICKET_LIBRO_RECLAMACION_FASE2.md` → Actualizar con nuevo enfoque consolidado
- `docs/PLAN_IMPLEMENTACION_TICKET_LIBRO_RECLAMACION_*.md` → Marcar como supersedidas por este documento
- `docs/LIBRO_RECLAMACIONES_IMPLEMENTACION_TECNICA.md` → Agregar sección de consolidación

---

## 4. Cambios Resumidos por Archivo

| Archivo | Acción | Detalle |
|---------|--------|---------|
| `database/migrations/2026_04_13_200000_consolidate_*.php` | **CREATE** | Nueva migración consolidadora |
| `database/migrations/2026_04_13_170000_create_ticket_*.php` | **DELETE** | Supersedida por consolidación |
| `database/migrations/2026_04_13_190000_add_codigo_*.php` | **DELETE** | Supersedida por consolidación |
| [app/Models/LibroReclamacion/LibroReclamacion.php](app/Models/LibroReclamacion/LibroReclamacion.php) | **UPDATE** | Agregar 23 campos, casts, relaciones, hooks |
| `app/Models/LibroReclamacion/TicketLibroReclamacion.php` | **DELETE** | Fusionado en LibroReclamacion |
| [app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php](app/Livewire/Erp/LibroReclamacion/LibroReclamacionCrear.php) | **UPDATE** | Cambiar `use`/crear modelo a `LibroReclamacion` |
| [app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php](app/Livewire/Erp/LibroReclamacion/LibroReclamacionEditar.php) | **UPDATE** | Idem, simplificar mount/update |
| [app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php](app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php) | **UPDATE** | Query a `LibroReclamacion`, simplificar búsqueda |
| [app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php](app/Livewire/Erp/LibroReclamacion/LibroReclamacionVer.php) | **UPDATE** | Simplificar mount, sin related fetch |
| [resources/views/livewire/erp/libro-reclamacion/*.blade.php](resources/views/livewire/erp/libro-reclamacion/) | **MINIMAL** | Limpiar referencias obsoletas (criar, editar, ver, lista) |
| [routes/erp/libro-reclamacion.php](routes/erp/libro-reclamacion.php) | **NONE** | Sin cambios necesarios |
| [app/Services/LibroReclamacion/LibroReclamacionNumeroService.php](app/Services/LibroReclamacion/LibroReclamacionNumeroService.php) | **NONE** | Sin cambios; ya es agnóstico |

---

## 5. Checklist de Verificación

### 5.1 Después de Aplicar Migración

- [ ] `php artisan migrate --force` completa sin errores
- [ ] `php artisan migrate:status` muestra migración consolidadora como "applied"
- [ ] `SHOW TABLES;` → Tabla `ticket_libro_reclamacions` NO existe
- [ ] `DESCRIBE libro_reclamacions;` → Contiene todas las 23 nuevas columnas
- [ ] Índices creados correctamente: `codigo`, `estado_legal`, `clasificacion`, `cliente_documento`
- [ ] Si había datos en `ticket_libro_reclamacions`: Datos copiados correctamente a `libro_reclamacions`

### 5.2 Después de Actualizar Código

- [ ] `get_errors()` en 6 componentes → 0 errores
- [ ] `get_errors()` en modelo LibroReclamacion → 0 errores
- [ ] `grep_search` para `TicketLibroReclamacion` → 0 resultados (excepto en archivos eliminados/docs)
- [ ] `php artisan cache:clear && php artisan config:cache` ejecuta sin errores
- [ ] Artisan commands funcionan: `php artisan list` sin warnings

### 5.3 Pruebas Funcionales

- [ ] **Crear reclamación:** Se guarda en `libro_reclamacions` con `codigo`, `estado_legal = NUEVO`, `clasificacion` automática
- [ ] **Editar reclamación:** Se actualiza sin errores, `nota_fuente` congelada (no se modifica)
- [ ] **Listar reclamaciones:** Filtros funcionan (estado_legal, clasificación, gestor)
- [ ] **Búsqueda:** Encuentra por código, documento, nombre, email
- [ ] **Visualizar:** Todos los campos se muestran correctamente
- [ ] **Eliminar lógicamente:** Soft delete funciona, `deleted_at` y `deleted_by` se registran
- [ ] **Relaciones:** Gestor, cliente, creador, actualizador se cargan correctamente

### 5.4 Pruebas de UI/UX

- [ ] **Tabs:** Cambio entre "Información General" y "Cliente" funciona (Alpine.js)
- [ ] **Búsqueda de cliente:** DNI search → Auto-fill de datos
- [ ] **Selección de lotes:** Agregar/quitar lotes funciona
- [ ] **Validación live:** Solo valida campos modelo-bound, no campos transientes (dni, lote_id)
- [ ] **Errors/Feedbacks:** Se muestran correctamente en componentes

---

## 6. Decisiones Documentadas

### ✅ Decisiones Aprobadas

1. **Consolidación única:** Una sola tabla `libro_reclamacions` maneja tanto intake como operaciones legales
2. **Funcionalidad preservada:** Tabs, búsqueda de cliente, selección de lotes, códigos auto-generados siguen igual
3. **Auditoría completa:** Mantener `created_by`, `updated_by`, `deleted_by` para trazabilidad
4. **Soft deletes:** Ambas acciones (delete logic) preservadas con `deleted_at`
5. **Códigos de unidad:** Continuar con auto-generación en `unidad_negocios.codigo` (sin cambios)
6. **Clasificación automática:** Continuar auto-computada, no editable por usuario
7. **Nota congelada:** Bloqueada después de creación, inmutable en edición

### 🔄 Cambios de Arquitectura

**Antes (Dos tablas):**
```
Web Form → libro_reclamacions (intake)
              ↓
ERP Legal → ticket_libro_reclamacions (gestión)
              ↓
              ← Relación: libro_reclamacion_ticket
```

**Después (Una tabla):**
```
Web Form → libro_reclamacions (intake + gestión)
ERP Legal ↓
          (Misma tabla, campos consolidados)
```

**Impacto:**
- ✅ Menos queries/joins
- ✅ Relacionamiento simplificado
- ✅ Migración/actualización única
- ✅ Modelo de datos más limpio

---

## 7. Riesgos y Mitigación

| Riesgo | Probabilidad | Impacto | Mitigación |
|--------|--------------|--------|-----------|
| Pérdida de datos al copiar | Baja | Alto | Backup BD antes de migración, verificar conteos antes/después |
| Conflictos de migraciones | Media | Media | Eliminar old migrations, testing en dev antes de prod |
| Campos no sincronizados | Media | Media | Validación exhaustiva post-migración, logs de copia |
| Queries rotas en componentes | Media | Medio | Tests funcionales e2e, validar cada componente |
| Permisos desalineados | Baja | Bajo | Verificar `modulo-libro-reclamacion.*` covers all routes |
| Relaciones quebradas | Media | Medio | Validar relaciones belongsTo en modelo actualizado |

---

## 8. Cronograma Estimado

| Fase | Tarea | Tiempo | Dependencias |
|------|-------|--------|--------------|
| 1 | Crear migración consolidadora | 1h | Ninguna |
| 1 | Eliminar old migrations del repo | 30min | Crear migración |
| 2 | Actualizar modelo LibroReclamacion | 1.5h | Fase 1 |
| 2 | Eliminar TicketLibroReclamacion.php | 15min | Modelo actualizado |
| 3 | Verificar LibroReclamacionNumeroService | 30min | Paralelo con 2 |
| 4 | Actualizar 4 componentes Livewire | 2h | Fase 2 |
| 5 | Limpiar vistas Blade | 1h | Fase 4 |
| 6 | Verificar rutas/permisos | 30min | Fase 4 |
| 7 | Search & cleanup referencias | 1h | Fase 5 |
| 7 | Actualizar documentación | 1h | Fase 6 |
| - | **Testing & validation** | **2-3h** | Todas |
| - | **Total** | **~12-13h** | - |

---

## 9. Post-Implementación

### 9.1 Rollback Plan
Si algo falla durante migración:

1. **Antes de ejecutar `migrate`:** Hacer backup de BD
2. **Si migration falla:** `php artisan migrate:rollback` (si se agregó down())
3. **Si código falla:** Git revert a commit anterior
4. **Si datos corruptos:** Restaurar desde backup

### 9.2 Monitoreo Post-Deploy
- Revisar logs: `tail -f storage/logs/laravel.log`
- Monitear errores en Sentry/Rollbar
- Verificar que cero broken relationships en ERP
- Checklist de funcionalidad completa (ver 5.3, 5.4)

### 9.3 Comunicación
- Notificar equipo legal sobre consolidación interna
- Confirmar que UX/funcionalidad es idéntica (no cambios visibles)
- Documento de soporte para support team si hay dudas

---

## 10. Referencias

**Documentación Relacionada:**
- [Plan anterior: DECISION_SHEET_TICKET_LIBRO_RECLAMACION_FASE2.md](docs/DECISION_SHEET_TICKET_LIBRO_RECLAMACION_FASE2.md) — Supersedido
- [Implementación técnica anterior](docs/LIBRO_RECLAMACIONES_IMPLEMENTACION_TECNICA.md) — Parcialmente válida

**Cambios en Este Documento vs Anterior:**
1. Consolidación de tablas (nuevo)
2. Eliminación de migraciones previas (nuevo)
3. Modelo único en lugar de dual (cambio arquitectónico)
4. Queries simplificadas (optimization)
5. Documentación centralizada en este .md

---

## 11. Aprobaciones

| Rol | Nombre | Fecha | Firma |
|-----|--------|-------|-------|
| Equipo de Desarrollo | _(Requerido)_ | - | - |
| Product Owner | _(Requerido)_ | - | - |
| DevOps/DBA | _(Requerido)_ | - | - |

---

**Documento generado:** 13 de Abril de 2026  
**Última actualización:** 13 de Abril de 2026  
**Status:** Listo para implementación ✅
