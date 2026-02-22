<?php

namespace App\Livewire\Crm\Whatsapp;

use Livewire\Component;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use Illuminate\Support\Facades\Auth;

class ChatConversacion extends Component
{
    public $conversacionId;
    public $nuevoMensaje = '';

    protected $listeners = ['conversacionSeleccionada' => 'cargarConversacion', 'mensajeRecibido' => '$refresh'];

    public function cargarConversacion($id)
    {
        $this->conversacionId = $id;

        // Marcar como leído al abrir
        $conversacion = WhatsappConversacion::find($id);
        if ($conversacion) {
            $conversacion->update(['mensajes_sin_leer' => 0]);
        }
    }

    public function enviarMensaje(\App\Services\WhatsappService $whatsappService)
    {
        if (empty($this->nuevoMensaje) || !$this->conversacionId)
            return;

        $conversacion = WhatsappConversacion::with('contacto.cliente')->find($this->conversacionId);

        // 1. Guardar en BD local como temporal
        $mensaje = WhatsappMensaje::create([
            'conversacion_id' => $this->conversacionId,
            'direccion' => 'saliente',
            'tipo' => 'texto',
            'contenido' => $this->nuevoMensaje,
            'wa_message_id' => 'TMP_' . time(),
            'estado' => 'enviado'
        ]);

        // 2. Enviar a Meta Cloud API
        $telefono = preg_replace('/\D/', '', $conversacion->contacto->wa_id);
        $response = $whatsappService->sendText($telefono, $this->nuevoMensaje);

        if ($response && isset($response['messages'][0]['id'])) {
            // Actualizar con el ID real de Meta
            $mensaje->update([
                'wa_message_id' => $response['messages'][0]['id']
            ]);
        } else {
            $mensaje->update([
                'estado' => 'fallido'
            ]);
        }

        $this->nuevoMensaje = '';
        $this->dispatch('mensajeEnviado');
    }

    public function render()
    {
        $conversacion = null;
        $mensajes = [];

        if ($this->conversacionId) {
            $conversacion = WhatsappConversacion::with('contacto.cliente')->find($this->conversacionId);
            $mensajes = WhatsappMensaje::where('conversacion_id', $this->conversacionId)
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return view('livewire.crm.whatsapp.chat-conversacion', [
            'conversacion' => $conversacion,
            'mensajes' => $mensajes
        ]);
    }
}
