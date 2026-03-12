<?php

namespace App\Livewire\Erp\EntregaFest\Itinerario;

use App\Models\EntregaFest;
use App\Models\EntregaFestItinerarioBloque;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Itinerario Staff - Entrega Fest')]
class StaffItinerario extends Component
{
    public EntregaFest $evento;

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['itinerarioBloques.checklists'])->findOrFail($id);
    }

    public function actualizarEstado($bloqueId, $nuevoEstado)
    {
        $bloque = EntregaFestItinerarioBloque::findOrFail($bloqueId);
        $bloque->update(['estado' => $nuevoEstado]);

        $this->evento->load('itinerarioBloques.checklists');
        
        $msg = match($nuevoEstado) {
            EntregaFestItinerarioBloque::ESTADO_CURSO => 'El bloque ha iniciado.',
            EntregaFestItinerarioBloque::ESTADO_COMPLETADO => 'El bloque ha sido finalizado.',
            default => 'Estado actualizado.'
        };

        $this->dispatch('notificar', ['titulo' => 'Itinerario', 'mensaje' => $msg, 'tipo' => 'success']);
    }

    public function toggleChecklist($checklistId)
    {
        $item = \App\Models\EntregaFestItinerarioChecklist::with('bloque')->findOrFail($checklistId);
        
        // Solo permitir si el bloque está EN CURSO
        if ($item->bloque->estado !== EntregaFestItinerarioBloque::ESTADO_CURSO) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Acción no permitida',
                'text' => 'Primero debes INICIAR este bloque para marcar tareas.'
            ]);
            return;
        }

        $item->update([
            'esta_listo' => !$item->esta_listo,
            'completado_at' => !$item->esta_listo ? now() : null,
            'completado_por_user_id' => !$item->esta_listo ? auth()->id() : null,
        ]);

        $this->evento->load('itinerarioBloques.checklists');
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.itinerario.staff-itinerario');
    }
}
