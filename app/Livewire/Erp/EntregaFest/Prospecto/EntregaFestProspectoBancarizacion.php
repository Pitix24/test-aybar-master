<?php

namespace App\Livewire\Erp\EntregaFest\Prospecto;

use App\Models\ProspectoBancarizacionEntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class EntregaFestProspectoBancarizacion extends Component
{
    public $prospectoId;
    public $prospecto;

    // Para agregar nueva bancarización
    public $cuota = '';
    public $importe = '';
    public $fecha_deposito_real = '';

    // Para editar
    public $editingId = null;
    public $editCuota = '';
    public $editImporte = '';
    public $editFecha = '';

    public function mount($prospectoId)
    {
        $this->prospectoId = $prospectoId;
        $this->prospecto = ProspectoEntregaFest::with('proyecto')->findOrFail($prospectoId);
    }

    public function addBancarizacion()
    {
        $this->validate([
            'cuota' => 'required|string|max:50',
            'importe' => 'required|numeric|min:0',
            'fecha_deposito_real' => 'required|date',
        ]);

        try {
            ProspectoBancarizacionEntregaFest::create([
                'entrega_fest_id' => $this->prospecto->entrega_fest_id,
                'prospecto_entrega_fest_id' => $this->prospectoId,
                'cuota' => trim($this->cuota),
                'importe' => $this->importe,
                'fecha_deposito_real' => $this->fecha_deposito_real,
            ]);

            $this->reset(['cuota', 'importe', 'fecha_deposito_real']);
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Agregado!',
                'text' => 'Bancarización registrada correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::error("[BANCARIZACION ADD] " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo registrar.']);
        }
    }

    public function edit($id)
    {
        $banc = ProspectoBancarizacionEntregaFest::findOrFail($id);
        $this->editingId = $id;
        $this->editCuota = $banc->cuota;
        $this->editImporte = $banc->importe;
        $this->editFecha = $banc->fecha_deposito_real->format('Y-m-d');
    }

    public function update()
    {
        $this->validate([
            'editCuota' => 'required|string|max:50',
            'editImporte' => 'required|numeric|min:0',
            'editFecha' => 'required|date',
        ]);

        try {
            $banc = ProspectoBancarizacionEntregaFest::findOrFail($this->editingId);
            $banc->update([
                'cuota' => trim($this->editCuota),
                'importe' => $this->editImporte,
                'fecha_deposito_real' => $this->editFecha,
            ]);

            $this->editingId = null;
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Actualizado!',
                'text' => 'Bancarización actualizada correctamente.'
            ]);
        } catch (\Exception $e) {
            Log::error("[BANCARIZACION UPDATE] " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo actualizar.']);
        }
    }

    public function cancel()
    {
        $this->editingId = null;
    }

    public function remove($id)
    {
        try {
            ProspectoBancarizacionEntregaFest::findOrFail($id)->delete();
            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => '¡Eliminado!',
                'text' => 'Registro eliminado.'
            ]);
        } catch (\Exception $e) {
            Log::error("[BANCARIZACION DELETE] " . $e->getMessage());
            $this->dispatch('alertaLivewire', ['type' => 'error', 'title' => 'Error', 'text' => 'No se pudo eliminar.']);
        }
    }

    public function render()
    {
        $bancarizaciones = ProspectoBancarizacionEntregaFest::where('prospecto_entrega_fest_id', $this->prospectoId)
            ->orderBy('id', 'asc')
            ->get();

        return view('livewire.erp.entrega-fest.prospecto.entrega-fest-prospecto-bancarizacion', [
            'bancarizaciones' => $bancarizaciones
        ]);
    }
}
