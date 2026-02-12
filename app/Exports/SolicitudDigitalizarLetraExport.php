<?php

namespace App\Exports;

use App\Models\SolicitudDigitalizarLetra;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SolicitudDigitalizarLetraExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $estado_id;
    protected $unidad_negocio_id;
    protected $proyecto_id;
    protected $fecha_inicio;
    protected $fecha_fin;

    public function __construct(
        $buscar = '',
        $estado_id = '',
        $unidad_negocio_id = '',
        $proyecto_id = '',
        $fecha_inicio = '',
        $fecha_fin = ''
    ) {
        $this->buscar = $buscar;
        $this->estado_id = $estado_id;
        $this->unidad_negocio_id = $unidad_negocio_id;
        $this->proyecto_id = $proyecto_id;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
    }

    public function collection()
    {
        return SolicitudDigitalizarLetra::query()
            ->with(['unidadNegocio', 'proyecto', 'userCliente.perfilCliente', 'estado'])
            ->when($this->buscar, function ($q) {
                $buscar = $this->buscar;
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('id', 'like', "%{$buscar}%")
                        ->orWhere('codigo_cliente', 'like', "%{$buscar}%")
                        ->orWhere('codigo_cuota', 'like', "%{$buscar}%")
                        ->orWhereHas('userCliente', function ($qUser) use ($buscar) {
                            $qUser->where('name', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('userCliente.perfilCliente', function ($qCliente) use ($buscar) {
                            $qCliente->where('dni', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($this->estado_id, fn($q) => $q->where('estado_solicitud_digitalizar_letra_id', $this->estado_id))
            ->when($this->unidad_negocio_id, fn($q) => $q->where('unidad_negocio_id', $this->unidad_negocio_id))
            ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
            ->when($this->fecha_inicio, fn($q) => $q->whereDate('created_at', '>=', $this->fecha_inicio))
            ->when($this->fecha_fin, fn($q) => $q->whereDate('created_at', '<=', $this->fecha_fin))
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->unidadNegocio?->nombre ?? '—',
                    $item->proyecto?->nombre ?? '—',
                    $item->etapa,
                    $item->manzana . ' / ' . $item->lote,
                    $item->numero_cuota,
                    $item->codigo_cuota,
                    $item->userCliente?->name ?? '—',
                    $item->userCliente?->perfilCliente?->dni ?? '—',
                    $item->estado?->nombre ?? 'Pendiente',
                    $item->created_at->format('d/m/Y H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'Empresa',
            'Proyecto',
            'Etapa',
            'Mz. / Lt.',
            'N° Cuota',
            'Código Cuota',
            'Cliente',
            'DNI',
            'Estado',
            'Fecha Solicitud',
        ];
    }
}
