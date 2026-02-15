<?php

namespace App\Livewire\Erp\Negocio\Sede;

use App\Models\Sede;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Sede')]
class SedeCrear extends Component
{
    public $nombre = '';
    public $direccion = '';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:sedes,nombre',
            'direccion' => 'nullable|string|max:500',
            'activo' => 'required|boolean',
        ];
    }

    public function validationAttributes()
    {
        return [
            'nombre' => 'nombre de la sede',
            'direccion' => 'dirección',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('sede.crear');

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

            Sede::create([
                'nombre' => $this->nombre,
                'direccion' => $this->direccion ?: null,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Creado',
                'text' => 'La sede se guardó correctamente.'
            ]);

            return redirect()->route('erp.sede.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('negocio')->error("[SEDE] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo completar la operación.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.negocio.sede.sede-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
