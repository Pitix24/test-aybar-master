# Sistema de Permisos y Roles

## Patrón de Nomenclatura

Todos los permisos siguen el patrón: `{modulo}.{accion}`

Ejemplo: `admin.ver`, `admin.crear`, `admin.editar`, `admin.eliminar`, `admin.exportar`

---

## Lista Completa de Permisos

### Módulo ERP

#### **Administradores (admin)**
- `admin.ver` - Ver listado de administradores
- `admin.crear` - Crear nuevos administradores
- `admin.editar` - Editar administradores existentes
- `admin.eliminar` - Eliminar administradores
- `admin.exportar` - Exportar datos de administradores

#### **Roles (rol)**
- `rol.ver` - Ver listado de roles
- `rol.crear` - Crear nuevos roles
- `rol.editar` - Editar roles existentes
- `rol.eliminar` - Eliminar roles

#### **Permisos (permiso)**
- `permiso.ver` - Ver listado de permisos
- `permiso.crear` - Crear nuevos permisos
- `permiso.editar` - Editar permisos existentes
- `permiso.eliminar` - Eliminar permisos

#### **Clientes (cliente)**
- `cliente.ver` - Ver listado de clientes
- `cliente.crear` - Crear nuevos clientes
- `cliente.editar` - Editar clientes existentes
- `cliente.eliminar` - Eliminar clientes
- `cliente.exportar` - Exportar datos de clientes

#### **Direcciones (direccion)**
- `direccion.ver` - Ver listado de direcciones
- `direccion.crear` - Crear nuevas direcciones
- `direccion.editar` - Editar direcciones existentes
- `direccion.eliminar` - Eliminar direcciones

#### **Unidades de Negocio (unidad-negocio)**
- `unidad-negocio.ver` - Ver listado de unidades de negocio
- `unidad-negocio.crear` - Crear nuevas unidades de negocio
- `unidad-negocio.editar` - Editar unidades de negocio existentes
- `unidad-negocio.eliminar` - Eliminar unidades de negocio

#### **Grupos de Proyecto (grupo-proyecto)**
- `grupo-proyecto.ver` - Ver listado de grupos de proyecto
- `grupo-proyecto.crear` - Crear nuevos grupos de proyecto
- `grupo-proyecto.editar` - Editar grupos de proyecto existentes
- `grupo-proyecto.eliminar` - Eliminar grupos de proyecto

#### **Proyectos (proyecto)**
- `proyecto.ver` - Ver listado de proyectos
- `proyecto.crear` - Crear nuevos proyectos
- `proyecto.editar` - Editar proyectos existentes
- `proyecto.eliminar` - Eliminar proyectos
- `proyecto.exportar` - Exportar datos de proyectos

#### **Sedes (sede)**
- `sede.ver` - Ver listado de sedes
- `sede.crear` - Crear nuevas sedes
- `sede.editar` - Editar sedes existentes
- `sede.eliminar` - Eliminar sedes

#### **Áreas (area)**
- `area.ver` - Ver listado de áreas
- `area.crear` - Crear nuevas áreas
- `area.editar` - Editar áreas existentes
- `area.eliminar` - Eliminar áreas

#### **Menús (menu)**
- `menu.ver` - Ver listado de menús
- `menu.crear` - Crear nuevos menús
- `menu.editar` - Editar menús existentes
- `menu.eliminar` - Eliminar menús

---

### Módulo ATC (Atención al Cliente)

#### **Tipos de Solicitud (tipo-solicitud)**
- `tipo-solicitud.ver` - Ver listado de tipos de solicitud
- `tipo-solicitud.crear` - Crear nuevos tipos de solicitud
- `tipo-solicitud.editar` - Editar tipos de solicitud existentes
- `tipo-solicitud.eliminar` - Eliminar tipos de solicitud
- `tipo-solicitud.exportar` - Exportar datos de tipos de solicitud

#### **Subtipos de Solicitud (sub-tipo-solicitud)**
- `sub-tipo-solicitud.ver` - Ver listado de subtipos de solicitud
- `sub-tipo-solicitud.crear` - Crear nuevos subtipos de solicitud
- `sub-tipo-solicitud.editar` - Editar subtipos de solicitud existentes
- `sub-tipo-solicitud.eliminar` - Eliminar subtipos de solicitud

