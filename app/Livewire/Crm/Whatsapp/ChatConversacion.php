<?php

namespace App\Livewire\Crm\Whatsapp;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Models\WhatsappPlantilla;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Storage;

class ChatConversacion extends Component
{
    use WithFileUploads;

    public ?int $conversacionId = null;
    public string $nuevoMensaje = '';
    public string $buscarEnChat = '';
    public bool $mostrarEmoticones = false;
    public bool $mostrarHerramientas = false;

    // Props de sidebars (pasadas como @props desde el container)
    public bool $sidebarBuscarMensajes = false;
    public bool $sidebarPlantillas = false;
    public bool $sidebarInfoContacto = false;
    public string $buscarPlantilla = '';

    protected $listeners = [
        'conversacionSeleccionada' => 'cargarConversacion',
        'mensajeRecibido' => '$refresh',
        'plantillaSeleccionada' => 'rellenarConPlantilla',
    ];

    // ─── Cargar conversación ────────────────────────────────────────────────
    public function cargarConversacion(int $id): void
    {
        $this->conversacionId = $id;
        $this->nuevoMensaje = '';
        $this->mostrarEmoticones = false;
        $this->mostrarHerramientas = false;

        WhatsappConversacion::where('id', $id)->update(['mensajes_sin_leer' => 0]);
    }

    // ─── Enviar texto ───────────────────────────────────────────────────────
    public function enviarMensaje(WhatsappService $whatsappService): void
    {
        $texto = trim($this->nuevoMensaje);
        if (blank($texto) || !$this->conversacionId)
            return;

        $conversacion = WhatsappConversacion::with('contacto')->find($this->conversacionId);
        if (!$conversacion)
            return;

        $mensaje = WhatsappMensaje::create([
            'conversacion_id' => $this->conversacionId,
            'direccion' => 'saliente',
            'tipo' => 'texto',
            'contenido' => $texto,
            'wa_message_id' => 'TMP_' . uniqid(),
            'estado' => 'enviando',
        ]);

        $conversacion->update(['last_message_at' => now()]);

        $telefono = preg_replace('/\D/', '', $conversacion->contacto->wa_id);
        $response = $whatsappService->sendText($telefono, $texto);

        $mensaje->update([
            'wa_message_id' => $response['messages'][0]['id'] ?? $mensaje->wa_message_id,
            'estado' => ($response && isset($response['messages'][0]['id'])) ? 'enviado' : 'fallido',
        ]);

        $this->nuevoMensaje = '';
        $this->dispatch('mensajeEnviado');
        $this->dispatch('mensajeRecibido');   // refresca la lista
    }

    // ─── Usar plantilla ─────────────────────────────────────────────────────
    public function usarPlantilla(int $id, WhatsappService $whatsappService): void
    {
        if (!$this->conversacionId)
            return;

        $plantilla = WhatsappPlantilla::findOrFail($id);
        $conversacion = WhatsappConversacion::with('contacto')->findOrFail($this->conversacionId);
        $telefono = preg_replace('/\D/', '', $conversacion->contacto->wa_id);

        $msg = WhatsappMensaje::create([
            'conversacion_id' => $this->conversacionId,
            'direccion' => 'saliente',
            'tipo' => 'template',
            'contenido' => $plantilla->nombre,
            'wa_message_id' => 'TMP_TPL_' . uniqid(),
            'estado' => 'enviando',
        ]);

        $conversacion->update(['last_message_at' => now()]);
        $response = $whatsappService->sendTemplate($telefono, $plantilla->nombre);

        $msg->update([
            'wa_message_id' => $response['messages'][0]['id'] ?? $msg->wa_message_id,
            'estado' => ($response && isset($response['messages'][0]['id'])) ? 'enviado' : 'fallido',
        ]);

        $this->dispatch('cerrarSidebarPlantillas');
        $this->dispatch('mensajeEnviado');
    }

    // ─── Rellenar input con plantilla ───────────────────────────────────────
    public function rellenarConPlantilla(string $texto): void
    {
        $this->nuevoMensaje = $texto;
    }

    // ─── Toggle emoticones / herramientas ──────────────────────────────────
    public function toggleEmoticones(): void
    {
        $this->mostrarEmoticones = !$this->mostrarEmoticones;
        $this->mostrarHerramientas = false;
    }

    public function toggleHerramientas(): void
    {
        $this->mostrarHerramientas = !$this->mostrarHerramientas;
        $this->mostrarEmoticones = false;
    }

    // ─── Insertar emoticón ──────────────────────────────────────────────────
    public function insertarEmoticon(string $emoji): void
    {
        $this->nuevoMensaje .= $emoji;
        $this->mostrarEmoticones = false;
    }

    // ─── Disparar apertura de modal multimedia al Container ─────────────────
    public function verMultimedia(string $url, string $tipo): void
    {
        $this->dispatch('abrirModalMultimedia', url: $url, tipo: $tipo);
    }

    public function render()
    {
        $conversacion = null;
        $mensajes = collect();
        $plantillas = collect();

        if ($this->conversacionId) {
            $conversacion = WhatsappConversacion::with('contacto.cliente')
                ->find($this->conversacionId);

            $mensajesQ = WhatsappMensaje::where('conversacion_id', $this->conversacionId)
                ->orderBy('created_at', 'asc');

            if ($this->buscarEnChat) {
                $mensajesQ->where('contenido', 'like', '%' . $this->buscarEnChat . '%');
            }

            $mensajes = $mensajesQ->get();
        }

        if ($this->sidebarPlantillas) {
            $qp = WhatsappPlantilla::query();
            if ($this->buscarPlantilla) {
                $qp->where('nombre', 'like', '%' . $this->buscarPlantilla . '%')
                    ->orWhere('contenido', 'like', '%' . $this->buscarPlantilla . '%');
            }
            $plantillas = $qp->latest()->get();
        }

        return view('livewire.crm.whatsapp.chat-conversacion', [
            'conversacion' => $conversacion,
            'mensajes' => $mensajes,
            'plantillas' => $plantillas,
        ]);
    }
}
