<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFest;
use App\Models\EntregaFestMopTarea;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EntregaFest\EntregaFestMopTareaExport;
use App\Models\User;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Tareas MOP - Entrega Fest')]
class MopTareaLista extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    #[Url]
    public $buscar = '';

    #[Url]
    public $fase = '';

    #[Url]
    public $user_id = '';

    #[Url]
    public $esta_completado = '';

    #[Url]
    public $perPage = 20;

    // Catálogos
    public $usuarios = [];

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->usuarios = User::permission('entrega-fest.gestor')->get();
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'fase', 'user_id', 'esta_completado', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'fase', 'user_id', 'esta_completado']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        // $this->authorize('entrega-fest.mop.exportar-filtro'); // Opcional dependiendo de tus permisos

        return Excel::download(
            new EntregaFestMopTareaExport(
                $this->evento->id,
                $this->buscar,
                $this->fase,
                $this->user_id,
                $this->esta_completado
            ),
            'mop_tareas_filtradas.xlsx'
        );
    }

    public function render()
    {
        $items = EntregaFestMopTarea::with(['user', 'media'])
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('titulo', 'like', '%' . $this->buscar . '%')
                        ->orWhere('instruccion', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->fase, fn($q) => $q->where('fase', $this->fase))
            ->when($this->user_id, fn($q) => $q->where('user_id', $this->user_id))
            ->when($this->esta_completado !== '', fn($q) => $q->where('esta_completado', $this->esta_completado))
            ->orderBy('fase')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.mop.mop-tarea-lista', compact('items'));
    }
}
