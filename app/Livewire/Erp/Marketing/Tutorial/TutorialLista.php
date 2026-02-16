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

    public $perPage = 10;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'activo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'activo', 'perPage']);
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
        // Pendiente: Implementar TutorialExport
        $this->dispatch('alertaLivewire', [
            'type' => 'info',
            'title' => 'Próximamente',
            'text' => 'La exportación filtrada estará disponible pronto.'
        ]);
    }

    public function exportExcelTodo()
    {
        $this->authorize('tutorial.exportar-todo');
        // Pendiente: Implementar TutorialExport
        $this->dispatch('alertaLivewire', [
            'type' => 'info',
            'title' => 'Próximamente',
            'text' => 'La exportación completa estará disponible pronto.'
        ]);
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

        $tutoriales = Tutorial::query()
            ->when($this->buscar, function ($query) {
                $query->where('titulo', 'like', '%' . $this->buscar . '%')
                    ->orWhere('video_id', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->orderBy('orden')
            ->paginate($this->perPage);

        return view('livewire.erp.marketing.tutorial.tutorial-lista', [
            'tutoriales' => $tutoriales
        ]);
    }
}
