# Paso 8: Visuales por Página — Guía Rápida para Armar

Para agilizar el proceso, te doy las instrucciones **visual por visual** para cada página. Solo necesitas **arrastrar y soltar** desde el panel de visualizaciones y el panel de datos.

> 💡 **Tip para ir rápido:** En cada visual, te indico exactamente qué campo o medida arrastrar y a dónde.

---

## 📄 Página 1: `Resumen`

Esta es la página ejecutiva con los KPIs principales de todo el sistema.

### Fila 1 — Tarjetas KPI principales (6 tarjetas)

| # | Visual | Medida a arrastrar | Formato |
|---|---|---|---|
| 1 | Tarjeta | `KPI Clientes Total` | Número entero |
| 2 | Tarjeta | `KPI Solicitudes Total` | Número entero |
| 3 | Tarjeta | `KPI Evidencias Total` | Número entero |
| 4 | Tarjeta | `KPI Tickets Total` | Número entero |
| 5 | Tarjeta | `KPI Citas Total` | Número entero |
| 6 | Tarjeta | `KPI Letras Total` | Número entero |

**Cómo crear cada tarjeta:**
1. En el panel de visualizaciones, haz clic en **"Tarjeta"** (ícono de número con recuadro)
2. Arrastra la medida desde `KPI_Medidas` al campo **"Campos"**
3. Ajusta el tamaño y alinéalas horizontalmente

### Fila 2 — Tarjetas de porcentaje (4 tarjetas)

| # | Visual | Medida | Formato |
|---|---|---|---|
| 7 | Tarjeta | `KPI Clientes % Activos` | Porcentaje |
| 8 | Tarjeta | `KPI Solicitudes % Cumplimiento` | Porcentaje |
| 9 | Tarjeta | `KPI Tickets % Cierre` | Porcentaje |
| 10 | Tarjeta | `KPI Citas % Efectividad` | Porcentaje |

### Fila 3 — Gráfico de línea temporal

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de líneas |
| **Eje X** | `aybar vw_pbi_v2_dim_fecha`[fecha] |
| **Valores (líneas)** | `KPI Tickets Total`, `KPI Citas Total`, `KPI Solicitudes Total` |
| **Leyenda** | Automática |

### Fila 4 — Dona resumen general

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de anillo (dona) |
| **Leyenda** | Crear una tabla auxiliar o usar valores fijos |
| **Valores** | `KPI Clientes Activos`, `KPI Clientes Inactivos` |

---

## 📄 Página 2: `Clientes y Direcciones`

### Fila 1 — Tarjetas (6 tarjetas)

| # | Medida | Formato |
|---|---|---|
| 1 | `KPI Clientes Total` | Entero |
| 2 | `KPI Clientes Activos` | Entero |
| 3 | `KPI Clientes Inactivos` | Entero |
| 4 | `KPI Clientes % Activos` | Porcentaje |
| 5 | `KPI Clientes % Email Verificado` | Porcentaje |
| 6 | `KPI Clientes % con Direccion` | Porcentaje |

### Fila 2 — Gráfico de barras: Top Regiones

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de barras horizontales |
| **Eje Y** | `aybar vw_pbi_v2_dim_region`[nombre] |
| **Eje X** | `KPI Direcciones Total` |
| **Filtro visual** | Top 10 por valor |

**Cómo aplicar filtro Top 10:**
1. Haz clic en el visual
2. En el panel de **Filtros**, busca el campo `nombre` (región)
3. Cambia **"Tipo de filtro"** a **"Top N"**
4. Escribe **10** y arrastra `KPI Direcciones Total` como valor
5. Aplica

### Fila 2 — Dona: Activos vs Inactivos

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de anillo (dona) |
| **Leyenda** | `aybar vw_pbi_v2_fact_clientes`[usuario_activo] |
| **Valores** | `KPI Clientes Total` |

### Fila 3 — Gráfico de línea: Registro de clientes en el tiempo

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de líneas |
| **Eje X** | `aybar vw_pbi_v2_dim_fecha`[fecha] |
| **Valores** | `KPI Clientes Total` |

### Fila 3 — Tarjetas de Direcciones

| # | Medida | Formato |
|---|---|---|
| 7 | `KPI Direcciones Total` | Entero |
| 8 | `KPI Direcciones Regiones Cubiertas` | Entero |
| 9 | `KPI Direcciones Distritos Cubiertos` | Entero |

