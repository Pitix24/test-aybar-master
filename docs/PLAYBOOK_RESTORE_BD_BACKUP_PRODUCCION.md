# Playbook: Restauración de Backup BD y Recuperación de Acceso ERP

## Propósito
Documento de procedimiento no destructivo para recuperar un backup antiguo de la base de datos, aplicar las migraciones pendientes, y restaurar permisos/roles de usuario manteniendo integridad de datos existentes.

**Aplicable a:** Restauración desde backup pre-Libro Reclamaciones o cualquier versión anterior que requiera recomposición de permisos.

---

## Prerrequisitos
- Acceso SSH o terminal del servidor.
- Backup `.sql` o punto de restauración disponible.
- Laravel en estado inicial (`php artisan` funciona).
- Usuario de BD con permisos suficientes para restaurar (ALTER, GRANT, etc.).

---

## Procedimiento

### 1. Respaldar Configuración Actual
Antes de cualquier cambio, guardar estado de `.env` actual por si es necesario rollback:

```bash
cp .env .env.backup-$(date +%Y%m%d_%H%M%S)
```

### 2. Apuntar `.env` a la Base de Datos Destino (Backup)
Editar `.env` y cambiar credenciales BD si el backup está en una instancia diferente:

```bash
DB_HOST=<host-del-backup>
DB_PORT=3306
DB_DATABASE=<nombre-db-backup>
DB_USERNAME=<usuario-backup>
DB_PASSWORD=<contraseña-backup>
```

Verificar que Laravel vea la BD:

```bash
php artisan tinker --execute "echo 'DB conectada: ' . DB::connection()->getDatabaseName() . PHP_EOL;"
```

**Esperado:** `DB conectada: nombre-db-backup`

### 3. Restaurar Backup (si no ya existe o está corrompido)
Si el backup NO está ya restaurado en la BD destino, restaurarlo ahora:

```bash
# Opción A: Desde archivo SQL (MySQL)
mysql -h <host> -u <usuario> -p<contraseña> <db> < /path/to/backup.sql

# Opción B: Desde punto de restauración (si es RDS/Cloud)
# Consultar documentación del proveedor de BD
```

Validar el restore:

```bash
mysql -h <host> -u <usuario> -p<contraseña> <db> -e "SELECT COUNT(*) AS total_tables FROM information_schema.tables WHERE table_schema = '<db>';"
```

**Esperado:** Número de tablas >= 50

### 4. Ejecutar Migraciones Pendientes
Aplicar SOLO las migraciones faltantes (no destructivas):

```bash
php artisan migrate
```

**Output esperado:**
```
...
Migrating: 2026_04_18_210100_make_libro_reclamacion_numero_unique_per_unidad.php
Migrated:  2026_04_18_210100_make_libro_reclamacion_numero_unique_per_unidad.php (XX ms)
```

**⚠️ NOTA:** Usar `migrate:fresh` SÓ si es desarrollo/staging. En producción usar sólo `migrate`.

### 5. Recrear Permisos y Roles
La restauración del backup recupera la estructura de BD, pero migraciones no crean permisos. Necesario ejecutar seeder:

```bash
php artisan db:seed --class=RolesYPermisosSeeder --force
```

**Output esperado:**
```
Creando permisos y roles...
✓ Creado: modulo-libro-reclamacion.ver
✓ Creado: ticket-libro-reclamacion.navegacion
✓ Creado: ticket-libro-reclamacion.lista
...
✓ Super Admin: 384 permisos
✓ Admin: 384 permisos
...
========================================
✓ Roles y permisos creados exitosamente
========================================
```

### 6. Limpiar Cache de Permisos
Spatie Permission cachea permisos; forzar refresh:

```bash
php artisan permission:cache-reset
```

**Output esperado:**
```
Permission cache flushed.
```

### 7. Limpiar Cachés Generales de Laravel
Asegurar que todas las cachés reflejen el estado nuevo:

```bash
php artisan optimize:clear
```

**Output esperado:**
```
INFO  Clearing cached bootstrap files.  

  config ........................................................ XX.XXms DONE
  cache ......................................................... XX.XXms DONE
  compiled ....................................................... X.XXms DONE
  events ......................................................... X.XXms DONE
  routes ......................................................... X.XXms DONE
  views ......................................................... XX.XXms DONE
```

### 7.5. Recrear Menús del ERP desde JSON
Al restaurar backups antiguos, la tabla `menus` puede quedar desactualizada y no mostrar nuevas opciones (por ejemplo, Libro Reclamaciones).

```bash
php artisan db:seed --class=MenuSeeder --force
```

**Output esperado:**
```
INFO  Seeding database.
```

### 7.6. Limpiar Caché Final
Después de recrear menús, limpiar caché nuevamente:

```bash
php artisan optimize:clear
```

### 8. Validar Permisos en BD (Opcional pero Recomendado)
Para confirmar que super-admin y otros roles tengan los permisos críticos:

