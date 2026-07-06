<?php

namespace App\Exports\Legal;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketNotarialExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $unidad_negocio_id;
    protected $proyecto_id;
    protected $estado_id;
    protected $gestor_id;
    protected $fecha_inicio;
    protected $fecha_fin;
    protected $todo;
    protected $perPage;
    protected $page;

    public function __construct(
        $buscar = '',
        $unidad_negocio_id = '',
        $proyecto_id = '',
        $estado_id = '',
        $gestor_id = '',
        $fecha_inicio = '',
        $fecha_fin = '',
        $todo = false,
        $perPage = null,
        $page = null
    ) {
        $this->buscar = $buscar;
        $this->unidad_negocio_id = $unidad_negocio_id;
        $this->proyecto_id = $proyecto_id;
        $this->estado_id = $estado_id;
        $this->gestor_id = $gestor_id;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->todo = $todo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        $query = Ticket::query()
            ->with(['area', 'estado', 'gestor', 'unidadNegocio', 'proyecto', 'tipoSolicitud', 'subTipoSolicitud'])
            ->cartasNotariales();

        if (!$this->todo) {
            $query->when($this->buscar, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('id', 'like', "%{$this->buscar}%")
                        ->orWhere('dni', 'like', "%{$this->buscar}%")
                        ->orWhere('nombres', 'like', "%{$this->buscar}%")
                        ->orWhere('asunto_inicial', 'like', "%{$this->buscar}%")
                        ->orWhere('asunto_respuesta', 'like', "%{$this->buscar}%");
                });
            })
                ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
                ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
                ->when($this->estado_id, fn($q) => $q->where('estado_ticket_id', $this->estado_id))
                ->when($this->gestor_id, fn($q) => $q->where('gestor_id', $this->gestor_id));
        }

        $query->when($this->fecha_inicio, fn($q) => $q->whereDate('created_at', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->whereDate('created_at', '<=', $this->fecha_fin))
            ->orderByDesc('created_at');

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get()->map(function ($item, $index) {
            return [
                $index + 1,
                $item->id,
                $item->dni,
                $item->nombres,
                $item->email,
                $item->celular,
                $item->unidadNegocio?->nombre ?? 'N/A',
                $item->proyecto?->nombre ?? 'N/A',
                $item->area?->nombre ?? 'N/A',
                $item->tipoSolicitud?->nombre ?? 'N/A',
                $item->subTipoSolicitud?->nombre ?? 'N/A',
                $item->estado?->nombre ?? 'N/A',
                $item->gestor?->name ?? 'Sin asignar',
                $item->asunto_inicial,
                $item->descripcion_inicial,
                $item->asunto_respuesta,
                $item->descripcion_respuesta,
                $item->created_at?->format('d/m/Y H:i'),
                $item->fecha_vencimiento?->format('d/m/Y H:i') ?? 'N/A',
                $item->origen,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'DNI',
            'Nombres',
            'Email',
            'Celular',
            'Empresa',
            'Proyecto',
            'Área',
            'Tipo Solicitud',
            'Sub Tipo Solicitud',
            'Estado',
            'Gestor',
            'Asunto Inicial',
            'Descripción Inicial',
            'Asunto Respuesta',
            'Descripción Respuesta',
            'Fecha Creación',
            'Fecha Vencimiento',
            'Origen',
        ];
    }
}
