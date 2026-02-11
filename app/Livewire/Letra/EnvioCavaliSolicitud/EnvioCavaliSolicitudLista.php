<?php

namespace App\Livewire\Letra\EnvioCavaliSolicitud;

use App\Models\EnvioCavali;
use App\Models\UnidadNegocio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Envíos CAVALI')]
class EnvioCavaliSolicitudLista extends Component
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
                            $qUnidad->where('nombre', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($this->estado, fn($q) => $q->where('estado', $this->estado))
            ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->orderBy('fecha_corte', 'desc')
            ->paginate($this->perPage);

        $unidadesNegocio = UnidadNegocio::all();

        return view('livewire.letra.envio-cavali-solicitud.envio-cavali-solicitud-lista', [
            'items' => $items,
            'unidadesNegocio' => $unidadesNegocio,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
