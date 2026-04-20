<?php
namespace App\Exports\EntregaFest;

use App\Models\ProspectoBancarizacionEntregaFest;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class EntregaFestProspectoBancarizacionExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $evento_id;
    protected $buscar;
    protected $proyecto_id;
    protected $estado;
    protected $is_todo;
    protected $perPage;
    protected $page;

    public function __construct($evento_id, $buscar = '', $proyecto_id = '', $estado = '', $is_todo = false, $perPage = 20, $page = 1)
    {
        $this->evento_id = $evento_id;
        $this->buscar = $buscar;
        $this->proyecto_id = $proyecto_id;
        $this->estado = $estado;
        $this->is_todo = $is_todo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function query()
    {
        $query = ProspectoBancarizacionEntregaFest::query()
            ->with(['prospecto', 'prospecto.proyecto'])
            ->where('entrega_fest_id', $this->evento_id);

        if (!$this->is_todo) {
            $query->when($this->buscar, function ($q) {
                $q->whereHas('prospecto', function ($sub) {
                    $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                })->orWhere('cuota', 'like', '%' . $this->buscar . '%');
            })
                ->when($this->proyecto_id, function ($q) {
                    $q->whereHas('prospecto', function ($sub) {
                        $sub->where('proyecto_id', $this->proyecto_id);
                    });
                })
                ->when($this->estado, function ($q) {
                    $q->where('estado', $this->estado);
                });
        }

        return $query->orderBy('fecha_deposito_real', 'desc');
    }

    public function headings(): array
    {
        return [
            'ID',
            'DNI',
            'Prospecto',
            'Proyecto',
            'Lote',
            'Manzana',
            'Cuota',
            'Importe',
            'Fecha Depósito Real',
            'Estado',
            'Creado el'
        ];
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->prospecto->dni,
            $item->prospecto->nombres,
            $item->prospecto->proyecto->nombre ?? '-',
            $item->prospecto->lote,
            $item->prospecto->manzana,
            $item->cuota,
            $item->importe,
            $item->fecha_deposito_real->format('d/m/Y'),
            $item->estado,
            $item->created_at->format('d/m/Y H:i'),
        ];
    }
}
