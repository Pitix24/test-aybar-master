<?php

namespace App\Livewire\Erp\Sistema\Menu;

use App\Models\Menu;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Ver Menú')]
class MenuVer extends Component
{
    public Menu $menu;

    public function mount($id)
    {
        $this->menu = Menu::with('parent')->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.erp.sistema.menu.menu-ver');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
