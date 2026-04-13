<?php

namespace App\Services\LibroReclamacion;

use App\Models\LibroReclamacion\LibroReclamacionContador;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class LibroReclamacionNumeroService
{
    public function generar(int $unidadNegocioId): array
    {
        return DB::transaction(function () use ($unidadNegocioId) {
            $unidad = UnidadNegocio::query()->find($unidadNegocioId);

            if (! $unidad) {
                throw new RuntimeException('No se encontro la unidad de negocio para generar el ticket.');
            }

            $this->asegurarContador($unidadNegocioId, $unidad->nombre ?? '');

            $contador = LibroReclamacionContador::query()
                ->where('unidad_negocio_id', $unidadNegocioId)
                ->lockForUpdate()
                ->first();

            if (! $contador) {
                throw new RuntimeException('No se pudo crear o recuperar el contador del libro de reclamaciones.');
            }

            $contador->siguiente_numero = $contador->siguiente_numero + 1;
            $contador->save();

            $serie = $this->resolverSerie();
            $numeroReclamo = $contador->siguiente_numero;

            return [
                'serie' => $serie,
                'numero_reclamo' => $numeroReclamo,
                'codigo_ticket' => $this->formatearCodigoTicket($this->resolverCodigoUnidad($unidad), $numeroReclamo),
            ];
        });
    }

    protected function asegurarContador(int $unidadNegocioId, string $razonSocial): void
    {
        $inicio = $this->resolverNumeroInicial($razonSocial);

        LibroReclamacionContador::query()->insertOrIgnore([
            'unidad_negocio_id' => $unidadNegocioId,
            'siguiente_numero' => $inicio,
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

    protected function formatearCodigoTicket(string $unidadCodigo, int $numeroReclamo): string
    {
        return sprintf('%s-%06d', $unidadCodigo, $numeroReclamo);
    }

    protected function resolverCodigoUnidad(?UnidadNegocio $unidad): string
    {
        if (! $unidad) {
            throw new RuntimeException('No se encontro la unidad de negocio para generar el ticket.');
        }

        $codigo = strtoupper(trim((string) $unidad->codigo));

        if (preg_match('/^[A-Z]{3}$/', $codigo)) {
            return $codigo;
        }

        if ($unidad->id) {
            return UnidadNegocio::generarCodigoSecuencial((int) $unidad->id);
        }

        return 'UNI';
    }

    protected function normalizarTexto(string $valor): string
    {
        $valor = mb_strtoupper(trim($valor));
        $valor = preg_replace('/\s+/', ' ', $valor);

        return $valor ?? '';
    }

}