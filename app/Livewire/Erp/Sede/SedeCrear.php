<?php

namespace App\Livewire\Erp\Sede;

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
            'nombre' => 'required|unique:sedes,nombre',
            'direccion' => 'nullable|string',
            'activo' => 'required|boolean',
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

            Sede::create([
                'nombre' => $this->nombre,
                'direccion' => $this->direccion,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardó correctamente.']);
            return redirect()->route('erp.sede.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear sede: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.sede.sede-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
