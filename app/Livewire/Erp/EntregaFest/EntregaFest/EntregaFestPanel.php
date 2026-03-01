<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Panel de Gestión - Entrega Fest')]
class EntregaFestPanel extends Component
{
    public EntregaFest $evento;

    // Contadores
    public int $totalProspectos = 0;
    public int $aprobados = 0;
    public int $totalInvitados = 0;
    public int $confirmados = 0;
    public int $asistentes = 0;
    public int $totalIncidencias = 0;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);

        $this->totalProspectos = $this->evento->prospectos()->count();
        $this->aprobados = $this->evento->prospectos()->where('estado_backoffice', 'aprobado')->count();
        $this->totalInvitados = $this->evento->invitados()->count();
        $this->confirmados = $this->evento->invitados()->where('confirmado', true)->count();
        $this->asistentes = $this->evento->invitados()->where('estado_confirmacion', 'confirmado')->count();
        $this->totalIncidencias = $this->evento->incidencias()->where('estado', 'Abierta')->count();
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-panel');
    }
}
