<?php

namespace App\Livewire\Erp\EntregaFest\EstadoCliente;

use App\Models\EntregaFestEstadoCliente;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Estado de Cliente - Entrega Fest')]
class EntregaFestEstadoClienteCrear extends Component
{
    public $nombre = '';
    public $color = '#64748b';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:entrega_fest_estado_clientes,nombre',
            'color' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del estado',
            'color' => 'color informativo',
            'activo' => 'estado',
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $this->authorize('prospecto-entrega-fest.editar');

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

            EntregaFestEstadoCliente::create([
                'nombre' => trim($this->nombre),
                'color' => $this->color ?? '#64748b',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'El estado de cliente se creó correctamente.'
            ]);

            return redirect()->route('erp.entrega-fest.estado-cliente.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('entrega-fest')->error("[ESTADO CLIENTE] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el estado de cliente.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.estado-cliente.entrega-fest-estado-cliente-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
