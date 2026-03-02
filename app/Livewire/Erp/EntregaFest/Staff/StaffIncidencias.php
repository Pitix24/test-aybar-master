<?php

namespace App\Livewire\Erp\EntregaFest\Staff;

use App\Models\EntregaFest;
use App\Models\EntregaFestIncidencia;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Reporte de Incidencias - Entrega Fest')]
class StaffIncidencias extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public $incidencias;
    public $staff_users;

    // Formulario
    public $tipo = 'Logística';
    public $prioridad = 'Media';
    public $descripcion = '';
    public $ubicacion = '';
    public $fotos = [];

    public $mostrarFormulario = false;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->cargarIncidencias();
        $this->staff_users = User::all();
    }

    public function cargarIncidencias()
    {
        $this->incidencias = EntregaFestIncidencia::with(['informante', 'responsable'])
            ->where('entrega_fest_id', $this->evento->id)
            ->latest()
            ->get();
    }

    public function reportar()
    {
        $this->validate([
            'tipo' => 'required',
            'prioridad' => 'required',
            'descripcion' => 'required|min:5',
            'ubicacion' => 'nullable',
            'fotos.*' => 'image|max:5120', // 5MB max
        ]);

        $incidencia = EntregaFestIncidencia::create([
            'entrega_fest_id' => $this->evento->id,
            'tipo' => $this->tipo,
            'prioridad' => $this->prioridad,
            'descripcion' => $this->descripcion,
            'ubicacion' => $this->ubicacion,
            'informante_user_id' => auth()->id(),
            'estado' => 'Abierta',
        ]);

        foreach ($this->fotos as $foto) {
            $incidencia->addMedia($foto->getRealPath())->toMediaCollection('evidencias');
        }

        $this->reset(['tipo', 'prioridad', 'descripcion', 'ubicacion', 'fotos', 'mostrarFormulario']);
        $this->cargarIncidencias();

        $this->dispatch('notificar', ['titulo' => 'Reportada', 'mensaje' => 'La incidencia ha sido registrada.', 'tipo' => 'success']);
    }

    public function cambiarPrioridad($id, $prio)
    {
        EntregaFestIncidencia::where('id', $id)->update(['prioridad' => $prio]);
        $this->cargarIncidencias();
    }

    public function cambiarEstado($id, $estado)
    {
        EntregaFestIncidencia::where('id', $id)->update(['estado' => $estado]);
        $this->cargarIncidencias();
    }

    public function asignarResponsable($id, $userId)
    {
        EntregaFestIncidencia::where('id', $id)->update(['responsable_user_id' => $userId]);
        $this->cargarIncidencias();
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.staff.staff-incidencias');
    }
}
