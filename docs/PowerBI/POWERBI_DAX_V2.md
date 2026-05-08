# Paquete Inicial de Medidas DAX V2

Este archivo contiene medidas DAX listas para copiar en Power BI Desktop, alineadas con las vistas `vw_pbi_v2_*`.

## 1) Recomendacion de implementacion

1. Crea una tabla vacia llamada `KPI_Medidas` en Power BI para centralizar todas las medidas.
2. Pega las medidas de este documento dentro de esa tabla.
3. Verifica relaciones activas entre hechos y dimensiones.
4. Valida resultados con las vistas QA (`vw_pbi_v2_qa_*`).

## 2) Convenciones usadas

1. Se excluyen registros con `deleted_at` no nulo en metricas operativas.
2. Se usan dimensiones de estado para evitar hardcode excesivo de IDs.
3. Los porcentajes usan `DIVIDE` para evitar division por cero.

## 3) Medidas base globales

    ```DAX
    KPI Fecha Max =
    MAX('vw_pbi_v2_dim_fecha'[fecha])

    KPI Fecha Min =
    MIN('vw_pbi_v2_dim_fecha'[fecha])
    ```

## 4) Clientes

```DAX
KPI Clientes Total =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_clientes'[id]),
    ISBLANK('vw_pbi_v2_fact_clientes'[deleted_at])
)

KPI Clientes Activos =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_clientes'[id]),
    ISBLANK('vw_pbi_v2_fact_clientes'[deleted_at]),
    'vw_pbi_v2_fact_clientes'[usuario_activo] = 1
)

KPI Clientes Inactivos =
[KPI Clientes Total] - [KPI Clientes Activos]

KPI Clientes Email Verificado =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_clientes'[id]),
    ISBLANK('vw_pbi_v2_fact_clientes'[deleted_at]),
    NOT(ISBLANK('vw_pbi_v2_fact_clientes'[email_verified_at]))
)

KPI Clientes con Direccion =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_clientes'[id]),
    ISBLANK('vw_pbi_v2_fact_clientes'[deleted_at]),
    NOT(ISBLANK('vw_pbi_v2_fact_clientes'[direccion_id]))
)

KPI Clientes % Activos =
DIVIDE([KPI Clientes Activos], [KPI Clientes Total], 0)

KPI Clientes % Email Verificado =
DIVIDE([KPI Clientes Email Verificado], [KPI Clientes Total], 0)

KPI Clientes % con Direccion =
DIVIDE([KPI Clientes con Direccion], [KPI Clientes Total], 0)
```

## 5) Administradores

```DAX
KPI Admins Total =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_admins'[id]),
    ISBLANK('vw_pbi_v2_fact_admins'[deleted_at])
)

KPI Admins Activos =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_admins'[id]),
    ISBLANK('vw_pbi_v2_fact_admins'[deleted_at]),
    'vw_pbi_v2_fact_admins'[activo] = 1
)

KPI Admins Inactivos =
[KPI Admins Total] - [KPI Admins Activos]

KPI Admins Promedio Roles =
AVERAGEX(
    VALUES('vw_pbi_v2_fact_admins'[id]),
    CALCULATE(MAX('vw_pbi_v2_fact_admins'[total_roles]))
)
```

## 6) Direcciones

```DAX
KPI Direcciones Total =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_direcciones'[id]),
    ISBLANK('vw_pbi_v2_fact_direcciones'[deleted_at])
)

KPI Direcciones Regiones Cubiertas =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_direcciones'[region_id]),
    ISBLANK('vw_pbi_v2_fact_direcciones'[deleted_at])
)

KPI Direcciones Distritos Cubiertos =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_direcciones'[distrito_id]),
    ISBLANK('vw_pbi_v2_fact_direcciones'[deleted_at])
)
```

## 7) Backoffice - Solicitudes de evidencia

```DAX
KPI Solicitudes Total =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_solicitud_evidencia_pago'[id]),
    ISBLANK('vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at])
)

KPI Solicitudes Validadas =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_solicitud_evidencia_pago'[id]),
    ISBLANK('vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at]),
    NOT(ISBLANK('vw_pbi_v2_fact_solicitud_evidencia_pago'[validacion_fecha_id]))
)

KPI Solicitudes Pendientes =
[KPI Solicitudes Total] - [KPI Solicitudes Validadas]

KPI Solicitudes Sin Asignar =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_solicitud_evidencia_pago'[id]),
    ISBLANK('vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at]),
    ISBLANK('vw_pbi_v2_fact_solicitud_evidencia_pago'[gestor_id])
)

KPI Solicitudes Asignadas =
[KPI Solicitudes Total] - [KPI Solicitudes Sin Asignar]

KPI Solicitudes % Cumplimiento =
DIVIDE([KPI Solicitudes Validadas], [KPI Solicitudes Total], 0)

KPI Solicitudes Monto Operacion =
CALCULATE(
    SUM('vw_pbi_v2_fact_solicitud_evidencia_pago'[monto_operacion]),
    ISBLANK('vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at])
)

KPI Solicitudes Total Evidencias Vinculadas =
CALCULATE(
    SUM('vw_pbi_v2_fact_solicitud_evidencia_pago'[total_evidencias]),
    ISBLANK('vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at])
)

KPI Solicitudes Total Emails Vinculados =
CALCULATE(
    SUM('vw_pbi_v2_fact_solicitud_evidencia_pago'[total_emails]),
    ISBLANK('vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at])
)
```

## 8) Backoffice - Evidencias

