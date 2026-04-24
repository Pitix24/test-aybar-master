<?php

namespace App\Livewire\Erp\Marketing\AvanceProyecto;

use App\Models\AvanceProyecto;
use App\Models\UnidadNegocio;
use App\Models\GrupoProyecto;
use App\Models\Proyecto;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Lista de Avances de Proyectos')]
class AvanceProyectoLista extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $buscar = '';

    #[Url(history: true)]
    public $unidad_id = '';

    #[Url(history: true)]
    public $grupo_id = '';

    #[Url(history: true)]
    public $proyecto_id = '';

    #[Url(history: true)]
    public $activo = '';

    public $perPage = 20;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'unidad_id', 'grupo_id', 'proyecto_id', 'activo', 'perPage'])) {
            $this->resetPage();
        }

        if ($property == 'unidad_id') {
            $this->grupo_id = '';
            $this->proyecto_id = '';
        }

        if ($property == 'grupo_id') {
            $this->proyecto_id = '';
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'unidad_id', 'grupo_id', 'proyecto_id', 'activo', 'perPage']);
        $this->resetPage();
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function toggleActivo($id)
    {
        $this->authorize('avance-proyecto.editar');

        try {
            $item = AvanceProyecto::findOrFail($id);
            $item->update(['activo' => !$item->activo]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Éxito',
                'text' => 'Estado actualizado correctamente.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el estado.'
            ]);
        }
    }

    public function render()
    {
        $this->authorize('avance-proyecto.lista');

        $unidades = UnidadNegocio::where('activo', true)->get();
        $grupos = GrupoProyecto::where('activo', true)->get();

        $proyectos = Proyecto::query()
            ->where('activo', true)
            ->when($this->unidad_id, function ($query) {
                $query->where('unidad_negocio_id', $this->unidad_id);
            })
            ->when($this->grupo_id, function ($query) {
                $query->where('grupo_proyecto_id', $this->grupo_id);
            })
            ->get();

        $items = AvanceProyecto::query()
            ->with(['unidadNegocio', 'grupoProyecto', 'proyecto'])
            ->when($this->buscar, function ($query) {
                $query->where('titulo', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->unidad_id, function ($query) {
                $query->where('unidad_negocio_id', $this->unidad_id);
            })
            ->when($this->grupo_id, function ($query) {
                $query->where('grupo_proyecto_id', $this->grupo_id);
            })
            ->when($this->proyecto_id, function ($query) {
                $query->where('proyecto_id', $this->proyecto_id);
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->orderBy('orden')
            ->paginate($this->perPage);

        return view('livewire.erp.marketing.avance-proyecto.avance-proyecto-lista', [
            'items' => $items,
            'unidades' => $unidades,
            'grupos' => $grupos,
            'proyectos' => $proyectos,
        ]);
    }
}