```bash
php artisan tinker
```

Dentro de tinker, ejecutar:

```php
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Confirmar permisos clave existen
echo "modulo-libro-reclamacion.ver: " . Permission::where('name', 'modulo-libro-reclamacion.ver')->count() . PHP_EOL;
echo "ticket-libro-reclamacion.navegacion: " . Permission::where('name', 'ticket-libro-reclamacion.navegacion')->count() . PHP_EOL;
echo "ticket-libro-reclamacion.lista: " . Permission::where('name', 'ticket-libro-reclamacion.lista')->count() . PHP_EOL;

// Confirmar super-admin tiene permisos
$superAdmin = Role::where('name', 'super-admin')->first();
echo "Rol super-admin existe: " . ($superAdmin ? "Sí" : "No") . PHP_EOL;
echo "Permisos asignados: " . ($superAdmin ? $superAdmin->permissions()->count() : 0) . PHP_EOL;
echo "Tiene modulo-libro-reclamacion.ver: " . ($superAdmin && $superAdmin->hasPermissionTo('modulo-libro-reclamacion.ver') ? "Sí" : "No") . PHP_EOL;

exit
```

**Esperado:**
```
modulo-libro-reclamacion.ver: 1
ticket-libro-reclamacion.navegacion: 1
ticket-libro-reclamacion.lista: 1
Rol super-admin existe: Sí
Permisos asignados: 384
Tiene modulo-libro-reclamacion.ver: Sí
```

---

## Validación Post-Restauración

### A. Acceso ERP Libro Reclamaciones (Manual en Navegador)
1. Abrir sesión en el ERP con cuenta **superadmin**.
2. Navegar a: `https://<app>/erp/libro-reclamacion`
3. **Esperado:** Carga la página sin error 403 y muestra tabla (aunque esté vacía si no hay datos).

### B. Limpiar Filtros (si la tabla se muestra vacía)
Si la tabla carga pero no hay registros:
1. Buscar botón **"Limpiar"** en sección de filtros.
2. Hacer clic.
3. **Esperado:** Resetea los parámetros de URL y vuelve a cargar la lista.

### C. Validar Específicamente cada Usuario (SI NECESARIO)
Si un usuario diferente a superadmin requiere acceso:

```bash
php artisan tinker
```

```php
use App\Models\User;
use Spatie\Permission\Models\Role;

// Buscar usuario
$user = User::where('email', 'usuario@empresa.com')->first();

// Ver roles del usuario
echo "Roles: " . $user?->getRoleNames()->join(', ') . PHP_EOL;

// Ver permisos directos (si no vienen de rol)
echo "Permisos directos: " . $user?->getDirectPermissions()->count() . PHP_EOL;

// Verificar permiso crítico
echo "Tiene 'ticket-libro-reclamacion.lista': " . ($user && $user->hasPermissionTo('ticket-libro-reclamacion.lista') ? "Sí" : "No") . PHP_EOL;

exit
```

---

## Troubleshooting

### Error: "Unauthorized" / 403 al acceder a ERP
**Causa probable:** Permisos no reasignados.
**Solución:**
```bash
php artisan db:seed --class=RolesYPermisosSeeder --force
php artisan permission:cache-reset
php artisan optimize:clear
```
Cerrar sesión e iniciar de nuevo.

### Error: "Column not found" después de `php artisan migrate`
**Causa probable:** Migraciones pendientes en el código pero no ejecutadas.
**Solución:**
```bash
php artisan migrate
```
Reejecutar. Si persiste, revisar si hay migrations custom en `database/migrations/`.

### Error: "Unknown column 'numero_reclamo'" al registrar Reclamo Web
**Causa probable:** Dump restaurado con versión antigua de `libro_reclamacions` (estructura desalineada con el código actual).

**Solución recomendada (no destructiva):**
1. Verificar columnas críticas:
```bash
php artisan tinker --execute "echo 'numero_reclamo='.(\Illuminate\Support\Facades\Schema::hasColumn('libro_reclamacions','numero_reclamo')?1:0).PHP_EOL; echo 'cliente_nombre='.(\Illuminate\Support\Facades\Schema::hasColumn('libro_reclamacions','cliente_nombre')?1:0).PHP_EOL;"
```
2. Ejecutar migración de alineación de esquema (si existe en el proyecto):
```bash
php artisan migrate --force
```
3. Limpiar cachés y reintentar:
```bash
php artisan permission:cache-reset
php artisan optimize:clear
```

**Nota:** Este caso suele aparecer cuando el dump trae la tabla `libro_reclamacions` sin columnas nuevas como `numero_reclamo`, `clasificacion` o `cliente_*`.

### Error: "Base table or view already exists" durante `php artisan migrate`
**Causa probable:** Desalineación entre la tabla `migrations` y el esquema real del backup. Ejemplo: la tabla `libro_reclamacions` ya existe físicamente, pero su migración no está registrada como ejecutada.

