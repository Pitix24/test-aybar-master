<?php

return [
    'serie' => env('LIBRO_RECLAMACION_SERIE', 'TCK'),
    'crear_erp_habilitado' => filter_var(env('LIBRO_RECLAMACION_CREAR_ERP_HABILITADO', false), FILTER_VALIDATE_BOOL),

    // Prefijos de 3 letras para codigo de ticket por unidad (sin depender de columna en BD).
    // Si una unidad no esta mapeada, se genera automaticamente por id (AAA, AAB, ...).
    'codigos_unidad_negocio' => [
        'por_id' => [
            1 => 'AYB',
            2=> 'PVD',
            3=> 'VNT',
            4=> 'AIN',
            5=> 'GPR',
            6=> 'LPE',
            7=> 'NIN',
            8=> 'RSP',
        ],
        'por_nombre' => [
            // 'AYBAR CORP. S.A.C.' => 'AYB',
        ],
    ],

    'unidad_default_id' => (int) env('LIBRO_RECLAMACION_UNIDAD_DEFAULT_ID', 0),
    'unidad_template_id' => 0,
    'unidad_template_nombre' => 'RECLAMOS_SIN_PROYECTO',
    'unidad_template_razon_social' => 'RECLAMOS SIN PROYECTO',

    // Contrato Fase 1: mapeo tecnico para autogeneracion de Ticket desde formulario web.
    'ticket_autocreacion' => [
        'habilitado' => filter_var(env('LIBRO_RECLAMACION_TICKET_AUTOCREACION_HABILITADO', true), FILTER_VALIDATE_BOOL),
        'area_legal_id' => (int) env('LIBRO_RECLAMACION_TICKET_AREA_ID', 3),
        'canal_id' => env('LIBRO_RECLAMACION_TICKET_CANAL_ID'),
        'canal_nombre' => env('LIBRO_RECLAMACION_TICKET_CANAL_NOMBRE', 'FORMULARIO WEB'),
        'tipo_solicitud_id' => (int) env('LIBRO_RECLAMACION_TICKET_TIPO_SOLICITUD_ID', 28),
        'tipo_solicitud_nombre' => env('LIBRO_RECLAMACION_TICKET_TIPO_SOLICITUD_NOMBRE', 'LIBRO DE RECLAMACIONES'),
        'prioridad_ticket_id' => (int) env('LIBRO_RECLAMACION_TICKET_PRIORIDAD_ID', 3),
        'created_by' => null,

        // El nombre de subtipo es un fallback. En fase 2 se usara para resolver el subtipo real por catalogo.
        'subtipo_por_tipo_pedido' => [
            'RECLAMO' => env('LIBRO_RECLAMACION_TICKET_SUBTIPO_RECLAMO_NOMBRE', 'RECLAMO'),
            'QUEJA' => env('LIBRO_RECLAMACION_TICKET_SUBTIPO_QUEJA_NOMBRE', 'QUEJA'),
        ],

        // Plantillas acordadas para contenido tecnico del ticket.
        'asunto' => [
            'formato' => ':tipo_pedido - :documento',
            'documento_default' => 'SIN DOCUMENTO',
            'tipo_default' => 'NO_DEFINIDO',
        ],
        'descripcion' => [
            'prefijo_detalle' => 'Cliente detalla lo siguiente:',
            'prefijo_pedido' => 'Cliente pide lo siguiente:',
        ],
    ],

    'aybar' => [
        'razon_social' => env('LIBRO_RECLAMACION_AYBAR_RAZON_SOCIAL', 'AYBAR CORP. S.A.C.'),
        'numero_inicial' => (int) env('LIBRO_RECLAMACION_AYBAR_NUMERO_INICIAL', 0),
    ],
];