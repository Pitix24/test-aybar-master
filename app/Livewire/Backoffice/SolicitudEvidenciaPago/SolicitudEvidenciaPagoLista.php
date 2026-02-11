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
#[Layout('layouts.erp.layout-erp')]
#[Title('Solicitudes de Evidencia de Pago')]
class SolicitudEvidenciaPagoLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $perPage = 20;

    #[Url]
    public $estado_id = '';

    #[Url]
    public $unidad_negocio_id = '';

    #[Url]
    public $proyecto_id = '';

    #[Url]
    public $admin = '';

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

    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'estado_id',
                'unidad_negocio_id',
                'proyecto_id',
                'admin',
                'fecha_inicio',
                'fecha_fin',
                'tipo_cierre',
                'tiene_validacion',
                'es_asbanc',
                'perPage'
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset([
            'estado_id',
            'unidad_negocio_id',
            'proyecto_id',
            'buscar',
            'admin',
            'fecha_inicio',
            'fecha_fin',
            'tipo_cierre',
            'tiene_validacion',
            'es_asbanc',
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
                $this->admin,
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
        $evidencias = SolicitudEvidenciaPago::query()
            ->with(['unidadNegocio', 'proyecto', 'userCliente.perfilCliente', 'estado', 'gestor'])
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
            ->when($this->admin, function ($q) {
                if ($this->admin === 'sin_asignar') {
                    $q->whereNull('gestor_id');
                } else {
                    $q->where('gestor_id', $this->admin);
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
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $estados = EstadoSolicitudEvidenciaPago::all();
        $empresas = UnidadNegocio::all();
        $proyectos = Proyecto::all();
        $usuarios_admin = User::role(['asesor-atc', 'supervisor-atc'])->get();

        return view('livewire.backoffice.solicitud-evidencia-pago.solicitud-evidencia-pago-lista', [
            'evidencias' => $evidencias,
            'estados' => $estados,
            'empresas' => $empresas,
            'proyectos' => $proyectos,
            'usuarios_admin' => $usuarios_admin,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
