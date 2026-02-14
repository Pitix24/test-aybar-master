<?php

namespace App\Livewire\Erp\Letra\SolicitudDigitalizarLetra;

use App\Models\EstadoSolicitudDigitalizarLetra;
use App\Models\Proyecto;
use App\Models\SolicitudDigitalizarLetra;
use App\Models\UnidadNegocio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\SolicitudDigitalizarLetraExport;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Solicitudes de Letras Digitales')]
class SolicitudDigitalizarLetraLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $estado_id = '';

    #[Url]
    public $unidad_negocio_id = '';

    #[Url]
    public $proyecto_id = '';

    #[Url]
    public $fecha_inicio = '';

    #[Url]
    public $fecha_fin = '';

    #[Url]
    public $perPage = 20;

    public $estados = [];
    public $unidades_negocios = [];
    public $proyectos = [];

    public function mount()
    {
        $this->estados = EstadoSolicitudDigitalizarLetra::all();
        $this->unidades_negocios = UnidadNegocio::all();

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
                'estado_id',
                'unidad_negocio_id',
                'proyecto_id',
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
            'estado_id',
            'unidad_negocio_id',
            'proyecto_id',
            'fecha_inicio',
            'fecha_fin',
        ]);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        abort_unless(auth()->user()->can('solicitud-digitalizar-letra.exportar'), 403);

        $nombreArchivo = 'solicitudes_letras_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new SolicitudDigitalizarLetraExport(
            $this->buscar,
            $this->estado_id,
            $this->unidad_negocio_id,
            $this->proyecto_id,
            $this->fecha_inicio,
            $this->fecha_fin
        ), $nombreArchivo);
    }

    public function render()
    {
        $items = SolicitudDigitalizarLetra::query()
            ->with(['unidadNegocio', 'proyecto', 'userCliente.perfilCliente', 'estado'])
            ->when($this->buscar, function ($q) {
                $buscar = $this->buscar;
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('id', 'like', "%{$buscar}%")
                        ->orWhere('codigo_cliente', 'like', "%{$buscar}%")
                        ->orWhere('codigo_cuota', 'like', "%{$buscar}%")
                        ->orWhereHas('userCliente', function ($qUser) use ($buscar) {
                            $qUser->where('name', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('userCliente.perfilCliente', function ($qCliente) use ($buscar) {
                            $qCliente->where('dni', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($this->estado_id, fn($q) => $q->where('estado_solicitud_digitalizar_letra_id', $this->estado_id))
            ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->fecha_inicio, fn($q) => $q->whereDate('created_at', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->whereDate('created_at', '<=', $this->fecha_fin))
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.letra.solicitud-digitalizar-letra.solicitud-digitalizar-letra-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
