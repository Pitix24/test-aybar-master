<?php

namespace App\Livewire\Erp\Marketing\Tutorial;

use App\Models\Tutorial;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle del Tutorial')]
class TutorialVer extends Component
{
    public Tutorial $tutorial;

    public function mount($id)
    {
        $this->tutorial = Tutorial::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.marketing.tutorial.tutorial-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
