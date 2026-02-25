<?php

namespace App\Exports\EntregaFest;

use App\Models\InvitadoEntregaFest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EntregaFestInvitadoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $entrega_fest_id;
    protected $buscar;
    protected $estado_confirmacion;
    protected $transporte;
    protected $todo;
    protected $perPage;
    protected $page;

    public function __construct(
        $entrega_fest_id,
        $buscar = '',
        $estado_confirmacion = '',
        $transporte = '',
        $todo = false,
        $perPage = null,
        $page = null
    ) {
        $this->entrega_fest_id = $entrega_fest_id;
        $this->buscar = $buscar;
        $this->estado_confirmacion = $estado_confirmacion;
        $this->transporte = $transporte;
        $this->todo = $todo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        $query = InvitadoEntregaFest::query()
            ->with(['prospecto.proyecto', 'prospecto.user'])
            ->where('entrega_fest_id', $this->entrega_fest_id);

        if (!$this->todo) {
            $query->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('prospecto', function ($sub) {
                        $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })->orWhere('codigo_invitado', 'like', '%' . $this->buscar . '%');
                });
            })
                ->when($this->estado_confirmacion, fn($q) => $q->where('estado_confirmacion', $this->estado_confirmacion))
                ->when($this->transporte, fn($q) => $q->where('transporte', $this->transporte));
        }

        $query->orderBy('id', 'desc');

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get();
    }

    public function map($item): array
    {
        static $index = 0;
        $index++;

        $transporteTexto = match ($item->transporte) {
            'bus' => 'BUS AYBAR',
            'propio' => 'MOVILIDAD PROPIA',
            'na' => 'N/A',
            default => strtoupper($item->transporte),
        };

        return [
            $index,
            $item->codigo_invitado,
            $item->prospecto->dni ?? 'N/A',
            $item->prospecto->nombres ?? 'N/A',
            $item->prospecto->proyecto->nombre ?? 'N/A',
            ($item->prospecto->lote ?? '') . ' ' . ($item->prospecto->manzana ?? ''),
            strtoupper($item->estado_confirmacion),
            $item->cantidad_acompanantes_permitidos,
            $transporteTexto,
            $item->confirmado ? 'SÍ' : 'NO',
            $item->created_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'N°',
            'Cód. Invitado',
            'DNI',
            'Cliente',
            'Proyecto',
            'Lote/Mz',
            'Estado Confirmación',
            'Acompañantes',
            'Transporte',
            'Confirmado Web',
            'Fecha Registro',
        ];
    }
}
