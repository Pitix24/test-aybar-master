<?php

namespace App\Exports;

use App\Models\Area;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AreaTiposExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected Area $area;
    protected string $buscar;

    public function __construct(Area $area, string $buscar = '')
    {
        $this->area = $area;
        $this->buscar = $buscar;
    }

    public function collection()
    {
        return $this->area->tiposSolicitud()
            ->when($this->buscar, function ($query) {
                $query->where('nombre', 'like', "%{$this->buscar}%");
            })
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->nombre,
                    $item->tiempo_solucion ?? 'Heredado',
                    $item->activo ? 'Activo' : 'Inactivo',
                    $item->pivot->created_at ? $item->pivot->created_at->format('Y-m-d H:i') : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Tipo de Solicitud',
            'Tiempo Solución (H)',
            'Estado',
            'Fecha Asignación',
        ];
    }
}
