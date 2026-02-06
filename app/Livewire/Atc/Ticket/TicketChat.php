<?php

namespace App\Livewire\Atc\Ticket;

use App\Models\Ticket;
use App\Models\TicketMensaje;
use App\Models\TicketArchivo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketChat extends Component
{
    use WithFileUploads;

    public Ticket $ticket;
    public $mensaje = '';
    public $es_interno = false;
    public $adjunto;
    public $isOpen = false;

    protected $listeners = ['toggleChat' => 'toggle'];

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->dispatch('chatOpened');
        }
    }

    public function enviar()
    {
        if (trim($this->mensaje) == '' && !$this->adjunto) {
            return;
        }

        try {
            DB::beginTransaction();

            $nuevoMensaje = TicketMensaje::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'mensaje' => $this->mensaje,
                'es_interno' => $this->es_interno,
            ]);

            if ($this->adjunto) {
                $path = $this->adjunto->store('tickets/' . $this->ticket->id . '/mensajes', 'public');
                TicketArchivo::create([
                    'archivable_type' => TicketMensaje::class,
                    'archivable_id' => $nuevoMensaje->id,
                    'user_id' => auth()->id(),
                    'nombre_original' => $this->adjunto->getClientOriginalName(),
                    'path' => $path,
                    'extension' => $this->adjunto->getClientOriginalExtension(),
                    'size' => $this->adjunto->getSize(),
                    'mime_type' => $this->adjunto->getMimeType(),
                ]);
            }

            DB::commit();

            $this->reset(['mensaje', 'adjunto', 'es_interno']);
            $this->dispatch('mensajeEnviado');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al enviar mensaje chat: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo enviar el mensaje.']);
        }
    }

    public function render()
    {
        $mensajes = TicketMensaje::where('ticket_id', $this->ticket->id)
            ->with(['user', 'archivos'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('livewire.atc.ticket.ticket-chat', [
            'mensajes' => $mensajes
        ]);
    }
}
