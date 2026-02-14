<?php

namespace App\Livewire\Erp\Backoffice\EstadoSolicitudEvidenciaPago;

use App\Models\EstadoSolicitudEvidenciaPago;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EstadoSolicitudEvidenciaPagoExport;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Estado de Solicitud de Evidencia de Pago')]
class EstadoSolicitudEvidenciaPagoLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $activo = '';

    #[Url]
    public $perPage = 20;

    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'activo',
                'perPage'
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        abort_unless(auth()->user()->can('estado-solicitud-evidencia-pago.exportar'), 403);
        return Excel::download(
            new EstadoSolicitudEvidenciaPagoExport(
                $this->buscar,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'estado-solicitud-evidencia-pagos.xlsx'
        );
    }

    public function render()
    {
        $items = EstadoSolicitudEvidenciaPago::query()
            ->when($this->buscar !== '', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                    ->orWhere('id', $this->buscar);
            })
            ->when(
                $this->activo !== '',
                fn($q) =>
                $q->where('activo', $this->activo)
            )
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.erp.backoffice.estado-solicitud-evidencia-pago.estado-solicitud-evidencia-pago-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
