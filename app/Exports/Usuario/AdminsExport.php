<?php

namespace App\Exports\Usuario;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AdminsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected ?string $buscar;
    protected ?string $role_id;
    protected ?string $activo;
    protected ?int $perPage;
    protected ?int $page;
    protected ?string $desde;
    protected ?string $hasta;
    protected bool $todo;

    public function __construct(
        ?string $buscar = null,
        ?string $role_id = null,
        ?string $activo = null,
        ?int $perPage = null,
        ?int $page = null,
        ?string $desde = null,
        ?string $hasta = null,
        bool $todo = false
    ) {
        $this->buscar = $buscar;
        $this->role_id = $role_id;
        $this->activo = $activo;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->todo = $todo;
    }

    public function collection()
    {
        $query = User::query()->where('rol', 'admin');

        if (!$this->todo) {
            $query->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->buscar}%")
                        ->orWhere('email', 'like', "%{$this->buscar}%")
                        ->orWhere('id', $this->buscar);
                });
            })
                ->when($this->role_id !== '', function ($q) {
                    $q->whereHas('roles', function ($sub) {
                        $sub->where('id', $this->role_id);
                    });
                })
                ->when($this->activo !== '', function ($q) {
                    $q->where('activo', $this->activo);
                });
        }

        $query->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->latest();

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get()->map(function ($item, $index) {
            return [
                $index + 1,
                $item->id,
                $item->name,
                $item->email,
                $item->getRoleNames()->implode(', '),
                $item->activo ? 'Activo' : 'Inactivo',
                $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
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
            'Roles',
            'Estado',
            'Fecha Creación',
        ];
    }
}
