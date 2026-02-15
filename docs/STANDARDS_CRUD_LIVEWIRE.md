# Estándar de Desarrollo para Módulos CRUD (Livewire + ERP)

Este documento define la estructura, lógica y componentes obligatorios para todos los módulos del sistema Aybar ERP, con el fin de mantener coherencia visual, técnica y de seguridad en todo el proyecto.

## 1. Estructura de Archivos y Nomenclatura
Cada módulo debe estar organizado en su propio directorio dentro de `app/Livewire/Erp/` y sus vistas en `resources/views/livewire/erp/`.

### Componentes Livewire (`app/Livewire/...`)
- `NombreLista.php`: Vista de tabla con filtros, búsqueda y paginación.
- `NombreCrear.php`: Formulario para el registro de nuevos datos.
- `NombreEditar.php`: Formulario para modificar datos existentes y acción de eliminar.
- `NombreVer.php`: Pantalla de detalle con todos los campos en modo de solo lectura.

### Vistas Blade (`resources/views/livewire/...`)
- Se deben usar rutas de archivos en minúsculas separadas por guiones (Kebab-case).
- Ej: `unidad-negocio-lista.blade.php`.

## 2. Configuración de Rutas y Permisos
Las rutas deben definirse en archivos específicos por módulo dentro de `routes/erp/`.

### Convención de Nombres de Rutas
- `erp.recurso.vista.todo` (Lista)
- `erp.recurso.vista.ver` (Consulta)
- `erp.recurso.vista.crear` (Creación)
- `erp.recurso.vista.editar` (Edición)

### Bloque de Comentarios de Permisos
Al final de cada archivo de rutas, se debe incluir el inventario de permisos bajo la convención `recurso.accion`:

```php
/*
ROL
1. rol.navegacion (Permite ver el ítem en el menú)
2. rol.lista (Acceso a la tabla de datos)
3. rol.ver (Acceso al detalle individual)
4. rol.crear
5. rol.editar
6. rol.eliminar
7. rol.exportar-filtro
8. rol.exportar-todo
*/
```

## 3. Estándar de Componentes (PHP)

### Directivas de Clase
```php
#[Lazy] // Carga diferida
#[Layout('layouts.erp.layout-erp')] // Layout obligatorio
#[Title('Título de la Página')] // Título de la pestaña
```

### Gestión de Filtros (Lista)
- Los filtros deben estar vinculados a la URL usando `#[Url]`.
- Propiedades estándar: `$buscar`, `$activo`, `$desde`, `$hasta`, `$perPage`.
- Resetear página al actualizar filtros en el método `updated($property)`.

### Autorización y Validación
- Usar `$this->authorize('recurso.accion')` en cada método de acción.
- Implementar `validationAttributes()` para mensajes de error amigables.
- Usar `DB::beginTransaction()` y `commit()` en operaciones que alteren la base de datos.

### Feedback y Registro (Logging)
- **Alertas**: Dispatcher `alertaLivewire` con `type`, `title` y `text`.
- **Logs**: Usar canales específicos (ej. `negocio`, `admins`, `usuarios`) definidos en `config/logging.php`.
- Prefijo obligatorio en logs: `[MODULO] Acción: mensaje`.

## 4. Estándar de Vistas (Blade)

### Layout General
- **Lista**: Ancho completo con filtros en la parte superior.
- **Crear**: Columna central de 8 (`g_columna_8`).
- **Editar**: Estructura 8/4 (`g_columna_8` para datos, `g_columna_4` para panel de **Auditoría**).
- **Ver**: Estructura central de 8 (`g_columna_8`), solo lectura y **sin panel de auditoría**.

### Componentes de UI
- **Loading Overlay**: `<x-loading-overlay wire:loading wire:target="metodo" message="Procesando..." />`.
- **Switches**: Uso del componente `g_switch` para estados activos/inactivos.
- **Botones**: 
    - `g_boton guardar` (Verde con icono `fa-save`)
    - `g_boton cancelar` (Utilizando `onclick="history.back()"` o redirección a la lista)
    - `g_boton dark` (Para botones de retroceso)
    - En la tabla: `g_accion ver`, `g_accion editar`, `g_accion eliminar`.

### Pestañas (Tabs)
Para formularios complejos, se debe usar Alpine.js con la siguiente estructura:
```html
<div x-data="{ activeTab: 'general' }">
    <div class="g_tab_navegacion">
        <div class="g_tab_botones">
            <button @click="activeTab = 'general'" :class="activeTab === 'general' ? 'g_tab_active' : 'g_tab_inactive'">...</button>
        </div>
    </div>
    <div x-show="activeTab === 'general'" class="g_tab_content">...</div>
</div>
```

## 5. Exportación a Excel
Implementación obligatoria de **Dual Export** en el componente de lista:
1. `exportExcelFiltro`: Exporta lo que el usuario está viendo actualmente (respeta filtros y búsqueda).
2. `exportExcelTodo`: Exporta la base de datos completa (solo respeta filtro de fechas).
- Las clases de exportación deben estar en `App\Exports\Modulo\ClaseExport.php`.

---
*Cualquier código que no cumpla con estos estándares será rechazado en la revisión técnica.*
