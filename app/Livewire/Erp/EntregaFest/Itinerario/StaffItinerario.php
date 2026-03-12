<?php

namespace App\Livewire\Erp\EntregaFest\Itinerario;

use App\Models\EntregaFest;
use App\Models\EntregaFestItinerarioBloque;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Itinerario Staff - Entrega Fest')]
class StaffItinerario extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public $evidencias = []; // Almacena temporalmente los archivos seleccionados

    public function mount($id)
    {
        $this->evento = EntregaFest::with(['itinerarioBloques.checklists'])->findOrFail($id);
    }

    public function actualizarEstado($bloqueId, $nuevoEstado)
    {
        $bloque = EntregaFestItinerarioBloque::findOrFail($bloqueId);
        $bloque->update(['estado' => $nuevoEstado]);

        $this->evento->load('itinerarioBloques.checklists');
        
        $msg = match($nuevoEstado) {
            EntregaFestItinerarioBloque::ESTADO_CURSO => 'El bloque ha iniciado.',
            EntregaFestItinerarioBloque::ESTADO_COMPLETADO => 'El bloque ha sido finalizado.',
            default => 'Estado actualizado.'
        };

        $this->dispatch('notificar', ['titulo' => 'Itinerario', 'mensaje' => $msg, 'tipo' => 'success']);
    }

    public function updatedEvidencias($file, $checklistId)
    {
        $this->validate([
            'evidencias.' . $checklistId => 'image|max:10240', // 10MB max
        ]);

        $item = \App\Models\EntregaFestItinerarioChecklist::with('bloque')->findOrFail($checklistId);

        // Solo permitir si el bloque está EN CURSO
        if ($item->bloque->estado !== EntregaFestItinerarioBloque::ESTADO_CURSO) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Acción no permitida',
                'text' => 'Primero debes INICIAR este bloque para adjuntar evidencia.'
            ]);
            return;
        }

        try {
            // Guardar foto
            $item->addMedia($file->getRealPath())
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('evidencias');

            // Marcar como listo
            $item->update([
                'esta_listo' => true,
                'completado_at' => now(),
                'completado_por_user_id' => auth()->id(),
            ]);

            $this->evento->load('itinerarioBloques.checklists');
            $this->dispatch('notificar', [
                'titulo' => 'Tarea Completada',
                'mensaje' => 'Se ha guardado la evidencia correctamente.',
                'tipo' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo guardar la foto. Reintente.'
            ]);
        }
    }

    public function toggleChecklist($checklistId)
    {
        $item = \App\Models\EntregaFestItinerarioChecklist::with('bloque')->findOrFail($checklistId);

        // Si ya está listo, ya no se puede desmarcar (requerimiento: evidencia)
        if ($item->esta_listo) {
            $this->dispatch('alertaLivewire', [
                'type' => 'info',
                'title' => 'Tarea Finalizada',
                'text' => 'Las tareas con evidencia ya no se pueden desmarcar.'
            ]);
            return;
        }
        
        // Solo permitir si el bloque está EN CURSO
        if ($item->bloque->estado !== EntregaFestItinerarioBloque::ESTADO_CURSO) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Acción no permitida',
                'text' => 'Primero debes INICIAR este bloque para marcar tareas.'
            ]);
            return;
        }

        // Si no tiene foto, no se puede marcar desde aquí si el requerimiento es foto.
        // Pero lo dejaré como un aviso para que usen la cámara.
        $this->dispatch('alertaLivewire', [
            'type' => 'info',
            'title' => 'Requiere Evidencia',
            'text' => 'Para completar esta tarea debes adjuntar una foto de evidencia.'
        ]);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.itinerario.staff-itinerario');
    }
}
