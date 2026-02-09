# ✅ Sistema de Permisos - IMPLEMENTADO

## 🎉 Estado: COMPLETADO

El sistema de permisos ha sido actualizado exitosamente con el nuevo formato usando puntos (`.`) en lugar de guiones (`-`).

---

## 📊 Resumen de Implementación

### **Permisos Creados: 101**
- ✅ Atención al Cliente: 44 permisos
- ✅ Backoffice & Pagos: 10 permisos
- ✅ Configuración & Seguridad: 17 permisos
- ✅ Proyectos & Organización: 30 permisos

### **Roles Configurados: 10**
- ✅ super-admin (177 permisos totales)
- ✅ admin (177 permisos totales)
- ✅ supervisor-atc (82 permisos)
- ✅ asesor-atc (8 permisos)
- ✅ supervisor-backoffice (20 permisos)
- ✅ asesor-backoffice (3 permisos)
- ✅ supervisor-legal
- ✅ asesor-legal
- ✅ supervisor-archivo
- ✅ asesor-archivo

---

## 📁 Archivos Modificados

### **1. Rutas**
- ✅ `routes/erp.php` - Actualizado con nuevo formato de permisos
- ✅ `routes/atc.php` - Agregado middleware de permisos

### **2. Componentes Livewire**
- ✅ `app/Livewire/Erp/Admin/AdminLista.php`
- ✅ `app/Livewire/Erp/Admin/AdminCrear.php`
- ✅ `app/Livewire/Erp/Admin/AdminEditar.php`

### **3. Seeders**
- ✅ `database/seeders/RolesYPermisosSeeder.php` - Actualizado y ejecutado

### **4. Documentación**
- ✅ `.agent/permisos-sistema.md` - Documentación completa
- ✅ `.agent/GUIA-IMPLEMENTACION-PERMISOS.md` - Guía de implementación

---

## 🔐 Patrón de Permisos Implementado

### **Formato:**
```
{modulo}.{accion}
```

### **Acciones Estándar:**
- `.ver` - Ver listado y acceder al módulo
- `.crear` - Crear nuevos registros
- `.editar` - Editar registros existentes
- `.eliminar` - Eliminar registros
- `.exportar` - Exportar datos (opcional)
- `.derivar` - Derivar tickets (específico)
- `.reportar` - Generar reportes (específico)
- `.validar` - Validar registros (específico)

### **Ejemplos:**
```
admin.ver
admin.crear
admin.editar
admin.eliminar
admin.exportar

ticket.ver
ticket.crear
ticket.editar
ticket.eliminar
ticket.exportar
ticket.derivar
ticket.reportar
ticket.validar
```

---

## 📋 Lista Completa de Permisos por Módulo

### **Atención al Cliente (44 permisos)**

#### Tipo Solicitud (5)
- tipo-solicitud.ver
- tipo-solicitud.crear
- tipo-solicitud.editar
- tipo-solicitud.eliminar
- tipo-solicitud.exportar

#### Sub Tipo Solicitud (4)
- sub-tipo-solicitud.ver
- sub-tipo-solicitud.crear
- sub-tipo-solicitud.editar
- sub-tipo-solicitud.eliminar

#### Prioridad Ticket (4)
- prioridad-ticket.ver
- prioridad-ticket.crear
- prioridad-ticket.editar
- prioridad-ticket.eliminar

#### Estado Ticket (4)
- estado-ticket.ver
- estado-ticket.crear
- estado-ticket.editar
- estado-ticket.eliminar

#### Canal (4)
- canal.ver
- canal.crear
- canal.editar
- canal.eliminar

#### Ticket (8)
- ticket.ver
- ticket.crear
- ticket.editar
- ticket.eliminar
- ticket.exportar
- ticket.derivar
- ticket.reportar
- ticket.validar

#### Motivo Cita (4)
- motivo-cita.ver
- motivo-cita.crear
- motivo-cita.editar
- motivo-cita.eliminar

#### Estado Cita (4)
- estado-cita.ver
- estado-cita.crear
- estado-cita.editar
- estado-cita.eliminar

#### Cita (6)
- cita.ver
- cita.crear
- cita.editar
- cita.eliminar
- cita.reportar
- cita.validar

