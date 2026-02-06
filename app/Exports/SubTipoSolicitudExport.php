<?php

namespace App\Exports;

use App\Models\SubTipoSolicitud;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubTipoSolicitudExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected string $buscar;
    protected ?int $tipo_solicitud_id;
    protected string $activo;
    protected int $perPage;
    protected int $page;

    public function __construct(string $buscar, ?int $tipo_solicitud_id, string $activo, int $perPage, int $page)
    {
        $this->buscar = $buscar;
        $this->tipo_solicitud_id = $tipo_solicitud_id;
        $this->activo = $activo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return SubTipoSolicitud::with('tipoSolicitud')
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', "%{$this->buscar}%");

                    if (is_numeric($this->buscar)) {
                        $q->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->tipo_solicitud_id, function ($query) {
                $query->where('tipo_solicitud_id', $this->tipo_solicitud_id);
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
                    $item->tipoSolicitud?->nombre ?? '-',
                    $item->nombre,
                    $item->tiempo_solucion ?? 'Heredado',
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
            'Tipo de Solicitud',
            'Nombre Sub-Tipo',
            'Tiempo Solución (Horas)',
            'Estado',
            'Fecha Creación',
        ];
    }
}
