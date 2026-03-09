<?php

namespace App\Livewire\Erp\EntregaFest\Recurso;

use App\Models\EntregaFest;
use App\Models\EntregaFestRecurso;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Recurso - Entrega Fest')]
class StaffRecursosEditar extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public EntregaFestRecurso $recurso;

    public $nombre_publico;
    public $tipo_recurso;
    public $archivo;

    public function mount($id, $recursoId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->recurso = EntregaFestRecurso::where('entrega_fest_id', $id)->findOrFail($recursoId);

        $this->nombre_publico = $this->recurso->nombre_publico;
        $this->tipo_recurso = $this->recurso->tipo_recurso;
    }

    protected function rules()
    {
        return [
            'nombre_publico' => 'required|string|max:150',
            'tipo_recurso' => 'required|in:MAPA,MANUAL,FOTO',
            'archivo' => 'nullable|file|max:10240', // 10MB max
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
            $this->recurso->update([
                'nombre_publico' => trim($this->nombre_publico),
                'tipo_recurso' => $this->tipo_recurso,
            ]);

            if ($this->archivo) {
                // Borrar archivo anterior y subir el nuevo
                $this->recurso->clearMediaCollection('recursos');
                $this->recurso->addMedia($this->archivo->getRealPath())
                    ->usingFileName($this->archivo->getClientOriginalName())
                    ->toMediaCollection('recursos');
            }

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Recurso actualizado correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.recurso.todo', $this->evento->id);

        } catch (\Exception $e) {
            Log::error('[STAFF RECURSO EDITAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el recurso.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.recurso.staff-recursos-editar');
    }
}
