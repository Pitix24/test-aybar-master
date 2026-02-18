<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ConsultaClienteService;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaCodigoClienteController extends Controller
{
    public function index(Request $request)
    {
        $dni = $request->input('dni');
        $informaciones = collect();

        if ($dni) {

            // 1️⃣ Buscar primero en base de datos
            $informaciones = DB::table('clientes_2')
                ->where('dni_ruc_cliente', $dni)
                ->get();

            // 2️⃣ Si no hay datos en DB, consultar API
            if ($informaciones->isEmpty()) {

                $cliente = Http::get("https://aybarcorp.com/slin/cliente/{$dni}")->json();

                if (!empty($cliente['empresas'])) {

                    foreach ($cliente['empresas'] as $empresa) {

                        $response = Http::get('https://aybarcorp.com/slin/lotes', [
                            'id_cliente' => $empresa['codigo'],
                            'id_empresa' => $empresa['id_empresa'],
                        ]);

                        if (!$response->successful()) {
                            continue;
                        }

                        foreach ($response->json() as $lote) {

                            $informaciones->push((object) [
                                'razon_social'            => $lote['razon_social'],
                                'codigo_cliente'          => $lote['id_recaudo'],
                                'nombre_completo_cliente' => $lote['apellidos_nombres'],
                                'codigo_proyecto'         => substr($lote['id_recaudo'], 0, 3),
                                'proyecto'                => $lote['descripcion'],
                                'etapa'                   => (int) $lote['id_etapa'],
                                'numero_lote'             => $lote['id_manzana'] . '-' . $lote['id_lote'],
                                'estado_lote'             => $lote['estado'] === 'O' ? 'VENDIDO' : 'DISPONIBLE',
                                'dni_ruc_cliente'         => $lote['nit'],
                            ]);
                        }
                    }
                }
            }
        }

        return view('web.paginas.consulta-codigo-cliente', compact('informaciones', 'dni'));
    }

    public function consultarClienteDbApi(Request $request, ConsultaClienteService $service)
    {
        $request->validate([
            'dni' => 'required',
        ]);

        $data = $service->consultar($request->dni);

        return response()->json([
            'data' => $data,
        ]);
    }
}
