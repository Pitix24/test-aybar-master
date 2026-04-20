<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('libro_reclamacions')) {
            return;
        }

        $stats = [
            'total_evaluados' => 0,
            'total_actualizados_registros' => 0,
            'total_actualizados_campos' => 0,
            'total_conflictos_campos' => 0,
            'total_pendientes_registros' => 0,
        ];

        DB::table('libro_reclamacions')
            ->select([
                'ticket',
                'cliente_tipo_documento',
                'cliente_documento',
                'cliente_nombre',
                'cliente_email',
                'cliente_celular',
                'cliente_direccion',
                'tipo_documento',
                'numero_documento',
                'nombre',
                'apellido_paterno',
                'apellido_materno',
                'email',
                'telefono',
                'domicilio',
            ])
            ->orderBy('ticket')
            ->chunkById(500, function ($rows) use (&$stats): void {
                foreach ($rows as $row) {
                    $stats['total_evaluados']++;

                    $updates = [];
                    $registroTienePendientes = false;

                    $this->resolverCampo(
                        $updates,
                        $stats,
                        $registroTienePendientes,
                        'cliente_tipo_documento',
                        $row->cliente_tipo_documento,
                        $this->mapearTipoDocumento($row->tipo_documento)
                    );

                    $this->resolverCampo(
                        $updates,
                        $stats,
                        $registroTienePendientes,
                        'cliente_documento',
                        $row->cliente_documento,
                        $this->limpiarTexto($row->numero_documento)
                    );

                    $this->resolverCampo(
                        $updates,
                        $stats,
                        $registroTienePendientes,
                        'cliente_nombre',
                        $row->cliente_nombre,
                        $this->construirNombreCompleto($row->nombre, $row->apellido_paterno, $row->apellido_materno)
                    );

                    $this->resolverCampo(
                        $updates,
                        $stats,
                        $registroTienePendientes,
                        'cliente_email',
                        $row->cliente_email,
                        $this->normalizarEmail($row->email)
                    );

                    $this->resolverCampo(
                        $updates,
                        $stats,
                        $registroTienePendientes,
                        'cliente_celular',
                        $row->cliente_celular,
                        $this->limpiarTexto($row->telefono)
                    );

                    $this->resolverCampo(
                        $updates,
                        $stats,
                        $registroTienePendientes,
                        'cliente_direccion',
                        $row->cliente_direccion,
                        $this->limpiarTexto($row->domicilio)
                    );

                    if ($registroTienePendientes) {
                        $stats['total_pendientes_registros']++;
                    }

                    if (! empty($updates)) {
                        DB::table('libro_reclamacions')
                            ->where('ticket', $row->ticket)
                            ->update($updates);

                        $stats['total_actualizados_registros']++;
                        $stats['total_actualizados_campos'] += count($updates);
                    }
                }
            }, 'ticket');

        Log::info('[FASE_3][LIBRO_RECLAMACIONES] Backfill canonico completado.', $stats);
    }

    public function down(): void
    {
        // Data migration without safe rollback. No-op by design.
    }

    private function resolverCampo(
        array &$updates,
        array &$stats,
        bool &$registroTienePendientes,
        string $campoCanonico,
        mixed $valorCanonico,
        ?string $valorLegacyTransformado
    ): void {
        $canonicoLimpio = $this->limpiarTexto($valorCanonico);

        if ($canonicoLimpio !== null) {
            if ($valorLegacyTransformado !== null && ! $this->sonEquivalentes($campoCanonico, $canonicoLimpio, $valorLegacyTransformado)) {
                $stats['total_conflictos_campos']++;
            }

            return;
        }

        if ($valorLegacyTransformado !== null) {
            $updates[$campoCanonico] = $valorLegacyTransformado;

            return;
        }

        $registroTienePendientes = true;
    }

    private function mapearTipoDocumento(mixed $valor): ?string
    {
        $limpio = $this->limpiarTexto($valor);

        if ($limpio === null) {
            return null;
        }

        $normalizado = strtoupper(str_replace(' ', '_', $limpio));
        $permitidos = ['DNI', 'RUC', 'CE', 'NO_DEFINIDO'];

        if (in_array($normalizado, $permitidos, true)) {
            return $normalizado;
        }

        return 'NO_DEFINIDO';
    }

    private function construirNombreCompleto(mixed $nombre, mixed $apellidoPaterno, mixed $apellidoMaterno): ?string
    {
        $partes = [];

        foreach ([$nombre, $apellidoPaterno, $apellidoMaterno] as $parte) {
            $limpio = $this->limpiarTexto($parte);

            if ($limpio !== null) {
                $partes[] = $limpio;
            }
        }

        if (empty($partes)) {
            return null;
        }

        return implode(' ', $partes);
    }

    private function normalizarEmail(mixed $valor): ?string
    {
        $limpio = $this->limpiarTexto($valor);

        return $limpio === null ? null : strtolower($limpio);
    }

    private function limpiarTexto(mixed $valor): ?string
    {
        $texto = trim((string) ($valor ?? ''));

        if ($texto === '') {
            return null;
        }

        $token = strtoupper(str_replace(['_', '.'], ' ', $texto));
        $token = preg_replace('/\s+/', ' ', $token) ?: '';

        if (in_array($token, ['-', 'N/D', 'NO DEFINIDO'], true)) {
            return null;
        }

        return $texto;
    }

    private function sonEquivalentes(string $campoCanonico, string $canonico, string $legacy): bool
    {
        if ($campoCanonico === 'cliente_email') {
            return strtolower($canonico) === strtolower($legacy);
        }

        if ($campoCanonico === 'cliente_tipo_documento') {
            $normalizarTipo = fn (string $valor): string => strtoupper(str_replace(' ', '_', trim($valor)));

            return $normalizarTipo($canonico) === $normalizarTipo($legacy);
        }

        $normalizar = static function (string $valor): string {
            $valor = strtoupper(trim($valor));
            $valor = preg_replace('/\s+/', ' ', $valor) ?: '';

            return $valor;
        };

        return $normalizar($canonico) === $normalizar($legacy);
    }
};
