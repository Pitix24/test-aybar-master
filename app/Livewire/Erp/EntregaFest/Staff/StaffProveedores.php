<?php

namespace App\Livewire\Erp\EntregaFest\Staff;

use App\Models\EntregaFest;
use App\Models\EntregaFestProveedor;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Proveedores - Entrega Fest')]
class StaffProveedores extends Component
{
    public EntregaFest $evento;
    public $proveedores;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
        $this->cargarProveedores();
    }

    public function cargarProveedores()
    {
        $this->proveedores = EntregaFestProveedor::with(['requerimientos'])
            ->where('entrega_fest_id', $this->evento->id)
            ->get();
    }

    public function actualizarEstado($id, $estado)
    {
        EntregaFestProveedor::where('id', $id)->update(['estado' => $estado]);
        $this->cargarProveedores();
        $this->dispatch('notificar', ['titulo' => 'Status', 'mensaje' => 'Proveedor actualizado.', 'tipo' => 'success']);
    }

    public function toggleRequerimiento($id)
    {
        $req = \App\Models\EntregaFestProveedorRequerimiento::findOrFail($id);
        $req->update(['esta_cubierto' => !$req->esta_cubierto]);
        $this->cargarProveedores();
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.staff.staff-proveedores');
    }
}