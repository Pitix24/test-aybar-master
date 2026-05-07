<?php

namespace App\Livewire\Erp\Soporte;

use App\Models\Area;
use App\Models\Erp\Soporte\Soporte;
use App\Models\Erp\Soporte\TipoSoporte;
use App\Models\Erp\Soporte\EstadoSoporte;
use App\Models\Erp\Soporte\PrioridadSoporte;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Tickets de Soporte')]
class SoporteLista extends Component
{
    use WithPagination;
    #[Url(as: 'q')]
    public string $buscar = '';
    #[Url]
    public ?int $tipo_id = null;
    #[Url]
    public ?int $estado_id = null;
    #[Url]
    public ?int $prioridad_id = null;
    #[Url]
    public ?int $area_id = null;
    #[Url]
    public string $gestor_id = '';
    #[Url]
    public string $desde = '';
    #[Url]
    public string $hasta = '';
    #[Url]
    public int $perPage = 20;
    public $gestores = [];
    public function mount(): void
    {
        $this->gestores = User::query()
            ->where('activo', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function updated(string $property): void
    {
        if (
            in_array($property, [
                'buscar',
                'tipo_id',
                'estado_id',
                'prioridad_id',
                'area_id',
                'gestor_id',
                'desde',
                'hasta',
                'perPage',
            ], true)
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros(): void
    {
        $this->reset(['buscar', 'tipo_id', 'estado_id', 'prioridad_id', 'area_id', 'gestor_id', 'desde', 'hasta']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function render()
    {
        $soportes = Soporte::query()
            ->with(['solicitante', 'gestor', 'tipoSoporte', 'prioridadSoporte', 'estadoSoporte', 'area'])
            ->when($this->buscar !== '', fn($q) =>
                $q->where(fn($sub) =>
                    $sub->where('codigo', 'like', "%{$this->buscar}%")
                        ->orWhere('titulo', 'like', "%{$this->buscar}%")
                )
            )
            ->when($this->tipo_id !== null, fn($q) => $q->where('tipo_soporte_id', $this->tipo_id))
            ->when($this->estado_id !== null, fn($q) => $q->where('estado_soporte_id', $this->estado_id))
            ->when($this->prioridad_id !== null, fn($q) => $q->where('prioridad_soporte_id', $this->prioridad_id))
            ->when($this->area_id !== null, fn($q) => $q->where('area_id', $this->area_id))
            ->when($this->gestor_id !== '', fn($q) => $q->where('gestor_id', $this->gestor_id))
            ->when($this->desde !== '', fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta !== '', fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->latest()
            ->paginate($this->perPage);

        $tipos = TipoSoporte::where('activo', true)->get(['id', 'nombre']);
        $estados = EstadoSoporte::where('activo', true)->get(['id', 'nombre']);
        $prioridades = PrioridadSoporte::where('activo', true)->get(['id', 'nombre']);
        $areas = Area::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']);

        return view('livewire.erp.soporte.soporte-lista', compact('soportes', 'tipos', 'estados', 'prioridades', 'areas'));
    }

    public function asignarTicket(int $id): void
    {
        $ticket = Soporte::findOrFail($id);
        $estadoEnProgreso = \App\Models\Erp\Soporte\EstadoSoporte::where('nombre', 'EN_PROGRESO')->first();
        $ticket->update([
            'gestor_id' => Auth::id(),
            'assigned_at' => now(),
            'estado_soporte_id' => $estadoEnProgreso?->id,
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Asignado',
            'text' => 'El ticket te fue asignado correctamente.',
        ]);

        $this->js('window.location.reload()');
    }

    public function marcarComoResuelto(int $id): void
    {
        $ticket = Soporte::findOrFail($id);
        $estadoResuelto = \App\Models\Erp\Soporte\EstadoSoporte::where('nombre', 'RESUELTO')->first();
        $ticket->update(['estado_soporte_id' => $estadoResuelto?->id]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Resuelto',
            'text' => 'El ticket fue marcado como resuelto.',
        ]);

        $this->js('window.location.reload()');
    }

    public function cerrarTicket(int $id): void
    {
        $ticket = Soporte::findOrFail($id);
        $estadoCerrado = \App\Models\Erp\Soporte\EstadoSoporte::where('nombre', 'CERRADO')->first();
        $ticket->update(['estado_soporte_id' => $estadoCerrado?->id]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Cerrado',
            'text' => 'El ticket fue cerrado.',
        ]);

        $this->js('window.location.reload()');
    }
}
