<?php

namespace App\Exports\EntregaFest;

use App\Models\AsistenciaEntregaFest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EntregaFestAsistenciaExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $entrega_fest_id;
    protected $todo;

    public function __construct(
        $entrega_fest_id,
        $todo = true
    ) {
        $this->entrega_fest_id = $entrega_fest_id;
        $this->todo = $todo;
    }

    public function collection()
    {
        $query = AsistenciaEntregaFest::query()
            ->with([
                'invitado.prospecto.proyecto',
                'invitado.copropietario.prospecto.proyecto',
                'user',
            ])
            ->whereHas('invitado', function ($q) {
                $q->where('entrega_fest_id', $this->entrega_fest_id);
            });

        $query->orderBy('fecha_checkin', 'desc');

        return $query->get();
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        $invitado = $item->invitado;
        $tipoInivitado = $invitado->prospecto_entrega_fest_id ? 'TITULAR' : 'COPROPIETARIO';

        return [
            $index,
            $invitado->codigo_invitado ?? 'N/A',
            $invitado->prospecto?->dni ?? $invitado->copropietario?->dni ?? 'N/A',
            $invitado->nombre_completo ?? 'N/A',
            $tipoInivitado,
            $invitado->prospecto?->proyecto?->nombre ?? $invitado->copropietario?->prospecto?->proyecto?->nombre ?? 'N/A',
            trim(($invitado->manzana ?? '') . ' ' . ($invitado->lote ?? '')) ?: 'N/A',
            $item->fecha_checkin->format('d/m/Y'),
            $item->fecha_checkin->format('H:i:s'),
            strtoupper($item->metodo),
            $item->user?->name ?? 'Sistema',
            $item->segunda_asistencia ? 'SI' : 'NO',
        ];
    }

    public function headings(): array
    {
        return [
            'N°',
            'Cód. Invitado',
            'DNI',
            'Cliente',
            'Tipo Titular',
            'Proyecto',
            'Mz/Lote',
            'Fecha Ingreso',
            'Hora Ingreso',
            'Método',
            'Responsable',
            '2da Asistencia',
        ];
    }
}
