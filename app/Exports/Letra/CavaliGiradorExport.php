<?php

namespace App\Exports\Letra;

use App\Models\EnvioCavali;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CavaliGiradorExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private EnvioCavali $envio)
    {
    }

    public function title(): string
    {
        return 'GIRADOR';
    }

    public function collection()
    {
        return $this->envio->solicitudes
            ->map(function ($s) {
                $u = $s->unidadNegocio;

                return [
                    'ruc_girador' => $u?->ruc,
                    'tipo_documento_rep_legal' => $u?->cavali_girador_tipo_documento,
                    'documento_rep' => $u?->cavali_girador_documento,
                    'nombre_rep_legal' => $u?->cavali_girador_nombre,
                    'apellido_rep_legal' => $u?->cavali_girador_apellido,
                    'email_rep_legal' => $u?->cavali_girador_email,
                    'telefono_rep_legal' => $u?->cavali_girador_telefono,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'RUC GIRADOR',
            'TIPO DOCUMENTO REP. LEGAL',
            'NUMERO DOCUMENTO REP. LEGAL',
            'NOMBRES REP. LEGAL',
            'APELLIDOS REP. LEGAL',
            'CORREO ELECTRONICO REP. LEGAL',
            'TELEFONO REP. LEGAL',
        ];
    }
}
