<?php

namespace App\Livewire\Erp\Area;

use App\Models\Area;
use App\Models\TipoSolicitud;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AreaTiposExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class AreaSolicitud extends Component
{
    use WithPagination;

    public Area $area;

    #[Url(as: 'qa')]
    public $searchAgregados = '';

    #[Url(as: 'qd')]
    public $searchDisponibles = '';

    public $perPageDisponibles = 15;

    public function mount($id)
    {
        $this->area = Area::findOrFail($id);
    }

    public function updated($property)
    {
        if (in_array($property, ['searchAgregados', 'searchDisponibles'])) {
            $this->resetPage();
        }
    }

    public function agregarTipo($tipoId)
    {
        $this->area->tiposSolicitud()->syncWithoutDetaching([$tipoId]);
        $this->dispatch('alertaLivewire', ['title' => 'Agregado', 'text' => 'Tipo de solicitud vinculado correctamente.']);
    }

    public function quitarTipo($tipoId)
    {
        $this->area->tiposSolicitud()->detach($tipoId);
        $this->dispatch('alertaLivewire', ['title' => 'Quitado', 'text' => 'Vínculo eliminado correctamente.']);
    }

    public function exportExcel()
    {
        return Excel::download(
            new AreaTiposExport($this->area, $this->searchAgregados),
            'tipos-asignados-' . strtolower($this->area->nombre) . '.xlsx'
        );
    }

    public function render()
    {
        // IDs de tipos ya asignados a esta área
        $idsAgregados = $this->area->tiposSolicitud()->pluck('tipo_solicituds.id')->toArray();

        // Tipos ya asignados (con filtro de búsqueda)
        $tiposAgregados = $this->area->tiposSolicitud()
            ->where('nombre', 'like', '%' . $this->searchAgregados . '%')
            ->orderBy('nombre')
            ->get();

        // Tipos disponibles (no asignados, con filtro de búsqueda)
        $tiposDisponibles = TipoSolicitud::whereNotIn('id', $idsAgregados)
            ->where('nombre', 'like', '%' . $this->searchDisponibles . '%')
            ->orderBy('nombre')
            ->paginate($this->perPageDisponibles, ['*'], 'pageDisponibles');

        return view('livewire.erp.area.area-solicitud', [
            'tiposAgregados' => $tiposAgregados,
            'tiposDisponibles' => $tiposDisponibles,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