---

## 📄 Página 3: `Backoffice`

### Fila 1 — Tarjetas Solicitudes (6 tarjetas)

| # | Medida | Formato |
|---|---|---|
| 1 | `KPI Solicitudes Total` | Entero |
| 2 | `KPI Solicitudes Validadas` | Entero |
| 3 | `KPI Solicitudes Pendientes` | Entero |
| 4 | `KPI Solicitudes % Cumplimiento` | Porcentaje |
| 5 | `KPI Solicitudes Monto Operacion` | Moneda (S/) |
| 6 | `KPI Solicitudes Total Evidencias Vinculadas` | Entero |

### Fila 2 — Tarjetas Evidencias (4 tarjetas)

| # | Medida | Formato |
|---|---|---|
| 7 | `KPI Evidencias Total` | Entero |
| 8 | `KPI Evidencias Monto Total` | Moneda (S/) |
| 9 | `KPI Evidencias Cierre Automatico` | Entero |
| 10 | `KPI Evidencias % Automatizacion` | Porcentaje |

### Fila 3 — Barras: Solicitudes por estado

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de barras horizontales |
| **Eje Y** | `aybar vw_pbi_v2_dim_estado_solicitud_evidencia_pago`[nombre] |
| **Eje X** | `KPI Solicitudes Total` |

### Fila 3 — Línea: Tendencia de solicitudes

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de líneas |
| **Eje X** | `aybar vw_pbi_v2_dim_fecha`[fecha] |
| **Valores** | `KPI Solicitudes Total`, `KPI Solicitudes Validadas` |

### Fila 4 — Dona: Cierre automático vs manual

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de anillo (dona) |
| **Valores** | `KPI Evidencias Cierre Automatico`, `KPI Evidencias Cierre Manual` |

### Fila 4 — Matriz: Detalle operativo por gestor

| Configuración | Valor |
|---|---|
| **Visual** | Matriz |
| **Filas** | `aybar vw_pbi_v2_fact_solicitud_evidencia_pago`[gestor_id] |
| **Valores** | `KPI Solicitudes Total`, `KPI Solicitudes Validadas`, `KPI Solicitudes % Cumplimiento` |

---

## 📄 Página 4: `Tickets`

### Fila 1 — Tarjetas (6 tarjetas)

| # | Medida | Formato |
|---|---|---|
| 1 | `KPI Tickets Total` | Entero |
| 2 | `KPI Tickets Cerrados` | Entero |
| 3 | `KPI Tickets Abiertos` | Entero |
| 4 | `KPI Tickets % Cierre` | Porcentaje |
| 5 | `KPI Tickets Sin Gestor` | Entero |
| 6 | `KPI Tickets Tiempo Promedio Cierre (Horas)` | Decimal 1 |

### Fila 2 — Barras: Tickets por estado

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de barras horizontales |
| **Eje Y** | `aybar vw_pbi_v2_dim_estado_ticket`[nombre] |
| **Eje X** | `KPI Tickets Total` |

### Fila 2 — Barras: Tickets por prioridad

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de barras horizontales |
| **Eje Y** | `aybar vw_pbi_v2_dim_prioridad_ticket`[nombre] |
| **Eje X** | `KPI Tickets Total` |

### Fila 3 — Línea: Tendencia de tickets

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de líneas |
| **Eje X** | `aybar vw_pbi_v2_dim_fecha`[fecha] |
| **Valores** | `KPI Tickets Total`, `KPI Tickets Cerrados` |

### Fila 3 — Dona: Tickets por canal

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de anillo (dona) |
| **Leyenda** | `aybar vw_pbi_v2_dim_canal`[nombre] |
| **Valores** | `KPI Tickets Total` |

### Fila 4 — Matriz: Detalle por gestor

| Configuración | Valor |
|---|---|
| **Visual** | Matriz |
| **Filas** | `aybar vw_pbi_v2_fact_tickets`[gestor_id] |
| **Valores** | `KPI Tickets Total`, `KPI Tickets Cerrados`, `KPI Tickets % Cierre`, `KPI Tickets Tiempo Promedio Cierre (Horas)` |

---

## 📄 Página 5: `Citas`

### Fila 1 — Tarjetas (5 tarjetas)

