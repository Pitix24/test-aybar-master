<?php

namespace App\Livewire\Erp\Marketing\Reglamento;

use App\Models\Reglamento;
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
#[Title('Lista de Reglamentos')]
class ReglamentoLista extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $buscar = '';

    #[Url(history: true)]
    public $proyecto_id = '';

    #[Url(history: true)]
    public $activo = '';

    public $perPage = 20;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'proyecto_id', 'activo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'proyecto_id', 'activo', 'perPage']);
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
        $this->authorize('reglamento.editar');

        try {
            $item = Reglamento::findOrFail($id);
            $item->update(['activo' => !$item->activo]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Éxito',
                'text' => 'Estado actualizado correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::channel('reglamento')->error("[REGLAMENTO] Error en toggleActivo: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'reglamento_id' => $id,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el estado.'
            ]);
        }
    }

    public function render()
    {
        $this->authorize('reglamento.lista');

        $proyectos = Proyecto::where('activo', true)->get();

        $items = Reglamento::query()
            ->with(['proyecto'])
            ->when($this->buscar, function ($query) {
                $query->where('titulo', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->proyecto_id, function ($query) {
                $query->where('proyecto_id', $this->proyecto_id);
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->orderBy('orden')
            ->paginate($this->perPage);

        return view('livewire.erp.marketing.reglamento.reglamento-lista', [
            'items' => $items,
            'proyectos' => $proyectos,
        ]);
    }
}
