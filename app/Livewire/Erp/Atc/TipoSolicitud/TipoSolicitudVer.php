<?php

namespace App\Livewire\Erp\Atc\TipoSolicitud;

use App\Models\TipoSolicitud;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle del Tipo de Solicitud')]
class TipoSolicitudVer extends Component
{
    public TipoSolicitud $tipo;

    public function mount($id)
    {
        $this->authorize('tipo-solicitud.ver');
        $this->tipo = TipoSolicitud::with('areas')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.atc.tipo-solicitud.tipo-solicitud-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
