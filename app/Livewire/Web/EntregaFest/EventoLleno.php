<?php

namespace App\Livewire\Web\EntregaFest;

use App\Models\EntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Aforo Completo - Entrega Fest')]
class EventoLleno extends Component
{
    public EntregaFest $evento;
    public string $nombreEvento;

    public function mount($slug)
    {
        $this->evento = EntregaFest::where('slug', $slug)->firstOrFail();
        $this->nombreEvento = $this->evento->nombre;
    }

    public function render()
    {
        return view('livewire.web.entrega-fest.evento-lleno');
    }
}
