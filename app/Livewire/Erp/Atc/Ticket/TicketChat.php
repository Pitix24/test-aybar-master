<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\Ticket;
use App\Models\TicketMensaje;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TicketChat extends Component
{
    public Ticket $ticket;
    public $mensaje = '';
    public $es_interno = false;
    public $isOpen = false;
    public $soloLectura = false;

    public function mount($ticket, $soloLectura = false)
    {
        $this->ticket = $ticket;
        $this->soloLectura = $soloLectura;
    }

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
        $this->authorize('ticket.chat');

        if (trim($this->mensaje) == '') {
            return;
        }

        try {
            DB::beginTransaction();

            TicketMensaje::create([
                'ticket_id' => $this->ticket->id,
                'user_id' => auth()->id(),
                'mensaje' => $this->mensaje,
                'es_interno' => $this->es_interno,
            ]);

            DB::commit();

            $this->reset(['mensaje', 'es_interno']);
            $this->dispatch('mensajeEnviado');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('ticket')->error('[TICKET] Error al enviar mensaje chat: ' . $e->getMessage());
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
