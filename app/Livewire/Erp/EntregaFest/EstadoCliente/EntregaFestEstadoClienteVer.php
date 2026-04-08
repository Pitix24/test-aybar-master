<?php

namespace App\Livewire\Erp\EntregaFest\EstadoCliente;

use App\Models\EntregaFestEstadoCliente;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Estado de Cliente - Entrega Fest')]
class EntregaFestEstadoClienteVer extends Component
{
    public EntregaFestEstadoCliente $estado_model;

    public function mount($id)
    {
        $this->estado_model = EntregaFestEstadoCliente::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.estado-cliente.entrega-fest-estado-cliente-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
