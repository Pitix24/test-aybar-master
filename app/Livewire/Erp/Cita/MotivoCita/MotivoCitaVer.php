<?php

namespace App\Livewire\Erp\Cita\MotivoCita;

use App\Models\MotivoCita;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Motivo de Cita')]
class MotivoCitaVer extends Component
{
    public MotivoCita $motivo;

    public function mount($id)
    {
        $this->motivo = MotivoCita::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.cita.motivo-cita.motivo-cita-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
