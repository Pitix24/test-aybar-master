<?php

namespace App\Livewire\Erp\EntregaFest\Contingencia;

use App\Models\EntregaFest;
use App\Models\EntregaFestContingencia;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Plan de Contingencia - Entrega Fest')]
class StaffContingencias extends Component
{
    public EntregaFest $evento;

    protected $listeners = ['eliminarContingenciaOn' => 'eliminarContingencia'];

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['contingencias'])->findOrFail($id);
    }

    public function eliminarContingencia($id)
    {
        $this->authorize('entrega-fest.staff');
        $contingencia = EntregaFestContingencia::where('entrega_fest_id', $this->evento->id)->findOrFail($id);

        try {
            $contingencia->delete();
            $this->evento->load(['contingencias']);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'Contingencia eliminada correctamente.'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[STAFF CONTINGENCIA ELIMINAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar la contingencia.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.contingencia.staff-contingencias');
    }
}
