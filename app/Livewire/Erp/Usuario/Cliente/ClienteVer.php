<?php

namespace App\Livewire\Erp\Usuario\Cliente;

use App\Models\Cita;
use App\Models\SolicitudDigitalizarLetra;
use App\Models\SolicitudEvidenciaPago;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Ver Movimientos Cliente')]
class ClienteVer extends Component
{
    use WithPagination;

    public $dni;
    public ?User $user_model = null;

    public $activo;

    // Filtros por sección
    public $perPageTickets = 5;
    public $perPageDigitalizar = 5;
    public $perPageCitas = 5;
    public $perPageEvidencias = 5;

    public function mount($dni = null)
    {
        $this->dni = $dni;

        if ($this->dni) {
            $this->user_model = User::whereHas('perfilCliente', function ($q) {
                $q->where('dni', $this->dni);
            })->with(['perfilCliente', 'direccion.region', 'direccion.provincia', 'direccion.distrito'])->first();

            if ($this->user_model) {
                $this->activo = $this->user_model->activo;
            }
        }
    }

    public function updatedActivo($value)
    {
        if ($this->user_model) {
            $this->user_model->activo = $value;
            $this->user_model->save();

            $this->dispatch(
                'notificacion',
                tipo: 'success',
                mensaje: 'Estado del usuario actualizado correctamente.'
            );
        }
    }

    public function updated($property)
    {
        // Resetear páginas al cambiar perPage
        if (str_contains($property, 'perPage')) {
            $this->resetPage('pageTickets');
            $this->resetPage('pageDigitalizar');
            $this->resetPage('pageCitas');
            $this->resetPage('pageEvidencias');
        }
    }

    public function render()
    {
        $tickets = Ticket::where('dni', $this->dni)
            ->with(['estado', 'prioridad', 'area', 'unidadNegocio'])
            ->orderByDesc('created_at')
            ->paginate($this->perPageTickets, ['*'], 'pageTickets');

        $solicitudes_digitalizar = SolicitudDigitalizarLetra::where('dni', $this->dni)
            ->with(['estado', 'unidadNegocio', 'proyecto'])
            ->orderByDesc('created_at')
            ->paginate($this->perPageDigitalizar, ['*'], 'pageDigitalizar');

        $citas = Cita::where('dni', $this->dni)
            ->with(['estado', 'motivo', 'sede', 'unidadNegocio'])
            ->orderByDesc('created_at')
            ->paginate($this->perPageCitas, ['*'], 'pageCitas');

        $solicitudes_evidencia = SolicitudEvidenciaPago::where('dni', $this->dni)
            ->with(['estado', 'unidadNegocio', 'proyecto'])
            ->orderByDesc('created_at')
            ->paginate($this->perPageEvidencias, ['*'], 'pageEvidencias');

        return view('livewire.erp.usuario.cliente.cliente-ver', [
            'tickets' => $tickets,
            'solicitudes_digitalizar' => $solicitudes_digitalizar,
            'citas' => $citas,
            'solicitudes_evidencia' => $solicitudes_evidencia,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
