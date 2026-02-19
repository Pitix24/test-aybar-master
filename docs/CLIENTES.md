# 👥 Gestión de Clientes y Fuentes de Datos

El sistema maneja la información de los clientes integrando tres fuentes de datos distintas. El nexo de unión entre todas ellas es el **DNI** del cliente.

## 🔄 Arquitectura de Fuentes de Datos

### 1. 🌐 API Externa (Aybar Slin)
Es la fuente de datos externa que provee información en tiempo real desde el servicio Slin.
- **Servicio:** `App\Services\AybarSlinService`
- **Método:** `getCliente(string $dni)`
- **Endpoint:** `GET {baseUrl}/cliente/{dni}`
- **Uso:** Se utiliza para validar y traer datos maestros del cliente que no residen localmente o que requieren actualización constante desde la fuente principal.

### 2. 🗄️ Tabla Legada / Temporal (`clientes_2`)
Una tabla utilizada para consultas masivas o datos históricos que aún se mantienen en una estructura separada.
- **Acceso:** `DB::table('clientes_2')`
- **Identificador:** Columna `dni`
- **Uso:** Consultas de migración o referencias cruzadas con datos antiguos.

### 3. 🏁 Base de Datos Local ERP (`clientes`)
Es la fuente de verdad para el funcionamiento interno del ERP Aybar. Aquí se gestiona la relación directa con el usuario y otros módulos.
- **Migración:** `database/migrations/2026_02_03_195433_create_clientes_table.php`
- **Modelo:** `App\Models\Cliente`
- **Identificador:** Columna `dni`
- **Uso:** Gestión de sesiones en el portal, asignación de lotes, tickets y trazabilidad interna.

---

## 🛠️ Lógica de Integración y Consulta

El sistema centraliza la lógica de búsqueda en un servicio especializado que decide el orden de prioridad y la combinación de estas fuentes:

- **Servicio Principal de Búsqueda:** `App\Services\ConsultaClienteService`
- **Flujo de Decisión:**
    1. Primero intenta buscar en la tabla local legada `clientes_2`.
    2. Si no hay resultados, consulta el portal externo a través de la API SLIN.
    3. Construye un objeto estandarizado de respuesta independientemente del origen.

### Pasos Recomendados para Desarrollo:

1. **Local:** Buscar en la tabla `clientes` para obtener la relación con el usuario y datos operativos.
2. **Histórico:** Consultar `clientes_2` si se requiere información de campañas o registros pasados.
3. **API:** Consultar `AybarSlinService` para obtener el estado actual o datos maestros actualizados.

> **Nota:** Al realizar búsquedas o filtros, siempre se debe priorizar el uso del **DNI** como índice para garantizar la integridad entre las tres capas.
