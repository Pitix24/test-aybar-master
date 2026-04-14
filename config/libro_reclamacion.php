<?php

return [
    'serie' => env('LIBRO_RECLAMACION_SERIE', 'TCK'),

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

    'aybar' => [
        'razon_social' => env('LIBRO_RECLAMACION_AYBAR_RAZON_SOCIAL', 'AYBAR CORP. S.A.C.'),
        'numero_inicial' => (int) env('LIBRO_RECLAMACION_AYBAR_NUMERO_INICIAL', 0),
    ],
];