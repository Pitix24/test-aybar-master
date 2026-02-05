<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AdminsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected ?int $role_id;
    protected $activo;
    protected $perPage;
    protected $page;

    public function __construct($buscar, ?int $role_id, $activo, $perPage, $page)
    {
        $this->buscar = $buscar;
        $this->role_id = $role_id;
        $this->activo = $activo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return User::query()
            ->when($this->buscar !== '', function ($q) {
                $q->where(function ($query) {
                    $query->where('name', 'like', "%{$this->buscar}%")
                        ->orWhere('email', 'like', "%{$this->buscar}%")
                        ->orWhere('id', $this->buscar);
                });
            })
            ->when($this->role_id !== '', function ($q) {
                $q->whereHas('roles', function ($query) {
                    $query->where('role_id', $this->role_id);
                });
            })
            ->when($this->activo !== '', function ($q) {
                $q->where('activo', $this->activo);
            })
            ->latest()
            ->paginate($this->perPage, ['*'], 'page', $this->page)
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->name,
                    $item->email,
                    $item->roles->pluck('name')->implode(', '),
                    $item->activo ? 'Activo' : 'Inactivo',
                    $item->created_at ? $item->created_at->format('Y-m-d H:i') : '-',
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
