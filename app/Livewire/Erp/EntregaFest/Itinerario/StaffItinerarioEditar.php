<?php

namespace App\Livewire\Erp\EntregaFest\Itinerario;

use App\Models\EntregaFest;
use App\Models\EntregaFestItinerarioBloque;
use App\Models\EntregaFestItinerarioChecklist;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Bloque - Itinerario')]
class StaffItinerarioEditar extends Component
{
    public EntregaFest $evento;
    public EntregaFestItinerarioBloque $bloque;

    // Campos del bloque
    public $titulo = '';
    public $hora_inicio = '';
    public $hora_fin = '';
    public $descripcion = '';
    public $ubicacion = '';
    public $orden = 0;
    public $estado = 'PENDIENTE';

    // Checklist inline
    public $nueva_tarea = '';

    protected function rules()
    {
        return [
            'titulo' => 'required|string|max:255',
            'hora_inicio' => 'required',
            'hora_fin' => 'nullable',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:255',
            'orden' => 'integer|min:0',
            'estado' => 'required|in:' . EntregaFestItinerarioBloque::ESTADO_PENDIENTE . ',' . EntregaFestItinerarioBloque::ESTADO_CURSO . ',' . EntregaFestItinerarioBloque::ESTADO_COMPLETADO,
            'nueva_tarea' => 'nullable|string|max:255',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'titulo' => 'título del bloque',
            'hora_inicio' => 'hora de inicio',
            'estado' => 'estado del bloque',
            'nueva_tarea' => 'tarea',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id, $bloqueId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->bloque = EntregaFestItinerarioBloque::with('checklists')
            ->where('entrega_fest_id', $id)
            ->findOrFail($bloqueId);

        $this->titulo = $this->bloque->titulo;
        $this->hora_inicio = $this->bloque->hora_inicio;
        $this->hora_fin = $this->bloque->hora_fin;
        $this->descripcion = $this->bloque->descripcion;
        $this->ubicacion = $this->bloque->ubicacion;
        $this->orden = $this->bloque->orden;
        $this->estado = $this->bloque->estado;
    }

    // ─── Bloque ───────────────────────────────────────────────────────────────

    public function update()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->bloque->update([
                'titulo' => trim($this->titulo),
                'hora_inicio' => $this->hora_inicio,
                'hora_fin' => $this->hora_fin ?: null,
                'descripcion' => $this->descripcion ?: null,
                'ubicacion' => $this->ubicacion ?: null,
                'orden' => $this->orden,
                'estado' => $this->estado,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Bloque actualizado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[ITINERARIO EDITAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el bloque.'
            ]);
        }
    }

    #[On('eliminarBloqueOn')]
    public function eliminarBloqueOn()
    {
        try {
            DB::beginTransaction();

            $titulo = $this->bloque->titulo;
            $this->bloque->checklists()->delete();
            $this->bloque->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El bloque '$titulo' fue eliminado."
            ]);

            return redirect()->route('erp.entrega-fest.itinerario.todo', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[ITINERARIO ELIMINAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el bloque.'
            ]);
        }
    }

    // ─── Checklist ────────────────────────────────────────────────────────────

    public function agregarTarea()
    {
        $this->validate(['nueva_tarea' => 'required|string|max:255']);

        EntregaFestItinerarioChecklist::create([
            'itinerario_bloque_id' => $this->bloque->id,
            'tarea' => trim($this->nueva_tarea),
            'esta_listo' => false,
        ]);

        $this->nueva_tarea = '';
        $this->bloque->load('checklists');
    }

    public function toggleTarea($checklistId)
    {
        $item = EntregaFestItinerarioChecklist::with('bloque')->findOrFail($checklistId);
        
        if ($item->bloque->estado !== EntregaFestItinerarioBloque::ESTADO_CURSO) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Acción no permitida',
                'text' => 'Primero debes INICIAR el bloque para marcar tareas.'
            ]);
            return;
        }

        $item->update([
            'esta_listo' => !$item->esta_listo,
            'completado_at' => !$item->esta_listo ? now() : null,
            'completado_por_user_id' => !$item->esta_listo ? auth()->id() : null,
        ]);

        $this->bloque->load('checklists');
    }

    public function eliminarTarea($checklistId)
    {
        EntregaFestItinerarioChecklist::findOrFail($checklistId)->delete();
        $this->bloque->load('checklists');
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.itinerario.staff-itinerario-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
