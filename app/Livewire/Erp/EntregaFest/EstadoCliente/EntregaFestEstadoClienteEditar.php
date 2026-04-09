<?php

namespace App\Livewire\Erp\EntregaFest\EstadoCliente;

use App\Models\EntregaFestEstadoCliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Estado de Cliente - Entrega Fest')]
class EntregaFestEstadoClienteEditar extends Component
{
    public EntregaFestEstadoCliente $estado_model;

    public $nombre;
    public $color;
    public $activo;

    public function mount($id)
    {
        $this->estado_model = EntregaFestEstadoCliente::findOrFail($id);

        $this->nombre = $this->estado_model->nombre;
        $this->color = $this->estado_model->color;
        $this->activo = (bool) $this->estado_model->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:entrega_fest_estado_clientes,nombre,' . $this->estado_model->id,
            'color' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del estado',
            'color' => 'color informativo',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('prospecto-entrega-fest.editar');

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

            $this->estado_model->update([
                'nombre' => trim($this->nombre),
                'color' => $this->color ?? '#64748b',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El estado de cliente se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[ESTADO CLIENTE] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->estado_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el estado de cliente.'
            ]);
        }
    }

    #[On('eliminarEstadoClienteOn')]
    public function eliminarEstadoClienteOn()
    {
        $this->authorize('prospecto-entrega-fest.editar');

        try {
            DB::beginTransaction();

            $nombre = $this->estado_model->nombre;
            $this->estado_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El estado de cliente '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.entrega-fest.estado-cliente.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[ESTADO CLIENTE] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->estado_model->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el estado de cliente.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.estado-cliente.entrega-fest-estado-cliente-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
