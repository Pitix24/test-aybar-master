<?php

namespace App\Exports\Atc;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $unidad_negocio_id;
    protected $proyecto_id;
    protected $estado_id;
    protected $area_id;
    protected $solicitud_id;
    protected $sub_tipo_solicitud_id;
    protected $canal_id;
    protected $usuario_admin_id;
    protected $prioridad_id;
    protected $fecha_inicio;
    protected $fecha_fin;
    protected $con_derivados;
    protected $con_citas;
    protected $con_hijos;
    protected $todo;

    public function __construct(
        $buscar = '',
        $unidad_negocio_id = '',
        $proyecto_id = '',
        $estado_id = '',
        $area_id = '',
        $solicitud_id = '',
        $sub_tipo_solicitud_id = '',
        $canal_id = '',
        $usuario_admin_id = '',
        $prioridad_id = '',
        $fecha_inicio = '',
        $fecha_fin = '',
        $con_derivados = '',
        $con_citas = '',
        $con_hijos = '',
        $todo = false
    ) {
        $this->buscar = $buscar;
        $this->unidad_negocio_id = $unidad_negocio_id;
        $this->proyecto_id = $proyecto_id;
        $this->estado_id = $estado_id;
        $this->area_id = $area_id;
        $this->solicitud_id = $solicitud_id;
        $this->sub_tipo_solicitud_id = $sub_tipo_solicitud_id;
        $this->canal_id = $canal_id;
        $this->usuario_admin_id = $usuario_admin_id;
        $this->prioridad_id = $prioridad_id;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->con_derivados = $con_derivados;
        $this->con_citas = $con_citas;
        $this->con_hijos = $con_hijos;
        $this->todo = $todo;
    }

    public function collection()
    {
        $query = Ticket::query()
            ->with(['cliente', 'area', 'estado', 'prioridad', 'gestor', 'unidadNegocio', 'proyecto', 'tipoSolicitud', 'subTipoSolicitud', 'canal']);

        if (!$this->todo) {
            $query->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('id', 'like', "%{$this->buscar}%")
                        ->orWhere('dni', 'like', "%{$this->buscar}%")
                        ->orWhere('nombres', 'like', "%{$this->buscar}%")
                        ->orWhere('asunto_inicial', 'like', "%{$this->buscar}%");
                });
            })
                ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
                ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
                ->when($this->estado_id, fn($q) => $q->where('estado_ticket_id', $this->estado_id))
                ->when($this->area_id, fn($q) => $q->where('area_id', $this->area_id))
                ->when($this->solicitud_id, fn($q) => $q->where('tipo_solicitud_id', $this->solicitud_id))
                ->when($this->sub_tipo_solicitud_id, fn($q) => $q->where('sub_tipo_solicitud_id', $this->sub_tipo_solicitud_id))
                ->when($this->canal_id, fn($q) => $q->where('canal_id', $this->canal_id))
                ->when($this->usuario_admin_id, fn($q) => $q->where('gestor_id', $this->usuario_admin_id))
                ->when($this->prioridad_id, fn($q) => $q->where('prioridad_ticket_id', $this->prioridad_id))
                ->when($this->con_derivados === '1', fn($q) => $q->whereHas('derivados'))
                ->when($this->con_derivados === '0', fn($q) => $q->whereDoesntHave('derivados'))
                ->when($this->con_citas === '1', fn($q) => $q->whereHas('citas'))
                ->when($this->con_citas === '0', fn($q) => $q->whereDoesntHave('citas'))
                ->when($this->con_hijos === '1', fn($q) => $q->where('ticket_padre_id', '!=', null))
                ->when($this->con_hijos === '0', fn($q) => $q->where('ticket_padre_id', null));
        }

        return $query->when($this->fecha_inicio, fn($q) => $q->whereDate('created_at', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->whereDate('created_at', '<=', $this->fecha_fin))
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->asunto_inicial,
                    $item->dni,
                    $item->nombres,
                    $item->cliente?->name ?? 'N/A',
                    $item->unidadNegocio?->nombre ?? 'N/A',
                    $item->proyecto?->nombre ?? 'N/A',
                    $item->area?->nombre ?? 'N/A',
                    $item->tipoSolicitud?->nombre ?? 'N/A',
                    $item->subTipoSolicitud?->nombre ?? 'N/A',
                    $item->estado?->nombre ?? 'N/A',
                    $item->prioridad?->nombre ?? 'N/A',
                    $item->gestor?->name ?? 'Sin asignar',
                    $item->canal?->nombre ?? 'N/A',
                    $item->created_at->format('d/m/Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Asunto',
            'DNI',
            'Nombres',
            'Cliente (User)',
            'Empresa',
            'Proyecto',
            'Área',
            'Tipo Solicitud',
            'Sub Tipo Solicitud',
            'Estado',
            'Prioridad',
            'Gestor',
            'Canal',
            'Fecha Creación',
        ];
    }
}
