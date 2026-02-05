<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\TipoSolicitud;
use App\Models\SubTipoSolicitud;
use Illuminate\Database\Seeder;

class TipoSolicitudSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nombre' => 'Soporte de Hardware',
                'tiempo_solucion' => 24,
                'activo' => true,
                'areas' => ['Soporte Técnico'],
                'subtipos' => [
                    ['nombre' => 'Reparación de Laptop/PC', 'tiempo_solucion' => 48],
                    ['nombre' => 'Sustitución de Periféricos', 'tiempo_solucion' => 4],
                    ['nombre' => 'Configuración de Impresoras', 'tiempo_solucion' => 2],
                ],
            ],
            [
                'nombre' => 'Soporte de Software',
                'tiempo_solucion' => 8,
                'activo' => true,
                'areas' => ['Soporte Técnico'],
                'subtipos' => [
                    ['nombre' => 'Acceso a ERP Aybar', 'tiempo_solucion' => 1],
                    ['nombre' => 'Restablecimiento de Password', 'tiempo_solucion' => 1],
                    ['nombre' => 'Instalación de Software Base', 'tiempo_solucion' => 4],
                ],
            ],
            [
                'nombre' => 'Gestión de Servicios',
                'tiempo_solucion' => 12,
                'activo' => true,
                'areas' => ['Atención al Cliente', 'Ventas'],
                'subtipos' => [
                    ['nombre' => 'Consulta de Estado de Pedido', 'tiempo_solucion' => 2],
                    ['nombre' => 'Actualización de Datos de Cliente', 'tiempo_solucion' => 4],
                    ['nombre' => 'Emisión de Comprobantes', 'tiempo_solucion' => 1],
                ],
            ],
            [
                'nombre' => 'Operaciones Logísticas',
                'tiempo_solucion' => 48,
                'activo' => true,
                'areas' => ['Logística'],
                'subtipos' => [
                    ['nombre' => 'Solicitud de Flete', 'tiempo_solucion' => 24],
                    ['nombre' => 'Inventario de Entrada', 'tiempo_solucion' => 48],
                    ['nombre' => 'Despacho Urgente', 'tiempo_solucion' => 8],
                ],
            ],
            [
                'nombre' => 'Gestión de Talento',
                'tiempo_solucion' => 72,
                'activo' => true,
                'areas' => ['Recursos Humanos'],
                'subtipos' => [
                    ['nombre' => 'Solicitud de Vacaciones', 'tiempo_solucion' => 24],
                    ['nombre' => 'Certificado de Trabajo', 'tiempo_solucion' => 48],
                    ['nombre' => 'Actualización de Seguro', 'tiempo_solucion' => 72],
                ],
            ],
        ];

        foreach ($data as $tipoData) {
            // 1. Crear el Tipo de Solicitud
            $tipoSolicitud = TipoSolicitud::updateOrCreate(
                ['nombre' => $tipoData['nombre']],
                [
                    'tiempo_solucion' => $tipoData['tiempo_solucion'],
                    'activo' => $tipoData['activo'],
                ]
            );

            // 2. Relacionar con Áreas (Pivot)
            $areasIds = Area::whereIn('nombre', $tipoData['areas'])->pluck('id');
            $tipoSolicitud->areas()->syncWithoutDetaching($areasIds);

            // 3. Crear los Subtipos
            foreach ($tipoData['subtipos'] as $subtipoData) {
                SubTipoSolicitud::updateOrCreate(
                    [
                        'tipo_solicitud_id' => $tipoSolicitud->id,
                        'nombre' => $subtipoData['nombre']
                    ],
                    [
                        'tiempo_solucion' => $subtipoData['tiempo_solucion'],
                        'activo' => true,
                    ]
                );
            }
        }
    }
}
