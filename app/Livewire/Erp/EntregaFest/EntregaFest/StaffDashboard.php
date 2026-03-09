<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Centro de Control Staff - Entrega Fest')]
class StaffDashboard extends Component
{
    public EntregaFest $evento;

    public function mount($id)
    {
        $this->evento = EntregaFest::withCount(['itinerarioBloques', 'proveedores', 'incidencias', 'invitados'])
            ->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.entrega-fest.entrega-fest.staff-dashboard');
    }
}
