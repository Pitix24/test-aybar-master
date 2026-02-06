<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TicketExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected string $buscar;
    protected string $estado;
    protected string $prioridad;
    protected int $perPage;
    protected int $page;

    public function __construct(string $buscar, string $estado, string $prioridad, int $perPage, int $page)
    {
        $this->buscar = $buscar;
        $this->estado = $estado;
        $this->prioridad = $prioridad;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return Ticket::query()
            ->with(['cliente', 'area', 'estado', 'prioridad', 'gestor'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('asunto_inicial', 'like', "%{$this->buscar}%")
                        ->orWhere('id', 'like', "%{$this->buscar}%")
                        ->orWhereHas('cliente', function ($client) {
                            $client->where('name', 'like', "%{$this->buscar}%");
                        });
                });
            })
            ->when($this->estado !== '', function ($query) {
                $query->where('estado_ticket_id', $this->estado);
            })
            ->when($this->prioridad !== '', function ($query) {
                $query->where('prioridad_ticket_id', $this->prioridad);
            })
            ->orderByDesc('created_at')
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->asunto_inicial,
                    $item->cliente?->name ?? 'N/A',
                    $item->area?->nombre ?? 'N/A',
                    $item->estado?->nombre ?? 'N/A',
                    $item->prioridad?->nombre ?? 'N/A',
                    $item->gestor?->name ?? 'N/A',
                    $item->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Asunto',
            'Cliente',
            'Área',
            'Estado',
            'Prioridad',
            'Gestor',
            'Fecha Creación',
        ];
    }
}
