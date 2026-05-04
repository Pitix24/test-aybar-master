<?php

namespace App\Livewire\Erp\Negocio\UnidadNegocio;

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
    public $direccion = '';
    public $cavali_girador_tipo_documento = '';
    public $cavali_girador_documento = '';
    public $cavali_girador_nombre = '';
    public $cavali_girador_apellido = '';
    public $cavali_girador_email = '';
    public $cavali_girador_telefono = '';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:unidad_negocios,nombre',
            'razon_social' => 'required|string|max:255',
            'ruc' => 'nullable|string|max:20|unique:unidad_negocios,ruc',
            'slin_id' => 'nullable|string|max:50|unique:unidad_negocios,slin_id',
            'direccion' => 'nullable|string|max:255',
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
            'direccion' => 'dirección',
            'cavali_girador_tipo_documento' => 'tipo doc. girador',
            'cavali_girador_documento' => 'nº doc. girador',
            'cavali_girador_nombre' => 'nombre girador',
            'cavali_girador_apellido' => 'apellido girador',
            'cavali_girador_email' => 'email girador',
            'cavali_girador_telefono' => 'teléfono girador',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('unidad-negocio.crear');

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

            UnidadNegocio::create([
                'nombre' => $this->nombre,
                'razon_social' => $this->razon_social,
                'ruc' => $this->ruc ?: null,
                'slin_id' => $this->slin_id ?: null,
                'direccion' => $this->direccion ?: null,
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
                'title' => 'Creado',
                'text' => 'La unidad de negocio se guardó correctamente.'
            ]);

            return redirect()->route('erp.unidad-negocio.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('negocio')->error("[UNIDAD NEGOCIO] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
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

    public function render()
    {
        return view('livewire.erp.negocio.unidad-negocio.unidad-negocio-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
