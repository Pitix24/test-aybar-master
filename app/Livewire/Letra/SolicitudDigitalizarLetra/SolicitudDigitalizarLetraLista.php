<?php

namespace App\Livewire\Letra\SolicitudDigitalizarLetra;

use App\Models\Proyecto;
use App\Models\SolicitudDigitalizarLetra;
use App\Models\UnidadNegocio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Solicitudes de Letras Digitales')]
class SolicitudDigitalizarLetraLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $perPage = 20;

    #[Url]
    public $unidad_negocio_id = '';

    #[Url]
    public $proyecto_id = '';

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'unidad_negocio_id', 'proyecto_id', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'unidad_negocio_id', 'proyecto_id']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function render()
    {
        $items = SolicitudDigitalizarLetra::query()
            ->with(['unidadNegocio', 'proyecto', 'userCliente.perfilCliente'])
            ->when($this->buscar, function ($q) {
                $buscar = $this->buscar;
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('id', 'like', "%{$buscar}%")
                        ->orWhere('codigo_cliente', 'like', "%{$buscar}%")
                        ->orWhereHas('userCliente', function ($qUser) use ($buscar) {
                            $qUser->where('name', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('userCliente.perfilCliente', function ($qCliente) use ($buscar) {
                            $qCliente->where('dni', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($this->unidad_negocio_id, function ($q) {
                $q->where('unidad_negocio_id', $this->unidad_negocio_id);
            })
            ->when($this->proyecto_id, function ($q) {
                $q->where('proyecto_id', $this->proyecto_id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        $empresas = UnidadNegocio::all();
        $proyectos = Proyecto::when($this->unidad_negocio_id, function ($q) {
            $q->where('unidad_negocio_id', $this->unidad_negocio_id);
        })->get();

        return view('livewire.letra.solicitud-digitalizar-letra.solicitud-digitalizar-letra-lista', [
            'items' => $items,
            'empresas' => $empresas,
            'proyectos' => $proyectos,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
