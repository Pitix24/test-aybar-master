DEFINE

    -- =============================================
    -- GRUPO 1: MEDIDAS GLOBALES
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI Fecha Max] =
        MAX('aybar vw_pbi_v2_dim_fecha'[fecha])

    MEASURE 'KPI_Medidas'[KPI Fecha Min] =
        MIN('aybar vw_pbi_v2_dim_fecha'[fecha])

    -- =============================================
    -- GRUPO 2: CLIENTES - BASE
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI Clientes Total] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_clientes'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_clientes'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Clientes Activos] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_clientes'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_clientes'[deleted_at]),
            'aybar vw_pbi_v2_fact_clientes'[usuario_activo] = 1
        )

    MEASURE 'KPI_Medidas'[KPI Clientes Email Verificado] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_clientes'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_clientes'[deleted_at]),
            NOT(ISBLANK('aybar vw_pbi_v2_fact_clientes'[email_verified_at]))
        )

    MEASURE 'KPI_Medidas'[KPI Clientes con Direccion] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_clientes'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_clientes'[deleted_at]),
            NOT(ISBLANK('aybar vw_pbi_v2_fact_clientes'[direccion_id]))
        )

    -- CLIENTES - DERIVADAS
    MEASURE 'KPI_Medidas'[KPI Clientes Inactivos] =
        [KPI Clientes Total] - [KPI Clientes Activos]

    MEASURE 'KPI_Medidas'[KPI Clientes % Activos] =
        DIVIDE([KPI Clientes Activos], [KPI Clientes Total], 0)

    MEASURE 'KPI_Medidas'[KPI Clientes % Email Verificado] =
        DIVIDE([KPI Clientes Email Verificado], [KPI Clientes Total], 0)

    MEASURE 'KPI_Medidas'[KPI Clientes % con Direccion] =
        DIVIDE([KPI Clientes con Direccion], [KPI Clientes Total], 0)

    -- =============================================
    -- GRUPO 3: ADMINISTRADORES
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI Admins Total] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_admins'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_admins'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Admins Activos] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_admins'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_admins'[deleted_at]),
            'aybar vw_pbi_v2_fact_admins'[activo] = 1
        )

    MEASURE 'KPI_Medidas'[KPI Admins Inactivos] =
        [KPI Admins Total] - [KPI Admins Activos]

    MEASURE 'KPI_Medidas'[KPI Admins Promedio Roles] =
        AVERAGEX(
            VALUES('aybar vw_pbi_v2_fact_admins'[id]),
            CALCULATE(MAX('aybar vw_pbi_v2_fact_admins'[total_roles]))
        )

    -- =============================================
    -- GRUPO 4: DIRECCIONES
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI Direcciones Total] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_direcciones'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_direcciones'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Direcciones Regiones Cubiertas] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_direcciones'[region_id]),
            ISBLANK('aybar vw_pbi_v2_fact_direcciones'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Direcciones Distritos Cubiertos] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_direcciones'[distrito_id]),
            ISBLANK('aybar vw_pbi_v2_fact_direcciones'[deleted_at])
        )

    -- =============================================
    -- GRUPO 5: SOLICITUDES DE EVIDENCIA - BASE
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI Solicitudes Total] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Solicitudes Validadas] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at]),
            NOT(ISBLANK('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[validacion_fecha_id]))
        )

    MEASURE 'KPI_Medidas'[KPI Solicitudes Sin Asignar] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[gestor_id])
        )

    MEASURE 'KPI_Medidas'[KPI Solicitudes Monto Operacion] =
        CALCULATE(
            SUM('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[monto_operacion]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Solicitudes Total Evidencias Vinculadas] =
        CALCULATE(
            SUM('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[total_evidencias]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Solicitudes Total Emails Vinculados] =
        CALCULATE(
            SUM('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[total_emails]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_evidencia_pago'[deleted_at])
        )

    -- SOLICITUDES - DERIVADAS
    MEASURE 'KPI_Medidas'[KPI Solicitudes Pendientes] =
        [KPI Solicitudes Total] - [KPI Solicitudes Validadas]

    MEASURE 'KPI_Medidas'[KPI Solicitudes Asignadas] =
        [KPI Solicitudes Total] - [KPI Solicitudes Sin Asignar]

    MEASURE 'KPI_Medidas'[KPI Solicitudes % Cumplimiento] =
        DIVIDE([KPI Solicitudes Validadas], [KPI Solicitudes Total], 0)

    -- =============================================
    -- GRUPO 6: EVIDENCIAS - BASE
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI Evidencias Total] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_evidencia_pago'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_evidencia_pago'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Evidencias Monto Total] =
        CALCULATE(
            SUM('aybar vw_pbi_v2_fact_evidencia_pago'[monto]),
            ISBLANK('aybar vw_pbi_v2_fact_evidencia_pago'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Evidencias Cierre Automatico] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_evidencia_pago'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_evidencia_pago'[deleted_at]),
            NOT(ISBLANK('aybar vw_pbi_v2_fact_evidencia_pago'[slin_respuesta]))
        )

    -- EVIDENCIAS - DERIVADAS
    MEASURE 'KPI_Medidas'[KPI Evidencias Cierre Manual] =
        [KPI Evidencias Total] - [KPI Evidencias Cierre Automatico]

    MEASURE 'KPI_Medidas'[KPI Evidencias % Automatizacion] =
        DIVIDE([KPI Evidencias Cierre Automatico], [KPI Evidencias Total], 0)

    -- =============================================
    -- GRUPO 7: TICKETS - BASE
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI Tickets Total] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_tickets'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_tickets'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Tickets Cerrados] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_tickets'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_tickets'[deleted_at]),
            KEEPFILTERS('aybar vw_pbi_v2_dim_estado_ticket'[nombre] = "CERRADO")
        )

    MEASURE 'KPI_Medidas'[KPI Tickets Sin Gestor] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_tickets'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_tickets'[deleted_at]),
            ISBLANK('aybar vw_pbi_v2_fact_tickets'[gestor_id])
        )

    MEASURE 'KPI_Medidas'[KPI Tickets Total Derivaciones] =
        CALCULATE(
            SUM('aybar vw_pbi_v2_fact_tickets'[total_derivaciones]),
            ISBLANK('aybar vw_pbi_v2_fact_tickets'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Tickets Tiempo Promedio Cierre (Horas)] =
        AVERAGEX(
            FILTER(
                'aybar vw_pbi_v2_fact_tickets',
                ISBLANK('aybar vw_pbi_v2_fact_tickets'[deleted_at])
                    && NOT(ISBLANK('aybar vw_pbi_v2_fact_tickets'[created_fecha_id]))
                    && NOT(ISBLANK('aybar vw_pbi_v2_fact_tickets'[cierre_fecha_id]))
            ),
            DATEDIFF(
                'aybar vw_pbi_v2_fact_tickets'[created_fecha_id],
                'aybar vw_pbi_v2_fact_tickets'[cierre_fecha_id],
                HOUR
            )
        )

    -- TICKETS - DERIVADAS
    MEASURE 'KPI_Medidas'[KPI Tickets Abiertos] =
        [KPI Tickets Total] - [KPI Tickets Cerrados]

    MEASURE 'KPI_Medidas'[KPI Tickets % Cierre] =
        DIVIDE([KPI Tickets Cerrados], [KPI Tickets Total], 0)

    -- =============================================
    -- GRUPO 8: CITAS - BASE
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI Citas Total] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_citas'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_citas'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Citas Atendidas] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_citas'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_citas'[deleted_at]),
            KEEPFILTERS('aybar vw_pbi_v2_dim_estado_cita'[nombre] = "ATENDIDO")
        )

    MEASURE 'KPI_Medidas'[KPI Citas Canceladas] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_citas'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_citas'[deleted_at]),
            KEEPFILTERS('aybar vw_pbi_v2_dim_estado_cita'[nombre] = "CANCELADO")
        )

    -- CITAS - DERIVADAS
    MEASURE 'KPI_Medidas'[KPI Citas Pendientes] =
        [KPI Citas Total] - [KPI Citas Atendidas] - [KPI Citas Canceladas]

    MEASURE 'KPI_Medidas'[KPI Citas % Efectividad] =
        DIVIDE(
            [KPI Citas Atendidas],
            [KPI Citas Total] - [KPI Citas Canceladas],
            0
        )

    -- =============================================
    -- GRUPO 9: LETRAS - BASE
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI Letras Total] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_solicitud_digitalizar_letra'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_digitalizar_letra'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Letras Importe Total] =
        CALCULATE(
            SUM('aybar vw_pbi_v2_fact_solicitud_digitalizar_letra'[importe_cuota_decimal]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_digitalizar_letra'[deleted_at])
        )

    MEASURE 'KPI_Medidas'[KPI Letras Aprobadas] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_solicitud_digitalizar_letra'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_digitalizar_letra'[deleted_at]),
            KEEPFILTERS('aybar vw_pbi_v2_dim_estado_solicitud_digitalizar_letra'[nombre] = "APROBADO")
        )

    MEASURE 'KPI_Medidas'[KPI Letras Pendientes] =
        CALCULATE(
            DISTINCTCOUNT('aybar vw_pbi_v2_fact_solicitud_digitalizar_letra'[id]),
            ISBLANK('aybar vw_pbi_v2_fact_solicitud_digitalizar_letra'[deleted_at]),
            KEEPFILTERS('aybar vw_pbi_v2_dim_estado_solicitud_digitalizar_letra'[nombre] = "PENDIENTE")
        )

    -- LETRAS - DERIVADA
    MEASURE 'KPI_Medidas'[KPI Letras % Aprobacion] =
        DIVIDE([KPI Letras Aprobadas], [KPI Letras Total], 0)

    -- =============================================
    -- GRUPO 10: CONTROL QA
    -- =============================================
    MEASURE 'KPI_Medidas'[KPI QA Diferencia Conteo] =
        SUM('aybar vw_pbi_v2_qa_conteos_diff'[diferencia])

    MEASURE 'KPI_Medidas'[KPI QA Reglas Nulas (Total)] =
        SUM('aybar vw_pbi_v2_qa_null_keys'[total_nulos])

EVALUATE
ROW(
    "Fecha Max", [KPI Fecha Max],
    "Fecha Min", [KPI Fecha Min],
    "Total Clientes", [KPI Clientes Total],
    "Clientes Activos", [KPI Clientes Activos],
    "Total Tickets", [KPI Tickets Total],
    "Total Citas", [KPI Citas Total],
    "Total Letras", [KPI Letras Total],
    "QA Diferencia", [KPI QA Diferencia Conteo]
)
