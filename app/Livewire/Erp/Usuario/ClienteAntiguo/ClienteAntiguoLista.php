<?php

namespace App\Livewire\Erp\Usuario\ClienteAntiguo;

use App\Exports\Usuario\ClientesAntiguoExport;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Clientes Antiguo (DB2)')]
class ClienteAntiguoLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $codigo_cliente = '';

    #[Url]
    public $perPage = 20;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'codigo_cliente', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'codigo_cliente']);
        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('cliente-antiguo.exportar-filtro');

        return Excel::download(
            new ClientesAntiguoExport(
                buscar: $this->buscar,
                codigo_cliente: $this->codigo_cliente,
                perPage: $this->perPage,
                page: $this->getPage(),
                todo: false
            ),
            'clientes_antiguo_filtrados_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('cliente-antiguo.exportar-todo');

        return Excel::download(
            new ClientesAntiguoExport(todo: true),
            'clientes_antiguo_completos_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = DB::table('clientes_2')
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%')
                        ->orWhere('razon_social', 'like', '%' . $this->buscar . '%')
                        ->orWhere('codigo_proyecto', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->codigo_cliente !== '', function ($q) {
                $q->where('codigo_cliente', 'like', '%' . $this->codigo_cliente . '%');
            })
            ->orderByDesc('id')
            ->paginate($this->perPage);

        return view('livewire.erp.usuario.cliente-antiguo.cliente-antiguo-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
