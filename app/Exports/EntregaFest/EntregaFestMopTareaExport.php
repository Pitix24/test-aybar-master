<?php

namespace App\Exports\EntregaFest;

use App\Models\EntregaFestMopTarea;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntregaFestMopTareaExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $entrega_fest_id;
    protected $buscar;
    protected $fase;
    protected $user_id;
    protected $esta_completado;

    public function __construct(
        $entrega_fest_id,
        $buscar = '',
        $fase = '',
        $user_id = '',
        $esta_completado = ''
    ) {
        $this->entrega_fest_id = $entrega_fest_id;
        $this->buscar = $buscar;
        $this->fase = $fase;
        $this->user_id = $user_id;
        $this->esta_completado = $esta_completado;
    }

    public function collection()
    {
        return EntregaFestMopTarea::query()
            ->with('user')
            ->where('entrega_fest_id', $this->entrega_fest_id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('titulo', 'like', '%' . $this->buscar . '%')
                        ->orWhere('instruccion', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->fase, fn($q) => $q->where('fase', $this->fase))
            ->when($this->user_id, fn($q) => $q->where('user_id', $this->user_id))
            ->when($this->esta_completado !== '', function ($query) {
                $query->where('esta_completado', $this->esta_completado);
            })
            ->orderBy('fase')
            ->get()
            ->map(function ($item, $index) {
                return [
                    $index + 1,
                    $item->user?->name ?? '—',
                    $item->titulo,
                    $item->fase,
                    $item->instruccion,
                    $item->esta_completado ? 'COMPLETADA' : 'PENDIENTE',
                    $item->completado_at ? $item->completado_at->format('d/m/Y H:i') : '—',
                    $item->getFirstMediaUrl('evidencias') ?: '—',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'N°',
            'Responsable',
            'Tarea',
            'Fase',
            'Instrucción',
            'Estado',
            'Fecha/Hora Completado',
            'Link Evidencia',
        ];
    }
}
