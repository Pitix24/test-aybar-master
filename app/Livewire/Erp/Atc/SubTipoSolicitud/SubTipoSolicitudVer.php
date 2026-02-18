<?php

namespace App\Livewire\Erp\Atc\SubTipoSolicitud;

use App\Models\SubTipoSolicitud;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle del Sub Tipo de Solicitud')]
class SubTipoSolicitudVer extends Component
{
    public SubTipoSolicitud $sub_tipo;

    public function mount($id)
    {
        $this->sub_tipo = SubTipoSolicitud::with('tipoSolicitud')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.atc.sub-tipo-solicitud.sub-tipo-solicitud-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
