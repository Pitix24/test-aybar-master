# Cambios planificados — Jerarquía de Roles (Modelo Simplificado)

**Fecha:** 04-06-2026
**Autor:** Matías Lázaro
**Estado:** 🔄 Plan simplificado — reemplaza la versión sobre-ingenierizada basada en `user_supervisors`.

#### Contexto del cambio

La implementación anterior con tablas extra, pivots de vínculos y reglas de módulos resultó demasiado compleja para el objetivo real. El nuevo enfoque será el estándar y más mantenible: **cada rol puede tener un único superior directo**, definido con un campo autoreferencial en `roles`.

La jerarquía queda desacoplada de la lógica de permisos y módulos. La jerarquía organiza roles; los permisos siguen siendo responsabilidad del sistema de permisos.

#### Resumen

**Objetivos principales**

- Simplificar la jerarquía a un solo nivel de relación: `roles.upper_id`.
- Eliminar pivots de jerarquía y la lógica de módulos comunes.
- Permitir elegir el rol superior al crear o editar un rol.
- Filtrar el selector de superior por `area_id` para facilitar la asignación.
- Evitar incongruencias: un rol no debe poder elegir como superior a un rol que esté por debajo de su propia cadena.

---

#### Modelo de datos propuesto

**Tabla `roles` — nuevas columnas**

| Columna    | Tipo                      | Notas                                                   |
| ---------- | ------------------------- | ------------------------------------------------------- |
| `area_id`  | `unsignedBigInteger NULL` | FK al área asociada al rol                              |
| `upper_id` | `unsignedBigInteger NULL` | FK autoreferencial a `roles.id`, `onDelete('set null')` |

- `NULL` en `upper_id` significa **rol raíz**.
- `area_id` permite agrupar y filtrar roles al crear o editar la jerarquía.
- Debe validarse que no existan **ciclos** en la cadena jerárquica.
- Conviene indexar `area_id` y `upper_id`.

---

#### Archivos a modificar / crear

- **Migraciones**
    - `database/migrations/2026_06_04_010000_add_area_id_and_upper_id_to_roles.php` — nueva: agrega `area_id` y `upper_id` a `roles`.
    - _(Opcional, fase posterior)_ migraciones para retirar `user_supervisors` y `user_supervisor_modules` una vez la transición esté validada.

- **Modelo**
    - `app/Models/Rol.php`
        - Relación `superior()` → `belongsTo(Rol::class, 'upper_id')`.
        - Relación `subordinados()` → `hasMany(Rol::class, 'upper_id')`.
        - Helper para recorrer la cadena ascendente.
        - Helper para construir el árbol descendente.
        - Validación anti-ciclos en eventos del modelo o en una capa de servicio.

- **Componente Livewire**
    - `app/Livewire/Erp/Sistema/Rol/RolJerarquia.php`
        - Simplificar el componente para trabajar sólo con la estructura `area_id` + `upper_id`.
        - Eliminar todo lo relacionado con `selectedModules`, pivots de módulos, vínculos rol-rol y validaciones por módulo.
        - Exponer filtros por área para el selector de superior.
        - Cargar la lista de roles disponibles excluyendo descendientes del rol actual.
        - Acciones básicas: asignar superior, quitar superior, renderizar árbol.

- **Vistas**
    - `resources/views/livewire/erp/sistema/rol/rol-jerarquia.blade.php`
        - Rediseño en dos paneles:
            - **Panel izquierdo**: roles agrupados por área, mostrando usuarios asignados al rol.
            - **Panel derecho**: árbol de jerarquía simple desde `upper_id`.
        - En crear/editar rol, selector de superior filtrado por área.
        - Evitar mostrar roles que generen ciclos como opciones válidas.

- **Servicios**
    - `app/Services/JerarquiaService.php`
        - `obtenerArbol()` para construir la jerarquía completa.
        - `obtenerCadenaAscendente(Rol $rol)` para listar superiores.
        - `validarSinCiclos(int $rolId, ?int $upperId)` para evitar asignaciones inválidas.

---

#### Estrategia de transición

1. Agregar `area_id` y `upper_id` a `roles`.
2. Mantener temporalmente las tablas antiguas para lectura o migración de datos.
3. Mapear la jerarquía actual desde la implementación previa hacia `upper_id`.
4. Validar manualmente los casos ambiguos.
5. Deprecar la solución vieja sólo cuando la nueva estructura esté estable.

---

#### Lógica funcional esperada

- Al crear o editar un rol, el usuario podrá escoger un **rol superior directo**.
- El selector de superior deberá poder filtrarse por `area_id` para que el proceso sea más simple.
- No deben mostrarse como superiores los roles que estén por debajo del rol en edición, para evitar ciclos.
- Si un rol no tiene superior, será un **rol raíz**.

---

#### Comandos propuestos

```bash
# Aplicar la migración propuesta
php artisan migrate --path=database/migrations/2026_06_04_010000_add_area_id_and_upper_id_to_roles.php
```

---

#### Cómo probar (pasos rápidos)

1. Aplicar la migración.
2. Abrir el módulo de roles y verificar que el formulario permita asignar `area_id` y `upper_id`.
3. Filtrar superiores por área.
4. Intentar elegir como superior un rol descendiente del actual: debe bloquearse.
5. Confirmar que el árbol se construye sólo desde `upper_id`.

---

#### Ventajas frente al diseño anterior

| Aspecto             | Diseño anterior                                | Diseño nuevo                         |
| ------------------- | ---------------------------------------------- | ------------------------------------ |
| Tablas de jerarquía | `user_supervisors` + `user_supervisor_modules` | Sólo `roles`                         |
| Reglas              | Duplicados, módulos comunes, pivots            | Sólo jerarquía directa y anti-ciclos |
| UX                  | Compleja y difícil de mantener                 | Simple y predecible                  |
| Consultas           | Joins múltiples                                | Relación autoreferencial directa     |

---

#### Limitaciones conocidas

- Un rol sólo puede tener **un superior directo**.
- La jerarquía no debe mezclar reglas de permiso; esa responsabilidad permanece en el sistema de permisos.

---

#### Próximos pasos

1. Crear migración para `area_id` y `upper_id`.
2. Refactorizar `Rol` y `RolJerarquia`.
3. Simplificar la vista Blade.
4. Refactorizar `JerarquiaService`.
5. Escribir tests de anti-ciclos y árbol.

---

#### Nota final

Este documento reemplaza el enfoque anterior de jerarquía por pivots. La meta ahora es una jerarquía RBAC simple, clara y fácil de mantener.
