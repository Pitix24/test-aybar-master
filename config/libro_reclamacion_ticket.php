<?php

return [
    'serie' => 'TCK',
    'crear_erp_habilitado' => false,

    // Prefijos de 3 letras para codigo de ticket por unidad (sin depender de columna en BD).
    // Si una unidad no esta mapeada, se genera automaticamente por id (AAA, AAB, ...).
    'codigos_unidad_negocio' => [
        'por_id' => [
            1 => 'AYB',
            2 => 'PVD',
            3 => 'VNT',
            4 => 'AIN',
            5 => 'GPR',
            6 => 'LPE',
            7 => 'NIN',
            8 => 'RSP',
        ],
        'por_nombre' => [
            // 'AYBAR CORP. S.A.C.' => 'AYB',
        ],
    ],

    'unidad_default_id' => 0,
    'unidad_template_id' => 0,
    'unidad_template_nombre' => 'RECLAMOS_SIN_PROYECTO',
    'unidad_template_razon_social' => 'RECLAMOS SIN PROYECTO',

    // Contrato Fase 1: mapeo tecnico para autogeneracion de Ticket desde formulario web.
    'ticket_autocreacion' => [
        'habilitado' => true,
        'area_legal_id' => 3,
        'canal_id' => null,
        'canal_nombre' => 'Libro Reclamación',
        'tipo_solicitud_id' => 28,
        'tipo_solicitud_nombre' => 'LIBRO DE RECLAMACIONES',
        'prioridad_ticket_id' => 1,
        'created_by' => 2562,
        'gestor_id' => 2562,

        // El nombre de subtipo es un fallback. En fase 2 se usara para resolver el subtipo real por catalogo.
        'subtipo_por_tipo_pedido' => [
            'RECLAMO' => 'RECLAMO',
            'QUEJA' => 'QUEJA',
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
        'razon_social' => 'AYBAR CORP. S.A.C.',
        'numero_inicial' => 0,
    ],
];