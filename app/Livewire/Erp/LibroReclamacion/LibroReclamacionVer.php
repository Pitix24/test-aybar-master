<?php

namespace App\Livewire\Erp\LibroReclamacion;

use App\Models\LibroReclamacion\LibroReclamacion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Ticket Libro Reclamacion')]
class LibroReclamacionVer extends Component
{
    public LibroReclamacion $ticket;

    public function mount($id): void
    {
        $this->authorize('libro-reclamacion.ver');

        $this->ticket = LibroReclamacion::with([
            'ticketRelacionado.estado',
            'unidadNegocio',
            'proyecto',
            'cliente',
            'gestor',
        ])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.libro-reclamacion.libro-reclamacion-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