#### **Estados de Ticket (estado-ticket)**
- `estado-ticket.ver` - Ver listado de estados de ticket
- `estado-ticket.crear` - Crear nuevos estados de ticket
- `estado-ticket.editar` - Editar estados de ticket existentes
- `estado-ticket.eliminar` - Eliminar estados de ticket

#### **Prioridades de Ticket (prioridad-ticket)**
- `prioridad-ticket.ver` - Ver listado de prioridades de ticket
- `prioridad-ticket.crear` - Crear nuevas prioridades de ticket
- `prioridad-ticket.editar` - Editar prioridades de ticket existentes
- `prioridad-ticket.eliminar` - Eliminar prioridades de ticket

#### **Canales (canal)**
- `canal.ver` - Ver listado de canales
- `canal.crear` - Crear nuevos canales
- `canal.editar` - Editar canales existentes
- `canal.eliminar` - Eliminar canales

#### **Tickets (ticket)**
- `ticket.ver` - Ver listado de tickets
- `ticket.crear` - Crear nuevos tickets
- `ticket.editar` - Editar tickets existentes
- `ticket.eliminar` - Eliminar tickets
- `ticket.exportar` - Exportar datos de tickets

---

## Implementación en Código

### 1. En las Rutas (Middleware)

```php
// Protección a nivel de grupo (ver listado)
Route::group(['middleware' => ['permission:admin.ver']], function () {
    Route::prefix('admin')->name('admin.vista.')->group(function () {
        Route::get('/', AdminLista::class)->name('todo');
        
        // Protección adicional para crear
        Route::get('/crear', AdminCrear::class)
            ->middleware('permission:admin.crear')
            ->name('crear');
        
        // Protección adicional para editar
        Route::get('/editar/{id}', AdminEditar::class)
            ->middleware('permission:admin.editar')
            ->name('editar');
    });
});
```

### 2. En los Componentes Livewire

#### Componente Lista (AdminLista.php)
```php
public function exportExcel()
{
    abort_unless(auth()->user()->can('admin.exportar'), 403);
    
    return Excel::download(new AdminsExport(...), 'usuarios_admin.xlsx');
}
```

#### Componente Crear (AdminCrear.php)
```php
public function store()
{
    abort_unless(auth()->user()->can('admin.crear'), 403);
    
    $this->validate();
    
    DB::transaction(function () {
        // lógica de creación
    });
}
```

#### Componente Editar (AdminEditar.php)
```php
public function mount($id)
{
    // NO necesitas verificar aquí, la ruta ya tiene middleware
    $this->admin = User::findOrFail($id);
}

public function update()
{
    abort_unless(auth()->user()->can('admin.editar'), 403);
    
    $this->validate();
    
    DB::transaction(function () {
        // lógica de actualización
    });
}

public function eliminar()
{
    abort_unless(auth()->user()->can('admin.eliminar'), 403);
    
    DB::transaction(function () {
        $this->admin->delete();
    });
}
```

---

## Crear Permisos en la Base de Datos

### Opción 1: Usando Tinker

```bash
php artisan tinker
```

```php
use Spatie\Permission\Models\Permission;

// Permisos de Admin
Permission::create(['name' => 'admin.ver', 'guard_name' => 'web']);
Permission::create(['name' => 'admin.crear', 'guard_name' => 'web']);
Permission::create(['name' => 'admin.editar', 'guard_name' => 'web']);
Permission::create(['name' => 'admin.eliminar', 'guard_name' => 'web']);
Permission::create(['name' => 'admin.exportar', 'guard_name' => 'web']);

// Repite para cada módulo...
```

### Opción 2: Crear un Seeder

