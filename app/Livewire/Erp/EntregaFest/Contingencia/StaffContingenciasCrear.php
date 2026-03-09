<?php

namespace App\Livewire\Erp\EntregaFest\Contingencia;

use App\Models\EntregaFest;
use App\Models\EntregaFestContingencia;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Añadir Contingencia - Entrega Fest')]
class StaffContingenciasCrear extends Component
{
    public EntregaFest $evento;

    public $escenario = '';
    public $accion = '';

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    protected function rules()
    {
        return [
            'escenario' => 'required|string|max:200',
            'accion' => 'required|string',
        ];
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
            EntregaFestContingencia::create([
                'entrega_fest_id' => $this->evento->id,
                'escenario' => trim($this->escenario),
                'accion' => trim($this->accion),
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Registrado!',
                'text' => 'Plan de contingencia añadido correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.contingencia.todo', $this->evento->id);

        } catch (\Exception $e) {
            Log::error('[STAFF CONTINGENCIA CREAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo registrar la contingencia.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.contingencia.staff-contingencias-crear');
    }
}
