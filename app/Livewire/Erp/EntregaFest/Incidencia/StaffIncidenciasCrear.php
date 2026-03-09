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
#[Title('Reportar Incidencia - Entrega Fest')]
class StaffIncidenciasCrear extends Component
{
    use WithFileUploads;

    public EntregaFest $evento;

    // Formulario
    public $tipo = 'Logística';
    public $prioridad = 'MEDIA';
    public $descripcion = '';
    public $ubicacion = '';
    public $fotos = [];

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    protected function rules()
    {
        return [
            'tipo' => 'required|string|max:100',
            'prioridad' => 'required|in:BAJA,MEDIA,ALTA',
            'descripcion' => 'required|min:5',
            'ubicacion' => 'nullable|string|max:200',
            'fotos.*' => 'image|max:5120', // 5MB max
        ];
    }

    public function store()
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

            $incidencia = EntregaFestIncidencia::create([
                'entrega_fest_id' => $this->evento->id,
                'tipo' => $this->tipo,
                'prioridad' => $this->prioridad,
                'descripcion' => $this->descripcion,
                'ubicacion' => $this->ubicacion,
                'informante_user_id' => auth()->id(),
                'estado' => 'ABIERTO',
            ]);

            foreach ($this->fotos as $foto) {
                $incidencia->addMedia($foto->getRealPath())->toMediaCollection('evidencias');
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Reportada!',
                'text' => 'La incidencia ha sido registrada correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.incidencia.todo', $this->evento->id);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[STAFF INCIDENCIA CREAR] ' . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo registrar la incidencia.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.incidencia.staff-incidencias-crear');
    }
}