Crea un archivo: `database/seeders/PermisosSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermisosSeeder extends Seeder
{
    public function run()
    {
        $permisos = [
            // Admin
            'admin.ver', 'admin.crear', 'admin.editar', 'admin.eliminar', 'admin.exportar',
            
            // Rol
            'rol.ver', 'rol.crear', 'rol.editar', 'rol.eliminar',
            
            // Permiso
            'permiso.ver', 'permiso.crear', 'permiso.editar', 'permiso.eliminar',
            
            // Cliente
            'cliente.ver', 'cliente.crear', 'cliente.editar', 'cliente.eliminar', 'cliente.exportar',
            
            // Direccion
            'direccion.ver', 'direccion.crear', 'direccion.editar', 'direccion.eliminar',
            
            // Unidad Negocio
            'unidad-negocio.ver', 'unidad-negocio.crear', 'unidad-negocio.editar', 'unidad-negocio.eliminar',
            
            // Grupo Proyecto
            'grupo-proyecto.ver', 'grupo-proyecto.crear', 'grupo-proyecto.editar', 'grupo-proyecto.eliminar',
            
            // Proyecto
            'proyecto.ver', 'proyecto.crear', 'proyecto.editar', 'proyecto.eliminar', 'proyecto.exportar',
            
            // Sede
            'sede.ver', 'sede.crear', 'sede.editar', 'sede.eliminar',
            
            // Area
            'area.ver', 'area.crear', 'area.editar', 'area.eliminar',
            
            // Menu
            'menu.ver', 'menu.crear', 'menu.editar', 'menu.eliminar',
            
            // Tipo Solicitud
            'tipo-solicitud.ver', 'tipo-solicitud.crear', 'tipo-solicitud.editar', 'tipo-solicitud.eliminar', 'tipo-solicitud.exportar',
            
            // Sub Tipo Solicitud
            'sub-tipo-solicitud.ver', 'sub-tipo-solicitud.crear', 'sub-tipo-solicitud.editar', 'sub-tipo-solicitud.eliminar',
            
            // Estado Ticket
            'estado-ticket.ver', 'estado-ticket.crear', 'estado-ticket.editar', 'estado-ticket.eliminar',
            
            // Prioridad Ticket
            'prioridad-ticket.ver', 'prioridad-ticket.crear', 'prioridad-ticket.editar', 'prioridad-ticket.eliminar',
            
            // Canal
            'canal.ver', 'canal.crear', 'canal.editar', 'canal.eliminar',
            
            // Ticket
            'ticket.ver', 'ticket.crear', 'ticket.editar', 'ticket.eliminar', 'ticket.exportar',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name' => $permiso,
                'guard_name' => 'web'
            ]);
        }
    }
}
```

Luego ejecuta:

```bash
php artisan db:seed --class=PermisosSeeder
```

### Opción 3: Asignar todos los permisos a un rol de Super Admin

```php
use Spatie\Permission\Models\Role;

$superAdmin = Role::findByName('Super Admin');
$superAdmin->givePermissionTo(Permission::all());
```

---

## Verificación en Vistas Blade

Puedes ocultar botones o secciones según los permisos:

```blade
@can('admin.crear')
    <a href="{{ route('erp.admin.vista.crear') }}" class="btn btn-primary">
        Crear Usuario
    </a>
@endcan

@can('admin.editar')
    <button wire:click="editar({{ $admin->id }})">Editar</button>
@endcan

@can('admin.eliminar')
    <button wire:click="eliminar({{ $admin->id }})">Eliminar</button>
@endcan

@can('admin.exportar')
    <button wire:click="exportExcel">Exportar Excel</button>
@endcan
```

---

## Resumen de Cuándo Verificar Permisos

| Ubicación | Cuándo verificar | Ejemplo |
|-----------|------------------|---------|
| **Rutas** | Siempre (middleware) | `->middleware('permission:admin.ver')` |
| **mount()** | Solo si necesitas lógica especial | Generalmente NO |
| **Métodos de acción** | Siempre en acciones críticas | `store()`, `update()`, `eliminar()`, `exportar()` |
| **Vistas Blade** | Para mostrar/ocultar elementos UI | `@can('admin.crear')` |

---

## Total de Permisos

- **ERP**: 50 permisos
- **ATC**: 26 permisos
- **TOTAL**: 76 permisos
