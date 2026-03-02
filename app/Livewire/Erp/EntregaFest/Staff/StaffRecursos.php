<?php

namespace App\Livewire\Erp\EntregaFest\Staff;

use App\Models\EntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Recursos y Protocolos - Entrega Fest')]
class StaffRecursos extends Component
{
    use \Livewire\WithFileUploads;

    public EntregaFest $evento;
    public $tab = 'MAPAS';

    // Para crear Recursos
    public $nombre_publico = '';
    public $tipo_recurso = 'MAPA';
    public $archivo;

    // Para crear Protocolos
    public $p_titulo = '';
    public $p_contenido = '';

    // Para crear Contingencias
    public $c_escenario = '';
    public $c_accion = '';

    public $mostrarFormulario = false;

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['recursos', 'protocolos', 'contingencias'])->findOrFail($id);
    }

    public function agregarRecurso()
    {
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
        $this->evento->load(['recursos', 'protocolos', 'contingencias']);
        $this->dispatch('notificar', ['titulo' => 'Añadido', 'mensaje' => 'Recurso guardado.', 'tipo' => 'success']);
    }

    public function eliminarRecurso($id)
    {
        \App\Models\EntregaFestRecurso::findOrFail($id)->delete();
        $this->evento->load(['recursos', 'protocolos', 'contingencias']);
    }

    public function agregarProtocolo()
    {
        $this->validate([
            'p_titulo' => 'required',
            'p_contenido' => 'required',
        ]);

        \App\Models\EntregaFestProtocolo::create([
            'entrega_fest_id' => $this->evento->id,
            'titulo' => $this->p_titulo,
            'contenido' => $this->p_contenido,
        ]);

        $this->reset(['p_titulo', 'p_contenido', 'mostrarFormulario']);
        $this->evento->load(['recursos', 'protocolos', 'contingencias']);
    }

    public function eliminarProtocolo($id)
    {
        \App\Models\EntregaFestProtocolo::findOrFail($id)->delete();
        $this->evento->load(['recursos', 'protocolos', 'contingencias']);
    }

    public function agregarContingencia()
    {
        $this->validate([
            'c_escenario' => 'required',
            'c_accion' => 'required',
        ]);

        \App\Models\EntregaFestContingencia::create([
            'entrega_fest_id' => $this->evento->id,
            'escenario' => $this->c_escenario,
            'accion' => $this->c_accion,
        ]);

        $this->reset(['c_escenario', 'c_accion', 'mostrarFormulario']);
        $this->evento->load(['recursos', 'protocolos', 'contingencias']);
    }

    public function eliminarContingencia($id)
    {
        \App\Models\EntregaFestContingencia::findOrFail($id)->delete();
        $this->evento->load(['recursos', 'protocolos', 'contingencias']);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.staff.staff-recursos');
    }
}