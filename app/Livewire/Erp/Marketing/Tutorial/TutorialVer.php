<?php

namespace App\Livewire\Erp\Marketing\Tutorial;

use App\Models\Tutorial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Detalle del Tutorial')]
class TutorialVer extends Component
{
    public Tutorial $tutorial;

    public function mount(Tutorial $tutorial)
    {
        $this->tutorial = $tutorial->load('miniatura');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        $this->authorize('tutorial.ver');

        return view('livewire.erp.marketing.tutorial.tutorial-ver');
    }
}
