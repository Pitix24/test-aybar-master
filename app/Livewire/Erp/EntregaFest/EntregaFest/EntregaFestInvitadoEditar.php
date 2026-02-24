<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Invitado - Entrega Fest')]
class EntregaFestInvitadoEditar extends Component
{
    public EntregaFest $evento;
    public InvitadoEntregaFest $invitado;

    public $cantidad_acompanantes_permitidos;
    public $confirmado;

    protected $rules = [
        'cantidad_acompanantes_permitidos' => 'required|integer|min:0',
        'confirmado' => 'boolean',
    ];

    public function mount($id, $invitadoId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->invitado = InvitadoEntregaFest::with('prospecto')->where('entrega_fest_id', $this->evento->id)->findOrFail($invitadoId);

        $this->cantidad_acompanantes_permitidos = $this->invitado->cantidad_acompanantes_permitidos;
        $this->confirmado = $this->invitado->confirmado;
    }

    public function update()
    {
        $this->validate();

        $this->invitado->update([
            'cantidad_acompanantes_permitidos' => $this->cantidad_acompanantes_permitidos,
            'confirmado' => $this->confirmado,
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Actualizado',
            'text' => 'Invitación de ' . ($this->invitado->prospecto->nombre_completo) . ' actualizada.'
        ]);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-invitado-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
