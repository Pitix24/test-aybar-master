<?php

namespace App\Livewire\Erp\Atc\TipoSolicitud;

use App\Models\Area;
use App\Models\TipoSolicitud;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Tipo de Solicitud')]
class TipoSolicitudCrear extends Component
{
    public $nombre = '';
    public $tiempo_solucion = '';
    public $activo = true;
    public $selectedAreas = [];

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:tipo_solicituds,nombre',
            'tiempo_solucion' => 'required|numeric|min:0',
            'activo' => 'required|boolean',
            'selectedAreas' => 'nullable|array',
            'selectedAreas.*' => 'exists:areas,id',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del tipo de solicitud',
            'tiempo_solucion' => 'tiempo de solución',
            'activo' => 'estado',
            'selectedAreas' => 'áreas vinculadas',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('tipo-solicitud.crear');

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

            $nuevo = TipoSolicitud::create([
                'nombre' => trim($this->nombre),
                'tiempo_solucion' => $this->tiempo_solucion,
                'activo' => $this->activo ?? false,
            ]);

            if (!empty($this->selectedAreas)) {
                $nuevo->areas()->sync($this->selectedAreas);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => 'Tipo de solicitud creado correctamente.'
            ]);

            return redirect()->route('erp.tipo-solicitud.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tipo_solicitud')->error("[TIPO SOLICITUD] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el tipo de solicitud.'
            ]);
        }
    }

    public function render()
    {
        $areas = Area::select('id', 'nombre')->orderBy('nombre')->get();
        return view('livewire.erp.atc.tipo-solicitud.tipo-solicitud-crear', compact('areas'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
