<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use App\Models\TicketMensaje;
use App\Models\TicketArchivo;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketChat extends Component
{
    public Ticket $ticket;
    public $mensaje = '';
    public $es_interno = false;
    public $isOpen = false;

    #[On('toggleChat')]
    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen) {
            $this->dispatch('chatOpened');
        }
    }

    public function enviar()
    {
        abort_unless(auth()->user()->can('ticket.ver'), 403);

        if (trim($this->mensaje) == '') {
            return;
        }

        try {
            TicketMensaje::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'mensaje' => $this->mensaje,
                'es_interno' => $this->es_interno,
            ]);

            $this->reset(['mensaje', 'es_interno']);
            $this->dispatch('mensajeEnviado');
        } catch (\Exception $e) {
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

        return view('livewire.erp.atc.ticket.ticket-chat', [
            'mensajes' => $mensajes
        ]);
    }
}
