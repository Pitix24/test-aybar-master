<?php

namespace App\Exports;

use App\Models\Area;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AreaUsersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected Area $area;
    protected string $buscar;

    public function __construct(Area $area, string $buscar = '')
    {
        $this->area = $area;
        $this->buscar = $buscar;
    }

    public function collection()
    {
        return $this->area->users()
            ->where(function ($query) {
                $query->where('name', 'like', "%{$this->buscar}%")
                    ->orWhere('email', 'like', "%{$this->buscar}%");
            })
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->id,
                    $item->name,
                    $item->email,
                    $item->rol,
                    $item->pivot->is_principal ? 'Principal' : 'Miembro',
                    $item->pivot->created_at ? $item->pivot->created_at->format('Y-m-d H:i') : '-',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Nombre Usuario',
            'Email',
            'Rol',
            'Tipo Asignación',
            'Fecha Asignación',
        ];
    }
}
