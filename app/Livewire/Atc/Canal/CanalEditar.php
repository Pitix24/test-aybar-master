<?php

namespace App\Livewire\Atc\Canal;

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
    public Canal $canal;

    public $nombre;
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:canals,nombre,' . $this->canal->id,
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->canal = Canal::findOrFail($id);

        $this->nombre = $this->canal->nombre;
        $this->activo = $this->canal->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        abort_unless(auth()->user()->can('canal.editar'), 403);
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->canal->update([
                'nombre' => $this->nombre,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar canal: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarCanalOn')]
    public function eliminarCanalOn()
    {
        abort_unless(auth()->user()->can('canal.eliminar'), 403);
        try {
            DB::beginTransaction();

            $this->canal->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.canal.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar canal: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.atc.canal.canal-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
