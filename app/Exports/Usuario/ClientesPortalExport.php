<?php

namespace App\Exports\Usuario;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ClientesPortalExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected ?string $buscar;
    protected ?string $email;
    protected ?string $activo;
    protected ?string $verificado;
    protected ?string $tratamiento;
    protected ?string $politica;
    protected ?string $desde;
    protected ?string $hasta;
    protected ?int $perPage;
    protected ?int $page;
    protected bool $todo;

    public function __construct(
        ?string $buscar = null,
        ?string $email = null,
        ?string $activo = null,
        ?string $verificado = null,
        ?string $tratamiento = null,
        ?string $politica = null,
        ?string $desde = null,
        ?string $hasta = null,
        ?int $perPage = null,
        ?int $page = null,
        bool $todo = false
    ) {
        $this->buscar = $buscar;
        $this->email = $email;
        $this->activo = $activo;
        $this->verificado = $verificado;
        $this->tratamiento = $tratamiento;
        $this->politica = $politica;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->todo = $todo;
    }

    public function collection()
    {
        $query = User::query()
            ->where('users.rol', 'cliente')
            ->leftJoin('clientes', 'clientes.user_id', '=', 'users.id');

        if (!$this->todo) {
            $query->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('users.name', 'like', "%{$this->buscar}%")
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
                });
        }

        $query->when($this->desde, fn($q) => $q->whereDate('users.created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('users.created_at', '<=', $this->hasta))
            ->latest('users.created_at');

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->select(
            'users.id',
            'users.name',
            'users.email',
            'clientes.dni',
            'users.created_at',
            'users.email_verified_at',
            'users.politica_uno',
            'users.politica_dos',
            'users.activo'
        )->get()->map(function ($u, $index) {
            return [
                $index + 1,
                $u->id,
                $u->name,
                $u->email,
                $u->dni ?? '-',
                $u->created_at ? $u->created_at->format('Y-m-d H:i') : '-',
                $u->email_verified_at ? 'Sí' : 'No',
                $u->politica_uno ? 'Sí' : 'No',
                $u->politica_dos ? 'Sí' : 'No',
                $u->activo ? 'Activo' : 'Inactivo',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Nombre',
            'Email',
            'DNI',
            'Fecha Creación',
            'Verificado',
            'Tratamiento D.P.',
            'Política Comercial',
            'Estado',
        ];
    }
}
