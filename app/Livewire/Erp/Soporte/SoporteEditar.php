<?php

namespace App\Livewire\Erp\Soporte;

use App\Models\Area;
use App\Models\User;
use App\Models\Erp\Soporte\EstadoSoporte;
use App\Models\Erp\Soporte\PrioridadSoporte;
use App\Models\Erp\Soporte\Soporte;
use App\Models\Erp\Soporte\TipoSoporte;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Ticket de Soporte')]

class SoporteEditar extends Component
{
    public Soporte $soporte;
    public ?int $tipo_soporte_id = null;
    public ?int $prioridad_soporte_id = null;
    public ?int $estado_soporte_id = null;
    public ?int $area_id = null;
    public ?int $solicitante_id = null;
    public ?int $gestor_id = null;
    public ?string $titulo = null;
    public ?string $descripcion = null;
    public ?string $observaciones = null;

    public function mount(Soporte $soporte): void
    {
        // Validar permiso de editar soporte
        $this->authorize('update', $soporte);

        $this->soporte = $soporte;
        $this->tipo_soporte_id = $soporte->tipo_soporte_id;
        $this->prioridad_soporte_id = $soporte->prioridad_soporte_id;
        $this->estado_soporte_id = $soporte->estado_soporte_id;
        $this->area_id = $soporte->area_id;
        $this->solicitante_id = $soporte->solicitante_id;
        $this->gestor_id = $soporte->gestor_id;
        $this->titulo = $soporte->titulo;
        $this->descripcion = $soporte->descripcion;
        $this->observaciones = $soporte->observaciones;
    }

    public function render()
    {
        $solicitantes = User::where('activo', true)->orderBy('name')->get(['id', 'name']);

        $gestoresQuery = User::where('activo', true);
        if ($this->area_id) {
            $gestoresQuery->whereHas('areas', function ($q) {
                $q->where('area_id', $this->area_id);
            });
        }
        $gestores = $gestoresQuery->orderBy('name')->get(['id', 'name']);

        return view('livewire.erp.soporte.soporte-editar', [
            'tipos' => TipoSoporte::where('activo', true)->get(),
            'prioridades' => PrioridadSoporte::where('activo', true)->get(),
            'estados' => EstadoSoporte::where('activo', true)->get(),
            'areas' => Area::where('activo', true)->orderBy('nombre')->get(['id', 'nombre']),
            'solicitantes' => $solicitantes,
            'gestores' => $gestores,
        ]);
    }

    public function rules(): array
    {
        return [
            'tipo_soporte_id' => 'required|exists:tipo_soportes,id',
            'prioridad_soporte_id' => 'required|exists:prioridad_soportes,id',
            'estado_soporte_id' => 'required|exists:estado_soportes,id',
            'area_id' => 'nullable|exists:areas,id',
            'solicitante_id' => 'nullable|exists:users,id',
            'gestor_id' => 'nullable|exists:users,id',
            'titulo' => 'required|string|min:3|max:255',
            'descripcion' => 'required|string|min:10',
            'observaciones' => 'nullable|string|max:2000',
        ];
    }

    public function guardar(): void
    {
        $this->validate();
        $this->soporte->tipo_soporte_id = $this->tipo_soporte_id;
        $this->soporte->prioridad_soporte_id = $this->prioridad_soporte_id;
        $this->soporte->estado_soporte_id = $this->estado_soporte_id;
        $this->soporte->area_id = $this->area_id;
        $this->soporte->solicitante_id = $this->solicitante_id;
        $this->soporte->gestor_id = $this->gestor_id;
        $this->soporte->titulo = $this->titulo;
        $this->soporte->descripcion = $this->descripcion;
        $this->soporte->observaciones = $this->observaciones;
        $this->soporte->save();

        session()->flash('success', 'Ticket actualizado correctamente.');
        $this->redirectRoute('erp.soporte.vista.todo', navigate: true);
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

    public function marcarResuelto(): void
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
