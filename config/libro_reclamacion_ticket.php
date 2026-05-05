<?php

return [
    'serie' => 'NUL',
    'crear_erp_habilitado' => false,

    // Contrato Fase 1: mapeo tecnico para autogeneracion de Ticket desde formulario web.
    'ticket_autocreacion' => [
        'habilitado' => true,
        'area_legal_id' => 3,
        'canal_id' => null,
        'canal_nombre' => 'Libro Reclamación',
        'tipo_solicitud_id' => 28,
        'tipo_solicitud_nombre' => 'LIBRO DE RECLAMACIONES',
        'prioridad_ticket_id' => 1,
        'created_by' => 3066,
        'gestor_id' => 3066,

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
        'numero_inicial' => env('LIBRO_RECLAMACION_AYBAR_NUMERO_INICIAL', 1301),
    ],
];
