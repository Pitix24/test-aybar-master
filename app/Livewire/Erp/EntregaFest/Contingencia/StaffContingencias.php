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

    // Para crear Contingencias
    public $c_escenario = '';
    public $c_accion = '';

    public $mostrarFormulario = false;

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['contingencias'])->findOrFail($id);
    }

    public function agregarContingencia()
    {
        $this->authorize('entrega-fest.staff');

        $this->validate([
            'c_escenario' => 'required',
            'c_accion' => 'required',
        ]);

        EntregaFestContingencia::create([
            'entrega_fest_id' => $this->evento->id,
            'escenario' => $this->c_escenario,
            'accion' => $this->c_accion,
        ]);

        $this->reset(['c_escenario', 'c_accion', 'mostrarFormulario']);
        $this->evento->load(['contingencias']);
        $this->dispatch('notificar', ['titulo' => 'Añadido', 'mensaje' => 'Plan de contingencia guardado.', 'tipo' => 'success']);
    }

    public function eliminarContingencia($id)
    {
        $this->authorize('entrega-fest.staff');
        EntregaFestContingencia::findOrFail($id)->delete();
        $this->evento->load(['contingencias']);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.contingencia.staff-contingencias');
    }
}
