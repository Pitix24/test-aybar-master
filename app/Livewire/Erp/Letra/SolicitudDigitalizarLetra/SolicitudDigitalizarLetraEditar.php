<?php

namespace App\Livewire\Erp\Letra\SolicitudDigitalizarLetra;

use App\Models\EstadoSolicitudDigitalizarLetra;
use App\Models\SolicitudDigitalizarLetra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Solicitud de Letra Digital')]
class SolicitudDigitalizarLetraEditar extends Component
{
    public SolicitudDigitalizarLetra $solicitud;

    // Campos editables
    public $estado_solicitud_digitalizar_letra_id;

    protected function rules()
    {
        return [
            'estado_solicitud_digitalizar_letra_id' => 'required|exists:estado_solicitud_digitalizar_letras,id',
        ];
    }

    public function mount($id)
    {
        $this->solicitud = SolicitudDigitalizarLetra::with(['unidadNegocio', 'proyecto', 'userCliente.perfilCliente', 'estado'])->findOrFail($id);
        $this->estado_solicitud_digitalizar_letra_id = $this->solicitud->estado_solicitud_digitalizar_letra_id;
    }

    public function update()
    {
        abort_unless(auth()->user()->can('solicitud-digitalizar-letra.editar'), 403);

        $this->validate();

        try {
            DB::beginTransaction();

            $this->solicitud->update([
                'estado_solicitud_digitalizar_letra_id' => $this->estado_solicitud_digitalizar_letra_id,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Cambios guardados correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error SolicitudDigitalizarLetraEditar@update: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudieron guardar los cambios.']);
        }
    }

    public function render()
    {
        return view('livewire.erp.letra.solicitud-digitalizar-letra.solicitud-digitalizar-letra-editar', [
            'estados' => EstadoSolicitudDigitalizarLetra::where('activo', true)->get(),
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
