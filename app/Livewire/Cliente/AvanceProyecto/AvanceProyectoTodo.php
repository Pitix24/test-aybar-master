<?php

namespace App\Livewire\Cliente\AvanceProyecto;

use Livewire\Component;

use App\Models\AvanceProyecto;

class AvanceProyectoTodo extends Component
{
    public $unidad_id = "";
    public $proyecto_id = "";

    public function registrarClick($id)
    {
        if (session()->has('impersonator_id')) {
            return;
        }
        $avanceProyecto = AvanceProyecto::find($id);
        if ($avanceProyecto) {
            $avanceProyecto->increment('clicks');
        }
    }

    public function render()
    {
        $unidades = \App\Models\UnidadNegocio::where('activo', true)->get();
        $proyectos = \App\Models\Proyecto::where('activo', true)
            ->when($this->unidad_id, function ($query) {
                $query->where('unidad_negocio_id', $this->unidad_id);
            })
            ->get();

        $avanceProyectos = AvanceProyecto::where('activo', true)
            ->with(['miniatura', 'unidadNegocio', 'proyecto'])
            ->when($this->unidad_id, function ($query) {
                $query->where('unidad_negocio_id', $this->unidad_id);
            })
            ->when($this->proyecto_id, function ($query) {
                $query->where('proyecto_id', $this->proyecto_id);
            })
            ->orderBy('orden')
            ->get();

        return view('livewire.cliente.avance-proyecto.avance-proyecto-todo', [
            'avanceProyectos' => $avanceProyectos,
            'unidades' => $unidades,
            'proyectos' => $proyectos,
        ]);
    }
}
