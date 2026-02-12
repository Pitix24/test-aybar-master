<?php

namespace App\Exports;

use App\Models\Menu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MenuExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $activo;
    protected $perPage;
    protected $page;

    public function __construct($buscar, $activo, $perPage, $page)
    {
        $this->buscar = $buscar;
        $this->activo = $activo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return Menu::query()
            ->whereNull('parent_id')
            ->when($this->buscar !== '', function ($q) {
                $q->where('nombre', 'like', "%{$this->buscar}%")
                    ->orWhere('id', $this->buscar);
            })
            ->when($this->activo !== '', function ($q) {
                $q->where('activo', $this->activo);
            })
            ->orderBy('orden')
            ->paginate($this->perPage, ['*'], 'page', $this->page)
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->nombre,
                    $item->ruta ?? $item->url ?? '-',
                    $item->icono ?? '-',
                    $item->nivel,
                    $item->orden,
                    $item->permiso ?? '-',
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
            'Ruta/URL',
            'Icono',
            'Nivel',
            'Orden',
            'Permiso',
            'Estado',
            'Fecha Creación',
        ];
    }
}
