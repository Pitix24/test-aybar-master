<?php

namespace App\Livewire\Erp\UnidadNegocio;

use App\Models\UnidadNegocio;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp')]
class UnidadNegocioCrear extends Component
{
    #[Validate('required|string|max:255|unique:unidad_negocios,nombre')]
    public $nombre = '';

    #[Validate('required|string|max:255')]
    public $razon_social = '';

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $validated = $this->validate();

        try {
            DB::beginTransaction();

            $unidadNegocio = UnidadNegocio::create($validated);

            DB::commit();

            session()->flash('success', 'Unidad de negocio creada exitosamente.');

            //return $this->redirect(route('erp.unidad-negocio.vista.todo'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('error', 'Ocurrió un error al crear la unidad de negocio.');

            $this->dispatch('alertaLivewire', [
                'title' => 'Error',
                'text' => 'No se pudo guardar. Intente nuevamente.'
            ]);

            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.unidad-negocio.unidad-negocio-crear');
    }
}
