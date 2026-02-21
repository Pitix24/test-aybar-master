<?php

namespace App\Exports\Letra;

use App\Models\EnvioCavali;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CavaliLetrasExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private EnvioCavali $envio)
    {
    }

    public function title(): string
    {
        return 'LETRAS';
    }

    public function collection()
    {
        return $this->envio->solicitudes->map(function ($s) {
            $unidad = $s->unidadNegocio;

            return [
                'codigo_venta' => $s->codigo_venta,
                'tipo_venta' => 'VENT',
                'ruc_cuenta_matriz' => $unidad?->ruc,
                'ruc_titular' => $unidad?->ruc,
                'tipo_documento_girador' => 'RUC',
                'numero_documento_girador' => $unidad?->ruc,
                'razon_social_girador' => $unidad?->razon_social,
                'numero_letra' => $s->codigo_venta . '-' . $s->numero_cuota,
                'referencia_girador' => '',
                'fecha_giro' => now()->format('Y-m-d'),
                'lugar_giro' => '',
                'fecha_vencimiento' => $s->fecha_vencimiento,
                'moneda' => 'S',
                'importe' => $s->importe_cuota,
                'lugar_pago' => '',
                'clausula_prorroga' => '',
                'plaza' => '',
                'nombre_proyecto' => $s->nombre_proyecto,
                'protesto' => '',
                'tipo_transferencia' => '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'CODIGO DE VENTA',
            'TIPO VENTA',
            'RUC CUENTA MATRIZ',
            'RUC TITULAR',
            'TIPO DOCUMENTO GIRADOR',
            'NUMERO DOCUMENTO GIRADOR',
            'RAZON SOCIAL GIRADOR',
            'NUMERO DE LETRA',
            'REFERENCIA GIRADOR',
            'FECHA GIRO',
            'LUGAR GIRO',
            'FECHA VENCIMIENTO',
            'MONEDA',
            'IMPORTE',
            'LUGAR PAGO',
            'CLAUSULA PROROOGA',
            'PLAZA',
            'NOMBRE PROYECTO',
            'PROTESTO',
            'TIPO TRANSFERENCIA',
        ];
    }
}
