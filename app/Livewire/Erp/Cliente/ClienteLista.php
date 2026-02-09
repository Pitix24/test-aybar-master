<?php

namespace App\Livewire\Erp\Cliente;

use App\Models\User;
use App\Exports\ClientesExport;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;
use Maatwebsite\Excel\Facades\Excel;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Clientes')]
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
    public $fecha_inicio = '';

    #[Url]
    public $fecha_fin = '';

    #[Url]
    public $perPage = 20;

    /**
     * Reset de paginación centralizado
     */
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
                'fecha_inicio',
                'fecha_fin',
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
            'fecha_inicio',
            'fecha_fin',
        ]);

        $this->perPage = 20;
        $this->resetPage();
    }

    public function exportExcel()
    {
        abort_unless(auth()->user()->can('admin.exportar'), 403);

        return Excel::download(
            new ClientesWebExport(
                $this->buscar,
                $this->email,
                $this->activo,
                $this->verificado,
                $this->tratamiento,
                $this->politica,
                $this->fecha_inicio,
                $this->fecha_fin,
                $this->perPage,
                $this->getPage()
            ),
            'clientes.xlsx'
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

            ->when(
                $this->email !== '',
                fn($q) =>
                $q->where('users.email', 'like', "%{$this->email}%")
            )

            ->when(
                $this->activo !== '',
                fn($q) =>
                $q->where('users.activo', $this->activo)
            )

            ->when(
                $this->tratamiento !== '',
                fn($q) =>
                $q->where('users.politica_uno', $this->tratamiento)
            )

            ->when(
                $this->politica !== '',
                fn($q) =>
                $q->where('users.politica_dos', $this->politica)
            )

            ->when($this->verificado !== '', function ($q) {
                $this->verificado == '1'
                    ? $q->whereNotNull('users.email_verified_at')
                    : $q->whereNull('users.email_verified_at');
            })

            ->when(
                $this->fecha_inicio,
                fn($q) =>
                $q->whereDate('users.created_at', '>=', $this->fecha_inicio)
            )

            ->when(
                $this->fecha_fin,
                fn($q) =>
                $q->whereDate('users.created_at', '<=', $this->fecha_fin)
            )

            ->select('users.*')
            ->latest('users.created_at')
            ->paginate($this->perPage);

        return view('livewire.erp.cliente.cliente-lista', compact('items'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
