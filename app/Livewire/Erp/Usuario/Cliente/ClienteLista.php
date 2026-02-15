<?php

namespace App\Livewire\Erp\Usuario\Cliente;

use App\Models\User;
use App\Exports\Usuario\ClientesPortalExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Clientes Portal')]
class ClienteLista extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url]
    public $email = '';

    #[Url]
    public $activo = '';

    #[Url]
    public $verificado = '';

    #[Url]
    public $tratamiento = '';

    #[Url]
    public $politica = '';

    #[Url]
    public $desde = '';

    #[Url]
    public $hasta = '';

    #[Url]
    public $perPage = 20;

    public function updated($property)
    {
        if (
            in_array($property, [
                'buscar',
                'email',
                'activo',
                'verificado',
                'tratamiento',
                'politica',
                'desde',
                'hasta',
                'perPage',
            ])
        ) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset([
            'buscar',
            'email',
            'activo',
            'verificado',
            'tratamiento',
            'politica',
            'desde',
            'hasta',
        ]);

        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('cliente.exportar-filtro');

        return Excel::download(
            new ClientesPortalExport(
                buscar: $this->buscar,
                email: $this->email,
                activo: $this->activo,
                verificado: $this->verificado,
                tratamiento: $this->tratamiento,
                politica: $this->politica,
                desde: $this->desde,
                hasta: $this->hasta,
                perPage: $this->perPage,
                page: $this->getPage(),
                todo: false
            ),
            'clientes_portal_filtrados_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('cliente.exportar-todo');

        return Excel::download(
            new ClientesPortalExport(
                desde: $this->desde,
                hasta: $this->hasta,
                todo: true
            ),
            'clientes_portal_completos_' . now()->format('Y-m-d_H-i') . '.xlsx'
        );
    }

    public function render()
    {
        $items = User::query()
            ->where('users.rol', 'cliente')
            ->leftJoin('clientes', 'clientes.user_id', '=', 'users.id')
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($query) {
                    $query->where('users.name', 'like', "%{$this->buscar}%")
                        ->orWhere('clientes.dni', 'like', "%{$this->buscar}%");
                });
            })
            ->when($this->email !== '', fn($q) => $q->where('users.email', 'like', "%{$this->email}%"))
            ->when($this->activo !== '', fn($q) => $q->where('users.activo', $this->activo))
            ->when($this->tratamiento !== '', fn($q) => $q->where('users.politica_uno', $this->tratamiento))
            ->when($this->politica !== '', fn($q) => $q->where('users.politica_dos', $this->politica))
            ->when($this->verificado !== '', function ($q) {
                $this->verificado == '1'
                    ? $q->whereNotNull('users.email_verified_at')
                    : $q->whereNull('users.email_verified_at');
            })
            ->when($this->desde, fn($q) => $q->whereDate('users.created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('users.created_at', '<=', $this->hasta))
            ->select('users.*', 'clientes.dni')
            ->latest('users.created_at')
            ->paginate($this->perPage);

        return view('livewire.erp.usuario.cliente.cliente-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
