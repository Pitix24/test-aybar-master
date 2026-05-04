<?php

namespace App\Services\LibroReclamacion;

use App\Models\LibroReclamacion\LibroReclamacion;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LibroReclamacionNumeroService
{
    public function generar(?int $unidadNegocioId): array
    {
        return DB::transaction(function () use ($unidadNegocioId) {
            $serie = $this->resolverSerie();

            // Global generation when no unidad provided (unidadNegocioId <= 0)
            if ((int) $unidadNegocioId <= 0) {
                $ultimoNumero = LibroReclamacion::query()
                    ->whereNull('unidad_negocio_id')
                    ->max('numero_reclamo');

                $numeroConfigurado = $this->resolverNumeroInicial(null);

                if (is_null($ultimoNumero)) {
                    $numeroReclamo = max(1, $numeroConfigurado);
                } else {
                    $numeroReclamo = max((int) $ultimoNumero + 1, $numeroConfigurado);
                }

                $codigoTicket = $this->formatearCodigoTicket($serie, $numeroReclamo);

                return [
                    'serie' => $serie,
                    'numero_reclamo' => $numeroReclamo,
                    'codigo_ticket' => $codigoTicket,
                ];
            }

            // Lock the business unit row to serialize number allocation per unit.
            $unidad = UnidadNegocio::query()
                ->whereKey($unidadNegocioId)
                ->lockForUpdate()
                ->first();

            if (!$unidad) {
                throw new RuntimeException('No se encontro la unidad de negocio para generar el ticket.');
            }

            $ultimoNumero = LibroReclamacion::query()
                ->where('unidad_negocio_id', $unidadNegocioId)
                ->max('numero_reclamo');

            $numeroConfigurado = $this->resolverNumeroInicial($unidad);

            if (is_null($ultimoNumero)) {
                $numeroReclamo = max(1, $numeroConfigurado);
            } else {
                $numeroReclamo = max((int) $ultimoNumero + 1, $numeroConfigurado);
            }

            return [
                'serie' => $serie,
                'numero_reclamo' => $numeroReclamo,
                'codigo_ticket' => $this->formatearCodigoTicket($this->resolverCodigoUnidad($unidad), $numeroReclamo),
            ];
        });
    }

    protected function resolverNumeroInicial(?UnidadNegocio $unidad): int
    {
        if (!$unidad) {
            return 0;
        }

        $config = config('libro_reclamacion_ticket', []);

        // Priority 1: Match by ID (ID 1 is Aybar)
        if ($unidad->id === 1) {
            return (int) data_get($config, 'aybar.numero_inicial', 0);
        }

        // Priority 2: Match by Razon Social (fallback)
        $razonSocial = (string) ($unidad->razon_social ?: $unidad->nombre ?: '');
        $aybarRazonSocial = $this->normalizarTexto(data_get($config, 'aybar.razon_social', 'AYBAR CORP. S.A.C.'));

        if ($this->normalizarTexto($razonSocial) === $aybarRazonSocial) {
            return (int) data_get($config, 'aybar.numero_inicial', 0);
        }

        return 0;
    }

    protected function resolverSerie(): string
    {
        return (string) data_get(config('libro_reclamacion_ticket', []), 'serie', 'TCK');
    }

    protected function formatearCodigoTicket(string $unidadCodigo, int $numeroReclamo): string
    {
        return sprintf('%s-%06d', $unidadCodigo, $numeroReclamo);
    }

    protected function resolverCodigoUnidad(?UnidadNegocio $unidad): string
    {
        if (!$unidad) {
            return 'TCK';
        }

        $codigoUnidad = strtoupper(trim((string) ($unidad->codigo ?? '')));
        if (preg_match('/^[A-Z]{3}$/', $codigoUnidad)) {
            return $codigoUnidad;
        }

        if ($unidad->id) {
            return UnidadNegocio::generarCodigoSecuencial((int) $unidad->id);
        }

        return 'TCK';
    }

    protected function normalizarTexto(string $valor): string
    {
        $valor = mb_strtoupper(trim($valor));
        $valor = preg_replace('/\s+/', ' ', $valor);

        return $valor ?? '';
    }
}
