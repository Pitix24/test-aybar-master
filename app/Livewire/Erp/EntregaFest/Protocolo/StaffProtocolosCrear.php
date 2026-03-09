<?php

namespace App\Livewire\Erp\EntregaFest\Protocolo;

use App\Models\EntregaFest;
use App\Models\EntregaFestProtocolo;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Añadir Protocolo - Entrega Fest')]
class StaffProtocolosCrear extends Component
{
    public EntregaFest $evento;

    public $titulo = '';
    public $contenido = '';

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    protected function rules()
    {
        return [
            'titulo' => 'required|string|max:150',
            'contenido' => 'required|string',
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
            EntregaFestProtocolo::create([
                'entrega_fest_id' => $this->evento->id,
                'titulo' => trim($this->titulo),
                'contenido' => trim($this->contenido),
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Registrado!',
                'text' => 'Protocolo añadido correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.protocolo.todo', $this->evento->id);

        } catch (\Exception $e) {
            Log::error('[STAFF PROTOCOLO CREAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo registrar el protocolo.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.protocolo.staff-protocolos-crear');
    }
}
