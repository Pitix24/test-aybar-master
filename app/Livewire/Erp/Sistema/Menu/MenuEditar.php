<?php

namespace App\Livewire\Erp\Sistema\Menu;

use App\Models\Menu;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Editar Menú')]
class MenuEditar extends Component
{
    public Menu $menu;

    public $parent_id;
    public $nombre;
    public $ruta;
    public $url;
    public $icono;
    public $nivel;
    public $orden;
    public $permiso;
    public $activo;

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

    public function validationAttributes()
    {
        return [
            'parent_id' => 'menú padre',
            'nombre' => 'nombre',
            'ruta' => 'ruta (route name)',
            'url' => 'URL externa',
            'icono' => 'icono',
            'nivel' => 'nivel',
            'orden' => 'orden',
            'permiso' => 'permiso requerido',
            'activo' => 'estado',
        ];
    }

    public function mount($id)
    {
        $this->menu = Menu::findOrFail($id);
        $this->parent_id = $this->menu->parent_id;
        $this->nombre = $this->menu->nombre;
        $this->ruta = $this->menu->ruta;
        $this->url = $this->menu->url;
        $this->icono = $this->menu->icono;
        $this->nivel = $this->menu->nivel;
        $this->orden = $this->menu->orden;
        $this->permiso = $this->menu->permiso;
        $this->activo = $this->menu->activo;
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

    public function update()
    {
        $this->authorize('menu.editar');

        try {
            $this->validate();
            $this->normalizarRutas();

            DB::beginTransaction();

            $this->menu->update([
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

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'El ítem del menú se actualizó correctamente.',
            ]);

        } catch (ValidationException $e) {
            $this->dispatch('alertaLivewire', [
                'type' => 'warning',
                'title' => 'Advertencia',
                'text' => 'Verifique los errores de los campos resaltados.'
            ]);
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('menus')->error("[MENU] Error al actualizar menú: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'menu_id' => $this->menu->id,
                'datos' => $this->all(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el menú.',
            ]);
        }
    }

    #[On('eliminarMenuOn')]
    public function eliminarMenuOn()
    {
        $this->authorize('menu.eliminar');

        try {
            DB::beginTransaction();
            $menuId = $this->menu->id;
            $menuNombre = $this->menu->nombre;

            $this->menu->delete();
            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Eliminado',
                'text' => 'El ítem del menú se eliminó correctamente.',
            ]);

            return redirect()->route('erp.menu.vista.todo');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('menus')->error("[MENU] Error al eliminar menú: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'menu_id' => $this->menu->id,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo eliminar el menú.',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.erp.sistema.menu.menu-editar', [
            'menusPadre' => Menu::where('nivel', '<', 4)
                ->where('id', '!=', $this->menu->id)
                ->orderBy('nombre')
                ->get(),
            'allPermissions' => Permission::orderBy('name')->get(),
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
