<?php

namespace App\Livewire\Cliente\Perfil;

use App\Models\Direccion;
use App\Models\Region;
use App\Models\Distrito;
use App\Models\Provincia;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class DireccionEditar extends Component
{
    public ?Direccion $direccion_seleccionada = null;

    public $departamentos = [];
    public $provincias = [];
    public $distritos = [];

    public $region_id;
    public $provincia_id;
    public $distrito_id;

    public $recibe_nombres;
    public $recibe_celular;
    public $direccion;
    public $direccion_numero;
    public $codigo_postal;
    public $opcional;
    public $instrucciones;

    public $origen = '';

    protected function rules()
    {
        return [
            'recibe_nombres' => 'nullable|string',
            'recibe_celular' => 'nullable|string',
            'region_id' => 'required|integer',
            'provincia_id' => 'required|integer',
            'distrito_id' => 'required|integer',
            'direccion' => 'required|string',
            'direccion_numero' => 'required|string',
            'codigo_postal' => 'required|string',
        ];
    }

    public function mount($origen = null)
    {
        $this->origen = $origen;
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

        $this->recibe_nombres = $dir->recibe_nombres;
        $this->recibe_celular = $dir->recibe_celular;
        $this->direccion = $dir->direccion;
        $this->direccion_numero = $dir->direccion_numero;
        $this->codigo_postal = $dir->codigo_postal;
        $this->opcional = $dir->opcional;
        $this->instrucciones = $dir->instrucciones;

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

    public function saveDireccion()
    {
        $this->validate();

        if (!$this->direccion_seleccionada) {
            $this->direccion_seleccionada = new Direccion();
            $this->direccion_seleccionada->user_id = Auth::id();
        }

        $this->direccion_seleccionada->fill([
            'recibe_nombres' => $this->recibe_nombres,
            'recibe_celular' => $this->recibe_celular,
            'direccion' => $this->direccion,
            'direccion_numero' => $this->direccion_numero,
            'codigo_postal' => $this->codigo_postal,
            'opcional' => $this->opcional,
            'instrucciones' => $this->instrucciones,
            'region_id' => $this->region_id,
            'provincia_id' => $this->provincia_id,
            'distrito_id' => $this->distrito_id,
        ]);

        $this->direccion_seleccionada->save();

        session()->flash('success', 'Dirección actualizado correctamente.');
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
}
