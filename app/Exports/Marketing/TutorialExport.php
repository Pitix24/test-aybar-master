<?php

namespace App\Exports\Marketing;

use App\Models\Tutorial;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TutorialExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $buscar;
    protected $activo;
    protected $desde;
    protected $hasta;
    protected $todo;
    protected $perPage;
    protected $page;

    public function __construct(
        $buscar = '',
        $activo = '',
        $desde = '',
        $hasta = '',
        $todo = false,
        $perPage = null,
        $page = null
    ) {
        $this->buscar = $buscar;
        $this->activo = $activo;
        $this->desde = $desde;
        $this->hasta = $hasta;
        $this->todo = $todo;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function collection()
    {
        $query = Tutorial::query();

        if (!$this->todo) {
            $query->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('titulo', 'like', '%' . $this->buscar . '%')
                        ->orWhere('video_id', 'like', '%' . $this->buscar . '%');
                });
            })
                ->when($this->activo !== '', function ($query) {
                    $query->where('activo', $this->activo);
                })
                ->when($this->desde, function ($query) {
                    $query->whereDate('created_at', '>=', $this->desde);
                })
                ->when($this->hasta, function ($query) {
                    $query->whereDate('created_at', '<=', $this->hasta);
                });
        }

        $query->orderBy('orden');

        if (!$this->todo && $this->perPage && $this->page) {
            $query->skip(($this->page - 1) * $this->perPage)->take($this->perPage);
        }

        return $query->get()->map(function ($item, $index) {
            return [
                $index + 1,
                $item->id,
                $item->titulo,
                $item->video_id,
                $item->clicks,
                $item->activo ? 'Activo' : 'Inactivo',
                $item->orden,
                $item->created_at->format('d/m/Y H:i'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'N°',
            'ID',
            'Título',
            'Video ID',
            'Clicks',
            'Estado',
            'Orden',
            'Fecha Creación',
        ];
    }
}
