<?php

namespace App\Livewire\Erp\UnidadNegocio;

use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.erp.layout-erp')]
class UnidadNegocioEditar extends Component
{
    public UnidadNegocio $unidadNegocio;

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

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:unidad_negocios,nombre,' . $this->unidadNegocio->id,
            'razon_social' => 'required',
            'ruc' => 'nullable|unique:unidad_negocios,ruc,' . $this->unidadNegocio->id,
            'slin_id' => 'nullable|unique:unidad_negocios,slin_id,' . $this->unidadNegocio->id,
            'cavali_girador_tipo_documento' => 'nullable',
            'cavali_girador_documento' => 'nullable',
            'cavali_girador_nombre' => 'nullable',
            'cavali_girador_apellido' => 'nullable',
            'cavali_girador_email' => 'nullable|email',
            'cavali_girador_telefono' => 'nullable',
        ];
    }

    public function mount($id)
    {
        $this->unidadNegocio = UnidadNegocio::findOrFail($id);
        $this->nombre = $this->unidadNegocio->nombre;
        $this->razon_social = $this->unidadNegocio->razon_social;
        $this->ruc = $this->unidadNegocio->ruc;
        $this->slin_id = $this->unidadNegocio->slin_id;
        $this->cavali_girador_tipo_documento = $this->unidadNegocio->cavali_girador_tipo_documento;
        $this->cavali_girador_documento = $this->unidadNegocio->cavali_girador_documento;
        $this->cavali_girador_nombre = $this->unidadNegocio->cavali_girador_nombre;
        $this->cavali_girador_apellido = $this->unidadNegocio->cavali_girador_apellido;
        $this->cavali_girador_email = $this->unidadNegocio->cavali_girador_email;
        $this->cavali_girador_telefono = $this->unidadNegocio->cavali_girador_telefono;
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
                'ruc' => $this->ruc,
                'slin_id' => $this->slin_id,
                'cavali_girador_tipo_documento' => $this->cavali_girador_tipo_documento,
                'cavali_girador_documento' => $this->cavali_girador_documento,
                'cavali_girador_nombre' => $this->cavali_girador_nombre,
                'cavali_girador_apellido' => $this->cavali_girador_apellido,
                'cavali_girador_email' => $this->cavali_girador_email,
                'cavali_girador_telefono' => $this->cavali_girador_telefono,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizo correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar unidad de negocio: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.unidad-negocio.unidad-negocio-editar');
    }
}
