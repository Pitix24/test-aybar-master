<?php

namespace App\Livewire\Crm\Correo;

use App\Models\CorreoCampana;
use App\Models\CorreoContacto;
use App\Models\CorreoLista;
use App\Models\CorreoPlantilla;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.erp.layout-erp')]
#[Title('Dashboard Email Marketing')]
class CorreoDashboard extends Component
{
    public function render()
    {
        return view('livewire.crm.correo.correo-dashboard', [
            'totalContactos' => CorreoContacto::count(),
            'totalListas' => CorreoLista::count(),
            'totalPlantillas' => CorreoPlantilla::count(),
            'totalCampanas' => CorreoCampana::count(),
            'campanasRecientes' => CorreoCampana::with('plantilla', 'lista')->latest()->take(5)->get(),
        ]);
    }
}
