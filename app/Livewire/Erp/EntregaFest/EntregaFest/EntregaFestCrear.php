<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\Cliente;
use App\Models\EntregaFest;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Crear Entrega Fest')]
class EntregaFestCrear extends Component
{
    public $nombre, $descripcion, $codigo, $fecha_entrega;
    public $unidad_negocio_id, $proyecto_id, $cliente_id;
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'codigo' => 'required|string|max:50|unique:entrega_fests,codigo',
        'fecha_entrega' => 'required|date',
        'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
        'proyecto_id' => 'nullable|exists:proyectos,id',
        'cliente_id' => 'required|exists:clientes,id',
        'activo' => 'boolean',
    ];

    public function mount()
    {
        $this->fecha_entrega = date('Y-m-d');
        // Autogenerar código básico si se desea, o dejar vacío
        $this->codigo = 'EF-' . date('Y') . '-' . str_pad(EntregaFest::count() + 1, 3, '0', STR_PAD_LEFT);
    }

    public function store()
    {
        $this->validate();

        $evento = EntregaFest::create([
            'unidad_negocio_id' => $this->unidad_negocio_id,
            'proyecto_id' => $this->proyecto_id,
            'cliente_id' => $this->cliente_id,
            'user_id' => Auth::id(),
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'codigo' => $this->codigo,
            'fecha_entrega' => $this->fecha_entrega,
            'activo' => $this->activo,
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Creado',
            'text' => 'Evento de Entrega Fest creado correctamente.'
        ]);

        return redirect()->route('erp.entrega-fest.vista.todo');
    }

    public function render()
    {
        $unidades = UnidadNegocio::where('activo', true)->get();
        $proyectos = $this->unidad_negocio_id
            ? Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->where('activo', true)->get()
            : [];
        $clientes = Cliente::limit(100)->get(); // Simplificado para este ejemplo

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-crear', [
            'unidades' => $unidades,
            'proyectos' => $proyectos,
            'clientes' => $clientes,
        ]);
    }
}
