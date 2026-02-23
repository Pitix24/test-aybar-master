<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Detalles Entrega Fest')]
class EntregaFestVer extends Component
{
    public EntregaFest $evento;

    public function mount($id)
    {
        $this->evento = EntregaFest::with([
            'unidadNegocio',
            'proyectos',
            'cliente',
            'user',
            'prospectos.user',
            'invitados.prospecto'
        ])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
