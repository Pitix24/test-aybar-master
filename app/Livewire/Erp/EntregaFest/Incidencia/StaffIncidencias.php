<?php

namespace App\Livewire\Erp\EntregaFest\Incidencia;

use App\Models\EntregaFest;
use App\Models\EntregaFestIncidencia;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte de Incidencias - Entrega Fest')]
class StaffIncidencias extends Component
{
    public EntregaFest $evento;
    public $incidencias;
    public $staff_users;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->cargarIncidencias();
        $this->staff_users = User::permission('entrega-fest.gestor')->get();
    }

    public function cargarIncidencias()
    {
        $this->incidencias = EntregaFestIncidencia::with(['informante', 'responsable'])
            ->where('entrega_fest_id', $this->evento->id)
            ->latest()
            ->get();
    }

    public function cambiarEstado($id, $estado)
    {
        $this->authorize('entrega-fest.staff');
        
        $incidencia = EntregaFestIncidencia::findOrFail($id);
        $incidencia->update(['estado' => $estado]);

        $msg = match($estado) {
            'PROCESO' => 'La incidencia ahora está en curso.',
            'RESUELTO' => 'La incidencia ha sido marcada como resuelta.',
            'ABIERTO' => 'La incidencia ha sido reabierta.',
            default => 'Estado actualizado.'
        };

        $this->dispatch('notificar', [
            'titulo' => 'Incidencias',
            'mensaje' => $msg,
            'tipo' => 'success'
        ]);

        $this->cargarIncidencias();
    }

    public function guardarSolucion($id, $solucion)
    {
        $this->authorize('entrega-fest.staff');
        
        EntregaFestIncidencia::where('id', $id)->update(['solucion' => $solucion]);
        
        $this->dispatch('notificar', [
            'titulo' => 'Bitácora',
            'mensaje' => 'La solución ha sido registrada correctamente.',
            'tipo' => 'success'
        ]);
        
        $this->cargarIncidencias();
    }

    public function asignarResponsable($id, $userId)
    {
        $this->authorize('entrega-fest.staff');
        EntregaFestIncidencia::where('id', $id)->update(['responsable_user_id' => $userId ?: null]);
        
        $this->dispatch('notificar', [
            'titulo' => 'Asignación',
            'mensaje' => 'Responsable actualizado.',
            'tipo' => 'success'
        ]);

        $this->cargarIncidencias();
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.incidencia.staff-incidencias');
    }
}
