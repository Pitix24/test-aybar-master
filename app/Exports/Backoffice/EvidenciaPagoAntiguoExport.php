<?php

namespace App\Exports\Erp\Backoffice;

use App\Models\EvidenciaPagoAntiguo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EvidenciaPagoAntiguoExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected ?string $buscar;
    protected ?string $buscar_lote;
    protected ?int $unidadNegocio;
    protected ?int $proyecto;
    protected ?int $estado;
    protected ?string $tieneFechaDeposito;
    protected ?string $tieneImagen;
    protected ?string $tieneNumeroOperacion;
    protected ?string $tieneCodigoCuenta;
    protected int $perPage;
    protected int $page;

    public function __construct(
        $buscar,
        $buscar_lote,
        $unidadNegocio,
        $proyecto,
        $estado,
        $tieneFechaDeposito,
        $tieneImagen,
        $tieneNumeroOperacion,
        $tieneCodigoCuenta,
        $perPage,
        $page,
    ) {
        $this->buscar = $buscar;
        $this->buscar_lote = $buscar_lote;
        $this->unidadNegocio = $unidadNegocio !== '' ? (int) $unidadNegocio : null;
        $this->proyecto = $proyecto !== '' ? (int) $proyecto : null;
        $this->estado = $estado !== '' ? (int) $estado : null;
        $this->tieneFechaDeposito = $tieneFechaDeposito;
        $this->tieneImagen = $tieneImagen;
        $this->tieneNumeroOperacion = $tieneNumeroOperacion;
        $this->tieneCodigoCuenta = $tieneCodigoCuenta;
        $this->perPage = (int) $perPage;
        $this->page = (int) $page;
    }

    public function collection()
    {
        return EvidenciaPagoAntiguo::query()
            ->with(['unidadNegocio', 'proyecto', 'estado'])
            ->when($this->buscar, function ($q) {
                $q->where(function ($qq) {
                    $qq->where('id', 'like', "%{$this->buscar}%")
                        ->orWhere('codigo_cliente', 'like', "%{$this->buscar}%")
                        ->orWhere('nombres_cliente', 'like', "%{$this->buscar}%");
                });
            })
            ->when($this->buscar_lote, function ($q, $buscar_lote) {
                $q->where('lote', 'like', "%{$buscar_lote}%");
            })
            ->when(
                $this->estado,
                fn($q) =>
                $q->where('estado_solicitud_evidencia_pago_id', $this->estado)
            )
            ->when(
                $this->unidadNegocio,
                fn($q) =>
                $q->where('unidad_negocio_id', $this->unidadNegocio)
            )
            ->when(
                $this->proyecto,
                fn($q) =>
                $q->where('proyecto_id', $this->proyecto)
            )
            ->when(
                $this->tieneFechaDeposito === 'si',
                fn($q) => $q->whereNotNull('fecha_deposito')
            )
            ->when(
                $this->tieneFechaDeposito === 'no',
                fn($q) => $q->whereNull('fecha_deposito')
            )
            ->when(
                $this->tieneImagen === 'si',
                fn($q) => $q->whereNotNull('imagen_url')
            )
            ->when(
                $this->tieneImagen === 'no',
                fn($q) => $q->whereNull('imagen_url')
            )
            ->when(
                $this->tieneNumeroOperacion === 'si',
                fn($q) => $q->whereNotNull('operacion_numero')
            )
            ->when(
                $this->tieneNumeroOperacion === 'no',
                fn($q) => $q->whereNull('operacion_numero')
            )
            ->when(
                $this->tieneCodigoCuenta === 'si',
                fn($q) => $q->whereNotNull('codigo_cuenta')
            )
            ->when(
                $this->tieneCodigoCuenta === 'no',
                fn($q) => $q->whereNull('codigo_cuenta')
            )
            ->orderByDesc('created_at')
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(fn($e, $index) => [
                $index + 1,
                $e->id,
                $e->unidadNegocio->nombre ?? '',
                $e->proyecto->nombre ?? '',
                $e->codigo_cliente,
                $e->nombres_cliente,
                optional($e->estado)->nombre,
                $e->fecha_deposito ? $e->fecha_deposito->format('Y-m-d') : '',
                $e->imagen_url,
                $e->monto,
                $e->operacion_numero,
                $e->numero_cuota,
                $e->codigo_cuenta,
            ]);
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Empresa',
            'Proyecto',
            'Código Cliente',
            'Cliente',
            'Estado',
            'Fecha Depósito',
            'Imagen',
            'Monto',
            'N° Operación',
            'N° Cuota',
            'Codigo cuenta',
        ];
    }
}
