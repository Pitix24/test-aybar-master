<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFest;
use App\Models\EntregaFestMopTarea;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Mis Tareas MOP - Entrega Fest')]
class StaffMop extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public $fase = 'ANTES';
    public $evidencias = [];

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    public function updatedEvidencias($file, $tareaId)
    {
        $this->validate([
            'evidencias.' . $tareaId => 'image|max:10240',
        ]);

        $tarea = EntregaFestMopTarea::where('user_id', auth()->id())
            ->where('id', $tareaId)
            ->firstOrFail();

        try {
            // Guardar foto
            $tarea->addMedia($file->getRealPath())
                ->usingFileName($file->getClientOriginalName())
                ->toMediaCollection('evidencias');

            // Marcar como completado
            $tarea->update([
                'esta_completado' => true,
                'completado_at' => now(),
            ]);

            $this->dispatch('notificar', [
                'titulo' => 'Tarea Completada',
                'mensaje' => 'Se ha guardado la evidencia y completado la tarea.',
                'tipo' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notificar', [
                'titulo' => 'Error',
                'mensaje' => 'No se pudo guardar la foto. Reintente.',
                'tipo' => 'error'
            ]);
        }
    }

    public function toggleTarea($tareaId)
    {
        $tarea = EntregaFestMopTarea::where('user_id', auth()->id())
            ->where('id', $tareaId)
            ->firstOrFail();

        if ($tarea->esta_completado) {
            $this->dispatch('notificar', [
                'titulo' => 'Tarea Finalizada',
                'mensaje' => 'Las tareas con evidencia no se pueden desmarcar.',
                'tipo' => 'info'
            ]);
            return;
        }

        $this->dispatch('notificar', [
            'titulo' => 'Requiere Foto',
            'mensaje' => 'Usa el botón de cámara para completar esta tarea.',
            'tipo' => 'info'
        ]);
    }

    public function render()
    {
        $tareas = EntregaFestMopTarea::where('entrega_fest_id', $this->evento->id)
            ->where('user_id', auth()->id())
            ->where('fase', $this->fase)
            ->get();

        return view('livewire.erp.entrega-fest.mop.staff-mop', [
            'tareas' => $tareas
        ]);
    }
}