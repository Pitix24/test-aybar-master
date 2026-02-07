<?php

namespace App\Livewire\Erp\Menu;

use App\Models\Menu;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MenuEditar extends Component
{
    public $menu_id;
    public $parent_id;
    public $nombre;
    public $ruta;
    public $url;
    public $icono;
    public $nivel;
    public $orden;
    public $roles = [];
    public $permisos = [];
    public $activo;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'ruta' => 'nullable|string|max:255',
        'url' => 'nullable|string|max:255',
        'icono' => 'nullable|string|max:100',
        'parent_id' => 'nullable|exists:menus,id',
        'orden' => 'required|integer',
    ];

    public function mount($id)
    {
        $menu = Menu::findOrFail($id);
        $this->menu_id = $menu->id;
        $this->parent_id = $menu->parent_id;
        $this->nombre = $menu->nombre;
        $this->ruta = $menu->ruta;
        $this->url = $menu->url;
        $this->icono = $menu->icono;
        $this->nivel = $menu->nivel;
        $this->orden = $menu->orden;
        $this->roles = $menu->roles ?? [];
        $this->permisos = $menu->permisos ?? [];
        $this->activo = $menu->activo;
    }

    public function updatedParentId($value)
    {
        if ($value) {
            $parent = Menu::find($value);
            $this->nivel = ($parent->nivel ?? 0) + 1;
        } else {
            $this->nivel = 1;
        }
    }

    public function guardar()
    {
        $this->validate();

        $menu = Menu::findOrFail($this->menu_id);
        $menu->update([
            'parent_id' => $this->parent_id ?: null,
            'nombre' => $this->nombre,
            'ruta' => $this->ruta ?: '#',
            'url' => $this->url ?: '#',
            'icono' => $this->icono ?: 'fa-solid fa-circle',
            'nivel' => $this->nivel,
            'orden' => $this->orden,
            'roles' => array_filter($this->roles),
            'permisos' => array_filter($this->permisos),
            'activo' => $this->activo,
        ]);

        $this->dispatch('notificacion', [
            'titulo' => 'Éxito',
            'mensaje' => 'Ítem del menú actualizado correctamente.',
            'tipo' => 'success'
        ]);

        return redirect()->route('erp.menu.vista.todo');
    }

    public function render()
    {
        return view('livewire.erp.menu.menu-crear', [ // Reutilizamos la vista de crear
            'menusPadre' => Menu::where('nivel', '<', 4)
                ->where('id', '!=', $this->menu_id) // Evitar que sea su propio padre
                ->orderBy('nombre')
                ->get(),
            'allRoles' => Role::orderBy('name')->get(),
            'allPermissions' => Permission::orderBy('name')->get(),
            'editando' => true
        ]);
    }
}
