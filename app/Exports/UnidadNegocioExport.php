<?php

namespace App\Exports;

use App\Models\UnidadNegocio;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UnidadNegocioExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected string $buscar;
    protected int $perPage;
    protected int $page;

    public function __construct(string $buscar, int $perPage, int $page)
    {
        $this->buscar = $buscar;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        return UnidadNegocio::query()
            ->search($this->buscar)
            ->orderByDesc('created_at')
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->nombre,
                    $item->razon_social ?? '-',
                    $item->ruc ?? '-',
                    $item->slin_id ?? '-',
                    $item->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Nombre',
            'Razón Social',
            'RUC',
            'Slin ID',
            'Fecha Creación',
        ];
    }
}
