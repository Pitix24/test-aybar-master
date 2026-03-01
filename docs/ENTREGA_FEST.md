# SISTEMA WEB - ENTREGAFEST

Resumen del proyecto para la gestión del evento **Entregafest**.

## 1. Objetivo
Desarrollar un aplicativo web responsive que diferencie entre usuarios externos (clientes) e internos (staff), asegurando que la información operativa, legal y de proveedores sea privada.

## 2. Roles y Permisos (Spatie)

Para este sistema, utilizaremos los roles y permisos gestionados por **Spatie** (que ya están integrados en las rutas del ERP) para asegurar una operación fluida y segura.

### Roles Principales
1. **ADMIN_EVENTO**: Superusuario del evento con gestión total sobre configuración, itinerario, proveedores y usuarios.
2. **STAFF_OPERATIVO**: Responsable de la ejecución en campo (Check-in, seguimiento del itinerario, atención de incidencias).
3. **STAFF_LECTURA**: Acceso para perfiles comerciales o legales que necesitan monitorear el avance sin editar datos operativos.
4. **PROVEEDOR**: (Opcional) Acceso restringido para visualizar sus propios horarios y requerimientos.

### Matriz de Permisos (Slugs sugeridos)

| Categoría | Permiso Spatie (Slug) | ADMIN | OPERATIVO | LECTURA |
| :--- | :--- | :---: | :---: | :---: |
| **General** | `modulo-entrega-fest.ver` | ✅ | ✅ | ✅ |
| | `entrega-fest.navegacion` | ✅ | ✅ | ✅ |
| **Gestión** | `entrega-fest.lista` / `.ver` | ✅ | ✅ | ✅ |
| | `entrega-fest.crear` / `.editar` | ✅ | ❌ | ❌ |
| **Invitados** | `entrega-fest.prospectos` / `.invitados` | ✅ | ✅ | ✅ |
| | `entrega-fest.asistencia` (Check-in) | ✅ | ✅ | ❌ |
| **Itinerario** | `entrega-fest.itinerario.ver` | ✅ | ✅ | ✅ |
| | `entrega-fest.itinerario.ejecutar` | ✅ | ✅ | ❌ |
| | `entrega-fest.itinerario.admin` | ✅ | ❌ | ❌ |
| **MOP** | `entrega-fest.mop.ver` | ✅ | ✅ | ❌ |
| | `entrega-fest.mop.admin` | ✅ | ❌ | ❌ |
| **Proveedores**| `entrega-fest.proveedores.ver` | ✅ | ✅ | ✅ |
| | `entrega-fest.proveedores.admin` | ✅ | ❌ | ❌ |
| **Incidencias**| `entrega-fest.incidencias.reportar` | ✅ | ✅ | ✅ |
| | `entrega-fest.incidencias.gestionar` | ✅ | ✅ | ❌ |
| **Recursos** | `entrega-fest.recursos.ver` | ✅ | ✅ | ✅ |
| | `entrega-fest.recursos.admin` | ✅ | ❌ | ❌ |


## 3. Módulos Principales

### A. Módulo Externo (Clientes)
- **Validación:** Ingreso mediante DNI o código de cliente.
- **Registro:** Formulario de asistencia (asiste/no asiste), acompañantes, transporte y observaciones.
- **Confirmación:** Mensaje de éxito tras el registro.

### B. Módulo Interno (Staff)
- **Dashboard Operativo:** Resumen en vivo de aforo, transporte, bloques de itinerario, llamados de proveedores e incidencias.
- **Gestión de Asistencia:** Listado filtrable y exportable de invitados.
- **Check-in (Día D):** Registro rápido de ingreso por DNI/Nombre.
- **Itinerario (Run of Show):** Control de tiempos y checklist de actividades por bloque.
- **Mi MOP:** Tareas personalizadas por rol y fase (Antes/Durante/Cierre).
- **Proveedores:** Control de requerimientos, horarios de montaje y call sheet.
- **Incidencias:** Registro y seguimiento de problemas por tipo y prioridad.
- **Contenido Auxiliar:** Mapas, textos de protocolo y planes de contingencia.

## 4. Estructura de Datos (Entidades MVP)
- `Event`: Configuración del evento y links públicos.
- `AttendanceRegistration`: Datos del cliente, estado de asistencia y check-in.
- `User`: Usuarios del staff con roles definidos.
- `RunOfShowBlock`: Bloques horarios con estados y checklists.
- `MOPUserTask`: Tareas específicas asignadas a usuarios.
- `Provider`: Información de servicios, horarios y requerimientos.
- `Incident`: Reportes de logística, seguridad o proveedores.

## 5. Reglas de Negocio y UX
- **Velocidad:** El check-in debe ser de un solo clic.
- **Mobile First:** Uso prioritario desde dispositivos móviles para el staff.
- **Integridad:** Bloqueo de duplicados en check-in y restricción de edición post-envío para clientes.
- **Exportación:** Generación de reportes en Excel para análisis post-evento.

## 6. Stack Tecnológico Recomendado
- **Frontend:** React / Next.js (PWA recomendada).
- **Backend:** Node.js (Express/Nest), Laravel o Django.
- **Base de Datos:** PostgreSQL.
- **Autenticación:** JWT con control de acceso basado en roles (RBAC).


## SEGUNDA PARTE: MÓDULO STAFF (OPERACIONES)

Este bloque detalla la infraestructura necesaria para la gestión interna del evento, asegurando que el staff tenga herramientas en tiempo real para el control operativo.

### 1. Plan de Tablas y Estructura Técnica

