<?php

namespace App\Livewire\Web\EntregaFest;

use App\Models\EntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Evento Concluido - Entrega Fest')]
class EventoConcluido extends Component
{
    public EntregaFest $evento;
    public string $nombreEvento;
    public ?string $fechaEvento = null;

    public function mount($slug)
    {
        $this->evento = EntregaFest::where('slug', $slug)->firstOrFail();
        $this->nombreEvento = $this->evento->nombre;
        $this->fechaEvento  = $this->evento->fecha_entrega?->isoFormat('dddd, D [de] MMMM [de] YYYY');

        // 🛡️ Defensa: si por alguna razón llegan aquí pero el evento aún está vigente,
        // los regresamos con un 404 para no exponer este view sin contexto.
        if ($this->evento->vigente()) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.web.entrega-fest.evento-concluido');
    }
}
