<?php

namespace App\Livewire\Erp\Menu;

use App\Models\Menu;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Lazy;
use Livewire\Component; // Added for MenuCrear and MenuEditar
use Livewire\WithPagination; // Added for MenuCrear and MenuEditar
use Spatie\Permission\Models\Permission; // Added for MenuCrear based on instruction snippet

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Gestión de Menú')]
class MenuLista extends Component
{
    use WithPagination;

    public $buscar = '';

    protected $listeners = ['delete' => 'eliminar'];

    public function eliminar($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->delete();
        $this->dispatch('notificacion', [
            'titulo' => 'Éxito',
            'mensaje' => 'Ítem del menú eliminado correctamente.',
            'tipo' => 'success'
        ]);
    }

    public function toggleActivo($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->activo = !$menu->activo;
        $menu->save();
    }

    public function render()
    {
        $items = Menu::whereNull('parent_id')
            ->where('nombre', 'like', '%' . $this->buscar . '%')
            ->with([
                'submenus' => function ($query) {
                    $query->with([
                        'submenus' => function ($q) {
                            $q->with('submenus');
                        }
                    ]);
                }
            ])
            ->orderBy('orden')
            ->paginate(50);

        return view('livewire.erp.menu.menu-lista', [
            'items' => $items
        ]);
    }
}
