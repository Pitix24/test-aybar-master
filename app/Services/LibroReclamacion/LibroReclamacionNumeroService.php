<?php

namespace App\Services\LibroReclamacion;

use App\Models\LibroReclamacion\LibroReclamacion;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LibroReclamacionNumeroService
{
    public function generar(int $unidadNegocioId): array
    {
        return DB::transaction(function () use ($unidadNegocioId) {
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

            $serie = $this->resolverSerie();

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
            throw new RuntimeException('No se encontro la unidad de negocio para generar el ticket.');
        }

        $codigoConfigurado = $this->resolverCodigoConfigurado($unidad);
        if ($codigoConfigurado !== null) {
            return $codigoConfigurado;
        }

        if ($unidad->id) {
            return UnidadNegocio::generarCodigoSecuencial((int) $unidad->id);
        }

        return 'UNI';
    }

    protected function resolverCodigoConfigurado(UnidadNegocio $unidad): ?string
    {
        $config = (array) data_get(config('libro_reclamacion_ticket', []), 'codigos_unidad_negocio', []);
        $porId = (array) data_get($config, 'por_id', []);
        $porNombre = (array) data_get($config, 'por_nombre', []);

        $id = (int) ($unidad->id ?? 0);
        if ($id > 0 && array_key_exists($id, $porId)) {
            $codigo = strtoupper(trim((string) $porId[$id]));
            if (preg_match('/^[A-Z]{3}$/', $codigo)) {
                return $codigo;
            }
        }

        $nombreNormalizado = $this->normalizarTexto((string) ($unidad->nombre ?? ''));
        $razonSocialNormalizada = $this->normalizarTexto((string) ($unidad->razon_social ?? ''));

        foreach ($porNombre as $nombre => $codigo) {
            $clave = $this->normalizarTexto((string) $nombre);

            if ($clave === $nombreNormalizado || $clave === $razonSocialNormalizada) {
                $codigo = strtoupper(trim((string) $codigo));
                if (preg_match('/^[A-Z]{3}$/', $codigo)) {
                    return $codigo;
                }
            }
        }

        return null;
    }

    protected function normalizarTexto(string $valor): string
    {
        $valor = mb_strtoupper(trim($valor));
        $valor = preg_replace('/\s+/', ' ', $valor);

        return $valor ?? '';
    }

}