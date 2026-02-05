<?php

namespace App\Exports;

use App\Models\Proyecto;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProyectosExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected string $buscar;
    protected ?int $unidad_negocio_id;
    protected ?int $grupo_proyecto_id;
    protected string $activo;
    protected int $perPage;
    protected int $page;

    public function __construct(string $buscar, ?int $unidad_negocio_id, ?int $grupo_proyecto_id, string $activo, int $perPage, int $page)
    {
        $this->buscar = $buscar;
        $this->unidad_negocio_id = $unidad_negocio_id;
        $this->grupo_proyecto_id = $grupo_proyecto_id;
        $this->activo = $activo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return Proyecto::with(['unidadNegocio', 'grupoProyecto'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', "%{$this->buscar}%");

                    if (is_numeric($this->buscar)) {
                        $q->orWhere('id', (int) $this->buscar);
                    }
                });
            })
            ->when($this->unidad_negocio_id, function ($query) {
                $query->where('unidad_negocio_id', $this->unidad_negocio_id);
            })
            ->when($this->grupo_proyecto_id, function ($query) {
                $query->where('grupo_proyecto_id', $this->grupo_proyecto_id);
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
                    $item->unidadNegocio?->nombre ?? '-',
                    $item->grupoProyecto?->nombre ?? '-',
                    $item->nombre,
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
            'Unidad de Negocio',
            'Grupo Proyecto',
            'Nombre',
            'Estado',
            'Fecha Creación',
        ];
    }
}
