<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$mensajes = App\Models\WhatsappMensaje::orderBy('id', 'desc')->take(10)->get();
foreach ($mensajes as $m) {
    echo "ID: {$m->id} | Dir: {$m->direccion} | Cont: {$m->contenido} | WaID: {$m->wa_message_id}\n";
}
