<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\Cliente;
use App\Models\EntregaFest;
use App\Models\Proyecto;
use App\Models\UnidadNegocio;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Entrega Fest')]
class EntregaFestEditar extends Component
{
    public $eventoId;
    public $nombre, $descripcion, $codigo, $fecha_entrega;
    public $unidad_negocio_id, $proyecto_id, $cliente_id;
    public $activo;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'codigo' => 'required|string|max:50|unique:entrega_fests,codigo,' . $this->eventoId,
            'fecha_entrega' => 'required|date',
            'unidad_negocio_id' => 'required|exists:unidad_negocios,id',
            'proyecto_id' => 'nullable|exists:proyectos,id',
            'cliente_id' => 'required|exists:clientes,id',
            'activo' => 'boolean',
        ];
    }

    public function mount($id)
    {
        $evento = EntregaFest::findOrFail($id);
        $this->eventoId = $evento->id;
        $this->nombre = $evento->nombre;
        $this->descripcion = $evento->descripcion;
        $this->codigo = $evento->codigo;
        $this->fecha_entrega = $evento->fecha_entrega->format('Y-m-d');
        $this->unidad_negocio_id = $evento->unidad_negocio_id;
        $this->proyecto_id = $evento->proyecto_id;
        $this->cliente_id = $evento->cliente_id;
        $this->activo = $evento->activo;
    }

    public function update()
    {
        $this->validate();

        $evento = EntregaFest::findOrFail($this->eventoId);
        $evento->update([
            'unidad_negocio_id' => $this->unidad_negocio_id,
            'proyecto_id' => $this->proyecto_id,
            'cliente_id' => $this->cliente_id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'codigo' => $this->codigo,
            'fecha_entrega' => $this->fecha_entrega,
            'activo' => $this->activo,
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Actualizado',
            'text' => 'Evento actualizado correctamente.'
        ]);
    }

    public function render()
    {
        $unidades = UnidadNegocio::where('activo', true)->get();
        $proyectos = $this->unidad_negocio_id
            ? Proyecto::where('unidad_negocio_id', $this->unidad_negocio_id)->where('activo', true)->get()
            : [];
        $clientes = Cliente::limit(100)->get();

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-editar', [
            'unidades' => $unidades,
            'proyectos' => $proyectos,
            'clientes' => $clientes,
            'evento' => EntregaFest::findOrFail($this->eventoId),
        ]);
    }
}
