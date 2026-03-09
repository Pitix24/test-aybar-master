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
    use \Livewire\WithFileUploads;

    public EntregaFest $evento;

    // Para crear Recursos
    public $nombre_publico = '';
    public $tipo_recurso = 'MAPA';
    public $archivo;

    public $mostrarFormulario = false;

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['recursos'])->findOrFail($id);
    }

    public function agregarRecurso()
    {
        $this->authorize('entrega-fest.staff');

        $this->validate([
            'nombre_publico' => 'required',
            'tipo_recurso' => 'required',
            'archivo' => 'required|file|max:10240', // 10MB
        ]);

        $recurso = \App\Models\EntregaFestRecurso::create([
            'entrega_fest_id' => $this->evento->id,
            'nombre_publico' => $this->nombre_publico,
            'tipo_recurso' => $this->tipo_recurso,
        ]);

        $recurso->addMedia($this->archivo->getRealPath())
            ->usingFileName($this->archivo->getClientOriginalName())
            ->toMediaCollection();

        $this->reset(['nombre_publico', 'tipo_recurso', 'archivo', 'mostrarFormulario']);
        $this->evento->load(['recursos']);
        $this->dispatch('notificar', ['titulo' => 'Añadido', 'mensaje' => 'Recurso guardado.', 'tipo' => 'success']);
    }

    public function eliminarRecurso($id)
    {
        $this->authorize('entrega-fest.staff');
        \App\Models\EntregaFestRecurso::findOrFail($id)->delete();
        $this->evento->load(['recursos']);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.recurso.staff-recursos');
    }
}