#### Calendario (1)
- calendario.ver

---

### **Backoffice & Pagos (10 permisos)**

#### Estado Evidencia Pago (4)
- estado-evidencia-pago.ver
- estado-evidencia-pago.crear
- estado-evidencia-pago.editar
- estado-evidencia-pago.eliminar

#### Evidencia Pago (6)
- evidencia-pago.ver
- evidencia-pago.crear
- evidencia-pago.editar
- evidencia-pago.eliminar
- evidencia-pago.validar
- evidencia-pago.reporte

---

### **Configuración & Seguridad (17 permisos)**

#### Rol (4)
- rol.ver
- rol.crear
- rol.editar
- rol.eliminar

#### Permiso (4)
- permiso.ver
- permiso.crear
- permiso.editar
- permiso.eliminar

#### Admin (5)
- admin.ver
- admin.crear
- admin.editar
- admin.eliminar
- admin.exportar

#### Cliente (5)
- cliente.ver
- cliente.crear
- cliente.editar
- cliente.eliminar
- cliente.exportar

#### Menu (4)
- menu.ver
- menu.crear
- menu.editar
- menu.eliminar

---

### **Proyectos & Organización (30 permisos)**

#### Dirección (4)
- direccion.ver
- direccion.crear
- direccion.editar
- direccion.eliminar

#### Unidad Negocio (4)
- unidad-negocio.ver
- unidad-negocio.crear
- unidad-negocio.editar
- unidad-negocio.eliminar

#### Grupo Proyecto (4)
- grupo-proyecto.ver
- grupo-proyecto.crear
- grupo-proyecto.editar
- grupo-proyecto.eliminar

#### Proyecto (5)
- proyecto.ver
- proyecto.crear
- proyecto.editar
- proyecto.eliminar
- proyecto.exportar

#### Sede (4)
- sede.ver
- sede.crear
- sede.editar
- sede.eliminar

#### Área (4)
- area.ver
- area.crear
- area.editar
- area.eliminar

---

## 🔒 Implementación de Seguridad en Capas

### **Capa 1: Rutas (Middleware)**
Primera línea de defensa - Protege el acceso a las páginas

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

### **Capa 2: Métodos de Acción (Componentes Livewire)**
Segunda línea de defensa - Protege acciones específicas

```php
// En AdminCrear.php
public function store()
{
    abort_unless(auth()->user()->can('admin.crear'), 403);
    
    $this->validate();
    DB::transaction(function () {
        // lógica de creación
    });
}

// En AdminEditar.php
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

// En AdminLista.php
public function exportExcel()
{
    abort_unless(auth()->user()->can('admin.exportar'), 403);
    // lógica...
}
```

### **Capa 3: Vistas Blade**
Tercera línea de defensa - Oculta elementos de UI

```blade
@can('admin.crear')
    <a href="{{ route('erp.admin.vista.crear') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Crear Usuario
    </a>
@endcan

@can('admin.editar')
    <button wire:click="editar({{ $admin->id }})" class="btn btn-sm btn-warning">
        <i class="fas fa-edit"></i> Editar
    </button>
@endcan

@can('admin.eliminar')
    <button wire:click="$dispatch('confirmarEliminar', { id: {{ $admin->id }} })" 
            class="btn btn-sm btn-danger">
        <i class="fas fa-trash"></i> Eliminar
    </button>
@endcan

@can('admin.exportar')
    <button wire:click="exportExcel" class="btn btn-success">
        <i class="fas fa-file-excel"></i> Exportar Excel
    </button>
@endcan
```

---

## 🎯 Próximos Pasos Recomendados

### 1. ✅ Actualizar Componentes Livewire Restantes

Aplica el mismo patrón a los demás módulos que aún no tienen verificaciones:

**Módulos ERP:**
- [ ] Cliente (ClienteCrear, ClienteEditar, ClienteLista)
- [ ] Rol (RolCrear, RolEditar, RolLista)
- [ ] Permiso (PermisoCrear, PermisoEditar, PermisoLista)
- [ ] Proyecto (ProyectoCrear, ProyectoEditar, ProyectoLista)
- [ ] Unidad Negocio
- [ ] Grupo Proyecto
- [ ] Sede
- [ ] Área
- [ ] Dirección
- [ ] Menu

