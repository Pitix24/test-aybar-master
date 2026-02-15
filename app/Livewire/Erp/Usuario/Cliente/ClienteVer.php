<?php

namespace App\Livewire\Erp\Usuario\Cliente;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Ver Cliente Portal')]
class ClienteVer extends Component
{
    public User $user;

    public function mount($id)
    {
        $this->user = User::with('cliente')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.usuario.cliente.cliente-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
