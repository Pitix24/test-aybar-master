<?php

namespace App\Livewire\Erp\Atc\SubTipoSolicitud;

use App\Models\SubTipoSolicitud;
use App\Models\TipoSolicitud;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Atc\SubTipoSolicitudExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Sub Tipos de Solicitud')]
class SubTipoSolicitudLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $tipo_solicitud_id = '';

    #[Url]
    public $activo = '';

    #[Url]
    public $perPage = 20;

    #[Url]
    public $desde = '';

    #[Url]
    public $hasta = '';

    public $tipos_solicitud = [];

    public function mount()
    {
        $this->tipos_solicitud = TipoSolicitud::select('id', 'nombre')->orderBy('nombre')->get();
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'tipo_solicitud_id', 'activo', 'perPage', 'desde', 'hasta'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'tipo_solicitud_id', 'activo', 'desde', 'hasta']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('sub-tipo-solicitud.exportar-filtro');

        return Excel::download(
            new SubTipoSolicitudExport(
                buscar: $this->buscar,
                tipo_solicitud_id: $this->tipo_solicitud_id,
                activo: $this->activo,
                perPage: $this->perPage,
                page: $this->getPage(),
                desde: $this->desde,
                hasta: $this->hasta,
                todo: false
            ),
            'sub_tipos_solicitud_filtrados_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('sub-tipo-solicitud.exportar-todo');

        return Excel::download(
            new SubTipoSolicitudExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'sub_tipos_solicitud_total_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = SubTipoSolicitud::query()
            ->with(['tipoSolicitud:id,nombre'])
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->buscar}%");
                    if (is_numeric($this->buscar)) {
                        $sub->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->tipo_solicitud_id !== '', fn($q) => $q->where('tipo_solicitud_id', $this->tipo_solicitud_id))
            ->when($this->activo !== '', fn($q) => $q->where('activo', $this->activo))
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.atc.sub-tipo-solicitud.sub-tipo-solicitud-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
