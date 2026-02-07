<?php

namespace App\Livewire\Atc\SubTipoSolicitud;

use App\Models\SubTipoSolicitud;
use App\Models\TipoSolicitud;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SubTipoSolicitudExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Sub Tipo de Solicitud')]
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

    public $tipos_solicitud = [];

    public function mount()
    {
        $this->tipos_solicitud = TipoSolicitud::select('id', 'nombre')->orderBy('nombre')->get();
    }

    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'tipo_solicitud_id',
                'activo',
                'perPage'
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'tipo_solicitud_id', 'activo']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(
            new SubTipoSolicitudExport(
                $this->buscar,
                $this->tipo_solicitud_id !== '' ? (int) $this->tipo_solicitud_id : null,
                $this->activo,
                $this->perPage,
                $this->getPage()
            ),
            'sub-tipo-solicitudes.xlsx'
        );
    }

    public function render()
    {
        $items = SubTipoSolicitud::query()
            ->with(['tipoSolicitud:id,nombre'])
            ->when($this->buscar !== '', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                    ->orWhere('id', $this->buscar);
            })
            ->when(
                $this->tipo_solicitud_id !== '',
                fn($q) =>
                $q->where('tipo_solicitud_id', $this->tipo_solicitud_id)
            )
            ->when(
                $this->activo !== '',
                fn($q) =>
                $q->where('activo', $this->activo)
            )
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.atc.sub-tipo-solicitud.sub-tipo-solicitud-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
