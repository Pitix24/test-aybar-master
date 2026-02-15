<?php

namespace App\Livewire\Erp\Negocio\UnidadNegocio;

use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Unidad de Negocio')]
class UnidadNegocioEditar extends Component
{
    public UnidadNegocio $unidad_model;

    public $nombre = '';
    public $razon_social = '';
    public $ruc = '';
    public $slin_id = '';
    public $cavali_girador_tipo_documento = '';
    public $cavali_girador_documento = '';
    public $cavali_girador_nombre = '';
    public $cavali_girador_apellido = '';
    public $cavali_girador_email = '';
    public $cavali_girador_telefono = '';
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:unidad_negocios,nombre,' . $this->unidad_model->id,
            'razon_social' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:20|unique:unidad_negocios,ruc,' . $this->unidad_model->id,
            'slin_id' => 'nullable|string|max:50|unique:unidad_negocios,slin_id,' . $this->unidad_model->id,
            'cavali_girador_tipo_documento' => 'nullable|string|max:50',
            'cavali_girador_documento' => 'nullable|string|max:20',
            'cavali_girador_nombre' => 'nullable|string|max:255',
            'cavali_girador_apellido' => 'nullable|string|max:255',
            'cavali_girador_email' => 'nullable|email|max:255',
            'cavali_girador_telefono' => 'nullable|string|max:20',
            'activo' => 'required|boolean',
        ];
    }

    public function validationAttributes()
    {
        return [
            'nombre' => 'nombre comercial',
            'razon_social' => 'razón social',
            'ruc' => 'RUC',
            'slin_id' => 'SLIN ID',
            'cavali_girador_tipo_documento' => 'tipo doc. girador',
            'cavali_girador_documento' => 'nº doc. girador',
            'cavali_girador_nombre' => 'nombre girador',
            'cavali_girador_apellido' => 'apellido girador',
            'cavali_girador_email' => 'email girador',
            'cavali_girador_telefono' => 'teléfono girador',
        ];
    }

    public function mount($id)
    {
        $this->unidad_model = UnidadNegocio::findOrFail($id);
        $this->nombre = $this->unidad_model->nombre;
        $this->razon_social = $this->unidad_model->razon_social;
        $this->ruc = $this->unidad_model->ruc;
        $this->slin_id = $this->unidad_model->slin_id;
        $this->cavali_girador_tipo_documento = $this->unidad_model->cavali_girador_tipo_documento;
        $this->cavali_girador_documento = $this->unidad_model->cavali_girador_documento;
        $this->cavali_girador_nombre = $this->unidad_model->cavali_girador_nombre;
        $this->cavali_girador_apellido = $this->unidad_model->cavali_girador_apellido;
        $this->cavali_girador_email = $this->unidad_model->cavali_girador_email;
        $this->cavali_girador_telefono = $this->unidad_model->cavali_girador_telefono;
        $this->activo = $this->unidad_model->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        $this->authorize('unidad-negocio.editar');

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

            $this->unidad_model->update([
                'nombre' => $this->nombre,
                'razon_social' => $this->razon_social,
                'ruc' => $this->ruc ?: null,
                'slin_id' => $this->slin_id ?: null,
                'cavali_girador_tipo_documento' => $this->cavali_girador_tipo_documento ?: null,
                'cavali_girador_documento' => $this->cavali_girador_documento ?: null,
                'cavali_girador_nombre' => $this->cavali_girador_nombre ?: null,
                'cavali_girador_apellido' => $this->cavali_girador_apellido ?: null,
                'cavali_girador_email' => $this->cavali_girador_email ?: null,
                'cavali_girador_telefono' => $this->cavali_girador_telefono ?: null,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'La unidad de negocio se actualizó correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('negocio')->error("[UNIDAD NEGOCIO] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->unidad_model->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo completar la operación.'
            ]);
        }
    }

    #[On('eliminarUnidadNegocioOn')]
    public function eliminarUnidadNegocioOn()
    {
        $this->authorize('unidad-negocio.eliminar');

        try {
            DB::beginTransaction();

            $id = $this->unidad_model->id;
            $nombre = $this->unidad_model->nombre;

            $this->unidad_model->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'Se eliminó correctamente.'
            ]);

            return redirect()->route('erp.unidad-negocio.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('negocio')->error("[UNIDAD NEGOCIO] Error al eliminar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $id ?? null,
                'nombre' => $nombre ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar. Verifique si tiene registros relacionados.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.negocio.unidad-negocio.unidad-negocio-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
