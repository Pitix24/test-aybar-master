<?php

namespace App\Livewire\Erp\UnidadNegocio;

use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp')]
class UnidadNegocioEditar extends Component
{
    public UnidadNegocio $unidadNegocio;

    #[Validate]
    public $nombre = '';

    #[Validate('required|string|max:255')]
    public $razon_social = '';

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:unidad_negocios,nombre,' . $this->unidadNegocio->id,
        ];
    }

    public function mount($id)
    {
        $this->unidadNegocio = UnidadNegocio::findOrFail($id);
        $this->nombre = $this->unidadNegocio->nombre;
        $this->razon_social = $this->unidadNegocio->razon_social;
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

            $this->unidadNegocio->update([
                'nombre' => $this->nombre,
                'razon_social' => $this->razon_social,
            ]);

            DB::commit();

            session()->flash('success', 'Unidad de negocio actualizada exitosamente.');
            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizo correctamente.']);

            //return $this->redirect(route('erp.unidad-negocio.vista.todo'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Ocurrió un error al actualizar la unidad de negocio.');

            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);

            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.unidad-negocio.unidad-negocio-editar');
    }
}
