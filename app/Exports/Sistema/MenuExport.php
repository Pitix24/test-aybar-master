<?php

namespace App\Exports\Sistema;

use App\Models\Menu;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MenuExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected ?string $buscar;
    protected ?string $activo;
    protected ?int $perPage;
    protected ?int $page;
    protected ?string $desde;
    protected ?string $hasta;
    protected bool $todo;

    public function __construct(
        ?string $buscar = null,
        ?string $activo = null,
        ?int $perPage = null,
        ?int $page = null,
        ?string $desde = null,
        ?string $hasta = null,
        bool $todo = false
    ) {
        $this->buscar = $buscar;
        $this->activo = $activo;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->todo = $todo;
    }

    public function collection()
    {
        $query = Menu::query()->whereNull('parent_id');

        if (!$this->todo) {
            $query->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->buscar}%")
                        ->orWhere('id', $this->buscar);
                });
            })
                ->when($this->activo !== '', function ($q) {
                    $q->where('activo', $this->activo);
                });
        }

        $query->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderBy('orden');

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get()->map(function ($item, $index) {
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
