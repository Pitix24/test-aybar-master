<?php

namespace App\Livewire\Erp\Backoffice\EstadoSolicitudEvidenciaPago;

use App\Models\EstadoSolicitudEvidenciaPago;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Estado de Solicitud de Evidencia de Pago')]
class EstadoSolicitudEvidenciaPagoEditar extends Component
{
    public EstadoSolicitudEvidenciaPago $estadoSolicitudEvidenciaPago;

    public $nombre;
    public $color;
    public $icono;
    public $activo = false;

    protected function rules()
    {
        return [
            'nombre' => 'required|unique:estado_solicitud_evidencia_pagos,nombre,' . $this->estadoSolicitudEvidenciaPago->id,
            'color' => 'nullable|string',
            'icono' => 'nullable|string',
            'activo' => 'required|boolean',
        ];
    }

    public function mount($id)
    {
        $this->estadoSolicitudEvidenciaPago = EstadoSolicitudEvidenciaPago::findOrFail($id);

        $this->nombre = $this->estadoSolicitudEvidenciaPago->nombre;
        $this->color = $this->estadoSolicitudEvidenciaPago->color;
        $this->icono = $this->estadoSolicitudEvidenciaPago->icono;
        $this->activo = $this->estadoSolicitudEvidenciaPago->activo;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function update()
    {
        abort_unless(auth()->user()->can('estado-solicitud-evidencia-pago.editar'), 403);
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', ['title' => 'Advertencia', 'text' => 'Verifique los errores de los campos resaltados.']);
            throw $e;
        }

        try {
            DB::beginTransaction();

            $this->estadoSolicitudEvidenciaPago->update([
                'nombre' => $this->nombre,
                'color' => $this->color,
                'icono' => $this->icono,
                'activo' => $this->activo,
            ]);

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se actualizó correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar estado de solicitud de evidencia de pago: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo actualizar. Intente nuevamente.']);
            return;
        }
    }

    #[On('eliminarEstadoSolicitudEvidenciaPagoOn')]
    public function eliminarEstadoSolicitudEvidenciaPagoOn()
    {
        abort_unless(auth()->user()->can('estado-solicitud-evidencia-pago.eliminar'), 403);
        try {
            DB::beginTransaction();

            $this->estadoSolicitudEvidenciaPago->delete();

            DB::commit();

            $this->dispatch('alertaLivewire', ['title' => 'Eliminado', 'text' => 'Se eliminó correctamente.']);
            return redirect()->route('erp.estado-solicitud-evidencia-pago.vista.todo');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar estado de solicitud de evidencia de pago: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo eliminar. Intente nuevamente.']);
            return;
        }
    }

    public function render()
    {
        return view('livewire.erp.backoffice.estado-solicitud-evidencia-pago.estado-solicitud-evidencia-pago-editar');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
