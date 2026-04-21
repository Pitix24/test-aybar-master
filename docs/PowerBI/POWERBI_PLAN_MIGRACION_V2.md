# Plan Maestro BI V2 (ERP -> Power BI)

Objetivo: recrear y migrar el modelo analítico de forma controlada, tomando como fuente oficial el esquema actual del ERP (migraciones) y dejando una base estable para crecimiento.

## 1. Alcance V1

Dominios incluidos:

1. Usuario (clientes, admins, direcciones)
2. Backoffice (solicitudes y evidencias de pago)
3. ATC (tickets y derivaciones)
4. Citas
5. Letras

Entregables de alcance:

1. Modelo estrella final
2. Vistas SQL V2 (`vw_pbi_v2_*`)
3. Dataset Power BI con relaciones y medidas base
4. Validación de KPIs vs ERP

## 2. Fases de ejecución

## Fase A. Descubrimiento y diseño (2-3 días)

1. Consolidar diccionario de datos desde migraciones
2. Confirmar grano de cada tabla de hechos
3. Confirmar fecha de negocio por hecho (`created_at`, `fecha_inicio`, `enviado_at`, etc.)
4. Congelar catálogo de KPIs con negocio

Salida de fase:

1. Matriz KPI aprobada
2. Modelo lógico aprobado

## Fase B. Capa SQL analítica V2 (2-4 días)

1. Crear vistas `vw_pbi_v2_dim_*`
2. Crear vistas `vw_pbi_v2_fact_*`
3. Crear vistas QA `vw_pbi_v2_qa_*` para conciliación
4. Probar conteos y llaves

Salida de fase:

1. Script ejecutable en ambiente DEV
2. Checklist de validación técnica

## Fase C. Dataset y modelo Power BI (2-3 días)

1. Importar vistas V2
2. Definir relaciones (`Many-to-one`, filtro simple)
3. Crear medidas DAX base
4. Configurar incremental refresh (si aplica)

Salida de fase:

1. Dataset validado en DEV

## Fase D. Reportería y validación de negocio (2-3 días)

1. Construir páginas por dominio
2. Ejecutar conciliación ERP vs BI
3. Ajustar métricas con negocio
4. Cierre UAT

Salida de fase:

1. Reporte listo para publicación

## 3. Gobierno de datos

Reglas:

1. Fuente de verdad estructural: `database/migrations`
2. Fuente de verdad funcional (KPI): componentes de reportes ERP y validación con negocio
3. No editar visuales para corregir datos; la corrección va en capa SQL o DAX centralizado
4. Versionar vistas por release (`v2`, `v3`, etc.)

## 4. Estrategia de conciliación

Validaciones mínimas por dominio:

1. Conteo total de registros
2. Conteo por estado
3. Conteo por mes
4. Suma de importes monetarios

Criterio de aceptación sugerido:

1. Variación = 0 para conteos críticos
2. Variación <= 0.1% para métricas agregadas complejas

## 5. Riesgos y mitigación

Riesgo: cambios de estructura en ERP durante construcción BI.

Mitigación:

1. Congelar release de migraciones durante ventana de modelado
2. Si hay cambios urgentes, versionar V2.1 y actualizar solo capa SQL

Riesgo: ambigüedad de fechas para tendencias.

Mitigación:

1. Definir fecha de negocio por KPI en matriz
2. Documentar en DAX (`FechaTicketCreado`, `FechaTicketCierre`, etc.)

Riesgo: inconsistencias por soft delete.

Mitigación:

1. Definir por KPI si se excluye o incluye `deleted_at`
2. Estandarizar regla por hecho

## 6. Checklist de salida a producción

1. Script SQL V2 ejecutado sin errores
2. Vistas QA revisadas y aprobadas
3. Dataset publicado y refresco exitoso
4. KPI críticos conciliados
5. UAT firmado por usuario clave

## 7. Archivos operativos de esta fase

1. `docs/PowerBI/POWERBI_QUERIES_V2.sql`
2. `docs/PowerBI/POWERBI_MATRIZ_KPI_TEMPLATE.md`
3. `docs/PowerBI/POWERBI_PLAN_MIGRACION_V2.md`
