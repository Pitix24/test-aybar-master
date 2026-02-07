<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ConsultaClienteService
{
    public function consultar(string $dni): array
    {
        // 1. Buscar en BD
        $informaciones = DB::table('clientes_2')
            ->where('dni', $dni)
            ->get()
            ->map(function ($row) {
                $row->origen = 'antiguo';
                return $row;
            });

        if ($informaciones->isNotEmpty()) {
            return [
                'estado' => 'ok',
                'mensaje' => 'Cliente encontrado en DB Antiguo',
                'origen' => 'antiguo',
                'data'   => $informaciones,
            ];
        }

        // 2. Consultar servicio externo
        $clienteResponse = Http::get("https://aybarcorp.com/slin/cliente/{$dni}");

        if (! $clienteResponse->successful()) {
            return [
                'estado'  => 'error',
                'mensaje' => 'No existe cliente en API SLIN ni en DB Antiguo',
                'data'    => collect(),
            ];
        }

        $cliente = $clienteResponse->json();

        // ❌ No existe cliente
        if (empty($cliente)) {
            return [
                'estado'  => 'no_cliente',
                'mensaje' => 'Cliente no encontrado en API SLIN',
                'data'    => collect(),
            ];
        }

        // ⚠ Existe cliente pero no tiene empresas
        if (empty($cliente['empresas'])) {
            return [
                'estado'  => 'cliente_sin_lotes',
                'mensaje' => 'Cliente encontrado pero sin empresa en API SLIN',
                'data'    => collect(),
            ];
        }

        // 3. Buscar lotes
        $informaciones = collect();

        foreach ($cliente['empresas'] as $empresa) {
            $response = Http::get('https://aybarcorp.com/slin/lotes', [
                'id_cliente' => $empresa['codigo'],
                'id_empresa' => $empresa['id_empresa'],
            ]);

            if (! $response->successful()) {
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
                    'nombre' => $lote['apellidos_nombres'],
                    'codigo_proyecto'         => substr($lote['id_recaudo'], 0, 3),
                    'proyecto'                => $lote['descripcion'],
                    'etapa'                   => (int) $lote['id_etapa'],
                    'numero_lote'             => $lote['id_manzana'] . '-' . $lote['id_lote'],
                    'estado_lote'             => $lote['estado'] === 'O' ? 'VENDIDO' : 'DISPONIBLE',
                    'dni'         => $lote['nit'],
                ]);
            }
        }

        // ⚠ Cliente existe pero no tiene lotes
        if ($informaciones->isEmpty()) {
            return [
                'estado'  => 'cliente_sin_lotes',
                'mensaje' => 'Cliente encontrado pero sin lotes en API SLIN',
                'data'    => collect(),
            ];
        }

        // ✅ Todo OK
        return [
            'estado' => 'ok',
            'mensaje' => 'Cliente encontrado en API SLIN',
            'origen' => 'slin',
            'data'   => $informaciones,
        ];
    }
}
