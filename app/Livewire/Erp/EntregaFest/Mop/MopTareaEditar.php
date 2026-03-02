<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFest;
use App\Models\EntregaFestMopTarea;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Tarea MOP')]
class MopTareaEditar extends Component
{
    public EntregaFest $evento;
    public EntregaFestMopTarea $tarea;

    public $user_id = '';
    public $titulo = '';
    public $fase = 'ANTES';
    public $instruccion = '';
    public $esta_completado = false;

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:255',
            'fase' => 'required|in:ANTES,DURANTE,CIERRE',
            'instruccion' => 'required|string',
            'esta_completado' => 'boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'user_id' => 'responsable',
            'titulo' => 'título de la tarea',
            'fase' => 'fase',
            'instruccion' => 'instrucción',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id, $tareaId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->tarea = EntregaFestMopTarea::where('entrega_fest_id', $id)->findOrFail($tareaId);

        $this->user_id = $this->tarea->user_id;
        $this->titulo = $this->tarea->titulo;
        $this->fase = $this->tarea->fase;
        $this->instruccion = $this->tarea->instruccion;
        $this->esta_completado = (bool) $this->tarea->esta_completado;
    }

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

            $this->tarea->update([
                'user_id' => $this->user_id,
                'titulo' => trim($this->titulo),
                'fase' => $this->fase,
                'instruccion' => trim($this->instruccion),
                'esta_completado' => $this->esta_completado,
                'completado_at' => $this->esta_completado ? ($this->tarea->completado_at ?? now()) : null,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Tarea MOP actualizada correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[MOP TAREA EDITAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la tarea.'
            ]);
        }
    }

    #[On('eliminarTareaOn')]
    public function eliminarTareaOn()
    {
        try {
            DB::beginTransaction();

            $titulo = $this->tarea->titulo;
            $this->tarea->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "La tarea '$titulo' fue eliminada."
            ]);

            return redirect()->route('entrega-fest.vista.staff.mop.tareas', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[MOP TAREA ELIMINAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar la tarea.'
            ]);
        }
    }

    public function render()
    {
        $usuarios = User::orderBy('name')->get(['id', 'name']);
        return view('livewire.erp.entrega-fest.mop.mop-tarea-editar', compact('usuarios'));
    }
}
