<?php

namespace App\Livewire\Erp\Backoffice\EvidenciaPagoAntiguo;

use App\Models\EstadoSolicitudEvidenciaPago;
use App\Models\EvidenciaPagoAntiguo;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Exports\Erp\Backoffice\EvidenciaPagoAntiguoExport;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Evidencias de Pago Stock')]
class EvidenciaPagoAntiguoLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(as: 'lote')]
    public $buscar_lote = '';

    #[Url]
    public $perPage = 20;

    #[Url]
    public $estado_id = '';

    #[Url]
    public $unidad_negocio_id = '';

    #[Url]
    public $proyecto_id = '';

    #[Url]
    public $tiene_fecha_deposito = '';

    #[Url]
    public $tiene_imagen = '';

    #[Url]
    public $tiene_numero_operacion = '';

    #[Url]
    public $tiene_codigo_cuenta = '';

    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'buscar_lote',
                'estado_id',
                'unidad_negocio_id',
                'proyecto_id',
                'tiene_fecha_deposito',
                'tiene_imagen',
                'tiene_numero_operacion',
                'tiene_codigo_cuenta',
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
            'buscar_lote',
            'tiene_fecha_deposito',
            'tiene_imagen',
            'tiene_numero_operacion',
            'tiene_codigo_cuenta',
        ]);

        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        $this->authorize('evidencia-pago-antiguo.exportar-filtro');

        return Excel::download(
            new EvidenciaPagoAntiguoExport(
                $this->buscar,
                $this->buscar_lote,
                $this->unidad_negocio_id,
                $this->proyecto_id,
                $this->estado_id,
                $this->tiene_fecha_deposito,
                $this->tiene_imagen,
                $this->tiene_numero_operacion,
                $this->tiene_codigo_cuenta,
                $this->perPage,
                $this->getPage(),
            ),
            'evidencia_pago_antiguo_stock.xlsx'
        );
    }

    public function render()
    {
        $evidencias = EvidenciaPagoAntiguo::query()
            ->with(['unidadNegocio', 'proyecto', 'estado'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', "%{$this->buscar}%")
                        ->orWhere('codigo_cliente', 'like', "%{$this->buscar}%")
                        ->orWhere('nombres_cliente', 'like', "%{$this->buscar}%");
                });
            })
            ->when($this->buscar_lote, function ($query, $buscar_lote) {
                $query->where('lote', 'like', "%{$buscar_lote}%");
            })
            ->when($this->estado_id, function ($q) {
                $q->where('estado_solicitud_evidencia_pago_id', $this->estado_id);
            })
            ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->tiene_fecha_deposito !== '', function ($q) {
                if ($this->tiene_fecha_deposito === 'si') {
                    $q->whereNotNull('fecha_deposito');
                }

                if ($this->tiene_fecha_deposito === 'no') {
                    $q->whereNull('fecha_deposito');
                }
            })
            ->when($this->tiene_imagen !== '', function ($q) {
                if ($this->tiene_imagen === 'si') {
                    $q->whereNotNull('imagen_url');
                }

                if ($this->tiene_imagen === 'no') {
                    $q->whereNull('imagen_url');
                }
            })
            ->when($this->tiene_numero_operacion !== '', function ($q) {
                if ($this->tiene_numero_operacion === 'si') {
                    $q->whereNotNull('operacion_numero');
                }

                if ($this->tiene_numero_operacion === 'no') {
                    $q->whereNull('operacion_numero');
                }
            })
            ->when($this->tiene_codigo_cuenta !== '', function ($q) {
                if ($this->tiene_codigo_cuenta === 'si') {
                    $q->whereNotNull('codigo_cuenta');
                }

                if ($this->tiene_codigo_cuenta === 'no') {
                    $q->whereNull('codigo_cuenta');
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $estados = EstadoSolicitudEvidenciaPago::all();
        $empresas = UnidadNegocio::all();
        $proyectos = Proyecto::all();

        return view('livewire.erp.backoffice.evidencia-pago-antiguo.evidencia-pago-antiguo-lista', [
            'evidencias' => $evidencias,
            'estados' => $estados,
            'empresas' => $empresas,
            'proyectos' => $proyectos,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
