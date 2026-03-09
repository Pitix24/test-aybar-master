<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MopTareaPlantillaExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'user_email' => 'staff_operativo_1@aybar.com',
                'titulo' => 'Verificar perimetro',
                'fase' => 'ANTES',
                'instruccion' => 'Recorrer todo el limite del area del evento verificando cintas de peligro.'
            ],
            [
                'user_email' => 'asesor_entrega_fest_1@aybar.com',
                'titulo' => 'Prueba de tablets',
                'fase' => 'ANTES',
                'instruccion' => 'Cargar y probar las 5 tablets asignadas para lectura de QR.'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'user_email',
            'titulo',
            'fase',
            'instruccion'
        ];
    }
}
