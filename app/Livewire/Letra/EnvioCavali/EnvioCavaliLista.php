<?php

namespace App\Livewire\Letra\EnvioCavali;

use App\Exports\CavaliExport;
use App\Models\EnvioCavali;
use App\Models\UnidadNegocio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Envíos CAVALI')]
class EnvioCavaliLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $perPage = 20;

    #[Url]
    public $estado = '';

    #[Url]
    public $unidad_negocio_id = '';

    public $unidades_negocios = [];

    public function mount()
    {
        $this->unidades_negocios = UnidadNegocio::orderBy('nombre')->get();
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'estado', 'unidad_negocio_id', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'estado', 'unidad_negocio_id']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportCavali($id)
    {
        abort_unless(auth()->user()->can('envio-cavali-solicitud.exportar'), 403);

        $envio = EnvioCavali::findOrFail($id);
        $nombreArchivo = 'Cavali_' . $envio->unidadNegocio->nombre . '_' . $envio->fecha_corte->format('Y-m-d') . '.xlsx';

        return Excel::download(new CavaliExport($envio), $nombreArchivo);
    }

    public function render()
    {
        $items = EnvioCavali::query()
            ->with(['unidadNegocio', 'solicitudes'])
            ->withCount('solicitudes')
            ->when($this->buscar, function ($q) {
                $buscar = $this->buscar;
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('fecha_corte', 'like', "%{$buscar}%")
                        ->orWhereHas('unidadNegocio', function ($qUnidad) use ($buscar) {
                            $qUnidad->where('nombre', 'like', "%{$buscar}%")
                                ->orWhere('razon_social', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($this->estado, fn($q) => $q->where('estado', $this->estado))
            ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->orderBy('fecha_corte', 'desc')
            ->paginate($this->perPage);

        return view('livewire.letra.envio-cavali.envio-cavali-lista', [
            'items' => $items,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
