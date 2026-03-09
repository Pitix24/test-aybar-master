<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFest;
use App\Models\EntregaFestMopTarea;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Mis Tareas MOP - Entrega Fest')]
class StaffMop extends Component
{
    public EntregaFest $evento;
    public $fase = 'ANTES';

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    public function toggleTarea($tareaId)
    {
        $tarea = EntregaFestMopTarea::where('user_id', auth()->id())
            ->where('id', $tareaId)
            ->firstOrFail();

        $tarea->update([
            'esta_completado' => !$tarea->esta_completado,
            'completado_at' => !$tarea->esta_completado ? now() : null,
        ]);

        $this->dispatch('notificar', ['titulo' => 'Listo', 'mensaje' => 'Tarea actualizada.', 'tipo' => 'success']);
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