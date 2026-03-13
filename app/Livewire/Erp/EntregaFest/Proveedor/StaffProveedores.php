<?php

namespace App\Livewire\Erp\EntregaFest\Proveedor;

use App\Models\EntregaFest;
use App\Models\EntregaFestProveedor;
use App\Models\EntregaFestProveedorRequerimiento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Proveedores - Entrega Fest')]
class StaffProveedores extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public $proveedores;
    public $evidencias = [];

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->cargarProveedores();
    }

    public function cargarProveedores()
    {
        $this->proveedores = EntregaFestProveedor::with(['requerimientos.user', 'requerimientos.media'])
            ->where('entrega_fest_id', $this->evento->id)
            ->get();
    }

    public function updatedEvidencias($file, $reqId)
    {
        $this->authorize('entrega-fest.staff');

        $req = EntregaFestProveedorRequerimiento::findOrFail($reqId);

        try {
            // Validar que sea imagen y tamaño máximo
            $this->validate([
                'evidencias.' . $reqId => 'image|max:10240', // 10MB
            ]);

            // Guardar evidencia
            $req->addMedia($file->getRealPath())->toMediaCollection('evidencias');

            // Marcar como completado
            $req->update([
                'esta_cubierto' => true,
                'user_id' => auth()->id(),
                'completado_at' => now(),
            ]);

            $this->cargarProveedores();
            $this->dispatch('notificar', ['titulo' => 'Completado', 'mensaje' => 'Evidencia guardada y requerimiento marcado.', 'tipo' => 'success']);

        } catch (\Exception $e) {
            $this->dispatch('notificar', ['titulo' => 'Error', 'mensaje' => 'No se pudo guardar la evidencia.', 'tipo' => 'error']);
        }
    }

    public function actualizarEstado($id, $estado)
    {
        EntregaFestProveedor::where('id', $id)->update(['estado' => $estado]);
        $this->cargarProveedores();
        $this->dispatch('notificar', ['titulo' => 'Status', 'mensaje' => 'Proveedor actualizado.', 'tipo' => 'success']);
    }

    public function toggleRequerimiento($id)
    {
        $req = EntregaFestProveedorRequerimiento::findOrFail($id);
        
        // Si ya está cubierto, no permitir desmarcar si tiene evidencia (o pedir confirmación si se desea)
        // Por consistencia con Itinerario/MOP, prevent desmarcar si hay evidencia
        if ($req->esta_cubierto && $req->media->count() > 0) {
            $this->dispatch('notificar', [
                'titulo' => 'Acción no permitida',
                'mensaje' => 'No se puede desmarcar un requerimiento con evidencia fotográfica.',
                'tipo' => 'warning'
            ]);
            return;
        }

        $req->update([
            'esta_cubierto' => !$req->esta_cubierto,
            'user_id' => !$req->esta_cubierto ? auth()->id() : null,
            'completado_at' => !$req->esta_cubierto ? now() : null,
        ]);
        $this->cargarProveedores();
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.proveedor.staff-proveedores');
    }
}