<?php

namespace App\Livewire\Erp\EntregaFest\Asistencia;

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
        //$this->authorize('entrega-fest.asistencia');

        $input = strtoupper(trim($this->codigo_qr));

        $invitado = InvitadoEntregaFest::where('entrega_fest_id', $this->evento->id)
            ->where(function ($q) use ($input) {
                $q->where('codigo_invitado', $input)
                    ->orWhereHas('prospecto', fn($sub) => $sub->where('dni', $input))
                    ->orWhereHas('copropietario', fn($sub) => $sub->where('dni', $input));
            })
            ->with(['prospecto', 'copropietario.prospecto'])
            ->first();

        if (!$invitado) {
            $this->mensaje = 'Código o DNI no reconocido para este evento.';
            $this->mensajeTipo = 'error';
            // Aquí NO vaciamos el $this->codigo_qr, para que pueda seguir escribiendo su DNI.
        } elseif (!$invitado->confirmado) {
            $this->mensaje = 'El invitado ' . ($invitado->nombre_completo) . ' NO ha confirmado su asistencia.';
            $this->mensajeTipo = 'error';
            $this->codigo_qr = ''; // Como sí se encontró el invitado, limpiamos la caja.
        } else {
            if ($invitado->asistencia) {
                $this->mensaje = 'El invitado ' . ($invitado->nombre_completo) . ' ya registró su ingreso a las ' . $invitado->asistencia->fecha_checkin->format('H:i');
                $this->mensajeTipo = 'warning';
                $this->codigo_qr = ''; // Como sí se encontró el invitado, limpiamos la caja.
            } else {
                // Determinar el método de registro
                $metodo = 'manual';
                if ($input === $invitado->codigo_invitado) {
                    $metodo = 'qr';
                } elseif ($input === ($invitado->prospecto->dni ?? '') || $input === ($invitado->copropietario->dni ?? '')) {
                    $metodo = 'dni';
                }

                AsistenciaEntregaFest::create([
                    'invitado_entrega_fest_id' => $invitado->id,
                    'user_id' => Auth::id(),
                    'fecha_checkin' => now(),
                    'metodo' => $metodo,
                ]);
                $this->mensaje = '¡Bienvenido(a) ' . ($invitado->nombre_completo) . '! Ingreso registrado.';
                $this->mensajeTipo = 'success';
                
                $this->codigo_qr = ''; // Todo salió bien, limpiamos la caja para el siguiente.
            }
        }
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

    public function exportExcelTodo()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EntregaFest\EntregaFestAsistenciaExport(
                $this->evento->id,
                true
            ),
            'asistencia_todo_' . $this->evento->codigo . '.xlsx'
        );
    }

    public function toggleSegundaAsistencia($id)
    {
        $asistencia = AsistenciaEntregaFest::findOrFail($id);
        $asistencia->segunda_asistencia = !$asistencia->segunda_asistencia;
        $asistencia->save();
    }

    public function render()
    {
        $items = AsistenciaEntregaFest::query()
            ->with([
                'invitado.prospecto.proyecto',
                'invitado.copropietario.prospecto.proyecto',
                'invitado.acompanantes',
                'user',
            ])
            ->whereHas('invitado', function ($q) {
                $q->where('entrega_fest_id', $this->evento->id);
            })
            ->when($this->buscar, function ($query) {
                $query->whereHas('invitado', function ($q) {
                    // Buscar en titular
                    $q->whereHas('prospecto', function ($sub) {
                        $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })
                        // Buscar en copropietario
                        ->orWhereHas('copropietario', function ($sub) {
                        $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })
                        ->orWhere('codigo_invitado', 'like', '%' . $this->buscar . '%');
                });
            })
            ->orderBy('fecha_checkin', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.asistencia.entrega-fest-asistencia', [
            'items' => $items
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

}
