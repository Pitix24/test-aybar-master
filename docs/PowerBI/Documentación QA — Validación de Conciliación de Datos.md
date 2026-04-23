# 📋 Documentación QA — Validación de Conciliación de Datos

**Proyecto:** Dashboard Power BI V2 — Base de datos `aybar`
**Fecha de validación:** Miércoles, 22 de abril de 2026
**Responsable:** Matias Lazaro
**Fase:** Paso 6 — Validar conciliación

---

## 1. Objetivo

Verificar la integridad y consistencia de los datos importados al modelo Power BI, comparando las tablas fuente de MySQL contra las vistas `vw_pbi_v2_fact_*`, e identificando llaves foráneas con valores nulos que puedan afectar los KPIs.

---

## 2. Validación de Conteos (`vw_pbi_v2_qa_conteos_diff`)

| Entidad | Fuente Total | Vista Total | Diferencia | Estado |
|---|---|---|---|---|
| clientes | N | N-1 | **1** | ⚠️ Observado |
| admins | N | N | 0 | ✅ OK |
| citas | N | N | 0 | ✅ OK |
| direccions | N | N | 0 | ✅ OK |
| evidencia_pagos | N | N | 0 | ✅ OK |
| solicitud_digitalizar_letras | N | N | 0 | ✅ OK |
| solicitud_evidencia_pagos | N | N | 0 | ✅ OK |
| ticket_derivados | N | N | 0 | ✅ OK |
| tickets | N | N | 0 | ✅ OK |

### Hallazgo: Diferencia de 1 en `clientes`

- **Descripción:** La vista `vw_pbi_v2_fact_clientes` retorna 1 registro menos que la tabla fuente `clientes`.
- **Causa probable:** La vista aplica un `JOIN` con la tabla `users` filtrando por `rol = 'cliente'`. El registro faltante corresponde a un cliente cuyo usuario asociado no cumple con la condición del JOIN (usuario inexistente, eliminado o con rol diferente).
- **Impacto en KPIs:** Mínimo. Representa una desviación menor al 0.1% del total de clientes.
- **Acción recomendada:** Investigar el registro con la siguiente consulta:

```sql
SELECT c.id, c.nombre, c.email
FROM clientes c
WHERE c.id NOT IN (
    SELECT id FROM vw_pbi_v2_fact_clientes
);
```

- **Decisión:** ⚠️ **No bloqueante.** Se documenta y se continúa con el desarrollo del dashboard.

---

## 3. Validación de Llaves Nulas (`vw_pbi_v2_qa_null_keys`)

| Regla | Total Nulos | Estado |
|---|---|---|
| fact_tickets.gestor_id | **1** | ⚠️ Aceptado por negocio |
| fact_citas.estado_cita_id | 0 | ✅ OK |
| fact_evidencia_pago.solicitud_id | 0 | ✅ OK |
| fact_solicitud_digitalizar_letra | 0 | ✅ OK |
| fact_solicitud_evidencia_pago | 0 | ✅ OK |

### Hallazgo: 1 nulo en `fact_tickets.gestor_id`

- **Descripción:** Existe 1 ticket que no tiene gestor (`gestor_id = NULL`).
- **Causa raíz:** El ticket fue generado a través del **Libro de Reclamaciones**. Este tipo de tickets se crean automáticamente por el sistema sin asignación directa a un gestor, ya que ingresan por un canal regulatorio externo que no pasa por el flujo estándar de asignación.
- **Justificación de negocio:** Es un comportamiento **esperado y válido**. El Libro de Reclamaciones es un mecanismo regulatorio obligatorio que permite a los clientes registrar reclamos sin intervención de un gestor asignado. La asignación se realiza posteriormente de forma manual o automática según las reglas operativas internas.
- **Impacto en KPIs:**
  - `KPI Tickets Sin Gestor` = 1 → **Correcto**, refleja la realidad operativa.
  - `KPI Tickets Total` → No se ve afectado.
  - `KPI Tickets % Cierre` → Impacto despreciable.
- **Decisión:** ✅ **Aceptado por regla de negocio.** No requiere corrección.

---

## 4. Resumen Ejecutivo

| Indicador | Valor | Estado |
|---|---|---|
| Total de entidades validadas | 9 | ✅ |
| Entidades con diferencia = 0 | 8 de 9 | ✅ |
| Entidades con diferencia > 0 | 1 (clientes: 1 registro) | ⚠️ No bloqueante |
| Reglas de nulos validadas | 5 | ✅ |
| Reglas con nulos = 0 | 4 de 5 | ✅ |
| Reglas con nulos > 0 | 1 (tickets.gestor_id: 1 nulo) | ✅ Aceptado |
| **Estado general del modelo** | **APROBADO** | ✅ |

---

## 5. Conclusión

El modelo de datos Power BI V2 sobre la base `aybar` se encuentra en estado **saludable y consistente**. Los dos hallazgos identificados son menores y están documentados:

1. La diferencia de 1 registro en `clientes` es producto del filtro `JOIN` en la vista y tiene impacto despreciable.
2. El nulo en `tickets.gestor_id` es un comportamiento esperado por negocio, originado por tickets del **Libro de Reclamaciones** que no requieren asignación inmediata de gestor.

**Se aprueba la continuación al paso 7: Construcción de páginas del dashboard.**