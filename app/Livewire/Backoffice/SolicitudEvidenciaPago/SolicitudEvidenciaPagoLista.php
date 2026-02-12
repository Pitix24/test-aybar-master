<?php

namespace App\Livewire\Backoffice\SolicitudEvidenciaPago;

use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\Proyecto;
use App\Models\SolicitudEvidenciaPago;
use App\Models\UnidadNegocio;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SolicitudEvidenciaPagoExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Solicitudes de Evidencia de Pago')]
class SolicitudEvidenciaPagoLista extends Component
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
    public $gestor_id = '';

    #[Url]
    public $fecha_inicio = '';

    #[Url]
    public $fecha_fin = '';

    #[Url]
    public $tipo_cierre = '';

    #[Url]
    public $tiene_validacion = '';

    #[Url]
    public $es_asbanc = '';

    #[Url]
    public $cantidad_evidencias = '';

    #[Url]
    public $cantidad_correos = '';

    #[Url]
    public $perPage = 20;

    public $estados = [];
    public $unidades_negocios = [];
    public $proyectos = [];
    public $usuarios_admin = [];

    public function mount()
    {
        $this->estados = EstadoSolicitudEvidenciaPago::all();
        $this->unidades_negocios = UnidadNegocio::all();
        $this->usuarios_admin = User::role(['asesor-atc', 'supervisor-atc'])->get();

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
        if (!is_null($this->unidad_negocio_id)) {
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
                'gestor_id',
                'fecha_inicio',
                'fecha_fin',
                'tipo_cierre',
                'tiene_validacion',
                'es_asbanc',
                'cantidad_evidencias',
                'cantidad_correos',
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
            'gestor_id',
            'fecha_inicio',
            'fecha_fin',
            'tipo_cierre',
            'tiene_validacion',
            'es_asbanc',
            'cantidad_evidencias',
            'cantidad_correos',
        ]);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        abort_unless(auth()->user()->can('solicitud-evidencia-pago.exportar'), 403);

        return Excel::download(
            new SolicitudEvidenciaPagoExport(
                $this->buscar,
                $this->unidad_negocio_id,
                $this->proyecto_id,
                $this->gestor_id,
                $this->estado_id,
                $this->fecha_inicio,
                $this->fecha_fin,
                $this->tipo_cierre,
                $this->tiene_validacion,
                $this->es_asbanc,
                $this->perPage,
                $this->getPage()
            ),
            'solicitudes-evidencia-pago.xlsx'
        );
    }

    public function render()
    {
        $query = SolicitudEvidenciaPago::query()
            ->with(['unidadNegocio', 'proyecto', 'userCliente.perfilCliente', 'estado', 'gestor'])
            ->withCount(['evidencias', 'correos'])
            ->when($this->buscar, function ($q) {
                $buscar = $this->buscar;
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('id', 'like', "%{$buscar}%")
                        ->orWhereHas('userCliente', function ($qUser) use ($buscar) {
                            $qUser->where('name', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('userCliente.perfilCliente', function ($qCliente) use ($buscar) {
                            $qCliente->where('dni', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($this->estado_id, function ($q) {
                $q->where('estado_solicitud_evidencia_pago_id', $this->estado_id);
            })
            ->when($this->gestor_id, function ($q) {
                if ($this->gestor_id === 'sin_asignar') {
                    $q->whereNull('gestor_id');
                } else {
                    $q->where('gestor_id', $this->gestor_id);
                }
            })
            ->when($this->unidad_negocio_id, function ($q) {
                $q->where('unidad_negocio_id', $this->unidad_negocio_id);
            })
            ->when($this->proyecto_id, function ($q) {
                $q->where('proyecto_id', $this->proyecto_id);
            })
            ->when($this->fecha_inicio, function ($q) {
                $q->whereDate('created_at', '>=', $this->fecha_inicio);
            })
            ->when($this->fecha_fin, function ($q) {
                $q->whereDate('created_at', '<=', $this->fecha_fin);
            })
            ->when($this->tipo_cierre, function ($q) {
                if ($this->tipo_cierre === 'api') {
                    $q->where('slin_evidencia', true);
                }
                if ($this->tipo_cierre === 'manual') {
                    $q->where('resuelto_manual', true);
                }
            })
            ->when($this->tiene_validacion !== '', function ($q) {
                if ($this->tiene_validacion === 'si') {
                    $q->whereNotNull('fecha_validacion');
                }
                if ($this->tiene_validacion === 'no') {
                    $q->whereNull('fecha_validacion');
                }
            })
            ->when($this->es_asbanc !== '', function ($q) {
                if ($this->es_asbanc === 'si') {
                    $q->where('slin_asbanc', true);
                }
                if ($this->es_asbanc === 'no') {
                    $q->where('slin_asbanc', false);
                }
            });

        $items = $query->when($this->cantidad_evidencias !== '', function ($q) {
            $q->has('evidencias', '=', $this->cantidad_evidencias);
        })
            ->when($this->cantidad_correos !== '', function ($q) {
                $q->has('correos', '=', $this->cantidad_correos);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.backoffice.solicitud-evidencia-pago.solicitud-evidencia-pago-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
