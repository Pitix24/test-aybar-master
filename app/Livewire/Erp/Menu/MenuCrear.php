<?php

namespace App\Livewire\Erp\Menu;

use App\Models\Menu;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MenuCrear extends Component
{
    public $parent_id = null;
    public $nombre;
    public $ruta = '#';
    public $url = '#';
    public $icono = 'fa-solid fa-circle';
    public $nivel = 1;
    public $orden = 0;
    public $roles = [];
    public $permisos = [];
    public $activo = true;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'ruta' => 'nullable|string|max:255',
        'url' => 'nullable|string|max:255',
        'icono' => 'nullable|string|max:100',
        'parent_id' => 'nullable|exists:menus,id',
        'orden' => 'required|integer',
    ];

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

        Menu::create([
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
            'mensaje' => 'Ítem del menú creado correctamente.',
            'tipo' => 'success'
        ]);

        return redirect()->route('erp.menu.vista.todo');
    }

    public function render()
    {
        return view('livewire.erp.menu.menu-crear', [
            'menusPadre' => Menu::where('nivel', '<', 4)->orderBy('nombre')->get(),
            'allRoles' => Role::orderBy('name')->get(),
            'allPermissions' => Permission::orderBy('name')->get(),
        ]);
    }
}
