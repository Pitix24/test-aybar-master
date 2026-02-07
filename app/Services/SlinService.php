<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SlinService
{
    protected string $baseUrl;
    protected string $user;
    protected string $password;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.slin.url'), '/');
        $this->user = config('services.slin.user');
        $this->password = config('services.slin.password');
    }

    public function getClientePorDni(string $dni): ?array
    {
        $response = Http::withBasicAuth($this->user, $this->password)
            ->timeout(10)
            ->get("{$this->baseUrl}/clientes/nit/{$dni}");

        return $response->failed() ? null : $response->json();
    }

    public function getLotes(string $idCliente, string $idEmpresa): ?array
    {
        $response = Http::withBasicAuth($this->user, $this->password)
            ->timeout(10)
            ->get("{$this->baseUrl}/lotes", [
                'id_cliente' => $idCliente,
                'id_empresa' => $idEmpresa,
            ]);

        return $response->failed() ? null : $response->json();
    }

    public function getCuotas(array $params): ?array
    {
        $response = Http::withBasicAuth($this->user, $this->password)
            ->timeout(15)
            ->get("{$this->baseUrl}/cuotas", [
                'id_empresa' => $params['id_empresa'],
                'id_cliente' => $params['id_cliente'],
                'id_proyecto' => $params['id_proyecto'],
                'id_etapa' => $params['id_etapa'],
                'id_manzana' => $params['id_manzana'],
                'id_lote' => $params['id_lote'],
            ]);

        return $response->failed() ? null : $response->json();
    }
}
