<?php

namespace App\Livewire\Erp\Negocio\Area;

use App\Models\Area;
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
#[Title('Crear Área')]
class AreaCrear extends Component
{
    public $nombre = '';
    public $email_buzon = '';
    public $color = '#3b82f6';
    public $icono = 'fa-solid fa-shapes';
    public $activo = true;
    public $selectedSedes = [];

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255|unique:areas,nombre',
            'email_buzon' => 'nullable|email|max:255',
            'color' => 'nullable|string|max:20',
            'icono' => 'nullable|string|max:100',
            'activo' => 'required|boolean',
            'selectedSedes' => 'nullable|array',
            'selectedSedes.*' => 'exists:sedes,id',
        ];
    }

    public function validationAttributes()
    {
        return [
            'nombre' => 'nombre del área',
            'email_buzon' => 'email de buzón',
            'selectedSedes' => 'sedes seleccionadas',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('area.crear');

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

            $area = Area::create([
                'nombre' => $this->nombre,
                'email_buzon' => $this->email_buzon ?: null,
                'color' => $this->color ?: '#3b82f6',
                'icono' => $this->icono ?: 'fa-solid fa-shapes',
                'activo' => $this->activo,
            ]);

            if (!empty($this->selectedSedes)) {
                $area->sedes()->sync($this->selectedSedes);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Creado',
                'text' => 'El área se guardó correctamente.'
            ]);

            return redirect()->route('erp.area.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('area')->error("[AREA] Error al crear: " . $e->getMessage(), [
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
        $sedes = Sede::where('activo', true)->orderBy('nombre')->get();
        return view('livewire.erp.negocio.area.area-crear', compact('sedes'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
