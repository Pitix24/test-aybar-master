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
#[Title('Editar Cliente Antiguo')]
class ClienteAntiguoEditar extends Component
{
    public $registro;
    public $informaciones;

    public $dni = '';
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

    public function mount($id)
    {
        $this->registro = DB::table('clientes_2')
            ->where('id', $id)
            ->first();

        if (!$this->registro) {
            abort(404);
        }

        $this->dni = $this->registro->dni;
        $this->razon_social = $this->registro->razon_social;
        $this->codigo_proyecto = $this->registro->codigo_proyecto;
        $this->proyecto = $this->registro->proyecto;
        $this->etapa = $this->registro->etapa;
        $this->lote = $this->registro->numero_lote;
        $this->estado_lote = $this->registro->estado_lote;
        $this->nombre = $this->registro->nombre;
        $this->codigo = $this->registro->codigo_cliente;

        $this->informaciones = DB::table('clientes_2')
            ->whereNotNull('dni')
            ->where('dni', '!=', '')
            ->where('dni', $this->dni)
            ->get();
    }

    public function update()
    {
        $this->authorize('cliente-antiguo.editar');

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

            DB::table('clientes_2')
                ->where('id', $this->registro->id)
                ->update([
                    'razon_social' => trim(strtoupper($this->razon_social)),
                    'codigo_cliente' => trim(strtoupper($this->codigo)),
                    'nombre' => trim(strtoupper($this->nombre)),
                    'codigo_proyecto' => trim(strtoupper($this->codigo_proyecto)),
                    'proyecto' => trim(strtoupper($this->proyecto)),
                    'etapa' => $this->etapa,
                    'numero_lote' => trim(strtoupper($this->lote)),
                    'estado_lote' => trim(strtoupper($this->estado_lote)),
                    'dni' => trim($this->dni),
                    'updated_at' => now(),
                ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'La información del cliente ha sido actualizada.'
            ]);

            return redirect()->route('erp.cliente-antiguo.vista.todo');

        } catch (Throwable $e) {
            DB::rollBack();
            Log::channel('clientes-antiguo')->error("[CLIENTE_ANTIGUO] Error al actualizar: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'target_id' => $this->registro->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error Crítico',
                'text' => 'No se pudo actualizar la información. El error ha sido reportado.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.usuario.cliente-antiguo.cliente-antiguo-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
