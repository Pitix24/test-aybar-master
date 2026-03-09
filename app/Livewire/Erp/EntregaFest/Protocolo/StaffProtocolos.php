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

    protected $listeners = ['eliminarProtocoloOn' => 'eliminarProtocolo'];

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['protocolos'])->findOrFail($id);
    }

    public function eliminarProtocolo($id)
    {
        $this->authorize('entrega-fest.staff');
        $protocolo = EntregaFestProtocolo::where('entrega_fest_id', $this->evento->id)->findOrFail($id);

        try {
            $protocolo->delete();
            $this->evento->load(['protocolos']);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'Protocolo eliminado correctamente.'
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('[STAFF PROTOCOLO ELIMINAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el protocolo.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.protocolo.staff-protocolos');
    }
}
