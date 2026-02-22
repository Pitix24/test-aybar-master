<?php

namespace App\Livewire\Erp\Cita\Cita;

use App\Models\Cita;
use App\Models\UnidadNegocio;
use App\Models\Proyecto;
use App\Models\Area;
use App\Models\EstadoCita;
use App\Models\MotivoCita;
use App\Models\Sede;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Cita\CitaExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Citas')]
class CitaLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $unidad_negocio_id = '';

    #[Url]
    public $proyecto_id = '';

    #[Url]
    public $sede_id = '';

    #[Url]
    public $motivo_cita_id = '';

    #[Url]
    public $estado_cita_id = '';

    #[Url]
    public $gestor_id = '';

    #[Url]
    public $area_id = '';

    #[Url]
    public $fecha_inicio = '';

    #[Url]
    public $fecha_fin = '';

    #[Url]
    public $perPage = 20;

    public $unidades = [];
    public $proyectos = [];
    public $sedes = [];
    public $motivos = [];
    public $estados = [];
    public $areas = [];
    public $gestores = [];

    public function mount()
    {
        $this->unidades = UnidadNegocio::all();
        $this->sedes = Sede::all();
        $this->motivos = MotivoCita::all();
        $this->estados = EstadoCita::all();
        $this->areas = Area::all();
        $this->gestores = User::role(['asesor-atc', 'supervisor-atc'])->get();

        if ($this->unidad_negocio_id) {
            $this->loadProyectos();
        }
    }

    public function updatedUnidadNegocioId($value)
    {
        $this->proyecto_id = '';
        $this->proyectos = [];
        if ($value) {
            $this->loadProyectos();
        }
    }

    public function loadProyectos()
    {
        if ($this->unidad_negocio_id) {
            $this->proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->get();
        }
    }

    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'unidad_negocio_id',
                'proyecto_id',
                'sede_id',
                'motivo_cita_id',
                'estado_cita_id',
                'gestor_id',
                'area_id',
                'fecha_inicio',
                'fecha_fin',
                'perPage'
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset([
            'buscar',
            'unidad_negocio_id',
            'proyecto_id',
            'sede_id',
            'motivo_cita_id',
            'estado_cita_id',
            'gestor_id',
            'area_id',
            'fecha_inicio',
            'fecha_fin'
        ]);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('cita.exportar-filtro');

        return Excel::download(
            new CitaExport(
                $this->buscar,
                $this->unidad_negocio_id,
                $this->proyecto_id,
                $this->sede_id,
                $this->motivo_cita_id,
                $this->estado_cita_id,
                $this->gestor_id,
                $this->area_id,
                $this->fecha_inicio,
                $this->fecha_fin,
                false,
                $this->perPage,
                $this->getPage()
            ),
            'citas_filtradas.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('cita.exportar-todo');

        return Excel::download(
            new CitaExport(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $this->fecha_inicio,
                $this->fecha_fin,
                true
            ),
            'citas_todo.xlsx'
        );
    }

    public function render()
    {
        $items = Cita::query()
            ->with(['unidadNegocio', 'proyecto', 'sede', 'motivo', 'estado', 'gestor', 'area'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', "%{$this->buscar}%")
                        ->orWhere('dni', 'like', "%{$this->buscar}%")
                        ->orWhere('nombres', 'like', "%{$this->buscar}%")
                        ->orWhere('asunto_solicitud', 'like', "%{$this->buscar}%");
                });
            })
            ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->sede_id, fn($q) => $q->where('sede_id', $this->sede_id))
            ->when($this->motivo_cita_id, fn($q) => $q->where('motivo_cita_id', $this->motivo_cita_id))
            ->when($this->gestor_id, fn($q) => $q->where('gestor_id', $this->gestor_id))
            ->when($this->estado_cita_id, fn($q) => $q->where('estado_cita_id', $this->estado_cita_id))
            ->when($this->area_id, fn($q) => $q->where('area_id', $this->area_id))
            ->when($this->fecha_inicio, fn($q) => $q->whereDate('fecha_inicio', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->whereDate('fecha_inicio', '<=', $this->fecha_fin))
            ->orderBy('fecha_inicio', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.cita.cita.cita-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