**Módulos ATC:**
- [ ] TipoSolicitud (TipoSolicitudCrear, TipoSolicitudEditar, TipoSolicitudLista)
- [ ] SubTipoSolicitud
- [ ] EstadoTicket
- [ ] PrioridadTicket
- [ ] Canal
- [ ] Ticket

### 2. ✅ Actualizar Vistas Blade

Agrega directivas `@can` en todas las vistas para:
- Botones de "Crear"
- Botones de "Editar"
- Botones de "Eliminar"
- Botones de "Exportar"
- Secciones completas según permisos

### 3. ✅ Actualizar JSON del Menú

Actualiza `erp-menu-principal.json` para incluir permisos:

```json
{
  "nombre": "Usuarios Admin",
  "icono": "users",
  "ruta": "erp.admin.vista.todo",
  "permiso": "admin.ver",
  "children": []
}
```

### 4. ✅ Crear Roles Personalizados

Define roles específicos según tu organización:

```php
// Ejemplo: Gerente de Proyectos
$gerenteProyectos = Role::create(['name' => 'gerente-proyectos']);
$gerenteProyectos->givePermissionTo([
    'proyecto.ver', 'proyecto.crear', 'proyecto.editar',
    'unidad-negocio.ver',
    'grupo-proyecto.ver',
    'cliente.ver',
]);

// Ejemplo: Soporte Técnico
$soporteTecnico = Role::create(['name' => 'soporte-tecnico']);
$soporteTecnico->givePermissionTo([
    'ticket.ver', 'ticket.crear', 'ticket.editar', 'ticket.derivar',
    'cliente.ver',
]);
```

---

## 🔍 Comandos de Verificación

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
$rol = Spatie\Permission\Models\Role::findByName('admin');
$rol->permissions->pluck('name');
```

### Verificar si un usuario tiene un permiso
```php
$user = App\Models\User::find(1);
$user->can('admin.crear'); // true o false
```

### Listar todos los permisos
```php
Spatie\Permission\Models\Permission::all()->pluck('name');
```

---

## 📚 Recursos

- **Documentación completa**: `.agent/permisos-sistema.md`
- **Seeder actualizado**: `database/seeders/RolesYPermisosSeeder.php`
- **Spatie Permission Docs**: https://spatie.be/docs/laravel-permission

---

## ⚠️ Notas Importantes

1. ✅ **Caché limpiada** - Los permisos están activos
2. ✅ **177 permisos totales** en el sistema (101 nuevos + 76 existentes)
3. ✅ **Super Admin y Admin** tienen todos los permisos
4. ⚠️ **Permisos antiguos** con guiones aún existen en la BD (no causan conflicto)
5. 🔄 **Middleware en rutas** es la primera línea de defensa
6. 🔒 **Verificación en métodos** es la segunda línea de defensa
7. 👁️ **Directivas @can** mejoran la UX ocultando elementos sin permiso

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

### Los cambios no se reflejan

```bash
# Limpiar caché de permisos
php artisan cache:clear
```

```php
// En tinker
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

### Verificar que las rutas tienen middleware

```bash
php artisan route:list --path=erp
```

---

## ✅ Checklist de Implementación

- [x] Actualizar formato de permisos (guiones → puntos)
- [x] Actualizar rutas ERP con middleware
- [x] Actualizar rutas ATC con middleware
- [x] Actualizar componentes Admin (Lista, Crear, Editar)
- [x] Ejecutar seeder de permisos
- [x] Limpiar caché
- [x] Verificar roles y permisos
- [ ] Actualizar resto de componentes Livewire
- [ ] Actualizar vistas Blade con @can
- [ ] Actualizar JSON del menú con permisos
- [ ] Crear roles personalizados según necesidad
- [ ] Documentar permisos por rol
- [ ] Capacitar usuarios sobre el sistema de permisos

---

## 🎉 ¡Implementación Exitosa!

El sistema de permisos está funcionando correctamente con el nuevo formato. Todos los usuarios con rol `super-admin` o `admin` tienen acceso completo al sistema.

**Fecha de implementación:** 2026-02-09
**Permisos creados:** 101
**Roles configurados:** 10
**Total de permisos en sistema:** 177
