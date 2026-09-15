<?php

namespace App\Livewire\Erp\Atc\SubTipoSolicitud;

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
    public $tipo_solicitud_id = "";
    public $nombre = '';
    public $tiempo_solucion = '';
    public $activo = true;

    public $tipos = [];

    protected function rules()
    {
        return [
            'tipo_solicitud_id' => 'required|exists:tipo_solicituds,id',
            'nombre' => 'required|unique:sub_tipo_solicituds,nombre',
            'tiempo_solucion' => 'nullable|numeric|min:0',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'tipo_solicitud_id' => 'tipo de solicitud',
            'nombre' => 'nombre del sub tipo de solicitud',
            'tiempo_solucion' => 'tiempo de solución',
            'activo' => 'estado',
        ];
    }

    public function mount()
    {
        $this->tipos = TipoSolicitud::select('id', 'nombre')->orderBy('nombre')->get();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('sub-tipo-solicitud.crear');

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

            SubTipoSolicitud::create([
                'tipo_solicitud_id' => $this->tipo_solicitud_id,
                'nombre' => trim($this->nombre),
                'tiempo_solucion' => $this->tiempo_solucion ?: null,
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => 'Sub tipo de solicitud creado correctamente.'
            ]);

            return redirect()->route('erp.sub-tipo-solicitud.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('sub_tipo_solicitud')->error("[SUB TIPO SOLICITUD] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el sub tipo de solicitud.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.sub-tipo-solicitud.sub-tipo-solicitud-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
