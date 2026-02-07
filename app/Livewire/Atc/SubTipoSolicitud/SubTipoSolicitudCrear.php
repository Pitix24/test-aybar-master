<?php

namespace App\Livewire\Atc\SubTipoSolicitud;

use App\Models\SubTipoSolicitud;
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
#[Title('Crear Sub Tipo de Solicitud')]
class SubTipoSolicitudCrear extends Component
{
    public $tipos_solicitud;
    public $tipo_solicitud_id = "";
    public $nombre = '';
    public $tiempo_solucion = '';
    public $activo = true;

    protected function rules()
    {
        return [
            'tipo_solicitud_id' => 'required|exists:tipo_solicituds,id',
            'nombre' => 'required|unique:sub_tipo_solicituds,nombre',
            'tiempo_solucion' => 'nullable|numeric|min:0',
            'activo' => 'required|boolean',
        ];
    }

    public function mount()
    {
        $this->tipos_solicitud = TipoSolicitud::where('activo', true)->get();
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

            SubTipoSolicitud::create([
                'tipo_solicitud_id' => $this->tipo_solicitud_id,
                'nombre' => $this->nombre,
                'tiempo_solucion' => $this->tiempo_solucion ?: null,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardó correctamente.']);
            return redirect()->route('erp.sub-tipo-solicitud.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear sub-tipo de solicitud: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.atc.sub-tipo-solicitud.sub-tipo-solicitud-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
