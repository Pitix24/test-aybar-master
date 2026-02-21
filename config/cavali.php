<?php

return [
    'notifications' => [
        'to' => env('CAVALI_NOTIFICATION_TO', 'PROGRAMADOR@aybarsac.com'),
        'cc' => env('CAVALI_NOTIFICATION_CC', 'mersmith14@gmail.com,gestiondeprocesos@aybarsac.com'),

        'daily_job' => [
            'subject' => 'Letras físicas pagadas - :razonSocial',
            'body' => "Estimados, se hace llegar la base de letras pagadas físicas a desmaterializar. :fecha\n\nEmpresa: :razonSocial\nTotal de solicitudes: :count",
        ],

        'individual_send' => [
            'subject' => 'Letra Individual - :razonSocial',
            'body' => "Estimados, se ha enviado una letra individual a desmaterializar.\n\nEmpresa: :razonSocial\nLetra: :letra",
        ]
    ]
];
