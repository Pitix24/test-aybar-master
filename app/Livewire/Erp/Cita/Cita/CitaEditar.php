<?php

namespace App\Livewire\Erp\Cita\Cita;

use App\Models\Cita;
use App\Models\EstadoCita;
use App\Models\MotivoCita;
use App\Models\Sede;
use App\Models\Area;
use App\Models\Ticket;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
class CitaEditar extends Component
{
    public Cita $cita;
    public $ticket;

    // Solo los campos editables son propiedades públicas individuales
    public $estado_cita_id = '';
    public $asunto_respuesta = '';
    public $descripcion_respuesta = '';

    protected function rules()
    {
        return [
            'estado_cita_id' => 'required|exists:estado_citas,id',
            'asunto_respuesta' => 'nullable|string|max:255',
            'descripcion_respuesta' => 'nullable|string',
        ];
    }

    public function mount($id)
    {
        $this->cita = Cita::with(['ticket', 'area', 'gestor', 'creador'])->findOrFail($id);
        $this->ticket = $this->cita->ticket;

        // Inicializamos solo lo editable
        $this->estado_cita_id = $this->cita->estado_cita_id;
        $this->asunto_respuesta = $this->cita->asunto_respuesta;
        $this->descripcion_respuesta = $this->cita->descripcion_respuesta;
    }

    public function update()
    {
        $this->authorize('cita.editar');

        $this->validate();

        try {
            DB::beginTransaction();

            $this->cita->update([
                'estado_cita_id' => $this->estado_cita_id,
                'asunto_respuesta' => $this->asunto_respuesta,
                'descripcion_respuesta' => $this->descripcion_respuesta,
                'updated_by' => auth()->id(),
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'La cita ha sido actualizada correctamente.']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar cita: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'Ocurrió un error al actualizar la cita.']);
        }
    }

    #[On('eliminarCitaOn')]
    public function eliminarCitaOn()
    {
        $this->authorize('cita.eliminar');

        try {
            DB::beginTransaction();
            $this->cita->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'La cita ha sido eliminada correctamente.']);
            return redirect()->route('erp.cita.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar cita: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar la cita.']);
        }
    }

    public function render()
    {
        $estados = EstadoCita::where('activo', true)->get();

        return view('livewire.erp.cita.cita.cita-editar', compact('estados'));
    }
}
