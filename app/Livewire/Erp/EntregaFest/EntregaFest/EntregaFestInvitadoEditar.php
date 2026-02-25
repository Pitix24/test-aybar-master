<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Invitado - Entrega Fest')]
class EntregaFestInvitadoEditar extends Component
{
    public EntregaFest $evento;
    public InvitadoEntregaFest $invitado;

    public $cantidad_acompanantes_permitidos = 0;
    public $confirmado = false;

    protected function rules()
    {
        return [
            'cantidad_acompanantes_permitidos' => 'required|integer|min:0',
            'confirmado' => 'boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'cantidad_acompanantes_permitidos' => 'cantidad de acompañantes',
            'confirmado' => 'confirmación',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount($id, $invitadoId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->invitado = InvitadoEntregaFest::with('prospecto')->where('entrega_fest_id', $this->evento->id)->findOrFail($invitadoId);

        $this->cantidad_acompanantes_permitidos = $this->invitado->cantidad_acompanantes_permitidos;
        $this->confirmado = $this->invitado->confirmado;
    }

    public function update()
    {
        $this->authorize('entrega-fest.invitados');

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

            $this->invitado->update([
                'cantidad_acompanantes_permitidos' => $this->cantidad_acompanantes_permitidos,
                'confirmado' => $this->confirmado,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Invitación de ' . ($this->invitado->prospecto->nombre_completo) . ' actualizada.'
            ]);

            return redirect()->route('erp.entrega-fest.vista.invitados', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[INVITADO EDITAR] Error al actualizar invitación: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'invitado_id' => $this->invitado->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la invitación.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-invitado-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
