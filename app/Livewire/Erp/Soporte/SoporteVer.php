<?php

namespace App\Livewire\Erp\Soporte;


use App\Models\Erp\Soporte\EstadoSoporte;
use App\Models\Erp\Soporte\Soporte;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Ticket de Soporte')]

class SoporteVer extends Component
{
    public Soporte $soporte;

    public function mount(Soporte $soporte): void
    {
        // Validar permiso de ver soporte
        $this->authorize('view', $soporte);

        $this->soporte = $soporte->load(['tipoSoporte', 'prioridadSoporte', 'estadoSoporte', 'solicitante', 'gestor', 'creador', 'area']);
    }

    public function render()
    {
        return view('livewire.erp.soporte.soporte-ver');
    }

    public function asignarGestor(): void
    {
        $estadoEnProgreso = EstadoSoporte::where('nombre', 'EN_PROGRESO')->first();

        $this->soporte->gestor_id = Auth::id();
        $this->soporte->assigned_at = now();
        $this->soporte->estado_soporte_id = $estadoEnProgreso?->id;
        $this->soporte->save();

        session()->flash('success', 'Te han asignado este ticket.');
        $this->redirectRoute('erp.soporte.vista.todo', navigate: true);
    }

    public function marcarComoResuelto(): void
    {
        $estadoResuelto = EstadoSoporte::where('nombre', 'RESUELTO')->first();

        $this->soporte->estado_soporte_id = $estadoResuelto?->id;
        $this->soporte->resuelto_at = now();
        $this->soporte->save();

        session()->flash('success', 'Ticket marcado como resuelto.');
        $this->redirectRoute('erp.soporte.vista.todo', navigate: true);
    }

    public function cerrar(): void
    {
        $estadoCerrado = EstadoSoporte::where('nombre', 'CERRADO')->first();

        $this->soporte->estado_soporte_id = $estadoCerrado?->id;
        $this->soporte->save();

        session()->flash('success', 'Ticket cerrado.');
        $this->redirectRoute('erp.soporte.vista.todo', navigate: true);
    }
}
