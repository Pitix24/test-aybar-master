<?php

namespace App\Livewire\Erp\EntregaFest\Staff;

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
        $this->dispatch('notificar', ['titulo' => 'Estado Actualizado', 'mensaje' => 'El bloque ha sido actualizado a ' . $nuevoEstado, 'tipo' => 'success']);
    }

    public function toggleChecklist($checklistId)
    {
        $item = \App\Models\EntregaFestItinerarioChecklist::findOrFail($checklistId);
        $item->update([
            'esta_listo' => !$item->esta_listo,
            'completado_at' => !$item->esta_listo ? now() : null,
            'completado_por_user_id' => !$item->esta_listo ? auth()->id() : null,
        ]);

        $this->evento->load('itinerarioBloques.checklists');
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.staff.staff-itinerario');
    }
}
