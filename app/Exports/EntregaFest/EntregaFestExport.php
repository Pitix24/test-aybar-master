<?php

namespace App\Exports\EntregaFest;

use App\Models\EntregaFest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntregaFestExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $activo;
    protected $unidad_negocio_id;
    protected $proyecto_id;
    protected $todo;
    protected $perPage;
    protected $page;

    public function __construct(
        $buscar = '',
        $activo = '',
        $unidad_negocio_id = '',
        $proyecto_id = '',
        $todo = false,
        $perPage = null,
        $page = null
    ) {
        $this->buscar = $buscar;
        $this->activo = $activo;
        $this->unidad_negocio_id = $unidad_negocio_id;
        $this->proyecto_id = $proyecto_id;
        $this->todo = $todo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        $query = EntregaFest::query()
            ->with(['gestor', 'proyectos'])
            ->withCount(['prospectos', 'invitados']);

        if (!$this->todo) {
            $query->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->buscar . '%')
                        ->orWhere('codigo', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->activo !== '', function ($query) {
                $query->where('activo', $this->activo);
            })
            ->when($this->unidad_negocio_id, function ($query) {
                $query->whereHas('proyectos', function ($q) {
                    $q->where('unidad_negocio_id', $this->unidad_negocio_id);
                });
            })
            ->when($this->proyecto_id, function ($query) {
                $query->whereHas('proyectos', function ($q) {
                    $q->where('proyectos.id', $this->proyecto_id);
                });
            });
        }

        $query->latest();

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->codigo,
                    $item->nombre,
                    $item->gestor?->name ?? 'N/A',
                    $item->proyectos->pluck('nombre')->implode(', '),
                    $item->fecha_entrega ? $item->fecha_entrega->format('d/m/Y') : 'N/A',
                    $item->activo ? 'Activo' : 'Inactivo',
                    $item->prospectos_count,
                    $item->invitados_count,
                    $item->created_at->format('d/m/Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Código',
            'Nombre',
            'Gestor',
            'Proyectos',
            'Fecha Entrega',
            'Estado',
            'Prospectos',
            'Invitados',
            'Fecha Creación',
        ];
    }
}
