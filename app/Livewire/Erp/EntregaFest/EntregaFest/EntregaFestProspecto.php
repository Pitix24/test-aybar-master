<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use App\Models\Proyecto;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EntregaFest\EntregaFestProspectoExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Prospectos del Evento')]
class EntregaFestProspecto extends Component
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
    public $estado_firma_contrato_firmado = '';

    #[Url(keep: true)]
    public $grupo = '';

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
        if (in_array($property, ['buscar', 'proyecto_id', 'estado', 'estado_firma_contrato_firmado', 'grupo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'proyecto_id', 'estado', 'estado_firma_contrato_firmado', 'grupo']);
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('entrega-fest.prospectos');

        return Excel::download(
            new EntregaFestProspectoExport(
                $this->evento->id,
                $this->buscar,
                $this->proyecto_id,
                $this->estado,
                $this->estado_firma_contrato_firmado,
                $this->grupo,
                false,
                $this->perPage,
                $this->getPage()
            ),
            'prospectos_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('entrega-fest.prospectos');

        return Excel::download(
            new EntregaFestProspectoExport(
                $this->evento->id,
                '',
                '',
                '',
                '',
                '',
                true
            ),
            'prospectos_todo_' . $this->evento->codigo . '.xlsx'
        );
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        $items = ProspectoEntregaFest::query()
            ->with(['proyecto', 'user', 'invitado'])
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombres', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%')
                        ->orWhere('email', 'like', '%' . $this->buscar . '%')
                        ->orWhere('celular', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->estado, fn($q) => $q->where('estado', $this->estado))
            ->when($this->estado_firma_contrato_firmado, fn($q) => $q->where('estado_firma_contrato_firmado', $this->estado_firma_contrato_firmado))
            ->when($this->grupo, fn($q) => $q->where('grupo', $this->grupo))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-prospecto', [
            'items' => $items
        ]);
    }
}