#### A. Itinerario (Run of Show) - El "Corazón" del Evento
Controla la línea de tiempo del evento.
- **`entrega_fest_itinerario_bloques`**
    - `entrega_fest_id` (FK): Vinculación al evento.
    - `hora_inicio` / `hora_fin`: Ventana de tiempo.
    - `titulo` / `descripcion` / `ubicacion`: Datos del bloque.
    - `responsable_rol`: Rol encargado de este bloque (ej. "Protocolo").
    - `estado`: `PENDIENTE`, `EN_CURSO`, `COMPLETADO`.
- **`entrega_fest_itinerario_checklists`**
    - `itinerario_bloque_id` (FK).
    - `tarea`: Descripción corta (ej. "Encender proyector").
    - `esta_listo`: Boolean.

#### B. MOP (Manual de Operaciones por Rol) - Tareas Personales
Personalización de acciones según el usuario logueado.
- **`entrega_fest_mop_plantillas`**
    - `rol_nombre`: Basado en los permisos de Spatie (ej. `staff-operativo`).
    - `fase`: `ANTES`, `DURANTE`, `CIERRE`.
    - `instruccion`: Texto de la tarea.
- **`entrega_fest_mop_tareas`**
    - `user_id` (FK): Staff asignado.
    - `entrega_fest_id` (FK).
    - `titulo` / `fase` / `instruccion`: Copiados de la plantilla al crear el evento.
    - `esta_completado`: Boolean + `completado_at`.

#### C. Proveedores y Requerimientos
Gestión de terceros y logística técnica.
- **`entrega_fest_proveedores`**
    - `nombre_comercial` / `contacto` / `servicio_tipo`.
    - `h_llegada` / `h_montaje` / `h_show` / `h_desmontaje`.
    - `estado`: `CONFIRMADO`, `EN_SITIO`, `COMPLETADO`.
- **`entrega_fest_proveedor_requerimientos`**
    - `proveedor_id` (FK).
    - `requerimiento`: Texto (ej. "Toma de corriente 220v").
    - `esta_cubierto`: Boolean.

#### D. Canal de Incidencias
Reporteo rápido de fallas o emergencias.
- **`entrega_fest_incidencias`**
    - `tipo`: `Logística`, `Seguridad`, `Salud`, `Técnico`.
    - `prioridad`: `Baja`, `Media`, `Alta`.
    - `descripcion` / `ubicacion`.
    - `informante_user_id` / `responsable_user_id` (FK).
    - `estado`: `Abierta`, `En Proceso`, `Resuelta`.

#### E. Recursos de Apoyo e Imágenes (Spatie Media Library)
En lugar de crear tablas de archivos por cada módulo, utilizaremos **Spatie Media Library** para centralizar todos los archivos del evento (Planos, Fotos de Incidencias, Manuales).
- **`media`** (Tabla única centralizada):
    - `model_type` / `model_id`: Relación polimórfica (ej. `Incidencia`, `Recurso`).
    - `collection_name`: Categoría del archivo (`fotos_incidencia`, `planos_evento`, `manuales_staff`).
    - `file_name` / `mime_type` / `size`: Metadatos automáticos.
    - `custom_properties`: Metadatos extra (ej. `titulo`, `descripcion`).

- **`entrega_fest_recursos`** (Solo metadatos de acceso):
    - `entrega_fest_id` (FK).
    - `nombre_publico`: Título para el Staff.
    - `tipo_recurso`: `MAPA`, `MANUAL`, `FOTO`.

- **`entrega_fest_protocolos`**: ID, evento_id, titulo, contenido (Guiones, mensajes).
- **`entrega_fest_contingencias`**: ID, evento_id, escenario, accion (¿Qué hacer si...?).

---

### 2. Orden de Ejecución Sugerido

Para optimizar el desarrollo en los 20 días propuestos, seguiremos este orden lógico:

| Fase | Tarea | Descripción |
| :--- | :--- | :--- |
| **01** | **Infraestructura de Soporte** | Crear tablas de Recursos, Protocolos y Contingencias. Son las más simples y permiten al Staff tener información vital de inmediato. |
| **02** | **Core de Tiempo (Itinerario)** | Implementar el Run of Show. Es la vista principal que usará el Staff para marcar el avance del evento. |
| **03** | **Logística Externa (Proveedores)** | Tablas de proveedores. Permite coordinar montajes antes de que lleguen los invitados. |
| **04** | **Tareas Individuales (MOP)** | Lógica para asignar tareas por rol. Esto personaliza la experiencia del staff. |
| **05** | **Control de Crisis (Incidencias)** | Canal de reportes. Se deja al final por ser un sistema de monitoreo reactivo. |
| **06** | **Dashboard Staff** | Integración de todas las tablas en una vista unificada "Live" para el Admin. |

---

### 3. Consideraciones Técnicas
- **Gestión de Archivos (Spatie Media Library):**
    - Se instalará el paquete `spatie/laravel-medialibrary`.
    - Se usará **una sola tabla `media`** para todo el sistema (estilo WordPress Media Library).
    - Permite generar miniaturas (thumbnails) automáticas para las fotos de incidencias, ahorrando ancho de banda al Staff.
- **Migraciones:** Usar prefijo `entrega_fest_` para evitar colisiones con otras tablas del ERP.
- **Roles:** Integrar con el sistema de roles actual del ERP (Spatie) para filtrar el MOP y el Itinerario.
- **Real-time:** Para el Dashboard, considerar un polling simple cada 30s o el uso de Livewire para actualizaciones sin recargar.
