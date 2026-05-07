<?php

namespace App\Livewire\Erp\Soporte\TipoSoporte;

use App\Models\Erp\Soporte\TipoSoporte;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Tipo de Soporte')]
class TipoSoporteCrear extends Component
{
    public $nombre = '';
    public $color = '#64748b';
    public $icono = 'fa-solid fa-circle-info';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:tipo_soportes,nombre',
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del tipo',
            'color' => 'color informativo',
            'icono' => 'icono representativo',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('tipo-soporte.accion-crear');

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

            TipoSoporte::create([
                'nombre' => trim($this->nombre),
                'color' => $this->color ?? '#64748b',
                'icono' => $this->icono ?? 'fa-solid fa-circle-info',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'El tipo de soporte se creó correctamente.'
            ]);

            return redirect()->route('erp.tipo-soporte.vista.lista');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('tipo_soporte')->error("[TIPO SOPORTE] Error al crear: " . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el tipo de soporte.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.soporte.tipo-soporte.tipo-soporte-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
