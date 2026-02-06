<?php

namespace App\Livewire\Erp\UnidadNegocio;

use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Unidad de Negocio')]
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
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:unidad_negocios,nombre',
            'razon_social' => 'required',
            'ruc' => 'nullable|unique:unidad_negocios,ruc',
            'slin_id' => 'nullable|unique:unidad_negocios,slin_id',
            'cavali_girador_tipo_documento' => 'nullable',
            'cavali_girador_documento' => 'nullable',
            'cavali_girador_nombre' => 'nullable',
            'cavali_girador_apellido' => 'nullable',
            'cavali_girador_email' => 'nullable|email',
            'cavali_girador_telefono' => 'nullable',
            'activo' => 'required|boolean',
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

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardo correctamente.']);
            $this->reset();
            return redirect()->route('erp.unidad-negocio.vista.todo');
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

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
