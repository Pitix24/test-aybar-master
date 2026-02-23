<?php

namespace App\Livewire\Erp\EntregaFest\ProspectoEntregaFest;

use App\Models\EntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Registrar Prospecto - Entrega Fest')]
class ProspectoEntregaFestCrear extends Component
{
    public $entrega_fest_id, $proyecto_id, $dni, $nombre, $apellidos, $observacion;
    public $codigo_cliente, $codigo_cuota, $lote, $manzana, $etapa;
    public $estado = 'pendiente';
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

    public function mount($eventoId = null)
    {
        $this->entrega_fest_id = $eventoId;
        if ($this->entrega_fest_id) {
            $this->loadProyectos();
        }
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

    public function store()
    {
        $this->validate();

        // Validar unicidad manual para dar mejor feedback
        $existe = ProspectoEntregaFest::where('entrega_fest_id', $this->entrega_fest_id)
            ->where('dni', $this->dni)
            ->exists();

        if ($existe) {
            $this->addError('dni', 'Esta persona ya está registrada para este evento.');
            return;
        }

        ProspectoEntregaFest::create([
            'entrega_fest_id' => $this->entrega_fest_id,
            'proyecto_id' => $this->proyecto_id,
            'user_id' => Auth::id(),
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
            'title' => 'Registrado',
            'text' => 'Prospecto registrado correctamente.'
        ]);

        return redirect()->route('erp.prospecto-entrega-fest.vista.todo');
    }

    public function render()
    {
        $eventos = EntregaFest::where('activo', true)->orderBy('fecha_entrega', 'desc')->get();
        return view('livewire.erp.entrega-fest.prospecto-entrega-fest.prospecto-entrega-fest-crear', [
            'eventos' => $eventos
        ]);
    }
}
