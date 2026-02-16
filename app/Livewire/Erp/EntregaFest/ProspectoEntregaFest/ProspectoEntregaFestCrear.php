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
    public $entrega_fest_id, $dni, $nombre, $apellidos, $observacion;
    public $estado = 'pendiente';

    protected $rules = [
        'entrega_fest_id' => 'required|exists:entrega_fests,id',
        'dni' => 'required|string|max:15',
        'nombre' => 'required|string|max:255',
        'apellidos' => 'required|string|max:255',
        'estado' => 'required|in:pendiente,observado,aprobado,rechazado',
        'observacion' => 'nullable|string',
    ];

    public function mount($eventoId = null)
    {
        $this->entrega_fest_id = $eventoId;
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
            'user_id' => Auth::id(),
            'dni' => $this->dni,
            'nombre' => $this->nombre,
            'apellidos' => $this->apellidos,
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
