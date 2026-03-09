<?php

namespace App\Livewire\Erp\EntregaFest\Protocolo;

use App\Models\EntregaFest;
use App\Models\EntregaFestProtocolo;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Protocolos y Discursos - Entrega Fest')]
class StaffProtocolos extends Component
{
    public EntregaFest $evento;

    // Para crear Protocolos
    public $p_titulo = '';
    public $p_contenido = '';

    public $mostrarFormulario = false;

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['protocolos'])->findOrFail($id);
    }

    public function agregarProtocolo()
    {
        $this->authorize('entrega-fest.staff');

        $this->validate([
            'p_titulo' => 'required',
            'p_contenido' => 'required',
        ]);

        EntregaFestProtocolo::create([
            'entrega_fest_id' => $this->evento->id,
            'titulo' => $this->p_titulo,
            'contenido' => $this->p_contenido,
        ]);

        $this->reset(['p_titulo', 'p_contenido', 'mostrarFormulario']);
        $this->evento->load(['protocolos']);
        $this->dispatch('notificar', ['titulo' => 'Añadido', 'mensaje' => 'Protocolo guardado.', 'tipo' => 'success']);
    }

    public function eliminarProtocolo($id)
    {
        $this->authorize('entrega-fest.staff');
        EntregaFestProtocolo::findOrFail($id)->delete();
        $this->evento->load(['protocolos']);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.protocolo.staff-protocolos');
    }
}
