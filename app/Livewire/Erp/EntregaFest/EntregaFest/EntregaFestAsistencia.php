<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\AsistenciaEntregaFest;
use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Asistencia del Evento')]
class EntregaFestAsistencia extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    #[Url(as: 'q')]
    public $buscar = '';

    public $codigo_qr = '';
    public $mensaje = '';
    public $mensajeTipo = '';

    #[Url(keep: true)]
    public $perPage = 20;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    public function updatedCodigoQr()
    {
        if (strlen($this->codigo_qr) >= 6) {
            $this->procesarCheckin();
        }
    }

    public function procesarCheckin()
    {
        $this->authorize('entrega-fest.asistencia');

        $invitado = InvitadoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->where('codigo_invitado', strtoupper(trim($this->codigo_qr)))
            ->with('prospecto')
            ->first();

        if (!$invitado) {
            $this->mensaje = 'Código no reconocido para este evento.';
            $this->mensajeTipo = 'error';
        } else {
            if ($invitado->asistencia) {
                $this->mensaje = 'El invitado ' . ($invitado->nombre_completo) . ' ya registró su ingreso a las ' . $invitado->asistencia->fecha_checkin->format('H:i');
                $this->mensajeTipo = 'warning';
            } else {
                AsistenciaEntregaFest::create([
                    'invitado_entrega_fest_id' => $invitado->id,
                    'user_id' => Auth::id(),
                    'fecha_checkin' => now(),
                    'metodo' => 'qr',
                ]);
                $this->mensaje = '¡Bienvenido(a) ' . ($invitado->nombre_completo) . '! Ingreso registrado.';
                $this->mensajeTipo = 'success';
            }
        }

        $this->codigo_qr = '';
        $this->dispatch('checkinProcesado');
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar']);
        $this->resetPage();
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        $items = AsistenciaEntregaFest::query()
            ->with(['invitado.prospecto.proyecto', 'user'])
            ->whereHas('invitado', function ($q) {
                $q->where('entrega_fest_id', $this->evento->id);
            })
            ->when($this->buscar, function ($query) {
                $query->whereHas('invitado.prospecto', function ($q) {
                    $q->where('nombres', 'like', '%' . $this->buscar . '%')
                        ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                })->orWhereHas('invitado', function ($q) {
                    $q->where('codigo_invitado', 'like', '%' . $this->buscar . '%');
                });
            })
            ->orderBy('fecha_checkin', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-asistencia', [
            'items' => $items
        ]);
    }
}
