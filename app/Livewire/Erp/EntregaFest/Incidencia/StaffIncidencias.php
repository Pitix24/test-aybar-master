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

    protected $listeners = ['eliminarIncidenciaOn' => 'eliminarIncidencia'];

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

    public function cambiarPrioridad($id, $prio)
    {
        $this->authorize('entrega-fest.staff');
        EntregaFestIncidencia::where('id', $id)->update(['prioridad' => $prio]);
        $this->cargarIncidencias();
    }

    public function cambiarEstado($id, $estado)
    {
        $this->authorize('entrega-fest.staff');
        EntregaFestIncidencia::where('id', $id)->update(['estado' => $estado]);
        $this->cargarIncidencias();
    }

    public function guardarSolucion($id, $solucion)
    {
        $this->authorize('entrega-fest.staff');
        EntregaFestIncidencia::where('id', $id)->update(['solucion' => $solucion]);
        $this->dispatch('notificar', [
            'titulo' => 'Solución guardada',
            'mensaje' => 'La solución ha sido registrada correctamente.',
            'tipo' => 'success'
        ]);
        $this->cargarIncidencias();
    }

    public function asignarResponsable($id, $userId)
    {
        $this->authorize('entrega-fest.staff');
        EntregaFestIncidencia::where('id', $id)->update(['responsable_user_id' => $userId]);
        $this->cargarIncidencias();
    }

    public function eliminarIncidencia($id)
    {
        $this->authorize('entrega-fest.staff');
        $incidencia = EntregaFestIncidencia::where('entrega_fest_id', $this->evento->id)->findOrFail($id);

        try {
            DB::beginTransaction();
            $incidencia->clearMediaCollection('evidencias');
            $incidencia->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminada!',
                'text' => 'La incidencia ha sido eliminada correctamente.'
            ]);

            $this->cargarIncidencias();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[STAFF INCIDENCIA ELIMINAR] ' . $e->getMessage());
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar la incidencia.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.incidencia.staff-incidencias');
    }
}
