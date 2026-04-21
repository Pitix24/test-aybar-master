<?php

namespace App\Livewire\Cliente\Perfil;

use App\Models\Direccion;
use App\Models\Region;
use App\Models\Distrito;
use App\Models\Provincia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Lazy;
use Livewire\Component;

#[Lazy]
class DireccionEditar extends Component
{
    public ?Direccion $direccion_seleccionada = null;

    public $paises = [];
    public $departamentos = [];
    public $provincias = [];
    public $distritos = [];

    public $pais_id;
    public $region_id;
    public $provincia_id;
    public $distrito_id;

    public $direccion;
    public $direccion_numero;
    public $codigo_postal;
    public $opcional;
    public $referencia;

    public $origen = '';

    public function mount($origen = null)
    {
        $this->origen = $origen;
        $this->paises = \App\Models\Pais::orderBy('id')->get();
        $this->departamentos = Region::all();

        $usuario = Auth::user();

        // Obtener primera dirección (si existe)
        $this->direccion_seleccionada = $usuario->direccion;

        if ($this->direccion_seleccionada) {
            $this->cargarDireccion();
        }
    }

    private function cargarDireccion()
    {
        $dir = $this->direccion_seleccionada;

        $this->direccion = $dir->direccion;
        $this->direccion_numero = $dir->direccion_numero;
        $this->codigo_postal = $dir->codigo_postal;
        $this->opcional = $dir->opcional;
        $this->referencia = $dir->referencia;

        $this->pais_id = $dir->pais_id;
        $this->region_id = $dir->region_id;
        $this->provincia_id = $dir->provincia_id;
        $this->distrito_id = $dir->distrito_id;

        if ($this->region_id) {
            $this->provincias = Provincia::where('region_id', $this->region_id)->get();
        }

        if ($this->provincia_id) {
            $this->distritos = Distrito::where('provincia_id', $this->provincia_id)->get();
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules(), [], $this->validationAttributes());
    }

    protected function rules()
    {
        return [
            'pais_id' => 'required|integer',
            'region_id' => 'required_if:pais_id,1|nullable|integer',
            'provincia_id' => 'required_if:pais_id,1|nullable|integer',
            'distrito_id' => 'required_if:pais_id,1|nullable|integer',
            'direccion' => 'required|string|max:255',
            'direccion_numero' => 'required|string|max:50',
            'codigo_postal' => 'required|string|max:10',
            'opcional' => 'nullable|string|max:255',
            'referencia' => 'nullable|string|max:500',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'pais_id' => 'país',
            'region_id' => 'departamento',
            'provincia_id' => 'provincia',
            'distrito_id' => 'distrito',
            'direccion' => 'dirección',
            'direccion_numero' => 'número',
            'codigo_postal' => 'código postal',
            'opcional' => 'información opcional',
            'referencia' => 'referencia',
        ];
    }

    public function saveDireccion()
    {
        if (session()->has('impersonator_id')) {
            session()->flash('error', 'Como administrador, usted solo tiene permisos de visualización. No puede realizar cambios en la cuenta del cliente.');
            return;
        }

        try {
            $this->validate();
        } catch (ValidationException $e) {
            session()->flash('error', 'Verifique los errores de los campos resaltados.');
            throw $e;
        }

        try {
            DB::beginTransaction();

            if (!$this->direccion_seleccionada) {
                $this->direccion_seleccionada = new Direccion();
                $this->direccion_seleccionada->user_id = Auth::id();
            }

            $this->direccion_seleccionada->fill([
                'pais_id' => $this->pais_id,
                'region_id' => $this->region_id,
                'provincia_id' => $this->provincia_id,
                'distrito_id' => $this->distrito_id,
                'direccion' => $this->direccion,
                'direccion_numero' => $this->direccion_numero,
                'opcional' => $this->opcional,
                'codigo_postal' => $this->codigo_postal,
                'referencia' => $this->referencia,
            ]);

            $this->direccion_seleccionada->save();

            DB::commit();

            session()->flash('success', 'Dirección actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar dirección: ' . $e->getMessage());
            session()->flash('error', 'No se pudo actualizar la dirección. Intente nuevamente.');
            return;
        }
    }

    public function updatedPaisId()
    {
        $this->region_id = null;
        $this->provincia_id = null;
        $this->distrito_id = null;
        $this->provincias = [];
        $this->distritos = [];
    }

    public function updatedRegionId()
    {
        $this->provincia_id = null;
        $this->distrito_id = null;
        $this->provincias = [];
        $this->distritos = [];

        if ($this->region_id) {
            $this->provincias = Provincia::where('region_id', $this->region_id)->get();
        }
    }

    public function updatedProvinciaId()
    {
        $this->distrito_id = null;
        $this->distritos = [];

        if ($this->provincia_id) {
            $this->distritos = Distrito::where('provincia_id', $this->provincia_id)->get();
        }
    }

    public function render()
    {
        return view('livewire.cliente.perfil.direccion-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
