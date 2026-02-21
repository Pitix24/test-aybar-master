<?php

namespace App\Exports\Letra;

use App\Models\EnvioCavali;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CavaliAceptanteExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private EnvioCavali $envio)
    {
    }

    public function title(): string
    {
        return 'ACEPTANTE';
    }

    public function collection()
    {
        return $this->envio->solicitudes->map(function ($s) {

            return [
                'codigo_venta' => $s->codigo_venta,
                'tipo_documento_aceptante' => 'DNI',
                'numero_documento_aceptante' => $s->dni,
                'nombres_aceptante' => $s->nombres,
                'apellidos_aceptante' => '',
                'domicilio_aceptante' => $s->direccion,
                'localidad_aceptante' => $s->distrito,
                'correo_electronico_aceptante' => $s->email,
                'telefono_casa_aceptante' => '',
                'celular_aceptante' => $s->celular,
                'tipo_firmante_aceptante' => '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'CODIGO DE VENTA',
            'TIPO DOCUMENTO ACEPTANTE',
            'NUMERO DOCUMENTO ACEPTANTE',
            'NOMBRES ACEPTANTE',
            'APELLIDOS ACEPTANTE',
            'DOMICILIO ACEPTANTE',
            'LOCALIDAD ACEPTANTE',
            'CORREO ELECTRONICO ACEPTANTE',
            'TELEFONO CASA ACEPTANTE',
            'CELULAR ACEPTANTE',
            'TIPO FIRMANTE ACEPTANTE',
        ];
    }
}
