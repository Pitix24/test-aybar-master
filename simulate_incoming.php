<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\WhatsappController;
use Illuminate\Http\Request;

$controller = new WhatsappController();

$payload = [
    'object' => 'whatsapp_business_account',
    'entry' => [
        [
            'id' => '115203301595544',
            'changes' => [
                [
                    'value' => [
                        'messaging_product' => 'whatsapp',
                        'metadata' => [
                            'display_phone_number' => '15550604620',
                            'phone_number_id' => '111079285343903'
                        ],
                        'contacts' => [
                            [
                                'profile' => ['name' => 'Pruebas Local'],
                                'wa_id' => '51960335525'
                            ]
                        ],
                        'messages' => [
                            [
                                'from' => '51960335525',
                                'id' => 'TEST_' . time(),
                                'timestamp' => time(),
                                'text' => ['body' => 'Mensaje de Prueba Interno'],
                                'type' => 'text'
                            ]
                        ]
                    ],
                    'field' => 'messages'
                ]
            ]
        ]
    ]
];

$request = Request::create('/api/whatsapp/webhook', 'POST', [], [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($payload));
$response = $controller->handleWebhook($request);

echo "Respuesta del controlador: " . $response->getStatusCode() . "\n";
echo "Revisa el log whatsapp.log para ver el rastro.\n";
