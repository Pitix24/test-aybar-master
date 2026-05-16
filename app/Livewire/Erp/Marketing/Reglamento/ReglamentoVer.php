<?php

namespace App\Livewire\Erp\Marketing\Reglamento;

use App\Models\Reglamento;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Detalle del Reglamento')]
class ReglamentoVer extends Component
{
    public Reglamento $reglamento;

    public function mount($id)
    {
        $this->reglamento = Reglamento::with(['proyecto'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.marketing.reglamento.reglamento-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
