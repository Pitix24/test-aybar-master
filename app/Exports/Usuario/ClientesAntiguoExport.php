<?php

namespace App\Exports\Usuario;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ClientesAntiguoExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected ?string $buscar;
    protected ?string $codigo_cliente;
    protected ?int $perPage;
    protected ?int $page;
    protected bool $todo;

    public function __construct(
        ?string $buscar = null,
        ?string $codigo_cliente = null,
        ?int $perPage = null,
        ?int $page = null,
        bool $todo = false
    ) {
        $this->buscar = $buscar;
        $this->codigo_cliente = $codigo_cliente;
        $this->perPage = $perPage;
        $this->page = $page;
        $this->todo = $todo;
    }

    public function collection()
    {
        $query = DB::table('clientes_2');

        if (!$this->todo) {
            $query->when($this->buscar !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nombre', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                });
            })
                ->when($this->codigo_cliente !== '', function ($q) {
                    $q->where('codigo_cliente', $this->codigo_cliente);
                });
        }

        $query->orderByDesc('id');

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get()->map(function ($item, $index) {
            return [
                $index + 1,
                $item->razon_social,
                $item->codigo_cliente,
                $item->nombre,
                $item->codigo_proyecto,
                $item->proyecto,
                $item->etapa,
                $item->numero_lote,
                $item->estado_lote,
                $item->dni,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'N°',
            'Razón Social',
            'Código Cliente',
            'Nombre',
            'Cód. Proyecto',
            'Proyecto',
            'Etapa',
            'N° Lote',
            'Estado Lote',
            'DNI/RUC',
        ];
    }
}
