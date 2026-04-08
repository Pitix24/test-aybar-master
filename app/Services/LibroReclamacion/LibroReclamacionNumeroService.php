<?php

namespace App\Services\LibroReclamacion;

use App\Models\LibroReclamacion\LibroReclamacionContador;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LibroReclamacionNumeroService
{
    public function generar(int $unidadNegocioId, string $razonSocial): array
    {
        return DB::transaction(function () use ($unidadNegocioId, $razonSocial) {
            $this->asegurarContador($unidadNegocioId, $razonSocial);

            $contador = LibroReclamacionContador::query()
                ->where('unidad_negocio_id', $unidadNegocioId)
                ->lockForUpdate()
                ->first();

            if (! $contador) {
                throw new RuntimeException('No se pudo crear o recuperar el contador del libro de reclamaciones.');
            }

            $contador->ultimo_numero = $contador->ultimo_numero + 1;
            $contador->save();

            $serie = $this->resolverSerie();
            $numeroReclamo = $contador->ultimo_numero;

            return [
                'serie' => $serie,
                'numero_reclamo' => $numeroReclamo,
                'codigo_ticket' => $this->formatearCodigoTicket($serie, $unidadNegocioId, $numeroReclamo),
            ];
        });
    }

    protected function asegurarContador(int $unidadNegocioId, string $razonSocial): void
    {
        $inicio = $this->resolverNumeroInicial($razonSocial);

        LibroReclamacionContador::query()->insertOrIgnore([
            'unidad_negocio_id' => $unidadNegocioId,
            'ultimo_numero' => $inicio,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    protected function resolverNumeroInicial(string $razonSocial): int
    {
        $config = config('libro_reclamacion', []);
        $aybarRazonSocial = $this->normalizarTexto(data_get($config, 'aybar.razon_social', 'AYBAR CORP. S.A.C.'));

        if ($this->normalizarTexto($razonSocial) === $aybarRazonSocial) {
            return (int) data_get($config, 'aybar.numero_inicial', 0);
        }

        return 0;
    }

    protected function resolverSerie(): string
    {
        return (string) data_get(config('libro_reclamacion', []), 'serie', 'TCK');
    }

    protected function formatearCodigoTicket(string $serie, int $unidadNegocioId, int $numeroReclamo): string
    {
        return sprintf('%s-%d-%06d', $serie, $unidadNegocioId, $numeroReclamo);
    }

    protected function normalizarTexto(string $valor): string
    {
        $valor = mb_strtoupper(trim($valor));
        $valor = preg_replace('/\s+/', ' ', $valor);

        return $valor ?? '';
    }
}