**Solución recomendada (no destructiva):**
1. Confirmar si la tabla existe:
```bash
php artisan tinker --execute "echo 'existe_libro=' . (DB::selectOne(\"SELECT COUNT(*) AS c FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'libro_reclamacions'\")->c > 0 ? 1 : 0) . PHP_EOL;"
```
2. Registrar la migración faltante sin recrear tabla:
```bash
php artisan tinker --execute "DB::table('migrations')->where('migration','2026_02_16_211526_create_libro_reclamacions_table')->delete(); DB::table('migrations')->insert(['migration' => '2026_02_16_211526_create_libro_reclamacions_table', 'batch' => DB::table('migrations')->max('batch') + 1]); echo 'ok' . PHP_EOL;"
```
3. Reintentar:
```bash
php artisan migrate
```

**Nota:** No usar `migrate:fresh` en producción para este caso, porque elimina tablas y datos.

### Error: "Command is not defined" en tinker
**Causa probable:** Namespace incorrecto o tinker no cargó autoload.
**Solución:**
```bash
php artisan clear-compiled
composer dump-autoload
php artisan tinker
```

### Tabla Libro Reclamaciones vacía pero otros módulos cargan
**Causa probable:** Datos del backup no incluyen registros de Libro Reclamaciones, o filtros persistidos en URL.
**Solución:**
1. Hacer clic en "Limpiar" filtros.
2. Verificar en tinker:
```php
use App\Models\LibroReclamacion\LibroReclamacion;
echo "Total registros: " . LibroReclamacion::count() . PHP_EOL;
```

### Menú ERP no muestra Libro Reclamaciones
**Causa probable:** La tabla `menus` restaurada desde backup antiguo no incluye el nodo de Libro Reclamaciones.
**Solución:**
```bash
php artisan db:seed --class=MenuSeeder --force
php artisan optimize:clear
```
Cerrar sesión e iniciar nuevamente.

---

## Resumen de Comandos (Copiar/Pegar)

Para ejecución rápida:

```bash
# 1. Respaldar ENV
cp .env .env.backup-$(date +%Y%m%d_%H%M%S)

# 2. Verificar conexión BD (reemplazar <db>)
php artisan tinker --execute "echo 'DB: ' . DB::connection()->getDatabaseName() . PHP_EOL;"

# 3. Ejecutar migraciones
php artisan migrate

# 4. Recrear permisos
php artisan db:seed --class=RolesYPermisosSeeder --force

# 5. Limpiar cachés
php artisan permission:cache-reset
php artisan optimize:clear

# 6. Recrear menú ERP
php artisan db:seed --class=MenuSeeder --force
php artisan optimize:clear

# 7. Validar (opcional)
php artisan tinker
# Dentro: Ver sección "Validar Permisos en BD" arriba
```

---

## Checklist Pre-Producción

- [ ] Backup de BD actual resguardado.
- [ ] Backup destino restaurado y verificado (tablas count >= 50).
- [ ] `.env` apunta a la BD correcta.
- [ ] `php artisan migrate` ejecutado sin errores.
- [ ] `php artisan db:seed --class=RolesYPermisosSeeder --force` ejecutado.
- [ ] `php artisan db:seed --class=MenuSeeder --force` ejecutado.
- [ ] Cachés limpiados (permission + optimize:clear, incluyendo post-menú).
- [ ] Acceso ERP Libro Reclamaciones probado sin error 403.
- [ ] Al menos un usuario superadmin validó acceso a la lista.
- [ ] Registros de Libro Reclamaciones están visibles (si existen en backup).

---

## Referencias Codebase

| Componente | Archivo | Líneas |
|-----------|---------|--------|
| Rutas ERP Libro Reclamaciones | `routes/erp/libro-reclamacion.php` | - |
| Componente Listado | `app/Livewire/Erp/LibroReclamacion/LibroReclamacionLista.php` | ~58 (authorize) |
| Seeder Permisos/Roles | `database/seeders/RolesYPermisosSeeder.php` | ~517-520 (super-admin) |
| Seeder Menú ERP | `database/seeders/MenuSeeder.php` | carga desde JSON |
| Migraciones Libro | `database/migrations/2026_02_16_211526_create_libro_reclamacions_table.php` | - |
| Configuración Permisos | `config/permission.php` | - |

---

## Notas Finales

- Este playbook es **no destructivo**: no usa `migrate:fresh` ni `migrate:refresh`.
- Es seguro repetir pasos 5-7 múltiples veces sin riesgo de pérdida de datos.
- Si necesita rollback completo, restaurar `.env.backup-*` anterior y revertir la BD.
- En caso de duda, ejecutar primero en **staging** antes de producción.

---

**Documento válido desde:** 2026-04-20
**Autor:** Bot Asistente
**Versión:** 1.0

