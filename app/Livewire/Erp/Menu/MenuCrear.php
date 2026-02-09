<?php

namespace App\Livewire\Erp\Menu;

use App\Models\Menu;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Crear Menú')]
class MenuCrear extends Component
{
    public $parent_id = null;
    public $nombre = '';
    public $ruta = null;
    public $url = null;
    public $icono = 'fa-solid fa-circle';
    public $nivel = 1;
    public $orden = 0;
    public $permiso = null;
    public $activo = true;

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'ruta' => 'nullable|string|max:255',
            'url' => 'nullable|string|max:255',
            'icono' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'orden' => 'required|integer|min:0',
            'nivel' => 'required|integer|min:1|max:4',
            'activo' => 'boolean',
            'permiso' => 'nullable|string|max:255',
        ];
    }

    public function updated($property)
    {
        $this->validateOnly($property);

        if ($property === 'parent_id') {
            if ($this->parent_id) {
                $parent = Menu::find($this->parent_id);
                $this->nivel = ($parent?->nivel ?? 0) + 1;
            } else {
                $this->nivel = 1;
            }
        }
    }

    protected function normalizarRutas()
    {
        $this->ruta = filled($this->ruta) ? trim($this->ruta) : null;
        $this->url = filled($this->url) ? trim($this->url) : null;

        if ($this->ruta && $this->url) {
            throw ValidationException::withMessages([
                'ruta' => 'No puede definir ruta y URL al mismo tiempo.',
                'url' => 'No puede definir URL y ruta al mismo tiempo.',
            ]);
        }
    }

    public function store()
    {
        abort_unless(auth()->user()->can('menu.crear'), 403);

        try {
            $this->validate();
            $this->normalizarRutas();

            DB::transaction(function () {
                Menu::create([
                    'parent_id' => $this->parent_id,
                    'nombre' => $this->nombre,
                    'ruta' => $this->ruta,
                    'url' => $this->url,
                    'icono' => $this->icono,
                    'nivel' => $this->nivel,
                    'orden' => $this->orden,
                    'permiso' => $this->permiso,
                    'activo' => $this->activo,
                ]);
            });

            $this->dispatch('alertaLivewire', [
                'title' => 'Creado',
                'text' => 'El ítem del menú se creó correctamente.',
            ]);

            return redirect()->route('erp.menu.vista.todo');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Error creando menú', ['error' => $e]);
            $this->dispatch('alertaLivewire', [
                'title' => 'Error',
                'text' => 'No se pudo crear el menú.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.menu.menu-crear', [
            'menusPadre' => Menu::where('nivel', '<', 4)->orderBy('nombre')->get(),
            'allPermissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function placeholder()
    {
        return '<x-placeholder />';
    }
}
