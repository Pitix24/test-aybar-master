<?php

namespace App\Livewire\Erp\Atc\Ticket;

use App\Models\FlujoPaso;
use App\Models\Ticket;
use App\Models\TicketPaso;
use Livewire\Component;

class TicketPasos extends Component
{
    public Ticket $ticket;

    public function mount(Ticket $ticket)
    {
        $this->ticket = $ticket;
        $this->inicializarPasos();
    }

    public function inicializarPasos()
    {
        // Si el ticket tiene un tipo de solicitud pero no tiene pasos registrados aún
        if ($this->ticket->tipo_solicitud_id && $this->ticket->pasos()->count() === 0) {
            $plantilla = FlujoPaso::where('tipo_solicitud_id', $this->ticket->tipo_solicitud_id)
                ->orderBy('orden')
                ->get();

            foreach ($plantilla as $paso) {
                TicketPaso::create([
                    'ticket_id' => $this->ticket->id,
                    'flujo_paso_id' => $paso->id,
                    'completado' => false,
                ]);
            }
            
            $this->ticket->refresh();
        }
    }

    public function togglePaso($pasoId)
    {
        $paso = TicketPaso::findOrFail($pasoId);
        
        $nuevoEstado = !$paso->completado;
        
        $paso->update([
            'completado' => $nuevoEstado,
            'fecha_completado' => $nuevoEstado ? now() : null,
            'user_id' => $nuevoEstado ? auth()->id() : null,
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Actualizado',
            'text' => 'El estado del paso ha sido actualizado.',
        ]);
    }

    public function render()
    {
        return view('livewire.erp.atc.ticket.ticket-pasos', [
            'pasos' => $this->ticket->pasos()
                ->with('flujoPaso')
                ->get()
                ->sortBy(fn($p) => $p->flujoPaso->orden ?? 999)
        ]);
    }
}
