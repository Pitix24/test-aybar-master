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
#[Title('Editar Protocolo - Entrega Fest')]
class StaffProtocolosEditar extends Component
{
    public EntregaFest $evento;
    public EntregaFestProtocolo $protocolo;

    public $titulo;
    public $contenido;

    public function mount($id, $protocoloId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->protocolo = EntregaFestProtocolo::where('entrega_fest_id', $id)->findOrFail($protocoloId);

        $this->titulo = $this->protocolo->titulo;
        $this->contenido = $this->protocolo->contenido;
    }

    protected function rules()
    {
        return [
            'titulo' => 'required|string|max:150',
            'contenido' => 'required|string',
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
            $this->protocolo->update([
                'titulo' => trim($this->titulo),
                'contenido' => trim($this->contenido),
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Protocolo actualizado correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.protocolo.todo', $this->evento->id);

        } catch (\Exception $e) {
            Log::error('[STAFF PROTOCOLO EDITAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el protocolo.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.protocolo.staff-protocolos-editar');
    }
}
