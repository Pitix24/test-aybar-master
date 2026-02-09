# Guía de Implementación - Sistema de Permisos

## ✅ Cambios Realizados

### 1. Rutas Actualizadas

#### **erp.php**
- ✅ Cambiado formato de permisos: `rol-ver` → `rol.ver`
- ✅ Agregado middleware de permisos a todos los grupos
- ✅ Agregado middleware específico para rutas de crear/editar
- ✅ Agregado protección a módulo `direccion` (antes no tenía)
- ✅ Agregado protección a módulo `menu` (antes no tenía)

#### **atc.php**
- ✅ Agregado middleware de permisos a todos los módulos
- ✅ Formato de permisos: `tipo-solicitud.ver`, `tipo-solicitud.crear`, etc.

### 2. Componentes Livewire Actualizados

#### **AdminLista.php**
- ✅ Agregada verificación `admin.exportar` en método `exportExcel()`

#### **AdminCrear.php**
- ✅ Agregada verificación `admin.crear` en método `store()`

#### **AdminEditar.php**
- ✅ Agregada verificación `admin.editar` en método `update()`
- ✅ Agregada verificación `admin.editar` en método `updatePassword()`
- ✅ Agregada verificación `admin.eliminar` en método `eliminarAdminOn()`

### 3. Archivos Creados

- ✅ `.agent/permisos-sistema.md` - Documentación completa de permisos
- ✅ `database/seeders/PermisosSeeder.php` - Seeder para crear todos los permisos
- ✅ `database/seeders/MigrarPermisosSeeder.php` - Seeder para migrar permisos antiguos

---

## 📋 Pasos a Seguir

### Paso 1: Ejecutar el Seeder de Permisos

```bash
php artisan db:seed --class=PermisosSeeder
```

Esto creará **76 permisos** en total:
- 50 permisos del módulo ERP
- 26 permisos del módulo ATC

### Paso 2 (Opcional): Migrar Permisos Antiguos

Si ya tienes permisos con el formato antiguo (con guiones), ejecuta:

```bash
php artisan db:seed --class=MigrarPermisosSeeder
```

Esto:
- Creará los nuevos permisos con puntos
- Copiará las asignaciones de roles y usuarios
- Mantendrá los permisos antiguos (por seguridad)

### Paso 3: Asignar Permisos a Roles

#### Opción A: Asignar todos los permisos a Super Admin

```bash
php artisan tinker
```

```php
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

$superAdmin = Role::where('name', 'Super Admin')->first();
if ($superAdmin) {
    $superAdmin->syncPermissions(Permission::all());
    echo "✓ Todos los permisos asignados a Super Admin\n";
}
```

#### Opción B: Asignar permisos específicos por rol

```php
$rolGerente = Role::where('name', 'Gerente')->first();
if ($rolGerente) {
    $rolGerente->givePermissionTo([
        'admin.ver',
        'cliente.ver', 'cliente.crear', 'cliente.editar',
        'proyecto.ver', 'proyecto.crear', 'proyecto.editar',
        'ticket.ver', 'ticket.crear', 'ticket.editar',
    ]);
}
```

### Paso 4: Limpiar Caché de Permisos

```bash
php artisan cache:clear
php artisan config:clear
```

### Paso 5: Verificar en la Aplicación

1. Inicia sesión con un usuario que tenga permisos
2. Intenta acceder a las diferentes rutas
3. Verifica que los botones de exportar, eliminar, etc. funcionen correctamente

---

## 🔍 Verificación de Permisos

### Verificar permisos de un usuario

```bash
php artisan tinker
```

```php
$user = App\Models\User::find(1);
$user->getAllPermissions()->pluck('name');
```

### Verificar permisos de un rol

```php
$rol = Spatie\Permission\Models\Role::where('name', 'Super Admin')->first();
$rol->permissions->pluck('name');
```

---

## 📝 Patrón de Implementación

