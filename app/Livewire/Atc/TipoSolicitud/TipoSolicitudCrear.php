<?php

namespace App\Livewire\Atc\TipoSolicitud;

use App\Models\Area;
use App\Models\TipoSolicitud;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
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

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $tipoSolicitud = TipoSolicitud::create([
                'nombre' => $this->nombre,
                'tiempo_solucion' => $this->tiempo_solucion,
                'activo' => $this->activo,
            ]);

            if (!empty($this->selectedAreas)) {
                $tipoSolicitud->areas()->sync($this->selectedAreas);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardó correctamente.']);
            return redirect()->route('erp.tipo-solicitud.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear tipo de solicitud: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        $areas = Area::orderBy('nombre')->get();
        return view('livewire.atc.tipo-solicitud.tipo-solicitud-crear', compact('areas'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
