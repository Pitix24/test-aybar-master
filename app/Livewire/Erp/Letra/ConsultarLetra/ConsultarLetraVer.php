<?php

namespace App\Livewire\Erp\Letra\ConsultarLetra;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Services\CavaliService;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Consultar Letras')]
class ConsultarLetraVer extends Component
{
    public $numeroLetra = '';
    public $resultado = null;

    public function consultar(CavaliService $service)
    {
        $this->validate([
            'numeroLetra' => 'required|string|min:3'
        ]);

        $this->resultado = $service->consultar($this->numeroLetra);

        if ($this->resultado['codigo'] !== '001') {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => $this->resultado['error'] ?? 'No se encontró la constancia para esta letra.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.letra.consultar-letra.consultar-letra-ver');
    }
}
