<?php

namespace App\Livewire\Erp\EntregaFest\ProspectoEntregaFest;

use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use App\Models\ProspectoEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Lista de Prospectos - Entrega Fest')]
class ProspectoEntregaFestLista extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $buscar = '';

    #[Url(history: true)]
    public $entrega_fest_id = '';

    #[Url(history: true)]
    public $estado = '';

    public $perPage = 15;

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'entrega_fest_id', 'estado', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'entrega_fest_id', 'estado']);
        $this->resetPage();
    }

    public function generarInvitado($prospectoId)
    {
        $prospecto = ProspectoEntregaFest::findOrFail($prospectoId);

        if ($prospecto->estado !== 'aprobado') {
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'Solo prospectos aprobados pueden ser invitados.']);
            return;
        }

        if ($prospecto->invitado) {
            $this->dispatch('alertaLivewire', ['type' => 'warning', 'title' => 'Aviso', 'text' => 'Esta persona ya es un invitado.']);
            return;
        }

        InvitadoEntregaFest::create([
            'entrega_fest_id' => $prospecto->entrega_fest_id,
            'prospecto_entrega_fest_id' => $prospecto->id,
            'codigo_invitado' => 'QR-' . strtoupper(bin2hex(random_bytes(4))),
            'cantidad_acompanantes_permitidos' => 2, // Valor por defecto
            'confirmado' => false,
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Invitación Generada',
            'text' => 'Se ha creado el registro del invitado y generado su código QR.'
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        $eventos = EntregaFest::orderBy('fecha_entrega', 'desc')->get();

        $items = ProspectoEntregaFest::query()
            ->with(['entregaFest', 'proyecto', 'user'])
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->buscar . '%')
                        ->orWhere('apellidos', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->entrega_fest_id, function ($query) {
                $query->where('entrega_fest_id', $this->entrega_fest_id);
            })
            ->when($this->estado, function ($query) {
                $query->where('estado', $this->estado);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.prospecto-entrega-fest.prospecto-entrega-fest-lista', [
            'items' => $items,
            'eventos' => $eventos,
        ]);
    }
}
