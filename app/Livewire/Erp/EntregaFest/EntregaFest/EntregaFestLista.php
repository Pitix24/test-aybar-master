<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Lista de Entrega Fest')]
class EntregaFestLista extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $buscar = '';

    #[Url(history: true)]
    public $activo = '1';

    #[Url(history: true)]
    public $unidad_negocio_id = '';

    #[Url(history: true)]
    public $proyecto_id = '';

    public $perPage = 10;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'activo', 'unidad_negocio_id', 'proyecto_id', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo', 'unidad_negocio_id', 'proyecto_id']);
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        // Placeholder
    }

    public function exportExcelTodo()
    {
        // Placeholder
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        $unidades = UnidadNegocio::where('activo', true)->get();
        $proyectos = [];
        if ($this->unidad_negocio_id) {
            $proyectos = Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->where('activo', true)->get();
        }

        $eventos = EntregaFest::query()
            ->with(['unidadNegocio', 'proyecto', 'user'])
            ->withCount(['prospectos', 'invitados'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->buscar . '%')
                        ->orWhere('codigo', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->when($this->unidad_negocio_id, function ($query) {
                $query->where('unidad_negocio_id', $this->unidad_negocio_id);
            })
            ->when($this->proyecto_id, function ($query) {
                $query->where('proyecto_id', $this->proyecto_id);
            })
            ->orderBy('fecha_entrega', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-lista', [
            'eventos' => $eventos,
            'unidades' => $unidades,
            'proyectos' => $proyectos,
        ]);
    }
}
