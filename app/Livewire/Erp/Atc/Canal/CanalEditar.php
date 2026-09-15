<?php

namespace App\Livewire\Erp\Atc\Canal;

use App\Models\Canal;
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
#[Title('Editar Canal')]
class CanalEditar extends Component
{
    public Canal $canal_model;

    public $nombre;
    public $activo;

    public function mount($id)
    {
        $this->canal_model = Canal::findOrFail($id);

        $this->nombre = $this->canal_model->nombre;
        $this->activo = (bool) $this->canal_model->activo;
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:canals,nombre,' . $this->canal_model->id,
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del canal',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('canal.editar');

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

            $this->canal_model->update([
                'nombre' => trim($this->nombre),
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'El canal se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('canal')->error("[CANAL] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->canal_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el canal.'
            ]);
        }
    }

    #[On('eliminarCanalOn')]
    public function eliminarCanalOn()
    {
        $this->authorize('canal.eliminar');

        try {
            DB::beginTransaction();

            $nombre = $this->canal_model->nombre;
            $this->canal_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => "El canal '$nombre' ha sido eliminado."
            ]);

            return redirect()->route('erp.canal.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('canal')->error("[CANAL] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->canal_model->id ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el canal.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.canal.canal-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
