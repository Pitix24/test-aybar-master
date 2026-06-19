<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ConsultaClienteService
{
    public function consultar(string $dni): array
    {
        // 1. PRIORIDAD: Consultar el servicio externo (SLIN) como fuente viva y actualizada
        try {
            $clienteResponse = Http::timeout(10)->get("https://aybarcorp.com/slin/cliente/{$dni}");

            if ($clienteResponse->successful()) {
                $cliente = $clienteResponse->json();

                // Si el cliente existe en la API externa de SLIN, procesamos sus lotes obligatoriamente
                if (!empty($cliente)) {

                    // Caso: Existe el cliente pero no tiene empresas vinculadas en SLIN
                    if (empty($cliente['empresas'])) {
                        return [
                            'estado'  => 'cliente_sin_lotes',
                            'mensaje' => 'Cliente encontrado pero sin empresa activa en API SLIN',
                            'data'    => collect(),
                        ];
                    }

                    $informaciones = collect();

                    foreach ($cliente['empresas'] as $empresa) {
                        $response = Http::timeout(10)->get('https://aybarcorp.com/slin/lotes', [
                            'id_cliente' => $empresa['codigo'],
                            'id_empresa' => $empresa['id_empresa'],
                        ]);

                        if (!$response->successful()) {
                            continue;
                        }

                        foreach ($response->json() as $lote) {
                            $informaciones->push((object) [
                                'id' => substr($lote['id_recaudo'], 0, 3)
                                    . $lote['id_manzana']
                                    . $lote['id_lote'],
                                'origen'                  => 'slin',
                                'razon_social'            => $lote['razon_social'],
                                'codigo_cliente'          => $lote['id_recaudo'],
                                'nombre'                  => $lote['apellidos_nombres'],
                                'codigo_proyecto'         => substr($lote['id_recaudo'], 0, 3),
                                'proyecto'                => $lote['descripcion'],
                                'etapa'                   => (int) $lote['id_etapa'],
                                'numero_lote'             => $lote['id_manzana'] . '-' . $lote['id_lote'],
                                'estado_lote'             => $lote['estado'] === 'O' ? 'VENDIDO' : 'DISPONIBLE',
                                'dni'                     => $lote['nit'],
                            ]);
                        }
                    }

                    // Caso: El cliente existe en SLIN pero las consultas de lotes devolvieron un arreglo vacío
                    if ($informaciones->isEmpty()) {
                        return [
                            'estado'  => 'cliente_sin_lotes',
                            'mensaje' => 'Cliente encontrado pero sin lotes vigentes en API SLIN',
                            'data'    => collect(),
                        ];
                    }

                    // ✅ Retorno exitoso desde la fuente principal
                    return [
                        'estado'  => 'ok',
                        'mensaje' => 'Cliente encontrado con datos actualizados en API SLIN',
                        'origen'  => 'slin',
                        'data'    => $informaciones,
                    ];
                }
            }
        } catch (\Exception $e) {
            // Registramos el error de conexión en los logs para auditoría, pero permitimos que el flujo continúe hacia la BD antigua
            Log::warning("[SLIN API] Error al consultar DNI {$dni}, procediendo a Fallback Histórico: " . $e->getMessage());
        }

        // 2. FALLBACK / RESPALDO: Si la API de SLIN falló o no encontró al cliente, se consulta la DB histórica
        $informacionesAntiguas = DB::table('clientes_2')
            ->where('dni', $dni)
            ->get()
            ->map(function ($row) {
                $row->origen = 'antiguo';
                return $row;
            });

        if ($informacionesAntiguas->isNotEmpty()) {
            return [
                'estado'  => 'ok',
                'mensaje' => 'Cliente no encontrado en SLIN. Cargado desde base de datos histórica',
                'origen'  => 'antiguo',
                'data'    => $informacionesAntiguas,
            ];
        }

        // 3. Si no existe registro en ninguna de las dos fuentes de información
        return [
            'estado'  => 'no_cliente',
            'mensaje' => 'No se encontró el DNI especificado en el sistema actual ni en el histórico',
            'data'    => collect(),
        ];
    }
}
