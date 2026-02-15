<?php

namespace App\Exports\Sistema;

use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PermisosExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected ?string $buscar;
    protected ?int $perPage;
    protected ?int $page;
    protected ?string $desde;
    protected ?string $hasta;
    protected bool $todo;

    public function __construct(
        ?string $buscar = null,
        ?int $perPage = null,
        ?int $page = null,
        ?string $desde = null,
        ?string $hasta = null,
        bool $todo = false
    ) {
        $this->buscar = $buscar;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->todo = $todo;
    }

    public function collection()
    {
        $query = Permission::query();

        if (!$this->todo && $this->buscar) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->buscar . '%')
                    ->orWhere('module', 'like', '%' . $this->buscar . '%');
            });
        }

        if ($this->desde) {
            $query->whereDate('created_at', '>=', $this->desde);
        }
        if ($this->hasta) {
            $query->whereDate('created_at', '<=', $this->hasta);
        }

        $query->orderBy('module')->orderBy('name');

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get()->map(function ($item, $index) {
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
