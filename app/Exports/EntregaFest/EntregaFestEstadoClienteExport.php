<?php

namespace App\Exports\EntregaFest;

use App\Models\EntregaFestEstadoCliente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EntregaFestEstadoClienteExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
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
        $query = EntregaFestEstadoCliente::query();

        if (!$this->todo) {
            $query->when($this->buscar, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', "%{$this->buscar}%")
                        ->orWhere('id', 'like', "%{$this->buscar}%");
                });
            })
                ->when($this->activo !== '', fn($q) => $q->where('activo', $this->activo));
        }

        $query->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderBy('id', 'desc');

        if (!$this->todo && $this->perPage && $this->page) {
            return $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage)->get();
        }

        return $query->get();
    }

    public function map($row): array
    {
        static $contador = 0;
        $contador++;

        return [
            $contador,
            $row->id,
            $row->nombre,
            $row->color,
            $row->activo ? 'Activo' : 'Inactivo',
            $row->created_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Nombre',
            'Color',
            'Estado',
            'Fecha Registro',
        ];
    }
}
