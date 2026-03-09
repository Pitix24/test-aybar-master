<?php

namespace App\Livewire\Erp\EntregaFest\Itinerario;

use App\Models\EntregaFest;
use App\Models\EntregaFestItinerarioBloque;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Crear Bloque - Itinerario')]
class StaffItinerarioCrear extends Component
{
    public EntregaFest $evento;

    public $titulo = '';
    public $hora_inicio = '';
    public $hora_fin = '';
    public $descripcion = '';
    public $ubicacion = '';
    public $responsable_rol = '';
    public $orden = 0;

    protected function rules()
    {
        return [
            'titulo' => 'required|string|max:255',
            'hora_inicio' => 'required',
            'hora_fin' => 'nullable',
            'descripcion' => 'nullable|string',
            'ubicacion' => 'nullable|string|max:255',
            'responsable_rol' => 'nullable|string|max:100',
            'orden' => 'integer|min:0',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'titulo' => 'título del bloque',
            'hora_inicio' => 'hora de inicio',
            'hora_fin' => 'hora de fin',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);

        // Sugerir el siguiente orden automáticamente
        $this->orden = EntregaFestItinerarioBloque::where('entrega_fest_id', $id)->max('orden') + 1;
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

            EntregaFestItinerarioBloque::create([
                'entrega_fest_id' => $this->evento->id,
                'titulo' => trim($this->titulo),
                'hora_inicio' => $this->hora_inicio,
                'hora_fin' => $this->hora_fin ?: null,
                'descripcion' => $this->descripcion ?: null,
                'ubicacion' => $this->ubicacion ?: null,
                'responsable_rol' => $this->responsable_rol ?: null,
                'orden' => $this->orden,
                'estado' => 'PENDIENTE',
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'Bloque de itinerario creado correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.staff.itinerario', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[ITINERARIO CREAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el bloque.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.itinerario.staff-itinerario-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
