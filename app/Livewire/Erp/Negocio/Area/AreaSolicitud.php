<?php

namespace App\Livewire\Erp\Negocio\Area;

use App\Models\Area;
use App\Models\TipoSolicitud;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Negocio\AreaTiposExport;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Tipos de Solicitud')]
class AreaSolicitud extends Component
{
    use WithPagination;

    public Area $area;

    #[Url(as: 'qa')]
    public $searchAgregados = '';

    #[Url(as: 'qd')]
    public $searchDisponibles = '';

    public $perPageAgregados = 15;
    public $perPageDisponibles = 15;

    public function mount($id)
    {
        $this->area = Area::findOrFail($id);
    }

    public function updated($property)
    {
        if ($property === 'searchAgregados' || $property === 'perPageAgregados') {
            $this->resetPage('pageAgregados');
        }
        if ($property === 'searchDisponibles' || $property === 'perPageDisponibles') {
            $this->resetPage('pageDisponibles');
        }
    }

    public function resetFiltrosAgregados()
    {
        $this->reset('searchAgregados');
        $this->resetPage('pageAgregados');
    }

    public function resetFiltrosDisponibles()
    {
        $this->reset('searchDisponibles');
        $this->resetPage('pageDisponibles');
    }

    public function agregarTipo($tipoId)
    {
        $this->authorize('area.agregar-solicitudes');

        try {
            DB::beginTransaction();

            $this->area->tiposSolicitud()->syncWithoutDetaching([$tipoId]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Agregado',
                'text' => 'Tipo de solicitud vinculado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('area')->error("[AREA SOLICITUD] Error al agregar tipo: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'area_id' => $this->area->id,
                'tipo_id' => $tipoId,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo vincular el tipo de solicitud.'
            ]);
        }
    }

    public function quitarTipo($tipoId)
    {
        $this->authorize('area.eliminar-solicitudes');

        try {
            DB::beginTransaction();

            $this->area->tiposSolicitud()->detach($tipoId);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Quitado',
                'text' => 'Vínculo eliminado correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('area')->error("[AREA SOLICITUD] Error al quitar tipo: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'area_id' => $this->area->id,
                'tipo_id' => $tipoId,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el vínculo.'
            ]);
        }
    }

    public function exportExcel()
    {
        $this->authorize('area.exportar-solicitudes');

        return Excel::download(
            new AreaTiposExport($this->area, $this->searchAgregados),
            'tipos-asignados-' . strtolower($this->area->nombre) . '.xlsx'
        );
    }

    public function render()
    {
        // Tipos ya asignados
        $tiposAgregados = $this->area->tiposSolicitud()
            ->where('nombre', 'like', '%' . $this->searchAgregados . '%')
            ->orderBy('nombre')
            ->paginate($this->perPageAgregados, ['*'], 'pageAgregados');

        $idsAgregados = $this->area->tiposSolicitud()->pluck('tipo_solicituds.id')->toArray();

        // Tipos disponibles (no asignados)
        $tiposDisponibles = TipoSolicitud::whereNotIn('id', $idsAgregados)
            ->where('nombre', 'like', '%' . $this->searchDisponibles . '%')
            ->orderBy('nombre')
            ->paginate($this->perPageDisponibles, ['*'], 'pageDisponibles');

        return view('livewire.erp.negocio.area.area-solicitud', [
            'tiposAgregados' => $tiposAgregados,
            'tiposDisponibles' => $tiposDisponibles,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