### En Rutas (Primera línea de defensa)

```php
Route::group(['middleware' => ['permission:admin.ver']], function () {
    Route::prefix('admin')->name('admin.vista.')->group(function () {
        Route::get('/', AdminLista::class)->name('todo');
        Route::get('/crear', AdminCrear::class)
            ->middleware('permission:admin.crear')
            ->name('crear');
        Route::get('/editar/{id}', AdminEditar::class)
            ->middleware('permission:admin.editar')
            ->name('editar');
    });
});
```

### En Componentes Livewire (Segunda línea de defensa)

```php
// En métodos de acción críticos
public function store()
{
    abort_unless(auth()->user()->can('admin.crear'), 403);
    // lógica...
}

public function update()
{
    abort_unless(auth()->user()->can('admin.editar'), 403);
    // lógica...
}

public function eliminar()
{
    abort_unless(auth()->user()->can('admin.eliminar'), 403);
    // lógica...
}

public function exportExcel()
{
    abort_unless(auth()->user()->can('admin.exportar'), 403);
    // lógica...
}
```

### En Vistas Blade (Ocultar elementos UI)

```blade
@can('admin.crear')
    <a href="{{ route('erp.admin.vista.crear') }}" class="btn btn-primary">
        Crear Usuario
    </a>
@endcan

@can('admin.eliminar')
    <button wire:click="$dispatch('confirmarEliminar')">
        Eliminar
    </button>
@endcan

@can('admin.exportar')
    <button wire:click="exportExcel">
        Exportar Excel
    </button>
@endcan
```

---

## 🎯 Próximos Pasos Recomendados

### 1. Actualizar otros componentes Livewire

Aplica el mismo patrón a los demás módulos:
- Cliente (ClienteCrear, ClienteEditar, ClienteLista)
- Proyecto (ProyectoCrear, ProyectoEditar, ProyectoLista)
- Ticket (TicketCrear, TicketEditar, TicketLista)
- etc.

### 2. Actualizar vistas Blade

Agrega directivas `@can` para ocultar botones según permisos:
- Botones de "Crear"
- Botones de "Editar"
- Botones de "Eliminar"
- Botones de "Exportar"

### 3. Crear roles predefinidos

Crea roles con permisos específicos:
- **Super Admin**: Todos los permisos
- **Gerente**: Ver, crear, editar (sin eliminar)
- **Operador**: Solo ver y crear
- **Soporte**: Solo módulos de tickets

### 4. Documentar permisos en el menú

Actualiza el JSON del menú para incluir los permisos necesarios:

```json
{
  "nombre": "Usuarios Admin",
  "icono": "users",
  "ruta": "erp.admin.vista.todo",
  "permiso": "admin.ver"
}
```

---

## 📚 Recursos

- **Documentación completa**: `.agent/permisos-sistema.md`
- **Seeder de permisos**: `database/seeders/PermisosSeeder.php`
- **Migración de permisos**: `database/seeders/MigrarPermisosSeeder.php`
- **Spatie Permission**: https://spatie.be/docs/laravel-permission

---

## ⚠️ Notas Importantes

1. **No elimines permisos antiguos** hasta verificar que todo funciona correctamente
2. **Limpia la caché** después de crear o modificar permisos
3. **Prueba con diferentes roles** para asegurar que los permisos funcionan
4. **Documenta los permisos** de cada rol para futura referencia
5. **Usa middleware en rutas** como primera línea de defensa
6. **Verifica en métodos** como segunda línea de defensa para acciones críticas

---

## 🐛 Troubleshooting

### Error: "User does not have the right permissions"

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear

# Verificar permisos del usuario
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $user->getAllPermissions()->pluck('name');
```

### Error: "Permission does not exist"

```bash
# Ejecutar seeder de permisos
php artisan db:seed --class=PermisosSeeder
```

### Los cambios no se reflejan

```bash
# Limpiar caché de permisos
php artisan cache:clear
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```
