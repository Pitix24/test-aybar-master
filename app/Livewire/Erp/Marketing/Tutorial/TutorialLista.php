<?php

namespace App\Livewire\Erp\Marketing\Tutorial;

use App\Models\Tutorial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Lista de Tutoriales')]
class TutorialLista extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $buscar = '';

    #[Url(history: true)]
    public $activo = '';

    #[Url(history: true)]
    public $desde = '';

    #[Url(history: true)]
    public $hasta = '';

    public $perPage = 20;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'activo', 'desde', 'hasta', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo', 'desde', 'hasta', 'perPage']);
        $this->resetPage();
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function exportExcelFiltro()
    {
        $this->authorize('tutorial.exportar-filtro');

        return Excel::download(
            new \App\Exports\Marketing\TutorialExport(
                $this->buscar,
                $this->activo,
                $this->desde,
                $this->hasta,
                false,
                $this->perPage,
                $this->getPage()
            ),
            'tutoriales_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('tutorial.exportar-todo');

        return Excel::download(
            new \App\Exports\Marketing\TutorialExport(
                '',
                '',
                $this->desde,
                $this->hasta,
                true
            ),
            'tutoriales_todos.xlsx'
        );
    }

    public function toggleActivo($id)
    {
        $this->authorize('tutorial.editar');

        try {
            $tutorial = Tutorial::findOrFail($id);
            $tutorial->update(['activo' => !$tutorial->activo]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Éxito',
                'text' => 'Estado actualizado correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::channel('marketing')->error("[TUTORIAL] Error en toggleActivo: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'tutorial_id' => $id,
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
        $this->authorize('tutorial.lista');

        $items = Tutorial::query()
            ->when($this->buscar, function ($query) {
                $query->where('titulo', 'like', '%' . $this->buscar . '%')
                    ->orWhere('video_id', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->when($this->desde, function ($query) {
                $query->whereDate('created_at', '>=', $this->desde);
            })
            ->when($this->hasta, function ($query) {
                $query->whereDate('created_at', '<=', $this->hasta);
            })
            ->orderBy('orden')
            ->paginate($this->perPage);

        return view('livewire.erp.marketing.tutorial.tutorial-lista', [
            'items' => $items
        ]);
    }
}
