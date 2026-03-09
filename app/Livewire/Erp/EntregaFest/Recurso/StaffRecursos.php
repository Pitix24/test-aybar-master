<?php

namespace App\Livewire\Erp\EntregaFest\Recurso;

use App\Models\EntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Recursos y Manuales - Entrega Fest')]
class StaffRecursos extends Component
{
    public EntregaFest $evento;

    protected $listeners = ['eliminarRecursoOn' => 'eliminarRecurso'];

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['recursos'])->findOrFail($id);
    }

    public function eliminarRecurso($id)
    {
        $this->authorize('entrega-fest.staff');
        $recurso = \App\Models\EntregaFestRecurso::where('entrega_fest_id', $this->evento->id)->findOrFail($id);

        try {
            $recurso->delete();
            $this->evento->load(['recursos']);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'Recurso eliminado correctamente.'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[STAFF RECURSO ELIMINAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el recurso.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.recurso.staff-recursos');
    }
}