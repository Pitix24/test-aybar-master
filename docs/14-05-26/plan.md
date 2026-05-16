## Plan: Reglamento por Proyecto en Marketing

Agregar un nuevo recurso Reglamento en Marketing con patrón equivalente a Tutorial/AvanceProyecto, pero reemplazando video por archivo PDF (hasta 50 MB) y aplicando visibilidad en Portal Cliente según la unión de proyectos asociados al cliente autenticado vía lotes de SLIN.

**Steps**
1. Fase 1 - Dominio y persistencia
2. Crear migración `reglamentos` con campos: `proyecto_id` (FK requerida), `titulo`, `descripcion` (nullable), `clicks` (default 0), `activo` (default true), `orden` (default 0), soft deletes y timestamps. *Bloquea fases 2 y 3.*
3. Crear modelo `App\Models\Reglamento` con `SoftDeletes`, `fillable`, `casts` y relaciones: `proyecto()` (`belongsTo`) y `archivoPdf()`/`archivos()` (`morphOne`/`morphMany` hacia `MarketingArchivo`). *Depende de 2.*
4. Reutilizar `marketing_archivos` para almacenar metadatos del PDF, usando `archivable_type = Reglamento::class`, ruta sugerida `marketing/reglamentos`. *Depende de 3.*
5. Fase 2 - ERP Marketing (CRUD completo)
6. Crear componentes Livewire ERP en `App\Livewire\Erp\Marketing\Reglamento`: `ReglamentoLista`, `ReglamentoCrear`, `ReglamentoEditar`, `ReglamentoVer` con atributos `#[Lazy]`, `#[Layout(...)]`, `#[Title(...)]` y patrón de alertas/transacciones/logs usado en Tutorial/AvanceProyecto. *Depende de 3-4.*
7. En crear/editar validar archivo: `required|file|mimes:pdf|max:51200` (50 MB), eliminar archivo anterior al reemplazarlo, y mantener `clicks` fuera de formularios ERP. *Depende de 6.*
8. Implementar eliminación en editar con evento `#[On('eliminarReglamentoOn')]`, borrando archivo físico (`Storage::disk('public')->delete(...)`), registro en `marketing_archivos` y soft delete del reglamento. *Depende de 6.*
9. Crear vistas Blade ERP: lista/crear/editar/ver en `resources/views/livewire/erp/marketing/reglamento/` replicando UX existente (filtros, paginación, estado activo, acciones, overlay loading). En lista incluir filtro por proyecto y estado; en crear/editar selector de proyecto obligatorio. *Depende de 6-8.*
10. Registrar rutas ERP en `routes/erp/marketing.php` bajo `permission:modulo-marketing.ver`, prefijo `reglamento`, names `erp.reglamento.vista.{todo,ver,crear,editar}` y middleware por permiso de acción, consistente con Tutorial. *Depende de 6.*
11. Fase 3 - Portal Cliente (lectura por pertenencia)
12. Crear `App\Http\Controllers\Cliente\ReglamentoController@index` y ruta `/cliente/reglamento` en `routes/cliente.php` que retorne vista módulo cliente. *Depende de 3.*
13. Crear componente `App\Livewire\Cliente\Reglamento\ReglamentoTodo` y vista `resources/views/livewire/cliente/reglamento/reglamento-todo.blade.php` para listar reglamentos activos donde `proyecto_id` pertenezca al conjunto de proyectos del cliente autenticado. *Depende de 12.*
14. Resolver pertenencia de proyectos del cliente usando el patrón de `LoteTodo`: obtener DNI de `Auth::user()->perfilCliente`, consultar SLIN para lotes y derivar `id_proyecto` únicos; mapear esos IDs a `proyectos.id` local para `whereIn` en reglamentos. Si no hay pertenencia válida, devolver lista vacía. *Depende de 13.*
15. En la vista cliente abrir PDF en modal o pestaña nueva con URL pública del archivo y registrar click al visualizar (`increment('clicks')`) con misma excepción de impersonación usada en Tutorial/AvanceProyecto cliente. *Depende de 13.*
16. Fase 4 - Seguridad, permisos y menú
17. Agregar permisos en `RolesYPermisosSeeder`: `reglamento.navegacion`, `reglamento.lista`, `reglamento.ver`, `reglamento.crear`, `reglamento.editar`, `reglamento.eliminar` (y opcionales de export si se decide después). *Depende de 10.*
18. Incorporar ítem de menú ERP en `public/erp-menu-principal.json` para Reglamento, con submenús Lista/Crear y permisos correspondientes. *Depende de 17.*
19. Definir canal de log `reglamento` en `config/logging.php` (si se mantiene convención por módulo) o reutilizar `marketing` explícitamente, evitando inconsistencias. *Paralelo con 17-18.*
20. Fase 5 - Verificación
21. Ejecutar migraciones y seeders relacionados, verificar acceso por permisos (rol sin permisos, con permisos, super-admin) y navegación ERP completa.
22. Probar carga de PDF válido hasta 50 MB, rechazo de no-PDF, reemplazo de archivo en edición y eliminación completa (físico + metadata + soft delete).
23. Validar Portal Cliente con casos: cliente con 1 proyecto, con múltiples proyectos, sin proyectos; asegurar que solo vea reglamentos de sus proyectos y que los clicks incrementen al abrir.
24. Ejecutar pruebas automatizadas mínimas (Feature/Livewire) para filtros ERP, validación de archivo, autorización y visibilidad por pertenencia en cliente.

