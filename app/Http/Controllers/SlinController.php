<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SlinController extends Controller
{
    private string $baseUrl;
    private string $baseUrlCronograma;
    private string $baseUrlEstadoCuenta;
    private string $baseUrlComprobante;
    private string $baseUrlGuardarEvidencia;
    private string $remoteBase;
    private string $user;
    private string $password;

    public function __construct()
    {
        $this->baseUrl = 'https://cloudapp.slin.com.pe:7444/activity/v1/api/aybar'; //cliente y lotes
        $this->baseUrlCronograma = 'https://cloudapp.slin.com.pe:7444/activity/api/v1/aybarweb/cronograma';
        $this->baseUrlEstadoCuenta = 'https://cloudapp.slin.com.pe:7444/activity/api/v1/aybarweb/estadocuenta';
        $this->baseUrlComprobante = 'https://prod.slin-ade.pe:8443/Utilidades/api/v1/aybarcorp/GetComprobantesBase64';
        $this->baseUrlGuardarEvidencia = 'https://prod.slin-ade.pe:8443/Utilidades/api/v1/aybarcorp/GuardarEvidencia';
        $this->remoteBase = 'https://aybarcorp.com/slin';

        $this->user = config('services.slin.user');
        $this->password = config('services.slin.password');
    }

    public function verComprobante(Request $request)
    {
        $empresa = $request->query('empresa');
        $comprobante = $request->query('comprobante');

        if (!$empresa || !$comprobante) {
            abort(400, 'Parámetros inválidos');
        }

        $response = Http::get('https://aybarcorp.com/slin/comprobante', [
            'empresa' => $empresa,
            'comprobante' => $comprobante,
        ]);

        if ($response->failed()) {
            abort(404, 'No se pudo obtener el comprobante');
        }

        $json = $response->json();

        if (empty($json['base64'])) {
            abort(500, 'Comprobante inválido');
        }

        $pdfBinary = base64_decode($json['base64']);

        return response($pdfBinary, 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="comprobante.pdf"');
    }

    public function getCliente($dni)
    {
        $response = Http::withBasicAuth($this->user, $this->password)
            ->get("{$this->baseUrl}/clientes/nit/{$dni}");

        if ($response->failed()) {
            return response()->json([
                'error' => true,
                'message' => 'Error consultando cliente',
                'details' => $response->body(),
            ], 500);
        }

        return $response->json();
    }

    public function getLotes(Request $request)
    {
        $request->validate([
            'id_cliente' => 'required',
            'id_empresa' => 'required',
        ]);

        $response = Http::withBasicAuth($this->user, $this->password)
            ->get("{$this->baseUrl}/lotes", [
                'id_cliente' => $request->id_cliente,
                'id_empresa' => $request->id_empresa,
            ]);

        if ($response->failed()) {
            return response()->json([
                'error' => true,
                'message' => 'Error consultando lotes',
                'details' => $response->body(),
            ], 500);
        }

        return $response->json();
    }

    public function getCuotas(Request $request)
    {
        $request->validate([
            'empresa' => 'required|string',
            'lote' => 'required|string',
            'cliente' => 'required|string',
            'contrato' => 'nullable|string',
            'servicio' => 'required|string',
        ]);

        $params = [
            'empresa' => $request->empresa,
            'lote' => $request->lote,
            'cliente' => $request->cliente,
            'contrato' => $request->contrato ?? '',
            'servicio' => $request->servicio,
        ];

        $response = Http::withBasicAuth($this->user, $this->password)
            ->acceptJson()
            ->timeout(15)
            ->get($this->baseUrlCronograma, $params);

        if ($response->failed()) {
            return response()->json([
                'error' => true,
                'status' => $response->status(),
                'detail' => $response->body(),
            ], 502);
        }

        return response()->json($response->json());
    }

    public function getEstadoCuenta(Request $request)
    {
        $request->validate([
            'empresa' => 'required|string',
            'lote' => 'required|string',
            'cliente' => 'required|string',
            'contrato' => 'nullable|string',
            'servicio' => 'required|string',
        ]);

        $params = [
            'empresa' => $request->empresa,
            'lote' => $request->lote,
            'cliente' => $request->cliente,
            'contrato' => $request->contrato ?? '',
            'servicio' => $request->servicio,
        ];

        $response = Http::withBasicAuth($this->user, $this->password)
            ->acceptJson()
            ->timeout(15)
            ->get($this->baseUrlEstadoCuenta, $params);

        if ($response->failed()) {
            return response()->json([
                'error' => true,
                'status' => $response->status(),
                'detail' => $response->body(),
            ], 502);
        }

        return response()->json($response->json());
    }

    public function getCuotaEstadoCuenta(Request $request)
    {
        $request->validate([
            'empresa' => 'required|string',
            'lote' => 'required|string',
            'cliente' => 'required|string',
            'contrato' => 'nullable|string',
            'servicio' => 'required|string',
        ]);

        $params = [
            'empresa' => $request->empresa,
            'lote' => $request->lote,
            'cliente' => $request->cliente,
            'contrato' => $request->contrato ?? '',
            'servicio' => $request->servicio,
        ];

        $lote = $params['lote'];

        /* ===============================
         * CRONOGRAMA
         * =============================== */
        $cronogramaResp = Http::withBasicAuth($this->user, $this->password)
            ->acceptJson()
            ->timeout(15)
            ->get($this->baseUrlCronograma, $params);

        if ($cronogramaResp->failed()) {
            return response()->json([
                'error' => true,
                'origen' => 'cronograma',
                'status' => $cronogramaResp->status(),
                'detail' => $cronogramaResp->body(),
            ], 502);
        }

        $cronograma = $cronogramaResp->json();

        $cronogramaCuotas = collect($cronograma['detalle_cuotas'] ?? [])
            ->keyBy('NroCuota');

        /* ===============================
         * ESTADO DE CUENTA
         * =============================== */
        $estadoResp = Http::withBasicAuth($this->user, $this->password)
            ->acceptJson()
            ->timeout(15)
            ->get($this->baseUrlEstadoCuenta, $params);

        if ($estadoResp->failed()) {
            return response()->json([
                'error' => true,
                'origen' => 'estado_cuenta',
                'status' => $estadoResp->status(),
                'detail' => $estadoResp->body(),
            ], 502);
        }

        $estadoCuentaApi = $estadoResp->json();

        /* ===============================
         * UNIFICACIÓN DE CUOTAS
         * =============================== */
        $cuotasUnificadas = collect($estadoCuentaApi['Cuotas'] ?? [])
            ->map(function ($cuota) use ($cronogramaCuotas, $lote) {

                $nroCuota = $cuota['NroCuota'];
                $cronograma = $cronogramaCuotas->get($nroCuota);

                return array_merge($cuota, [
                    'idCuota' => "{$lote}-{$nroCuota}",
                    'saldo_cronograma' => $cronograma['saldo'] ?? null,
                    'monto_cronograma' => $cronograma['Montocuota'] ?? null,
                    'codigo_cronograma' => $cronograma['codigo'] ?? null,
                ]);
            })
            ->values();

        /* ===============================
         * ESTRUCTURA FINAL
         * =============================== */

        // Mantener cabecera original del estado de cuenta
        $unificado = $estadoCuentaApi;

        // Agregar datos financieros reales desde cronograma
        $unificado['importe_financiado']
            = $cronograma['datos_cabecera']['importe_financiado'] ?? null;

        $unificado['importe_amortizado']
            = $cronograma['datos_cabecera']['importe_amortizado'] ?? null;

        // Reemplazar cuotas por versión unificada
        $unificado['Cuotas'] = $cuotasUnificadas;

        return response()->json($unificado);
    }

    public function getComprobante(Request $request)
    {
        $data = $request->validate([
            'empresa' => 'required|string',
            'comprobante' => 'required|string',
        ]);

        try {
            $url = sprintf(
                '%s/%s/%s',
                $this->baseUrlComprobante,
                $data['empresa'],
                $data['comprobante']
            );

            $response = Http::withBasicAuth($this->user, $this->password)
                ->acceptJson()
                ->timeout(20)
                ->get($url);

            if ($response->failed()) {
                return response()->json([
                    'error' => true,
                    'message' => 'Error consultando comprobante en SLIN',
                    'status' => $response->status(),
                    'details' => $response->body(),
                ], 502);
            }

            return response()->json($response->json());
        } catch (\Throwable $e) {
            \Log::error('SLIN GetComprobante error', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => true,
                'message' => 'Error interno del servidor',
            ], 500);
        }
    }

    public function postGuardarEvidencia(Request $request)
    {
        $request->validate([
            'lote'          => 'required|string',
            'cliente'       => 'required|string',
            'contrato'      => 'nullable|string',
            'idcobranzas'   => 'required|string',
            'base64Image'   => 'required|string',
            'nrooperacion'   => 'required|string',
            'fechaoperacion'   => 'required|string',
            'mtooperacion'   => 'required|string',
        ]);

        $payload = [
            'lote'        => $request->lote,
            'cliente'     => $request->cliente,
            'contrato'    => $request->contrato ?? '',
            'idcobranzas' => $request->idcobranzas,
            'base64Image' => $request->base64Image,
            'nrooperacion' => $request->nrooperacion,
            'fechaoperacion' => $request->fechaoperacion,
            'mtooperacion' => $request->mtooperacion,
        ];

        $response = Http::withBasicAuth($this->user, $this->password)
            ->acceptJson()
            ->contentType('application/json')
            ->timeout(30)
            ->post($this->baseUrlGuardarEvidencia, $payload);

        if ($response->failed()) {
            return response()->json([
                'error'  => true,
                'status' => $response->status(),
                'detail' => $response->body(),
            ], 502);
        }

        return response()->json([
            'error' => false,
            'data'  => $response->json(),
        ]);
    }

    public function probarCliente()
    {
        //$dni = "20508775742";//desmaterializada
        //$dni = "47693208";//desmaterializada
        //$dni = "72397392"; //desmaterializada
        $dni = "71598773"; //desmaterializada

        $response = Http::get("{$this->remoteBase}/cliente/{$dni}");

        return $response->json();
    }

    public function probarLotes()
    {
        /*$params = [
            "id_cliente" => "C14022",
            "id_empresa" => "014",
        ];*/

        /*$params = [
            "id_cliente" => "C19471",
            "id_empresa" => "014",
        ];*/

        $params = [
            "id_cliente" => "C19480",
            "id_empresa" => "018",
        ];

        $response = Http::get("{$this->remoteBase}/lotes", $params);

        return $response->json();
    }

    public function probarCuotas()
    {
        $params = [
            'empresa' => '014',
            'lote' => '00101-A-0001', //proyecto/etapa-manza-lote
            'cliente' => 'C10838',
            'contrato' => '', //opcional//si es null, porque fue migrado
            'servicio' => '02', //default, solo para cuotas
        ];

        $response = Http::get("{$this->remoteBase}/cuotas", $params);

        return $response->json();
    }

    public function probarEstadoCuenta()
    {
        $params = [
            'empresa' => '014',
            'lote' => '00101-A-0001', //proyecto/etapa-manza-lote
            'cliente' => 'C10838',
            'contrato' => '', //opcional//si es null, porque fue migrado
            'servicio' => '02', //default, solo para cuotas
        ];

        $response = Http::acceptJson()
            ->get("{$this->remoteBase}/estado-cuenta", $params);

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'error' => $response->body(),
            ]);
        }

        return response()->json([
            'status' => $response->status(),
            'data' => $response->json(),
        ]);
    }

    public function probarCuotaEstadoCuenta()
    {
        /*$params = [
            'empresa' => '014',
            'lote' => '02503-Y3-0017', //proyecto/etapa-manza-lote
            'cliente' => 'C14022',
            'contrato' => '', //opcional//si es null, porque fue migrado
            'servicio' => '02', //default, solo para cuotas
        ];*/

        /*$params = [
            'empresa' => '014',
            'lote' => '02503-S3-0016', //proyecto/etapa-manza-lote
            'cliente' => 'C19471',
            'contrato' => '', //opcional//si es null, porque fue migrado
            'servicio' => '02', //default, solo para cuotas
        ];*/

        $params = [
            'empresa' => '018',
            'lote' => '00101-J-0002', //proyecto/etapa-manza-lote
            'cliente' => 'C19480',
            'contrato' => '', //opcional//si es null, porque fue migrado
            'servicio' => '02', //default, solo para cuotas
        ];

        $response = Http::acceptJson()
            ->get("{$this->remoteBase}/cuota-estado-cuenta", $params);

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'error' => $response->body(),
            ]);
        }

        return response()->json([
            'status' => $response->status(),
            'data' => $response->json(),
        ]);
    }

    public function probarComprobante()
    {
        $params = [
            'empresa' => '019',
            'comprobante' => '01-FF01-00000002',
        ];

        $response = Http::acceptJson()
            ->get("{$this->remoteBase}/comprobante", $params);

        if ($response->failed()) {
            return response()->json([
                'status' => $response->status(),
                'error' => $response->body(),
            ]);
        }

        return response()->json([
            'status' => $response->status(),
            'data' => $response->json(),
        ]);
    }

    public function probarEvidencia()
    {
        $params = [
            'empresa' => '018',
            'lote' => '02003-N3-0006',
            'cliente' => 'C03704',
            'contrato' => '', //opcional//si es null, porque fue migrado
            'idcobranzas' => '02*02003-N3-0006*C03704-021',
            'base64Image' => '/9j/4}',
        ];

        $response = Http::post("{$this->remoteBase}/evidencia", $params);

        return $response->json();
    }
}
