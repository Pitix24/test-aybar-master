<?php

namespace App\Livewire\Erp\Backoffice\EstadoSolicitudEvidenciaPago;

use App\Models\EstadoSolicitudEvidenciaPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Estado de Evidencia de Pago')]
class EstadoSolicitudEvidenciaPagoCrear extends Component
{
    public $nombre = '';
    public $color = '#64748b';
    public $icono = 'fa-solid fa-circle-info';
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:estado_solicitud_evidencia_pagos,nombre',
            'color' => 'nullable|string|max:50',
            'icono' => 'nullable|string|max:50',
            'activo' => 'required|boolean',
        ];
    }

    protected function validationAttributes()
    {
        return [
            'nombre' => 'nombre del estado',
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
        $this->authorize('estado-solicitud-evidencia-pago.crear');

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

            EstadoSolicitudEvidenciaPago::create([
                'nombre' => trim($this->nombre),
                'color' => $this->color ?? '#64748b',
                'icono' => $this->icono ?? 'fa-solid fa-circle-info',
                'activo' => $this->activo ?? false,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Creado!',
                'text' => 'El estado se creó correctamente.'
            ]);

            return redirect()->route('erp.estado-solicitud-evidencia-pago.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('estado_solicitud_evidencia_pago')->error("[ESTADO EVIDENCIA] Error al crear: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo crear el estado.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.backoffice.estado-solicitud-evidencia-pago.estado-solicitud-evidencia-pago-crear');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
