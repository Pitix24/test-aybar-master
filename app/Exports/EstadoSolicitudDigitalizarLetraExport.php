<?php

namespace App\Exports;

use App\Models\EstadoSolicitudDigitalizarLetra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EstadoSolicitudDigitalizarLetraExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected string $buscar;
    protected string $activo;
    protected int $perPage;
    protected int $page;

    public function __construct(string $buscar, string $activo, int $perPage, int $page)
    {
        $this->buscar = $buscar;
        $this->activo = $activo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return EstadoSolicitudDigitalizarLetra::query()
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', "%{$this->buscar}%");

                    if (is_numeric($this->buscar)) {
                        $q->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->orderByDesc('created_at')
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->nombre,
                    $item->color ?? '-',
                    $item->icono ?? '-',
                    $item->activo ? 'Activo' : 'Inactivo',
                    $item->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Nombre',
            'Color',
            'Icono',
            'Estado',
            'Fecha Creación',
        ];
    }
}
