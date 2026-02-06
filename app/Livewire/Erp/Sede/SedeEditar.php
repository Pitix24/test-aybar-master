<?php

namespace App\Livewire\Erp\Sede;

use App\Models\Sede;
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
#[Title('Editar Sede')]
class SedeEditar extends Component
{
    public Sede $sede;

    public $nombre;
    public $direccion;
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:sedes,nombre,' . $this->sede->id,
            'direccion' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->sede = Sede::findOrFail($id);

        $this->nombre = $this->sede->nombre;
        $this->direccion = $this->sede->direccion;
        $this->activo = $this->sede->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->sede->update([
                'nombre' => $this->nombre,
                'direccion' => $this->direccion,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar sede: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarSedeOn')]
    public function eliminarSedeOn()
    {
        try {
            DB::beginTransaction();

            $this->sede->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.sede.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar sede: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.sede.sede-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
