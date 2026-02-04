<?php

namespace App\Livewire\Erp\UnidadNegocio;

use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.erp.layout-erp')]
class UnidadNegocioCrear extends Component
{
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
            'nombre' => 'required|string|max:255|unique:unidad_negocios,nombre',
            'razon_social' => 'required|string|max:255',
            'ruc' => 'nullable|string|unique:unidad_negocios,ruc',
            'slin_id' => 'nullable|string|max:255|unique:unidad_negocios,slin_id',
            'cavali_girador_tipo_documento' => 'nullable|string|max:255',
            'cavali_girador_documento' => 'nullable|string|max:255',
            'cavali_girador_nombre' => 'nullable|string|max:255',
            'cavali_girador_apellido' => 'nullable|string|max:255',
            'cavali_girador_email' => 'nullable|email|max:255',
            'cavali_girador_telefono' => 'nullable|string|max:20',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            UnidadNegocio::create([
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

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardo correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear unidad de negocio: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.unidad-negocio.unidad-negocio-crear');
    }
}
