<?php

namespace App\Exports\Sistema;

use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RolesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected string $buscar;
    protected int $perPage;
    protected int $page;

    public function __construct(string $buscar, int $perPage, int $page)
    {
        $this->buscar = $buscar;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return Role::query()
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->buscar}%");

                    if (is_numeric($this->buscar)) {
                        $q->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->orderBy('id', 'asc')
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->name,
                    $item->guard_name,
                    $item->permissions->count(),
                    $item->created_at ? $item->created_at->format('Y-m-d H:i') : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Rol',
            'Guard',
            'Cant. Permisos',
            'Fecha Creación',
        ];
    }
}
