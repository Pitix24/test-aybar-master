# Checklist Power BI - ERP Aybar

Esta guia te lleva paso a paso para cargar el modelo V2, crear relaciones y validar que el reporte quede listo.

## 1) Antes de empezar - LISTO

1. Ejecuta `docs/PowerBI/POWERBI_QUERIES_V2.sql` en la base de datos como un SQL Query (Alt + X)
2. Confirma que existen las vistas `vw_pbi_v2_*`.
3. Confirma que tienes acceso a Power BI Desktop.
4. Ten a mano la matriz KPI: `docs/PowerBI/POWERBI_MATRIZ_KPI_TEMPLATE.md`.

## 2) Cargar datos- LISTO

1. Abre Power BI Desktop.
2. Haz clic en `Obtener datos`.
3. Elige `MySQL database`.
4. Ingresa servidor, base de datos y credenciales.
5. Selecciona `Import`.
6. Marca estas vistas:
   1. `vw_pbi_v2_dim_fecha`
   2. `vw_pbi_v2_dim_usuario`
   3. `vw_pbi_v2_dim_region`
   4. `vw_pbi_v2_dim_provincia`
   5. `vw_pbi_v2_dim_distrito`
   6. `vw_pbi_v2_dim_unidad_negocio`
   7. `vw_pbi_v2_dim_proyecto`
   8. `vw_pbi_v2_dim_area`
   9. `vw_pbi_v2_dim_canal`
   10. `vw_pbi_v2_dim_tipo_solicitud`
   11. `vw_pbi_v2_dim_prioridad_ticket`
   12. `vw_pbi_v2_dim_estado_ticket`    
   13. `vw_pbi_v2_dim_estado_cita`
   14. `vw_pbi_v2_dim_motivo_cita`
   15. `vw_pbi_v2_dim_estado_solicitud_evidencia_pago`
   16. `vw_pbi_v2_dim_estado_solicitud_digitalizar_letra`
   17. `vw_pbi_v2_dim_sede`
   18. `vw_pbi_v2_fact_clientes`
   19. `vw_pbi_v2_fact_direcciones`
   20. `vw_pbi_v2_fact_admins`
   21. `vw_pbi_v2_fact_solicitud_evidencia_pago`
   22. `vw_pbi_v2_fact_solicitud_evidencia_pago_emails`
   23. `vw_pbi_v2_fact_evidencia_pago`
   24. `vw_pbi_v2_fact_tickets`
   25. `vw_pbi_v2_fact_ticket_derivados`
   26. `vw_pbi_v2_fact_citas`
   27. `vw_pbi_v2_fact_solicitud_digitalizar_letra`
   28. `vw_pbi_v2_qa_conteos_diff`
   29. `vw_pbi_v2_qa_null_keys`

## 3) Preparar el modelo

1. Ve a la vista de modelo.
2. Deja `vw_pbi_v2_dim_fecha` como calendario central.
3. Relaciona cada tabla de hecho con su dimensión principal.
4. Usa cardinalidad `Muchos a uno`.
5. Mantén el filtro en una sola dirección.
6. Evita relaciones duplicadas o ambiguas.

## 4) Relaciones recomendadas

