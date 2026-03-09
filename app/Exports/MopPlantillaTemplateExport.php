<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MopPlantillaTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'rol_nombre' => 'Staff Operativo',
                'fase' => 'DURANTE',
                'instruccion' => 'Supervisar flujo de personas en ingresos.',
                'prioridad' => 1
            ],
            [
                'rol_nombre' => 'Staff de Lectura',
                'fase' => 'DURANTE',
                'instruccion' => 'Validar QRs de invitados con la aplicación móvil.',
                'prioridad' => 2
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'rol_nombre',
            'fase',
            'instruccion',
            'prioridad'
        ];
    }
}
