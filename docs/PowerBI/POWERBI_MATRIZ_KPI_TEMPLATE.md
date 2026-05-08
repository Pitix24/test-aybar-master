# Matriz KPI - Plantilla (ERP -> Power BI)

Usa esta plantilla para definir cada KPI y evitar desviaciones entre ERP, SQL y Power BI.

## Instrucciones

1. Completa una fila por KPI.
2. No publiques un dashboard sin esta matriz aprobada.
3. Si cambias una regla de negocio, versiona la matriz.

## Matriz

| Dominio | KPI | Definicion de negocio | Formula funcional | Tabla/vista fuente | Campo fecha de negocio | Filtros obligatorios | Excluye soft delete | SQL referencia | Medida DAX | Visual Power BI | Responsable | Estado |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| Clientes | Total clientes | Cantidad de clientes registrados | Conteo de cliente unico | `vw_pbi_v2_fact_clientes` | `created_at` | Ninguno | Si | `COUNT(DISTINCT id)` | `[Total Clientes]` | Tarjeta | BI | Pendiente |
| Clientes | % activos | Clientes activos sobre total | activos / total | `vw_pbi_v2_fact_clientes` | `created_at` | `usuario_activo = 1` | Si | `SUM(activo)/COUNT(*)` | `[% Activos]` | Tarjeta | BI | Pendiente |
| Backoffice | Total solicitudes | Solicitudes de evidencia registradas | Conteo de solicitudes | `vw_pbi_v2_fact_solicitud_evidencia_pago` | `created_at` | Ninguno | Si | `COUNT(*)` | `[Total Solicitudes]` | Tarjeta | BI | Pendiente |
| Backoffice | Tasa cumplimiento | Validadas sobre total | validadas / total | `vw_pbi_v2_fact_solicitud_evidencia_pago` | `fecha_validacion` | `validacion_fecha_id NOT NULL` | Si | `SUM(validadas)/COUNT(*)` | `[% Cumplimiento]` | Tarjeta | BI | Pendiente |
| Tickets | Total tickets | Tickets creados | Conteo de tickets | `vw_pbi_v2_fact_tickets` | `created_at` | Ninguno | Si | `COUNT(*)` | `[Total Tickets]` | Tarjeta | BI | Pendiente |
| Tickets | Tiempo prom cierre | Promedio horas entre alta y validacion | avg(horas) | `vw_pbi_v2_fact_tickets` | `fecha_validacion` | `cierre_fecha_id NOT NULL` | Si | `AVG(TIMESTAMPDIFF(HOUR,created_at,fecha_validacion))` | `[Promedio Cierre Hrs]` | Tarjeta | BI | Pendiente |
| Citas | Total citas | Citas programadas | Conteo de citas | `vw_pbi_v2_fact_citas` | `fecha_inicio` | Ninguno | Si | `COUNT(*)` | `[Total Citas]` | Tarjeta | BI | Pendiente |
| Citas | Tasa efectividad | Atendidas sobre no canceladas | atendidas / (total - canceladas) | `vw_pbi_v2_fact_citas` | `fecha_inicio` | segun estado | Si | Definir por estado | `[% Efectividad Citas]` | Tarjeta | BI | Pendiente |
| Letras | Total solicitudes letra | Solicitudes de digitalizacion | Conteo | `vw_pbi_v2_fact_solicitud_digitalizar_letra` | `created_at` | Ninguno | Si | `COUNT(*)` | `[Total Letras]` | Tarjeta | BI | Pendiente |
| Letras | Importe total | Suma de importe de cuota | SUM importe | `vw_pbi_v2_fact_solicitud_digitalizar_letra` | `fecha_vencimiento` | Ninguno | Si | `SUM(importe_cuota_decimal)` | `[Importe Total Letras]` | Tarjeta | BI | Pendiente |

## Definiciones clave recomendadas

1. Fecha de negocio:
2. Tickets: `created_at` para ingreso, `fecha_validacion` para cierre.
3. Citas: `fecha_inicio` para programación.
4. Solicitudes de pago: `created_at` para ingreso, `fecha_validacion` para cumplimiento.

1. Regla soft delete:
2. Por defecto excluir registros con `deleted_at` no nulo en KPI operativos.
3. Si un KPI requiere histórico completo, marcarlo explícitamente.

## Control de cambios

| Version | Fecha | Cambio | Responsable |
|---|---|---|---|
| 1.0 | 2026-04-21 | Plantilla inicial | BI |
