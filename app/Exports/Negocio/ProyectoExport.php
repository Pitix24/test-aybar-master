<?php

namespace App\Exports\Negocio;

use App\Models\Proyecto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProyectoExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(
        private string $buscar = '',
        private string $activo = '',
        private string $unidad_negocio_id = '',
        private string $grupo_proyecto_id = '',
        private int $perPage = 20,
        private int $page = 1,
        private string $desde = '',
        private string $hasta = '',
        private bool $todo = false
    ) {
    }

    public function collection()
    {
        return Proyecto::query()
            ->with(['unidadNegocio', 'grupoProyecto'])
            ->when(!$this->todo, function ($query) {
                $query->when($this->buscar !== '', function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('nombre', 'like', "%{$this->buscar}%");
                        if (is_numeric($this->buscar)) {
                            $sub->orWhere('id', (int) $this->buscar);
                        }
                    });
                })
                    ->when($this->unidad_negocio_id !== '', function ($q) {
                        $q->where('unidad_negocio_id', $this->unidad_negocio_id);
                    })
                    ->when($this->grupo_proyecto_id !== '', function ($q) {
                        $q->where('grupo_proyecto_id', $this->grupo_proyecto_id);
                    })
                    ->when($this->activo !== '', function ($q) {
                        $q->where('activo', $this->activo);
                    })
                    ->skip(($this->page - 1) * $this->perPage)
                    ->take($this->perPage);
            })
            ->when($this->desde, fn($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->nombre,
                    $item->unidadNegocio->nombre ?? '-',
                    $item->grupoProyecto->nombre ?? '-',
                    $item->slin_id ?? '-',
                    $item->activo ? 'Activo' : 'Inactivo',
                    $item->created_at->format('d/m/Y H:i A'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Proyecto',
            'Unidad de Negocio',
            'Grupo de Proyecto',
            'SLIN ID',
            'Estado',
            'Fecha Registro',
        ];
    }
}
