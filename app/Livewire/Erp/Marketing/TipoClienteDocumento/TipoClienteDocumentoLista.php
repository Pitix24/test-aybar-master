<?php

namespace App\Livewire\Erp\Marketing\TipoClienteDocumento;

use App\Models\TipoClienteDocumento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Tipos de Documento de Clientes')]
class TipoClienteDocumentoLista extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $buscar = '';

    // 1. Nueva propiedad para el filtro
    #[Url(history: true)]
    public $activo = '';

    public $perPage = 20;

    public function updated($property)
    {
        // 2. Incluir 'activo' en el reset de página
        if (in_array($property, ['buscar', 'activo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        // 3. Incluir 'activo' en el limpiador
        $this->reset(['buscar', 'activo', 'perPage']);
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
        $this->authorize('tipo_cliente_documento.editar'); // Seguridad añadida

        try {
            $item = TipoClienteDocumento::findOrFail($id);
            $item->update(['activo' => !$item->activo]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Éxito',
                'text' => 'Estado actualizado correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::channel('marketing')->error("Error en toggleActivo: " . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el estado.'
            ]);
        }
    }

    public function render()
    {
        $this->authorize('tipo_cliente_documento.lista'); // Seguridad añadida

        $items = TipoClienteDocumento::query()
            ->when($this->buscar, function ($query) {
                // Se agrupan los OR para que no rompan el filtro "activo"
                $query->where(function($q) {
                    $q->where('nombre', 'like', '%' . $this->buscar . '%')
                      ->orWhere('descripcion', 'like', '%' . $this->buscar . '%');
                });
            })
            // 4. Agregar la lógica de filtrado SQL
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->orderBy('orden')
            ->paginate($this->perPage);

        return view('livewire.erp.marketing.tipo-cliente-documento.tipo-cliente-documento-lista', [
            'items' => $items,
        ]);
    }
}
