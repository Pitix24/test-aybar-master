<?php

namespace App\Exports\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EntregaFestProspectoExport implements FromCollection, WithHeadings, ShouldAutoSize, WithMapping
{
    protected $entrega_fest_id;
    protected $buscar;
    protected $proyecto_id;
    protected $estado_backoffice;
    protected $estado_contrato_preeliminar_emitido;
    protected $estado_firma_contrato_firmado;
    protected $grupo;
    protected $todo;
    protected $perPage;
    protected $page;

    public function __construct(
        $entrega_fest_id,
        $buscar = '',
        $proyecto_id = '',
        $estado_backoffice = '',
        $estado_contrato_preeliminar_emitido = '',
        $estado_firma_contrato_firmado = '',
        $grupo = '',
        $todo = false,
        $perPage = null,
        $page = null
    ) {
        $this->entrega_fest_id = $entrega_fest_id;
        $this->buscar = $buscar;
        $this->proyecto_id = $proyecto_id;
        $this->estado_backoffice = $estado_backoffice;
        $this->estado_contrato_preeliminar_emitido = $estado_contrato_preeliminar_emitido;
        $this->estado_firma_contrato_firmado = $estado_firma_contrato_firmado;
        $this->grupo = $grupo;
        $this->todo = $todo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        $query = ProspectoEntregaFest::query()
            ->with(['proyecto', 'user', 'invitado'])
            ->where('entrega_fest_id', $this->entrega_fest_id);

        if (!$this->todo) {
            $query->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombres', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%')
                        ->orWhere('email', 'like', '%' . $this->buscar . '%')
                        ->orWhere('celular', 'like', '%' . $this->buscar . '%');
                });
            })
                ->when($this->proyecto_id, fn($q) => $q->where('proyecto_id', $this->proyecto_id))
                ->when($this->estado_backoffice, fn($q) => $q->where('estado_backoffice', $this->estado_backoffice))
                ->when($this->estado_contrato_preeliminar_emitido, fn($q) => $q->where('estado_contrato_preeliminar_emitido', $this->estado_contrato_preeliminar_emitido))
                ->when($this->estado_firma_contrato_firmado, fn($q) => $q->where('estado_firma_contrato_firmado', $this->estado_firma_contrato_firmado))
                ->when($this->grupo, fn($q) => $q->where('grupo', $this->grupo));
        }

        $query->orderBy('id', 'desc');

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get();
    }

    public function map($p): array
    {
        static $index = 0;
        $index++;

        // Obtener etiquetas de estados
        $estadoBackoffice = ProspectoEntregaFest::ESTADO_BACKOFFICE[$p->estado_backoffice]['label'] ?? $p->estado_backoffice;
        $estadoContrato = ProspectoEntregaFest::ESTADO_CONTRATO_PRELIMINAR[$p->estado_contrato_preeliminar_emitido]['label'] ?? $p->estado_contrato_preeliminar_emitido;

        return [
            $index,
            $p->dni,
            $p->nombre_completo . " (" . $p->email . " / " . $p->celular . ")",
            $p->proyecto->nombre ?? 'N/A',
            ($p->lote ?? '') . ($p->manzana ?? ''),
            $p->fecha_culminacion_eecc ? \Carbon\Carbon::parse($p->fecha_culminacion_eecc)->format('d/m/Y') : '',
            $p->link_carpeta_eecc ?? '',
            $p->link_eecc_firmado ?? '',
            strtoupper($estadoBackoffice),
            strtoupper($estadoContrato),
            $p->fecha_firma ? \Carbon\Carbon::parse($p->fecha_firma)->format('d/m/Y') : '',
            $p->fecha_generacion_contrato ? \Carbon\Carbon::parse($p->fecha_generacion_contrato)->format('d/m/Y') : '',
            $p->invitado ? 'SÍ' : 'NO',
        ];
    }

    public function headings(): array
    {
        return [
            'N°',
            'DNI',
            'Cliente',
            'Proyecto',
            'Lote/Mz',
            'Fecha Culminación EECC',
            'Enlace Carpeta EECC',
            'Enlace EECC Firmado',
            'BackOffice',
            'Estado Contrato Preliminar',
            'Fecha para Firmar',
            'Fecha Firmado',
            'Invitado',
        ];
    }
}
