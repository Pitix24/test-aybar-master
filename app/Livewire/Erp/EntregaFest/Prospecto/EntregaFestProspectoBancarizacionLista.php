<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Models\EntregaFest;
use App\Models\ProspectoBancarizacionEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Bancarización del Evento')]
class EntregaFestProspectoBancarizacionLista extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $proyecto_id = '';

    #[Url(keep: true)]
    public $estado = '';

    #[Url(keep: true)]
    public $perPage = 20;

    // Catálogos
    public $proyectos = [];

    public function mount($id)
    {
        $this->evento = EntregaFest::with('proyectos')->findOrFail($id);
        $this->proyectos = $this->evento->proyectos;
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'proyecto_id', 'estado', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'proyecto_id', 'estado']);
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('prospecto.exportar-filtro');

        return Excel::download(
            new \App\Exports\EntregaFest\EntregaFestProspectoBancarizacionExport(
                $this->evento->id,
                $this->buscar,
                $this->proyecto_id,
                $this->estado,
                false,
                $this->perPage,
                $this->getPage()
            ),
            'bancarizaciones_filtradas.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('prospecto.exportar-todo');

        return Excel::download(
            new \App\Exports\EntregaFest\EntregaFestProspectoBancarizacionExport(
                $this->evento->id,
                '',
                '',
                '',
                true
            ),
            'bancarizaciones_todo_' . $this->evento->codigo . '.xlsx'
        );
    }

    public function render()
    {
        $items = ProspectoBancarizacionEntregaFest::query()
            ->with(['prospecto', 'prospecto.proyecto'])
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('prospecto', function ($sub) {
                        $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })->orWhere('cuota', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->proyecto_id, function ($query) {
                $query->whereHas('prospecto', function ($sub) {
                    $sub->where('proyecto_id', $this->proyecto_id);
                });
            })
            ->when($this->estado, fn($q) => $q->where('estado', $this->estado))
            ->orderBy('fecha_deposito_real', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.prospecto.entrega-fest-prospecto-bancarizacion-lista', [
            'items' => $items
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