```DAX
KPI Evidencias Total =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_evidencia_pago'[id]),
    ISBLANK('vw_pbi_v2_fact_evidencia_pago'[deleted_at])
)

KPI Evidencias Monto Total =
CALCULATE(
    SUM('vw_pbi_v2_fact_evidencia_pago'[monto]),
    ISBLANK('vw_pbi_v2_fact_evidencia_pago'[deleted_at])
)

KPI Evidencias Cierre Automatico =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_evidencia_pago'[id]),
    ISBLANK('vw_pbi_v2_fact_evidencia_pago'[deleted_at]),
    NOT(ISBLANK('vw_pbi_v2_fact_evidencia_pago'[slin_respuesta]))
)

KPI Evidencias Cierre Manual =
[KPI Evidencias Total] - [KPI Evidencias Cierre Automatico]

KPI Evidencias % Automatizacion =
DIVIDE([KPI Evidencias Cierre Automatico], [KPI Evidencias Total], 0)
```

## 9) Tickets

```DAX
KPI Tickets Total =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_tickets'[id]),
    ISBLANK('vw_pbi_v2_fact_tickets'[deleted_at])
)

KPI Tickets Cerrados =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_tickets'[id]),
    ISBLANK('vw_pbi_v2_fact_tickets'[deleted_at]),
    KEEPFILTERS('vw_pbi_v2_dim_estado_ticket'[nombre] = "CERRADO")
)

KPI Tickets Abiertos =
[KPI Tickets Total] - [KPI Tickets Cerrados]

KPI Tickets Sin Gestor =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_tickets'[id]),
    ISBLANK('vw_pbi_v2_fact_tickets'[deleted_at]),
    ISBLANK('vw_pbi_v2_fact_tickets'[gestor_id])
)

KPI Tickets Total Derivaciones =
CALCULATE(
    SUM('vw_pbi_v2_fact_tickets'[total_derivaciones]),
    ISBLANK('vw_pbi_v2_fact_tickets'[deleted_at])
)

KPI Tickets % Cierre =
DIVIDE([KPI Tickets Cerrados], [KPI Tickets Total], 0)

KPI Tickets Tiempo Promedio Cierre (Horas) =
AVERAGEX(
    FILTER(
        'vw_pbi_v2_fact_tickets',
        ISBLANK('vw_pbi_v2_fact_tickets'[deleted_at])
            && NOT(ISBLANK('vw_pbi_v2_fact_tickets'[cierre_fecha_id]))
            && NOT(ISBLANK('vw_pbi_v2_fact_tickets'[fecha_validacion]))
    ),
    DATEDIFF(
        'vw_pbi_v2_fact_tickets'[created_at],
        'vw_pbi_v2_fact_tickets'[fecha_validacion],
        HOUR
    )
)
```

## 10) Citas

```DAX
KPI Citas Total =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_citas'[id]),
    ISBLANK('vw_pbi_v2_fact_citas'[deleted_at])
)

KPI Citas Atendidas =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_citas'[id]),
    ISBLANK('vw_pbi_v2_fact_citas'[deleted_at]),
    KEEPFILTERS('vw_pbi_v2_dim_estado_cita'[nombre] = "ATENDIDO")
)

KPI Citas Canceladas =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_citas'[id]),
    ISBLANK('vw_pbi_v2_fact_citas'[deleted_at]),
    KEEPFILTERS('vw_pbi_v2_dim_estado_cita'[nombre] = "CANCELADO")
)

KPI Citas Pendientes =
[KPI Citas Total] - [KPI Citas Atendidas] - [KPI Citas Canceladas]

KPI Citas % Efectividad =
DIVIDE(
    [KPI Citas Atendidas],
    [KPI Citas Total] - [KPI Citas Canceladas],
    0
)
```

## 11) Letras

```DAX
KPI Letras Total =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_solicitud_digitalizar_letra'[id]),
    ISBLANK('vw_pbi_v2_fact_solicitud_digitalizar_letra'[deleted_at])
)

KPI Letras Importe Total =
CALCULATE(
    SUM('vw_pbi_v2_fact_solicitud_digitalizar_letra'[importe_cuota_decimal]),
    ISBLANK('vw_pbi_v2_fact_solicitud_digitalizar_letra'[deleted_at])
)

KPI Letras Aprobadas =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_solicitud_digitalizar_letra'[id]),
    ISBLANK('vw_pbi_v2_fact_solicitud_digitalizar_letra'[deleted_at]),
    KEEPFILTERS('vw_pbi_v2_dim_estado_solicitud_digitalizar_letra'[nombre] = "APROBADO")
)

KPI Letras Pendientes =
CALCULATE(
    DISTINCTCOUNT('vw_pbi_v2_fact_solicitud_digitalizar_letra'[id]),
    ISBLANK('vw_pbi_v2_fact_solicitud_digitalizar_letra'[deleted_at]),
    KEEPFILTERS('vw_pbi_v2_dim_estado_solicitud_digitalizar_letra'[nombre] = "PENDIENTE")
)

KPI Letras % Aprobacion =
DIVIDE([KPI Letras Aprobadas], [KPI Letras Total], 0)
```

## 12) Medidas de control QA

```DAX
KPI QA Diferencia Conteo =
SUM('vw_pbi_v2_qa_conteos_diff'[diferencia])

KPI QA Reglas Nulas (Total) =
SUM('vw_pbi_v2_qa_null_keys'[total_nulos])
```

## 13) Formato recomendado en Power BI

1. Medidas de porcentaje: formato `%` con 1 o 2 decimales.
2. Montos: moneda local con separador de miles.
3. Conteos: numero entero con separador.
4. Horas promedio: decimal con 1 o 2 decimales.

## 14) Nota operativa

Si algun nombre de estado cambia en catalogos (ejemplo: `CERRADO`, `ATENDIDO`, `APROBADO`), actualiza primero las medidas dependientes de texto o migra a logica por ID de estado aprobado por negocio.
