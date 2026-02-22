<?php

namespace App\Exports\Cita;

use App\Models\Cita;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CitaExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $unidad_negocio_id;
    protected $proyecto_id;
    protected $sede_id;
    protected $motivo_cita_id;
    protected $estado_cita_id;
    protected $gestor_id;
    protected $area_id;
    protected $fecha_inicio;
    protected $fecha_fin;
    protected $todo;
    protected $perPage;
    protected $page;

    public function __construct(
        $buscar = '',
        $unidad_negocio_id = '',
        $proyecto_id = '',
        $sede_id = '',
        $motivo_cita_id = '',
        $estado_cita_id = '',
        $gestor_id = '',
        $area_id = '',
        $fecha_inicio = '',
        $fecha_fin = '',
        $todo = false,
        $perPage = null,
        $page = null
    ) {
        $this->buscar = $buscar;
        $this->unidad_negocio_id = $unidad_negocio_id;
        $this->proyecto_id = $proyecto_id;
        $this->sede_id = $sede_id;
        $this->motivo_cita_id = $motivo_cita_id;
        $this->estado_cita_id = $estado_cita_id;
        $this->gestor_id = $gestor_id;
        $this->area_id = $area_id;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->todo = $todo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        $query = Cita::query()
            ->with(['unidadNegocio', 'proyecto', 'sede', 'motivo', 'estado', 'gestor', 'area']);

        if (!$this->todo) {
            $query->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', "%{$this->buscar}%")
                        ->orWhere('dni', 'like', "%{$this->buscar}%")
                        ->orWhere('nombres', 'like', "%{$this->buscar}%")
                        ->orWhere('asunto_solicitud', 'like', "%{$this->buscar}%");
                });
            })
                ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
                ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
                ->when($this->sede_id, fn($q) => $q->where('sede_id', $this->sede_id))
                ->when($this->motivo_cita_id, fn($q) => $q->where('motivo_cita_id', $this->motivo_cita_id))
                ->when($this->gestor_id, fn($q) => $q->where('gestor_id', $this->gestor_id))
                ->when($this->estado_cita_id, fn($q) => $q->where('estado_cita_id', $this->estado_cita_id))
                ->when($this->area_id, fn($q) => $q->where('area_id', $this->area_id))
                ->when($this->fecha_inicio, fn($q) => $q->whereDate('fecha_inicio', '>=', $this->fecha_inicio))
                ->when($this->fecha_fin, fn($q) => $q->whereDate('fecha_inicio', '<=', $this->fecha_fin));
        }

        $query->orderByDesc('fecha_inicio');

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->fecha_inicio ? $item->fecha_inicio->format('d/m/Y H:i') : 'N/A',
                    $item->dni,
                    $item->nombres,
                    $item->asunto_solicitud,
                    $item->unidadNegocio?->nombre ?? 'N/A',
                    $item->proyecto?->nombre ?? 'N/A',
                    $item->sede?->nombre ?? 'N/A',
                    $item->motivo?->nombre ?? 'N/A',
                    $item->area?->nombre ?? 'N/A',
                    $item->gestor?->name ?? 'Sin asignar',
                    $item->estado?->nombre ?? 'N/A',
                    $item->created_at->format('d/m/Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Fecha Cita',
            'DNI',
            'Nombres',
            'Asunto',
            'Empresa',
            'Proyecto',
            'Sede',
            'Motivo',
            'Área',
            'Gestor',
            'Estado',
            'Fecha Creación',
        ];
    }
}
