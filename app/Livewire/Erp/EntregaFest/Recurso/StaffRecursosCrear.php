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
#[Title('Añadir Recurso - Entrega Fest')]
class StaffRecursosCrear extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;

    public $nombre_publico = '';
    public $tipo_recurso = 'MAPA';
    public $archivo;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    protected function rules()
    {
        return [
            'nombre_publico' => 'required|string|max:150',
            'tipo_recurso' => 'required|in:MAPA,MANUAL,FOTO',
            'archivo' => 'required|file|max:10240', // 10MB max
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
            $recurso = EntregaFestRecurso::create([
                'entrega_fest_id' => $this->evento->id,
                'nombre_publico' => trim($this->nombre_publico),
                'tipo_recurso' => $this->tipo_recurso,
            ]);

            $recurso->addMedia($this->archivo->getRealPath())
                ->usingFileName($this->archivo->getClientOriginalName())
                ->toMediaCollection('recursos');

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Registrado!',
                'text' => 'Recurso añadido correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.recurso.todo', $this->evento->id);

        } catch (\Exception $e) {
            Log::error('[STAFF RECURSO CREAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo registrar el recurso.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.recurso.staff-recursos-crear');
    }
}