**Relevant files**
- `c:/laragon/www/aybar/database/migrations/*_create_reglamentos_table.php` — nueva tabla de reglamentos con FK a proyectos.
- `c:/laragon/www/aybar/app/Models/Reglamento.php` — modelo principal + relaciones a `Proyecto` y `MarketingArchivo`.
- `c:/laragon/www/aybar/app/Models/MarketingArchivo.php` — se reutiliza sin cambios estructurales (polimorfismo).
- `c:/laragon/www/aybar/app/Livewire/Erp/Marketing/Reglamento/ReglamentoLista.php` — filtros, paginación, toggle activo, autorizaciones.
- `c:/laragon/www/aybar/app/Livewire/Erp/Marketing/Reglamento/ReglamentoCrear.php` — alta y carga de PDF.
- `c:/laragon/www/aybar/app/Livewire/Erp/Marketing/Reglamento/ReglamentoEditar.php` — actualización, reemplazo y eliminación.
- `c:/laragon/www/aybar/app/Livewire/Erp/Marketing/Reglamento/ReglamentoVer.php` — detalle.
- `c:/laragon/www/aybar/resources/views/livewire/erp/marketing/reglamento/*.blade.php` — vistas ERP.
- `c:/laragon/www/aybar/routes/erp/marketing.php` — rutas y middleware de Reglamento ERP.
- `c:/laragon/www/aybar/app/Http/Controllers/Cliente/ReglamentoController.php` — entrada web cliente.
- `c:/laragon/www/aybar/resources/views/modules/cliente/reglamento.blade.php` — contenedor módulo cliente.
- `c:/laragon/www/aybar/app/Livewire/Cliente/Reglamento/ReglamentoTodo.php` — query por pertenencia a proyectos del cliente.
- `c:/laragon/www/aybar/resources/views/livewire/cliente/reglamento/reglamento-todo.blade.php` — listado y apertura de PDF.
- `c:/laragon/www/aybar/routes/cliente.php` — nueva ruta `/cliente/reglamento`.
- `c:/laragon/www/aybar/database/seeders/RolesYPermisosSeeder.php` — permisos de Reglamento.
- `c:/laragon/www/aybar/public/erp-menu-principal.json` — entrada de menú ERP.
- `c:/laragon/www/aybar/config/logging.php` — canal de log para trazabilidad.
- `c:/laragon/www/aybar/app/Livewire/Cliente/Lote/LoteTodo.php` — referencia para resolver proyectos del cliente por DNI/SLIN.

**Verification**
1. `php artisan migrate` y validación de esquema `reglamentos`.
2. `php artisan db:seed --class=RolesYPermisosSeeder` y validación de permisos creados.
3. Pruebas manuales ERP: crear/editar/ver/eliminar reglamento; toggle activo; filtros por proyecto.
4. Pruebas manuales Cliente: confirmar visibilidad solo por proyectos del cliente autenticado; abrir PDF y validar contador de clicks.
5. Verificar que impersonación no incremente clicks (paridad con patrón actual).
6. Ejecutar pruebas de regresión objetivo (Feature/Livewire) sobre autorización y filtros.

**Decisions**
- Incluye CRUD completo en ERP para Reglamento.
- Archivo permitido: solo PDF.
- Tamaño máximo por archivo: 50 MB (`max:51200`).
- Visibilidad cliente: unión de reglamentos de todos los proyectos a los que pertenece el cliente.
- Excluye en esta iteración: exportación Excel para Reglamento (se puede agregar luego si se requiere).

**Further Considerations**
1. Recomiendo priorizar vista cliente en pestaña nueva para PDFs pesados (mejor rendimiento/memoria) y dejar modal como mejora opcional.
2. Si SLIN entrega IDs de proyecto con formato distinto al local, se necesitará un mapeo explícito (tabla puente o normalización) antes del filtro `whereIn`.
3. Vale la pena corregir en paralelo la validación de pertenencia en otros flujos cliente detectados (cronograma/digitalizar) para homogeneidad de seguridad.