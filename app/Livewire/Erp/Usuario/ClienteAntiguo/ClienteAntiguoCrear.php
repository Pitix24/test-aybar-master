<?php

namespace App\Livewire\Erp\Usuario\ClienteAntiguo;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Cliente Antiguo')]
class ClienteAntiguoCrear extends Component
{
    public $dni = '';
    public $informaciones;

    public $razon_social = '';
    public $codigo_proyecto = '';
    public $proyecto = '';
    public $etapa = '';
    public $lote = '';
    public $estado_lote = '';
    public $nombre = '';
    public $codigo = '';

    protected function rules()
    {
        return [
            'dni' => 'required|string|max:20',
            'razon_social' => 'required|string|max:255',
            'codigo_proyecto' => 'nullable|string|max:5',
            'proyecto' => 'required|string|max:255',
            'etapa' => 'required|integer',
            'lote' => 'required|string|max:50',
            'estado_lote' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:50',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'dni' => 'DNI/RUC',
            'razon_social' => 'razón social',
            'codigo_proyecto' => 'código proyecto',
            'proyecto' => 'proyecto',
            'etapa' => 'etapa',
            'lote' => 'manzana-lote',
            'estado_lote' => 'estado lote',
            'nombre' => 'nombre completo',
            'codigo' => 'código cliente',
        ];
    }

    public function mount()
    {
        $this->informaciones = collect();
    }

    public function updated($propertyName)
    {
        if ($propertyName !== 'dni' && !collect(['informaciones', 'codigo_proyecto', 'estado_lote'])->contains($propertyName)) {
            $this->validateOnly($propertyName);
        }
    }

    public function buscarCliente()
    {
        $this->resetAntesDeBuscar();

        $this->validateOnly('dni');

        $this->informaciones = DB::table('clientes_2')
            ->where('dni', $this->dni)
            ->get();

        if ($this->informaciones->isEmpty()) {
            $this->dispatch('alertaLivewire', [
                'type' => 'info',
                'title' => 'Sin resultados',
                'text' => 'No se encontró información para el DNI/RUC ingresado en la base de datos antigua.'
            ]);
        } else {
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Encontrado!',
                'text' => 'Se han recuperado ' . $this->informaciones->count() . ' registros asociados.'
            ]);
        }
    }

    public function store()
    {
        $this->authorize('cliente-antiguo.crear');

        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Datos Incompletos',
                'text' => 'Por favor, revise los campos marcados en rojo.'
            ]);
            throw $e;
        }

        try {
            DB::beginTransaction();

            DB::table('clientes_2')->insert([
                'razon_social' => trim(strtoupper($this->razon_social)),
                'codigo_cliente' => trim(strtoupper($this->codigo)),
                'nombre' => trim(strtoupper($this->nombre)),
                'codigo_proyecto' => trim(strtoupper($this->codigo_proyecto)),
                'proyecto' => trim(strtoupper($this->proyecto)),
                'etapa' => $this->etapa,
                'numero_lote' => trim(strtoupper($this->lote)),
                'estado_lote' => trim(strtoupper($this->estado_lote)),
                'dni' => trim($this->dni),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Éxito!',
                'text' => 'Se ha creado el registro del cliente correctamente.'
            ]);

            return redirect()->route('erp.cliente-antiguo.vista.todo');
        } catch (Throwable $e) {
            DB::rollBack();

            try {
                $datos = method_exists($this, 'all') ? $this->all() : [];
                Log::channel('clientes-antiguo')->error("[CLIENTE_ANTIGUO] Error al crear: " . $e->getMessage(), [
                    'usuario_id' => auth()->id(),
                    'datos' => $datos,
                    'trace' => $e->getTraceAsString()
                ]);
            } catch (Throwable $logEx) {
                Log::error("[CLIENTE_ANTIGUO] Error al crear (log fallo): " . $e->getMessage(), [
                    'usuario_id' => auth()->id(),
                    'log_error' => $logEx->getMessage()
                ]);
            }

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error Crítico',
                'text' => 'No se pudo guardar la información. El error ha sido reportado.'
            ]);
        }
    }

    public function resetAntesDeBuscar()
    {
        $this->reset([
            'informaciones',
            'razon_social',
            'codigo_proyecto',
            'proyecto',
            'etapa',
            'lote',
            'estado_lote',
            'nombre',
            'codigo',
        ]);

        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.erp.usuario.cliente-antiguo.cliente-antiguo-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
