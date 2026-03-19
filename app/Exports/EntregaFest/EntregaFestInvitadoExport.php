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
    protected $confirmado;
    protected $transporte;
    protected $todo;
    protected $perPage;
    protected $page;

    public function __construct(
        $entrega_fest_id,
        $buscar = '',
        $confirmado = '',
        $transporte = '',
        $todo = false,
        $perPage = null,
        $page = null
    ) {
        $this->entrega_fest_id = $entrega_fest_id;
        $this->buscar = $buscar;
        $this->confirmado = $confirmado;
        $this->transporte = $transporte;
        $this->todo = $todo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        $query = InvitadoEntregaFest::query()
            ->with(['prospecto.proyecto', 'prospecto.user', 'acompanantes'])
            ->where('entrega_fest_id', $this->entrega_fest_id);

        if (!$this->todo) {
            $query->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('prospecto', function ($sub) {
                        $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })
                        ->orWhereHas('copropietario', function ($sub) {
                            $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                                ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                        })
                        ->orWhere('codigo_invitado', 'like', '%' . $this->buscar . '%');
                });
            })
                ->when($this->confirmado !== '', fn($q) => $q->where('confirmado', $this->confirmado))
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
            'BUS' => 'BUS AYBAR',
            'PROPIO' => 'MOVILIDAD PROPIA',
            default => $item->transporte,
        };

        $data = [
            $index,
            $item->codigo_invitado,
            $item->dni,
            $item->nombre_completo,
            $item->prospecto?->proyecto?->nombre ?? $item->copropietario?->prospecto?->proyecto?->nombre ?? 'N/A',
            ($item->lote ?? '') . ' ' . ($item->manzana ?? ''),
            $item->confirmado ? 'CONFIRMADO' : 'NO ASISTE',
            $item->cantidad_acompanantes_permitidos,
            $transporteTexto,
            $item->confirmado ? 'SÍ' : 'NO',
            $item->created_at->format('d/m/Y H:i'),
        ];

        // Agregar datos de hasta 3 acompañantes
        for ($i = 0; $i < 3; $i++) {
            $acompanante = $item->acompanantes[$i] ?? null;
            $data[] = $acompanante ? $acompanante->nombres : '';
            $data[] = $acompanante ? $acompanante->dni : '';
        }

        return $data;
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
            'Acompañante 1 Nombre',
            'Acompañante 1 DNI',
            'Acompañante 2 Nombre',
            'Acompañante 2 DNI',
            'Acompañante 3 Nombre',
            'Acompañante 3 DNI',
        ];
    }
}
