<?php

namespace App\Livewire\Erp\Area;

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
            'nombre' => 'required|unique:areas,nombre',
            'email_buzon' => 'nullable|email',
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
            'selectedSedes' => 'nullable|array',
            'selectedSedes.*' => 'exists:sedes,id',
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

            $area = Area::create([
                'nombre' => $this->nombre,
                'email_buzon' => $this->email_buzon,
                'color' => $this->color,
                'icono' => $this->icono,
                'activo' => $this->activo,
            ]);

            if (!empty($this->selectedSedes)) {
                $area->sedes()->sync($this->selectedSedes);
            }

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Creado', 'text' => 'Se guardó correctamente.']);
            return redirect()->route('erp.area.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear área: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo crear. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        $sedes = Sede::where('activo', true)->orderBy('nombre')->get();
        return view('livewire.erp.area.area-crear', compact('sedes'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
