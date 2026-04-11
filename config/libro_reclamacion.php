<?php

return [
    'serie' => env('LIBRO_RECLAMACION_SERIE', 'TCK'),

    'unidad_default_id' => (int) env('LIBRO_RECLAMACION_UNIDAD_DEFAULT_ID', 0),
    'unidad_template_id' => 0,
    'unidad_template_nombre' => 'RECLAMOS_SIN_PROYECTO',
    'unidad_template_razon_social' => 'RECLAMOS SIN PROYECTO',

    'aybar' => [
        'razon_social' => env('LIBRO_RECLAMACION_AYBAR_RAZON_SOCIAL', 'AYBAR CORP. S.A.C.'),
        'numero_inicial' => (int) env('LIBRO_RECLAMACION_AYBAR_NUMERO_INICIAL', 0),
    ],
];