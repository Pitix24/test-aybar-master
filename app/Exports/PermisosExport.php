<?php

namespace App\Exports;

use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PermisosExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $perPage;
    protected $page;

    public function __construct($buscar, $perPage, $page)
    {
        $this->buscar = $buscar;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return Permission::query()
            ->when($this->buscar, function ($query) {
                $query->where('name', 'like', '%' . $this->buscar . '%')
                    ->orWhere('module', 'like', '%' . $this->buscar . '%');
            })
            ->orderBy('module')
            ->orderBy('name')
            ->paginate($this->perPage, ['*'], 'page', $this->page)
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->module ?? 'Sin Módulo',
                    $item->name,
                    $item->guard_name,
                    $item->created_at ? $item->created_at->format('Y-m-d H:i') : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Módulo',
            'Permiso',
            'Guard',
            'Fecha Creación',
        ];
    }
}
