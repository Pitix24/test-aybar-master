<?php

namespace App\Exports\Negocio;

use App\Models\UnidadNegocio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnidadNegocioExport implements FromCollection, WithHeadings, ShouldAutoSize
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
        $query = UnidadNegocio::query();

        if (!$this->todo) {
            $query->when($this->buscar, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->buscar}%");

                    if (is_numeric($this->buscar)) {
                        $sub->orWhere('id', (int) $this->buscar);
                    }
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
                $item->nombre,
                $item->razon_social ?? '-',
                $item->ruc ?? '-',
                $item->slin_id ?? '-',
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
            'Razón Social',
            'RUC',
            'Slin ID',
            'Estado',
            'Fecha Creación',
        ];
    }
}
