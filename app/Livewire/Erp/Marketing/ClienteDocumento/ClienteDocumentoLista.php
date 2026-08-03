<?php

namespace App\Livewire\Erp\Marketing\ClienteDocumento;

use App\Models\ClienteDocumento;
use App\Models\Proyecto;
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
#[Title('Lista de Documentos de Clientes')]
class ClienteDocumentoLista extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $buscar = '';

    #[Url(history: true)]
    public $proyecto_id = '';

    #[Url(history: true)]
    public $tipo_cliente_documentos_id = '';

    // 1. Agregamos la propiedad para el nuevo filtro
    #[Url(history: true)]
    public $activo = '';

    public $perPage = 20;

    public function updated($property)
    {
        // 2. Incluimos 'activo' para que reinicie la paginación al cambiar
        if (in_array($property, ['buscar', 'proyecto_id', 'tipo_cliente_documentos_id', 'activo', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        // 3. Incluimos 'activo' para que se limpie con el botón
        $this->reset(['buscar', 'proyecto_id', 'tipo_cliente_documentos_id', 'activo', 'perPage']);
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
        $this->authorize('cliente_documento.editar');

        try {
            $item = ClienteDocumento::findOrFail($id);
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
        $this->authorize('cliente_documento.lista');

        $proyectos = Proyecto::where('activo', true)->get();
        $tipos = TipoClienteDocumento::where('activo', true)->get();

        $items = ClienteDocumento::query()
            ->with(['proyecto', 'tipoDocumento'])
            ->when($this->buscar, function ($query) {
                $query->where('titulo', 'like', '%' . $this->buscar . '%');
            })
            ->when($this->proyecto_id, function ($query) {
                $query->where('proyecto_id', $this->proyecto_id);
            })
            ->when($this->tipo_cliente_documentos_id, function ($query) {
                $query->where('tipo_cliente_documentos_id', $this->tipo_cliente_documentos_id);
            })
            // 4. Agregamos la condición a la consulta SQL
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->orderBy('orden')
            ->paginate($this->perPage);

        return view('livewire.erp.marketing.cliente-documento.cliente-documento-lista', [
            'items' => $items,
            'proyectos' => $proyectos,
            'tipos' => $tipos,
        ]);
    }
}
