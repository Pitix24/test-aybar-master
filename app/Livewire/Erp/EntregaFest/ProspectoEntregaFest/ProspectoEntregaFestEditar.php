<?php

namespace App\Livewire\Erp\EntregaFest\ProspectoEntregaFest;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Evaluar Prospecto - Entrega Fest')]
class ProspectoEntregaFestEditar extends Component
{
    public $prospectoId;
    public $entrega_fest_id, $proyecto_id, $dni, $nombre, $apellidos, $estado, $observacion;
    public $codigo_cliente, $codigo_cuota, $lote, $manzana, $etapa;
    public $proyectos = [];

    protected $rules = [
        'entrega_fest_id' => 'required|exists:entrega_fests,id',
        'proyecto_id' => 'required|exists:proyectos,id',
        'dni' => 'required|string|max:15',
        'nombre' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'estado' => 'required|in:pendiente,observado,aprobado,rechazado',
        'observacion' => 'nullable|string',
        'codigo_cliente' => 'nullable|string|max:50',
        'codigo_cuota' => 'nullable|string|max:50',
        'lote' => 'nullable|string|max:20',
        'manzana' => 'nullable|string|max:20',
        'etapa' => 'nullable|string|max:50',
    ];

    public function mount($id)
    {
        $prospecto = ProspectoEntregaFest::findOrFail($id);
        $this->prospectoId = $prospecto->id;
        $this->entrega_fest_id = $prospecto->entrega_fest_id;
        $this->proyecto_id = $prospecto->proyecto_id;
        $this->dni = $prospecto->dni;
        $this->nombre = $prospecto->nombre;
        $this->apellidos = $prospecto->apellidos;
        $this->estado = $prospecto->estado;
        $this->observacion = $prospecto->observacion;
        $this->codigo_cliente = $prospecto->codigo_cliente;
        $this->codigo_cuota = $prospecto->codigo_cuota;
        $this->lote = $prospecto->lote;
        $this->manzana = $prospecto->manzana;
        $this->etapa = $prospecto->etapa;

        $this->loadProyectos();
    }

    public function updatedEntregaFestId()
    {
        $this->proyecto_id = '';
        $this->loadProyectos();
    }

    public function loadProyectos()
    {
        if ($this->entrega_fest_id) {
            $evento = EntregaFest::find($this->entrega_fest_id);
            $this->proyectos = $evento ? $evento->proyectos : [];
        } else {
            $this->proyectos = [];
        }
    }

    public function update()
    {
        $this->validate();

        $prospecto = ProspectoEntregaFest::findOrFail($this->prospectoId);
        $prospecto->update([
            'entrega_fest_id' => $this->entrega_fest_id,
            'proyecto_id' => $this->proyecto_id,
            'dni' => $this->dni,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
            'codigo_cliente' => $this->codigo_cliente,
            'codigo_cuota' => $this->codigo_cuota,
            'lote' => $this->lote,
            'manzana' => $this->manzana,
            'etapa' => $this->etapa,
            'estado' => $this->estado,
            'observacion' => $this->observacion,
        ]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Actualizado',
            'text' => 'Prospecto actualizado y evaluado correctamente.'
        ]);
    }

    public function render()
    {
        $eventos = EntregaFest::where('activo', true)->orderBy('fecha_entrega', 'desc')->get();
        return view('livewire.erp.entrega-fest.prospecto-entrega-fest.prospecto-entrega-fest-editar', [
            'eventos' => $eventos
        ]);
    }
}