// 1. `vw_pbi_v2_fact_clientes[user_id] -> vw_pbi_v2_dim_usuario[id]`
// 2. `vw_pbi_v2_fact_clientes[created_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]`
// 3. `vw_pbi_v2_fact_direcciones[region_id] -> vw_pbi_v2_dim_region[id]`
// 4. `vw_pbi_v2_fact_direcciones[provincia_id] -> vw_pbi_v2_dim_provincia[id]`
// 5. `vw_pbi_v2_fact_direcciones[distrito_id] -> vw_pbi_v2_dim_distrito[id]`
// 6. `vw_pbi_v2_fact_direcciones[created_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]`
// 7. `vw_pbi_v2_fact_admins[created_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]`
// 8. `vw_pbi_v2_fact_solicitud_evidencia_pago[unidad_negocio_id] -> vw_pbi_v2_dim_unidad_negocio[id]`
// 9. `vw_pbi_v2_fact_solicitud_evidencia_pago[proyecto_id] -> vw_pbi_v2_dim_proyecto[id]`
// 10. `vw_pbi_v2_fact_solicitud_evidencia_pago[estado_solicitud_evidencia_pago_id] -> vw_pbi_v2_dim_estado_solicitud_evidencia_pago[id]`
// 11. `vw_pbi_v2_fact_solicitud_evidencia_pago[created_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]`
// 12. `vw_pbi_v2_fact_solicitud_evidencia_pago[validacion_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]` usando relación inactiva si la necesitas
// 13. `vw_pbi_v2_fact_evidencia_pago[created_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]`
// 14. `vw_pbi_v2_fact_evidencia_pago[operacion_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]` usando relación inactiva si la necesitas
// 15. `vw_pbi_v2_fact_tickets[area_id] -> vw_pbi_v2_dim_area[id]`
// 16. `vw_pbi_v2_fact_tickets[canal_id] -> vw_pbi_v2_dim_canal[id]`
// 17. `vw_pbi_v2_fact_tickets[tipo_solicitud_id] -> vw_pbi_v2_dim_tipo_solicitud[id]`
// 18. `vw_pbi_v2_fact_tickets[prioridad_ticket_id] -> vw_pbi_v2_dim_prioridad_ticket[id]`
// 19. `vw_pbi_v2_fact_tickets[estado_ticket_id] -> vw_pbi_v2_dim_estado_ticket[id]`
// 20. `vw_pbi_v2_fact_tickets[created_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]`
// 21. `vw_pbi_v2_fact_tickets[cierre_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]` como relación inactiva
// 22. `vw_pbi_v2_fact_ticket_derivados[created_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]`
// 23. `vw_pbi_v2_fact_citas[sede_id] -> vw_pbi_v2_dim_sede[id]`
// 24. `vw_pbi_v2_fact_citas[motivo_cita_id] -> vw_pbi_v2_dim_motivo_cita[id]`
// 25. `vw_pbi_v2_fact_citas[estado_cita_id] -> vw_pbi_v2_dim_estado_cita[id]`
// 26. `vw_pbi_v2_fact_citas[created_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]`
// 27. `vw_pbi_v2_fact_citas[inicio_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]` como relación inactiva
// 28. `vw_pbi_v2_fact_solicitud_digitalizar_letra[estado_solicitud_digitalizar_letra_id] -> vw_pbi_v2_dim_estado_solicitud_digitalizar_letra[id]`
// 29. `vw_pbi_v2_fact_solicitud_digitalizar_letra[created_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]`
// 30. `vw_pbi_v2_fact_solicitud_digitalizar_letra[vencimiento_fecha_id] -> vw_pbi_v2_dim_fecha[fecha_id]` como relación inactiva

## 5) Crear medidas

1. Abre la tabla `KPI_Medidas`.
2. Crea las medidas del archivo `docs/PowerBI/POWERBI_DAX_V2.md`.
3. Para facilitar el proceso, copia y pega el archivo `docs/PowerBI/POWERBI_DAX_QUERY_V2.md` en la Vista de Consultas DAX.
4. Empieza por las medidas base y luego las derivadas.
5. Verifica que no haya errores de nombres de columnas o tablas.

## 6) Validar conciliación

1. Crea una página llamada `QA`.
2. Coloca una tabla con `vw_pbi_v2_qa_conteos_diff`.
3. Valida que la columna `diferencia` sea 0.
4. Coloca una tabla con `vw_pbi_v2_qa_null_keys`.
5. Revisa qué llaves tienen nulos y si eso es aceptable por negocio.

## 7) Construir páginas del reporte

1. Crea una página `Resumen`.
2. Crea una página `Clientes y Direcciones`.
3. Crea una página `Backoffice`.
4. Crea una página `Tickets`.
5. Crea una página `Citas`.
6. Crea una página `Letras`.

## 8) Visuales recomendados por página

1. Tarjetas para KPIs.
2. Barras para top regiones, estados y gestores.
3. Línea para tendencias por fecha.
4. Dona para distribuciones.
5. Matriz para detalle operativo.

## 9) Orden de validacion final

1. QA de conteos.
2. QA de nulos.
3. KPI operativos principales.
4. Comparacion contra ERP.
5. UAT con usuario final.
