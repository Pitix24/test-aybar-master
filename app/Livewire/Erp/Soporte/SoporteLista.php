<?php

namespace App\Livewire\Erp\Soporte;

use App\Models\Area;
use App\Models\Erp\Soporte\Soporte;
use App\Models\Erp\Soporte\TipoSoporte;
use App\Models\Erp\Soporte\EstadoSoporte;
use App\Models\Erp\Soporte\PrioridadSoporte;
use App\Models\User;
use Carbon\Carbon;
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
    public $estado_id = null;
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
    public array $stats = [];
    public function mount(): void
    {
        $this->gestores = User::query()
            ->where('activo', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $this->cargarStats();
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
            $this->cargarStats();
        }
    }

    public function resetFiltros(): void
    {
        $this->reset(['buscar', 'tipo_id', 'estado_id', 'prioridad_id', 'area_id', 'gestor_id', 'desde', 'hasta']);
        $this->perPage = 20;
        $this->resetPage();
        $this->cargarStats();
    }

    // ── KPI filters ────────────────────────────────────────────────
    public function filterTotal(): void
    {
        $this->reset(['estado_id']);
        $this->resetPage();
        $this->cargarStats();
    }

    public function filterPendientes(): void
    {
        $this->estado_id = 'PENDIENTES';
        $this->resetPage();
        $this->cargarStats();
    }

    public function filterResueltos(): void
    {
        $this->estado_id = optional(
            EstadoSoporte::whereIn('nombre', ['RESUELTO'])->first()
        )->id;
        $this->resetPage();
        $this->cargarStats();
    }

    public function filterNoProcedentes(): void
    {
        $this->estado_id = optional(
            EstadoSoporte::whereIn('nombre', ['NO PROCEDE'])->first()
        )->id;
        $this->resetPage();
        $this->cargarStats();
    }

    public function cargarStats(): void
    {
        $base = Soporte::query()
            ->when($this->buscar !== '', fn($q) => $q->where(
                fn($sub) => $sub->where('codigo', 'like', "%{$this->buscar}%")
                    ->orWhere('titulo', 'like', "%{$this->buscar}%")
            ))
            ->when($this->tipo_id !== null, fn($q) => $q->where('tipo_soporte_id', $this->tipo_id))
            ->when($this->area_id !== null, fn($q) => $q->where('area_id', $this->area_id))
            ->when($this->gestor_id !== '', fn($q) => $q->where('gestor_id', $this->gestor_id))
            ->when($this->prioridad_id !== null, fn($q) => $q->where('prioridad_soporte_id', $this->prioridad_id))
            ->when($this->desde !== '', fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta !== '', fn($q) => $q->whereDate('created_at', '<=', $this->hasta));

        $total = (clone $base)->count();

        $pendienteIds = EstadoSoporte::whereIn('nombre', ['NUEVO', 'ABIERTO', 'EN_PROGRESO', 'EN_REVISION'])->pluck('id');
        $resueltoId   = optional(EstadoSoporte::where('nombre', 'RESUELTO')->first())->id;
        $noProcedentesId    = optional(EstadoSoporte::where('nombre', 'NO PROCEDE')->first())->id;

        $pendientes = (clone $base)->whereIn('estado_soporte_id', $pendienteIds)->count();

        $resueltos = $resueltoId
            ? (clone $base)->where('estado_soporte_id', $resueltoId)->count()
            : 0;

        $noProcedentes = $noProcedentesId
            ? (clone $base)->where('estado_soporte_id', $noProcedentesId)->count()
            : 0;

        $minFecha = Soporte::query()->min('created_at');
        $dias = 1;
        $fechaBasePromedio = null;
        if ($minFecha) {
            $fechaBasePromedio = Carbon::parse($minFecha);
            $dias = $fechaBasePromedio->copy()->startOfDay()
                ->diffInDays(Carbon::now()->startOfDay(), true) + 1;
        }
        $dias = max(1, (int) $dias);
        $promedio = (int) round($total / $dias);

        $this->stats = [
            'total'              => $total,
            'pendientes'         => $pendientes,
            'resueltos'          => $resueltos,
            'no_procedentes'     => $noProcedentes,
            'promedio_por_dia'   => $promedio,
            'dias'               => $dias,
            'fecha_base_promedio'=> $fechaBasePromedio?->format('d/m/Y'),
        ];
    }

    public function render()
    {
        // Validar permiso de listar soportes
        $this->authorize('viewAny', Soporte::class);

        $soportes = Soporte::query()
            ->with(['solicitante', 'gestor', 'tipoSoporte', 'prioridadSoporte', 'estadoSoporte', 'area'])
            ->when(
                $this->buscar !== '',
                fn($q) =>
                $q->where(
                    fn($sub) =>
                    $sub->where('codigo', 'like', "%{$this->buscar}%")
                        ->orWhere('titulo', 'like', "%{$this->buscar}%")
                )
            )
            ->when($this->tipo_id !== null, fn($q) => $q->where('tipo_soporte_id', $this->tipo_id))
            ->when($this->estado_id !== null, function ($q) {
                if ($this->estado_id === 'PENDIENTES') {
                    $pendienteIds = EstadoSoporte::whereIn('nombre', ['NUEVO', 'ABIERTO', 'EN_PROGRESO', 'EN_REVISION'])->pluck('id');
                    return $q->whereIn('estado_soporte_id', $pendienteIds);
                }
                return $q->where('estado_soporte_id', $this->estado_id);
            })
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
        $estadoNoProcede = \App\Models\Erp\Soporte\EstadoSoporte::where('nombre', 'NO PROCEDE')->first();
        $ticket->update(['estado_soporte_id' => $estadoNoProcede?->id]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'No Procede',
            'text' => 'El ticket fue marcado como NO PROCEDE.',
        ]);

        $this->js('window.location.reload()');
    }
}
