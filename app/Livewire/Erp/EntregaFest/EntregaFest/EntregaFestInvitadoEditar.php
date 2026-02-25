<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
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

    public $cantidad_acompanantes_permitidos = 0;
    public $confirmado = false;
    public $estado_confirmacion;
    public $transporte;
    public $observaciones_asistencia;
    public $codigo_invitado;

    public function mount($id, $invitadoId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->invitado = InvitadoEntregaFest::with(['prospecto.proyecto', 'prospecto.user'])
            ->where('entrega_fest_id', $this->evento->id)
            ->findOrFail($invitadoId);

        $this->cantidad_acompanantes_permitidos = $this->invitado->cantidad_acompanantes_permitidos;
        $this->confirmado = $this->invitado->confirmado;
        $this->estado_confirmacion = $this->invitado->estado_confirmacion;
        $this->transporte = $this->invitado->transporte;
        $this->observaciones_asistencia = $this->invitado->observaciones_asistencia;
        $this->codigo_invitado = $this->invitado->codigo_invitado;
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
