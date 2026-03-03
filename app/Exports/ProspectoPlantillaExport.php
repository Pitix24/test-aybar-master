<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProspectoPlantillaExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        // Retornamos un array vacío o con un ejemplo sutil
        return [
            [
                'proyecto_id' => 'ID_AQUI',
                'user_id' => '',
                'dni' => '12345678',
                'nombres' => 'JUAN PEREZ',
                'email' => 'juan@ejemplo.com',
                'celular' => '999888777',
                'lote' => '1',
                'manzana' => 'A',
                'grupo' => 'A',
                'gestor_backoffice_id' => '',
                'fecha_culminacion_eecc' => '',
                'link_carpeta_eecc' => '',
                'link_eecc_firmado' => '',
                'validador_backoffice_id' => '',
                'fecha_validacion_eecc' => '',
                'estado_backoffice' => 'pendiente',
                'estado_contrato_preeliminar_emitido' => 'pendiente',
                'estado_firma_contrato_firmado' => 'pendiente',
                'fecha_firma' => '',
                'fecha_generacion_contrato' => '',
                'dni_2' => '',
                'nombres_2' => '',
                'email_2' => '',
                'celular_2' => '',
                'dni_3' => '',
                'nombres_3' => '',
                'email_3' => '',
                'celular_3' => '',
                'dni_4' => '',
                'nombres_4' => '',
                'email_4' => '',
                'celular_4' => '',
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'proyecto_id',
            'user_id',
            'dni',
            'nombres',
            'email',
            'celular',
            'lote',
            'manzana',
            'grupo',
            'gestor_backoffice_id',
            'fecha_culminacion_eecc',
            'link_carpeta_eecc',
            'link_eecc_firmado',
            'validador_backoffice_id',
            'fecha_validacion_eecc',
            'estado_backoffice',
            'estado_contrato_preeliminar_emitido',
            'estado_firma_contrato_firmado',
            'fecha_firma',
            'fecha_generacion_contrato',
            'dni_2',
            'nombres_2',
            'email_2',
            'celular_2',
            'dni_3',
            'nombres_3',
            'email_3',
            'celular_3',
            'dni_4',
            'nombres_4',
            'email_4',
            'celular_4',
        ];
    }
}
