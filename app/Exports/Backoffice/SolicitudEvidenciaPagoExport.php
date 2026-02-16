<?php

namespace App\Exports\Backoffice;

use App\Models\SolicitudEvidenciaPago;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SolicitudEvidenciaPagoExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $unidad_negocio_id;
    protected $proyecto_id;
    protected $admin;
    protected $estado_id;
    protected $fecha_inicio;
    protected $fecha_fin;
    protected $tipo_cierre;
    protected $tiene_validacion;
    protected $es_asbanc;
    protected $cantidad_evidencias;
    protected $cantidad_correos;
    protected $perPage;
    protected $page;

    public function __construct(
        $buscar,
        $unidad_negocio_id,
        $proyecto_id,
        $admin,
        $estado_id,
        $fecha_inicio,
        $fecha_fin,
        $tipo_cierre,
        $tiene_validacion,
        $es_asbanc,
        $cantidad_evidencias = null,
        $cantidad_correos = null,
        $perPage = 20,
        $page = 1
    ) {
        $this->buscar = $buscar;
        $this->unidad_negocio_id = $unidad_negocio_id;
        $this->proyecto_id = $proyecto_id;
        $this->admin = $admin;
        $this->estado_id = $estado_id;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->tipo_cierre = $tipo_cierre;
        $this->tiene_validacion = $tiene_validacion;
        $this->es_asbanc = $es_asbanc;
        $this->cantidad_evidencias = $cantidad_evidencias;
        $this->cantidad_correos = $cantidad_correos;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return SolicitudEvidenciaPago::query()
            ->with(['unidadNegocio', 'proyecto', 'userCliente.perfilCliente', 'estado', 'gestor'])
            ->when($this->buscar, function ($q) {
                $buscar = $this->buscar;
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('id', 'like', "%{$buscar}%")
                        ->orWhereHas('userCliente', function ($qUser) use ($buscar) {
                            $qUser->where('name', 'like', "%{$buscar}%");
                        })
                        ->orWhereHas('userCliente.perfilCliente', function ($qCliente) use ($buscar) {
                            $qCliente->where('dni', 'like', "%{$buscar}%");
                        });
                });
            })
            ->when($this->estado_id, function ($q) {
                $q->where('estado_solicitud_evidencia_pago_id', $this->estado_id);
            })
            ->when($this->admin, function ($q) {
                if ($this->admin === 'sin_asignar') {
                    $q->whereNull('gestor_id');
                } else {
                    $q->where('gestor_id', $this->admin);
                }
            })
            ->when($this->unidad_negocio_id, function ($q) {
                $q->where('unidad_negocio_id', $this->unidad_negocio_id);
            })
            ->when($this->proyecto_id, function ($q) {
                $q->where('proyecto_id', $this->proyecto_id);
            })
            ->when($this->fecha_inicio, function ($q) {
                $q->whereDate('created_at', '>=', $this->fecha_inicio);
            })
            ->when($this->fecha_fin, function ($q) {
                $q->whereDate('created_at', '<=', $this->fecha_fin);
            })
            ->when($this->tipo_cierre, function ($q) {
                if ($this->tipo_cierre === 'api') {
                    $q->where('slin_evidencia', true);
                }
                if ($this->tipo_cierre === 'manual') {
                    $q->where('resuelto_manual', true);
                }
            })
            ->when($this->tiene_validacion !== '', function ($q) {
                if ($this->tiene_validacion === 'si') {
                    $q->whereNotNull('fecha_validacion');
                }
                if ($this->tiene_validacion === 'no') {
                    $q->whereNull('fecha_validacion');
                }
            })
            ->when($this->es_asbanc !== '', function ($q) {
                if ($this->es_asbanc === 'si') {
                    $q->where('slin_asbanc', true);
                }
                if ($this->es_asbanc === 'no') {
                    $q->where('slin_asbanc', false);
                }
            })
            ->when($this->cantidad_evidencias !== '' && !is_null($this->cantidad_evidencias), function ($q) {
                $q->has('evidencias', '=', $this->cantidad_evidencias);
            })
            ->when($this->cantidad_correos !== '' && !is_null($this->cantidad_correos), function ($q) {
                $q->has('correos', '=', $this->cantidad_correos);
            })
            ->orderBy('created_at', 'desc')
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->gestor?->name ?? 'Falta asignar',
                    $item->unidadNegocio?->nombre ?? 'N/A',
                    $item->proyecto?->nombre ?? 'N/A',
                    $item->etapa,
                    $item->manzana,
                    $item->lote,
                    $item->numero_cuota,
                    $item->userCliente?->name ?? 'N/A',
                    $item->userCliente?->perfilCliente?->dni ?? '—',
                    $item->estado?->nombre ?? 'N/A',
                    $item->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Gestor',
            'Unidad de Negocio',
            'Proyecto',
            'Etapa',
            'Mz.',
            'Lt.',
            'N° Cuota',
            'Cliente',
            'DNI',
            'Estado',
            'Fecha Creación',
        ];
    }
}
