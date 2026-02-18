<?php

namespace App\Livewire\Erp\Cita\EstadoCita;

use App\Models\EstadoCita;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Estado de Cita')]
class EstadoCitaVer extends Component
{
    public EstadoCita $estado;

    public function mount($id)
    {
        $this->estado = EstadoCita::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.cita.estado-cita.estado-cita-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
