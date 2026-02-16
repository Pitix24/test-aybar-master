<?php

namespace App\Livewire\Erp\EntregaFest\AsistenciaEntregaFest;

use App\Models\AsistenciaEntregaFest;
use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Control de Asistencia - Entrega Fest')]
class AsistenciaEntregaFestLista extends Component
{
    use WithPagination;

    public $codigo_qr = '';
    public $entrega_fest_id = '';
    public $mensaje = '';
    public $mensajeTipo = '';

    public function updatedCodigoQr()
    {
        if (strlen($this->codigo_qr) >= 6) {
            $this->procesarCheckin();
        }
    }

    public function procesarCheckin()
    {
        if (!$this->entrega_fest_id) {
            $this->mensaje = 'Seleccione un evento primero.';
            $this->mensajeTipo = 'error';
            $this->codigo_qr = '';
            return;
        }

        $invitado = InvitadoEntregaFest::where('entrega_fest_id', $this->entrega_fest_id)
            ->where('codigo_invitado', $this->codigo_qr)
            ->first();

        if (!$invitado) {
            $this->mensaje = 'Código QR no reconocido para este evento.';
            $this->mensajeTipo = 'error';
        } else {
            if ($invitado->asistencia) {
                $this->mensaje = 'Este invitado ya registró su ingreso a las ' . $invitado->asistencia->fecha_checkin->format('H:i');
                $this->mensajeTipo = 'warning';
            } else {
                AsistenciaEntregaFest::create([
                    'invitado_entrega_fest_id' => $invitado->id,
                    'user_id' => Auth::id(),
                    'fecha_checkin' => now(),
                    'metodo' => 'qr',
                ]);
                $this->mensaje = '¡Bienvenido(a) ' . $invitado->prospecto->nombre . '! Ingreso registrado.';
                $this->mensajeTipo = 'success';
            }
        }

        $this->codigo_qr = '';
        $this->dispatch('checkinProcesado');
    }

    public function render()
    {
        $eventos = EntregaFest::where('activo', true)->orderBy('fecha_entrega', 'desc')->get();

        $asistenciasRecientes = AsistenciaEntregaFest::query()
            ->with(['invitado.prospecto', 'invitado.entregaFest'])
            ->when($this->entrega_fest_id, function ($q) {
                $q->whereHas('invitado', function ($sq) {
                    $sq->where('entrega_fest_id', $this->entrega_fest_id);
                });
            })
            ->orderBy('fecha_checkin', 'desc')
            ->paginate(10);

        return view('livewire.erp.entrega-fest.asistencia-entrega-fest.asistencia-entrega-fest-lista', [
            'eventos' => $eventos,
            'asistenciasRecientes' => $asistenciasRecientes
        ]);
    }
}
