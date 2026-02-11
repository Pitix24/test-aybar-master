<?php

namespace App\Exports;

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
            $cliente = $s->userCliente;
            $persona = $cliente?->perfilCliente;
            $direccion = $cliente?->direccion; // En aybar es hasOne direccion

            return [
                'codigo_venta' => $s->codigo_venta,
                'tipo_documento_aceptante' => 'DNI',
                'numero_documento_aceptante' => $persona?->dni ?? '—',
                'nombres_aceptante' => $persona?->nombre ?? $cliente?->name,
                'apellidos_aceptante' => '',
                'domicilio_aceptante' => $direccion?->direccion,
                'localidad_aceptante' => $direccion?->distrito?->nombre,
                'correo_electronico_aceptante' => $cliente?->email,
                'telefono_casa_aceptante' => '',
                'celular_aceptante' => $persona?->telefono_principal,
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
