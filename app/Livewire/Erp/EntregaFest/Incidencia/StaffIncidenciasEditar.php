<?php

namespace App\Livewire\Erp\EntregaFest\Incidencia;

use App\Models\EntregaFest;
use App\Models\EntregaFestIncidencia;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Editar Incidencia - Entrega Fest')]
class StaffIncidenciasEditar extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;
    public EntregaFestIncidencia $incidencia;
    public $staff_users;

    // Formulario
    public $tipo;
    public $prioridad;
    public $descripcion;
    public $ubicacion;
    public $fotos = [];
    public $estado;
    public $responsable_user_id;

    public function mount($id, $incidenciasId)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->incidencia = EntregaFestIncidencia::where('entrega_fest_id', $id)->findOrFail($incidenciasId);
        $this->staff_users = User::all();

        // Cargar datos
        $this->tipo = $this->incidencia->tipo;
        $this->prioridad = $this->incidencia->prioridad;
        $this->descripcion = $this->incidencia->descripcion;
        $this->ubicacion = $this->incidencia->ubicacion;
        $this->estado = $this->incidencia->estado;
        $this->responsable_user_id = $this->incidencia->responsable_user_id;
    }

    protected function rules()
    {
        return [
            'tipo' => 'required|string|max:100',
            'prioridad' => 'required|in:BAJA,MEDIA,ALTA',
            'descripcion' => 'required|min:5',
            'ubicacion' => 'nullable|string|max:200',
            'estado' => 'required|in:ABIERTO,PROCESO,RESUELTO',
            'responsable_user_id' => 'nullable|exists:users,id',
            'fotos.*' => 'image|max:5120', // 5MB max
        ];
    }

    public function update()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->incidencia->update([
                'tipo' => $this->tipo,
                'prioridad' => $this->prioridad,
                'descripcion' => $this->descripcion,
                'ubicacion' => $this->ubicacion,
                'estado' => $this->estado,
                'responsable_user_id' => $this->responsable_user_id ?: null,
            ]);

            foreach ($this->fotos as $foto) {
                $this->incidencia->addMedia($foto->getRealPath())->toMediaCollection('evidencias');
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizada!',
                'text' => 'La incidencia ha sido actualizada correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.incidencia.todo', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[STAFF INCIDENCIA EDITAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar la incidencia.'
            ]);
        }
    }

    public function eliminarFoto($id)
    {
        $media = $this->incidencia->getMedia('evidencias')->where('id', $id)->first();
        if ($media) {
            $media->delete();
            $this->incidencia->refresh();
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.incidencia.staff-incidencias-editar');
    }
}
