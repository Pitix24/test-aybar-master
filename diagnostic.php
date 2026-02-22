<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "--- CONVERSACIONES ---\n";
foreach (App\Models\WhatsappConversacion::all() as $c) {
    echo "ID: {$c->id} | Contacto: {$c->contacto_id} | Cliente: {$c->cliente_id} | Estado: {$c->estado} | Unread: {$c->mensajes_sin_leer}\n";
}

echo "\n--- CONTACTOS ---\n";
foreach (App\Models\WhatsappContacto::all() as $ct) {
    echo "ID: {$ct->id} | WaID: {$ct->wa_id} | Nombre: {$ct->nombre_wa} | Cliente: {$ct->cliente_id}\n";
}

echo "\n--- ÚLTIMOS MENSAJES ENTRANTES ---\n";
foreach (App\Models\WhatsappMensaje::where('direccion', 'entrante')->latest()->take(5)->get() as $m) {
    echo "ID: {$m->id} | ConvID: {$m->conversacion_id} | Contenido: {$m->contenido} | Creado: {$m->created_at}\n";
}
