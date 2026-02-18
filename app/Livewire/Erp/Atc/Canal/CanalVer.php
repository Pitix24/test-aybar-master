<?php

namespace App\Livewire\Erp\Atc\Canal;

use App\Models\Canal;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle de Canal')]
class CanalVer extends Component
{
    public Canal $canal;

    public function mount($id)
    {
        $this->canal = Canal::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.atc.canal.canal-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
