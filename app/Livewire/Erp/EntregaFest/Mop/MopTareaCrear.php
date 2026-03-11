<?php

namespace App\Livewire\Erp\EntregaFest\Mop;

use App\Models\EntregaFest;
use App\Models\EntregaFestMopPlantilla;
use App\Models\EntregaFestMopTarea;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Crear Tarea MOP')]
class MopTareaCrear extends Component
{
    public EntregaFest $evento;

    public $user_id = '';
    public $titulo = '';
    public $fase = 'ANTES';
    public $instruccion = '';

    // Importar desde plantilla
    public $plantilla_id = '';

    protected function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'titulo' => 'required|string|max:255',
            'fase' => 'required|in:ANTES,DURANTE,CIERRE',
            'instruccion' => 'required|string',
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

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    // Importar datos desde una plantilla seleccionada
    public function importarPlantilla()
    {
        if (!$this->plantilla_id)
            return;

        $plantilla = EntregaFestMopPlantilla::find($this->plantilla_id);
        if ($plantilla) {
            $this->titulo = $plantilla->instruccion;
            $this->instruccion = $plantilla->instruccion;
            $this->fase = $plantilla->fase;
        }
    }

    public function store()
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

            EntregaFestMopTarea::create([
                'entrega_fest_id' => $this->evento->id,
                'user_id' => $this->user_id,
                'titulo' => trim($this->titulo),
                'fase' => $this->fase,
                'instruccion' => trim($this->instruccion),
                'esta_completado' => false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'Tarea MOP asignada correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.mop.tareas', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[MOP TAREA CREAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear la tarea.'
            ]);
        }
    }

    public function render()
    {
        $usuarios = User::permission('entrega-fest.gestor')->get();
        $plantillas = EntregaFestMopPlantilla::orderBy('fase')->orderBy('prioridad')->get();

        return view('livewire.erp.entrega-fest.mop.mop-tarea-crear', compact('usuarios', 'plantillas'));
    }
}
