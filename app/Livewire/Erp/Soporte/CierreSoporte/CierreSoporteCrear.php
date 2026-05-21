<?php

namespace App\Livewire\Erp\Soporte\CierreSoporte;

use App\Models\Erp\Soporte\CierreSoporte;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Cierre de Soporte')]
class CierreSoporteCrear extends Component
{
    public $nombre = '';
    public $color = '#64748b';
    public $icono = 'fa-solid fa-circle-info';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:cierre_soportes,nombre',
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del cierre',
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
        $this->authorize('cierre-soporte.accion-crear');

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

            CierreSoporte::create([
                'nombre' => trim($this->nombre),
                'color' => $this->color ?? '#64748b',
                'icono' => $this->icono ?? 'fa-solid fa-circle-info',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'El cierre de soporte se creó correctamente.'
            ]);

            return redirect()->route('erp.cierre-soporte.vista.lista');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('cierre_soporte')->error("[CIERRE SOPORTE] Error al crear: " . $e->getMessage());

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el cierre de soporte.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.soporte.cierre-soporte.cierre-soporte-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
