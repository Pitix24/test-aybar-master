<?php

namespace App\Exports\Letra;

use App\Models\EnvioCavali;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EnvioCavaliListExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $estado_id;
    protected $unidad_negocio_id;
    protected $todo;

    public function __construct(
        $buscar = '',
        $estado_id = '',
        $unidad_negocio_id = '',
        $todo = false
    ) {
        $this->buscar = $buscar;
        $this->estado_id = $estado_id;
        $this->unidad_negocio_id = $unidad_negocio_id;
        $this->todo = $todo;
    }

    public function collection()
    {
        $query = EnvioCavali::query()
            ->with(['unidadNegocio', 'estado'])
            ->withCount('solicitudes');

        if (!$this->todo) {
            $query->when($this->buscar, function ($q) {
                $buscar = $this->buscar;
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('fecha_corte', 'like', "%{$buscar}%")
                        ->orWhereHas('unidadNegocio', function ($qUnidad) use ($buscar) {
                            $qUnidad->where('nombre', 'like', "%{$buscar}%")
                                ->orWhere('razon_social', 'like', "%{$buscar}%");
                        });
                });
            })
                ->when($this->estado_id, fn($q) => $q->where('estado_solicitud_digitalizar_letra_id', $this->estado_id))
                ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id));
        }

        return $query->orderBy('fecha_corte', 'desc')
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->fecha_corte->format('Y-m-d'),
                    $item->unidadNegocio?->nombre ?? '—',
                    $item->solicitudes_count,
                    $item->estado?->nombre ?? 'Pendiente',
                    $item->enviado_at?->format('d/m/Y H:i') ?? '—',
                    $item->archivo_nombre ?? '—',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'Fecha Corte',
            'Unidad de Negocio',
            'Cant. Solicitudes',
            'Estado',
            'Fecha Envío',
            'Archivo ZIP',
        ];
    }
}