| # | Medida | Formato |
|---|---|---|
| 1 | `KPI Citas Total` | Entero |
| 2 | `KPI Citas Atendidas` | Entero |
| 3 | `KPI Citas Canceladas` | Entero |
| 4 | `KPI Citas Pendientes` | Entero |
| 5 | `KPI Citas % Efectividad` | Porcentaje |

### Fila 2 — Barras: Citas por estado

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de barras horizontales |
| **Eje Y** | `aybar vw_pbi_v2_dim_estado_cita`[nombre] |
| **Eje X** | `KPI Citas Total` |

### Fila 2 — Dona: Citas por motivo

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de anillo (dona) |
| **Leyenda** | `aybar vw_pbi_v2_dim_motivo_cita`[nombre] |
| **Valores** | `KPI Citas Total` |

### Fila 3 — Línea: Tendencia de citas

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de líneas |
| **Eje X** | `aybar vw_pbi_v2_dim_fecha`[fecha] |
| **Valores** | `KPI Citas Total`, `KPI Citas Atendidas` |

### Fila 3 — Barras: Citas por sede

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de barras horizontales |
| **Eje Y** | `aybar vw_pbi_v2_dim_sede`[nombre] |
| **Eje X** | `KPI Citas Total` |

---

## 📄 Página 6: `Letras`

### Fila 1 — Tarjetas (5 tarjetas)

| # | Medida | Formato |
|---|---|---|
| 1 | `KPI Letras Total` | Entero |
| 2 | `KPI Letras Aprobadas` | Entero |
| 3 | `KPI Letras Pendientes` | Entero |
| 4 | `KPI Letras % Aprobacion` | Porcentaje |
| 5 | `KPI Letras Importe Total` | Moneda (S/) |

### Fila 2 — Barras: Letras por estado

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de barras horizontales |
| **Eje Y** | `aybar vw_pbi_v2_dim_estado_solicitud_digitalizar_letra`[nombre] |
| **Eje X** | `KPI Letras Total` |

### Fila 2 — Dona: Distribución por estado

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de anillo (dona) |
| **Leyenda** | `aybar vw_pbi_v2_dim_estado_solicitud_digitalizar_letra`[nombre] |
| **Valores** | `KPI Letras Total` |

### Fila 3 — Línea: Tendencia de letras

| Configuración | Valor |
|---|---|
| **Visual** | Gráfico de líneas |
| **Eje X** | `aybar vw_pbi_v2_dim_fecha`[fecha] |
| **Valores** | `KPI Letras Total`, `KPI Letras Aprobadas` |

### Fila 3 — Matriz: Detalle por proyecto/unidad

| Configuración | Valor |
|---|---|
| **Visual** | Matriz |
| **Filas** | `aybar vw_pbi_v2_dim_proyecto`[nombre] |
| **Columnas** | `aybar vw_pbi_v2_dim_estado_solicitud_digitalizar_letra`[nombre] |
| **Valores** | `KPI Letras Total`, `KPI Letras Importe Total` |

---

## 🎨 Filtro global recomendado (todas las páginas)

Agrega un **Segmentador de fecha** en cada página (o en el encabezado):

| Configuración | Valor |
|---|---|
| **Visual** | Segmentador (Slicer) |
| **Campo** | `aybar vw_pbi_v2_dim_fecha`[fecha] |
| **Estilo** | Entre (rango de fechas) |

Esto permitirá filtrar **todas las visualizaciones** de la página por rango de fechas.

---

## Resumen total de visuales

| Página | Tarjetas | Barras | Líneas | Donas | Matrices | Total |
|---|---|---|---|---|---|---|
| Resumen | 10 | 0 | 1 | 1 | 0 | **12** |
| Clientes y Direcciones | 9 | 1 | 1 | 1 | 0 | **12** |
| Backoffice | 10 | 1 | 1 | 1 | 1 | **14** |
| Tickets | 6 | 2 | 1 | 1 | 1 | **11** |
| Citas | 5 | 2 | 1 | 1 | 0 | **9** |
| Letras | 5 | 1 | 1 | 1 | 1 | **9** |
| **Total** | **45** | **7** | **6** | **6** | **3** | **67** |

---

> 💡 **Tip para ir más rápido:** Crea primero **todas las tarjetas** de todas las páginas (son las más rápidas), luego pasa a las barras, después las líneas, luego las donas, y por último las matrices. Así mantienes el mismo flujo de trabajo y no cambias de tipo de visual constantemente.