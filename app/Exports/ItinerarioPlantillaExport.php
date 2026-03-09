<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItinerarioPlantillaExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            [
                'hora_inicio' => '08:00',
                'hora_fin' => '09:00',
                'titulo' => 'Apertura de Puertas',
                'descripcion' => 'Ingreso de invitados y registro inicial',
                'ubicacion' => 'Zona de Ingreso',
                'responsable_rol' => 'Seguridad / Recepción',
                'checklist' => 'Verificar QR;Entregar pulseras;Contar aforo'
            ],
            [
                'hora_inicio' => '09:00',
                'hora_fin' => '10:00',
                'titulo' => 'Palabras de Bienvenida',
                'descripcion' => 'Discurso del Gerente General',
                'ubicacion' => 'Escenario Principal',
                'responsable_rol' => 'Gerencia',
                'checklist' => 'Microfono listo;Agua en podio'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'hora_inicio',
            'hora_fin',
            'titulo',
            'descripcion',
            'ubicacion',
            'responsable_rol',
            'checklist'
        ];
    }
}
