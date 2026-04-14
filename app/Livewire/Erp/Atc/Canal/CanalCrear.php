<?php

namespace App\Livewire\Erp\Atc\Canal;

use App\Models\Canal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Canal')]
class CanalCrear extends Component
{
    public $nombre = '';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:canals,nombre',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del canal',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('canal.accion-crear');

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

            Canal::create([
                'nombre' => trim($this->nombre),
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'El canal se creó correctamente.'
            ]);

            return redirect()->route('erp.canal.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('canal')->error("[CANAL] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el canal.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.atc.canal.canal-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
