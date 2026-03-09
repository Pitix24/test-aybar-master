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
#[Title('Editar Contingencia - Entrega Fest')]
class StaffContingenciasEditar extends Component
{
    public EntregaFest $evento;
    public EntregaFestContingencia $contingencia;

    public $escenario;
    public $accion;

    public function mount($id, $contingenciaId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->contingencia = EntregaFestContingencia::where('entrega_fest_id', $id)->findOrFail($contingenciaId);

        $this->escenario = $this->contingencia->escenario;
        $this->accion = $this->contingencia->accion;
    }

    protected function rules()
    {
        return [
            'escenario' => 'required|string|max:200',
            'accion' => 'required|string',
        ];
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
            $this->contingencia->update([
                'escenario' => trim($this->escenario),
                'accion' => trim($this->accion),
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Contingencia actualizada correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.contingencia.todo', $this->evento->id);

        } catch (\Exception $e) {
            Log::error('[STAFF CONTINGENCIA EDITAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la contingencia.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.contingencia.staff-contingencias-editar');
    }
}
