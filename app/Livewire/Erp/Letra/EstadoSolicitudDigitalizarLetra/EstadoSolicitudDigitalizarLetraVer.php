<?php

namespace App\Livewire\Erp\Letra\EstadoSolicitudDigitalizarLetra;

use App\Models\EstadoSolicitudDigitalizarLetra;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Estado de Digitalización')]
class EstadoSolicitudDigitalizarLetraVer extends Component
{
    public EstadoSolicitudDigitalizarLetra $estado;

    public function mount($id)
    {
        $this->authorize('estado-solicitud-digitalizar-letra.ver');
        $this->estado = EstadoSolicitudDigitalizarLetra::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.letra.estado-solicitud-digitalizar-letra.estado-solicitud-digitalizar-letra-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
