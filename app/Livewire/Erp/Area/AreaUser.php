<?php

namespace App\Livewire\Erp\Area;

use App\Models\Area;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AreaUsersExport;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class AreaUser extends Component
{
    use WithPagination;

    public Area $area;

    #[Url(as: 'ua')]
    public $searchAgregados = '';

    #[Url(as: 'ud')]
    public $searchDisponibles = '';

    public $perPageDisponibles = 15;

    public function mount($id)
    {
        $this->area = Area::findOrFail($id);
    }

    public function updated($property)
    {
        if (in_array($property, ['searchAgregados', 'searchDisponibles'])) {
            $this->resetPage();
        }
    }

    public function agregarUsuario($userId)
    {
        $this->area->users()->syncWithoutDetaching([$userId => ['is_principal' => false]]);
        $this->dispatch('alertaLivewire', ['title' => 'Agregado', 'text' => 'Usuario asignado al área correctamente.']);
    }

    public function quitarUsuario($userId)
    {
        $this->area->users()->detach($userId);
        $this->dispatch('alertaLivewire', ['title' => 'Quitado', 'text' => 'Usuario retirado del área.']);
    }

    public function marcarPrincipal($userId)
    {
        // Quitar principal actual
        $this->area->users()->updateExistingPivot(
            $this->area->users()->wherePivot('is_principal', true)->pluck('users.id')->toArray(),
            ['is_principal' => false]
        );

        // Asignar nuevo principal
        $this->area->users()->updateExistingPivot($userId, ['is_principal' => true]);

        $this->dispatch('alertaLivewire', ['title' => 'Actualizado', 'text' => 'Se ha asignado el nuevo responsable del área.']);
    }

    public function exportExcel()
    {
        return Excel::download(
            new AreaUsersExport($this->area, $this->searchAgregados),
            'usuarios-area-' . strtolower($this->area->nombre) . '.xlsx'
        );
    }

    public function render()
    {
        // Usuarios ya asignados
        $usuariosAgregados = $this->area->users()
            ->where('rol', 'admin')
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchAgregados . '%')
                    ->orWhere('email', 'like', '%' . $this->searchAgregados . '%');
            })
            ->orderBy('name')
            ->get();

        $idsAgregados = $this->area->users()->pluck('users.id')->toArray();

        // Usuarios disponibles (no asignados)
        // Nota: Filtrado por rol 'admin' sugerido en el ejemplo
        $usuariosDisponibles = User::whereNotIn('id', $idsAgregados)
            ->where('rol', 'admin')
            ->where('activo', true)
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchDisponibles . '%')
                    ->orWhere('email', 'like', '%' . $this->searchDisponibles . '%');
            })
            ->orderBy('name')
            ->paginate($this->perPageDisponibles, ['*'], 'pageUsers');

        return view('livewire.erp.area.area-user', [
            'usuariosAgregados' => $usuariosAgregados,
            'usuariosDisponibles' => $usuariosDisponibles,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
