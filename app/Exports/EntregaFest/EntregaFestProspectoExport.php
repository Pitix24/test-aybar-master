<?php

namespace App\Exports\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EntregaFestProspectoExport implements FromQuery, WithHeadings, ShouldAutoSize, WithMapping
{
    /** Contador para la columna "N°" en el map(). */
    protected int $index = 0;

    /**
     * Recibe el array de filtros producido por filtrosActivos() del componente.
     * Si solo viene `evento_id`, exporta TODO el evento sin filtros adicionales.
     *
     * @param  array  $filtros
     */
    public function __construct(protected array $filtros) {}

    // ============================================================
    //                          QUERY
    // ============================================================

    /**
     * 🎯 Usa el MISMO scope que el listado y las stats.
     * Una sola fuente de verdad para los filtros.
     *
     * Cambiamos a FromQuery (en vez de FromCollection) por 2 razones:
     *  1. Maneja datasets grandes sin saturar memoria (chunks automáticos).
     *  2. No necesitamos paginar manualmente con skip/take.
     */
    public function query()
    {
        return ProspectoEntregaFest::query()
            ->with([
                'proyecto', 'reubicadoProyecto', 'user', 'invitado',
                'gestor', 'validador', 'estadoCliente',
            ])
            ->filtrado($this->filtros)
            ->orderBy('id', 'desc');
    }

    // ============================================================
    //                          MAPPING
    // ============================================================

    public function map($p): array
    {
        $this->index++;

        return [
            $this->index,
            $p->id,
            $p->proyecto->nombre ?? 'N/A',
            $p->reubicadoProyecto?->nombre ?? 'N/A',
            $p->dni,
            $p->nombres,
            $p->email,
            $p->celular,
            $p->manzana,
            $p->lote,
            ($p->reubicado_manzana || $p->reubicado_lote)
                ? (($p->reubicado_manzana ?? '') . '-' . ($p->reubicado_lote ?? ''))
                : 'N/A',
            $p->estadoCliente->nombre ?? 'ADENDA',
            $this->formatConfirmacion($p->preinvitacion_confirmada),
            $this->formatConfirmacion($p->invitacion_confirmada),
            $p->grupo,
            $p->gestor->name ?? 'No asignado',
            $this->formatFecha($p->gestor_fecha_asignacion, 'd/m/Y H:i'),
            $this->formatFecha($p->fecha_culminacion_eecc),
            $p->link_carpeta_eecc,
            $p->link_eecc_firmado,
            $p->estado_gestor_backoffice,
            $p->observacion_gestor_backoffice,
            $p->validador->name ?? 'No asignado',
            $this->formatFecha($p->fecha_validacion_eecc),
            $p->estado_backoffice,
            $p->estado_contrato_preeliminar_emitido,
            $p->estado_firma_contrato_firmado,
            $this->formatFecha($p->fecha_firma),
            $this->formatFecha($p->fecha_generacion_contrato),
            $p->user->name ?? 'Sistema',
            $p->invitado ? 'SÍ' : 'NO',
        ];
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID Prospecto',
            'Proyecto',
            'Proyecto Reubicado',
            'DNI',
            'Propietario',
            'Email',
            'Celular',
            'Manzana',
            'Lote',
            'Mz-Lt Reubicado',
            'Estado Cliente',
            'Pre-invitación Conf.',
            'Invitación Conf.',
            'Grupo',
            'Gestor BackOffice',
            'Fecha Asignación Gestor',
            'Fecha Culminación EECC',
            'Link Carpeta EECC',
            'Link EECC Firmado',
            'Estado Gestor BO',
            'Observación Gestor BO',
            'Validador BO (Supervisor)',
            'Fecha Validación BO',
            'Estado Supervisor BO',
            'Estado Contrato Preliminar',
            'Estado Firma Contrato',
            'Fecha Firma',
            'Fecha Generación Contrato',
            'Registrado por',
            '¿Invitado Confirmado?',
        ];
    }

    // ============================================================
    //                          HELPERS
    // ============================================================

    /** Formatea booleanos de confirmación (null = pendiente). */
    protected function formatConfirmacion(?int $valor): string
    {
        if (is_null($valor)) return 'PENDIENTE';
        return $valor ? 'SÍ' : 'NO';
    }

    /** Formatea fechas con manejo de null. */
    protected function formatFecha($fecha, string $formato = 'd/m/Y'): string
    {
        return $fecha ? Carbon::parse($fecha)->format($formato) : '';
    }
}
