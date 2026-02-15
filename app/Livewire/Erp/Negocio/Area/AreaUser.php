<?php

namespace App\Livewire\Erp\Negocio\Area;

use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Negocio\AreaUsersExport;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Vincular Usuarios')]
class AreaUser extends Component
{
    use WithPagination;

    public Area $area;

    #[Url(as: 'ua')]
    public $searchAgregados = '';

    #[Url(as: 'ud')]
    public $searchDisponibles = '';

    public $perPageAsignados = 15;
    public $perPageDisponibles = 15;

    public function mount($id)
    {
        $this->authorize('area.ver-usuarios');
        $this->area = Area::findOrFail($id);
    }

    public function updated($property)
    {
        if ($property === 'searchAgregados' || $property === 'perPageAsignados') {
            $this->resetPage('pageAsignados');
        }
        if ($property === 'searchDisponibles' || $property === 'perPageDisponibles') {
            $this->resetPage('pageDisponibles');
        }
    }

    public function resetFiltrosAgregados()
    {
        $this->reset('searchAgregados');
        $this->resetPage('pageAsignados');
    }

    public function resetFiltrosDisponibles()
    {
        $this->reset('searchDisponibles');
        $this->resetPage('pageDisponibles');
    }

    public function agregarUsuario($userId)
    {
        $this->authorize('area.editar');

        try {
            DB::beginTransaction();

            $this->area->users()->syncWithoutDetaching([$userId => ['is_principal' => false]]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Agregado',
                'text' => 'Usuario asignado al área correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('area')->error("[AREA USER] Error al agregar usuario: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'area_id' => $this->area->id,
                'target_user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo asignar el usuario.'
            ]);
        }
    }

    public function quitarUsuario($userId)
    {
        $this->authorize('area.editar');

        try {
            DB::beginTransaction();

            $this->area->users()->detach($userId);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Quitado',
                'text' => 'Usuario retirado del área.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('area')->error("[AREA USER] Error al quitar usuario: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'area_id' => $this->area->id,
                'target_user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo retirar al usuario.'
            ]);
        }
    }

    public function marcarPrincipal($userId)
    {
        $this->authorize('area.editar');

        try {
            DB::beginTransaction();

            // Quitar principal actual
            $this->area->users()->updateExistingPivot(
                $this->area->users()->wherePivot('is_principal', true)->pluck('users.id')->toArray(),
                ['is_principal' => false]
            );

            // Asignar nuevo principal
            $this->area->users()->updateExistingPivot($userId, ['is_principal' => true]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type' => 'success',
                'title' => 'Actualizado',
                'text' => 'Se ha asignado el nuevo responsable del área.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('area')->error("[AREA USER] Error al marcar principal: " . $e->getMessage(), [
                'usuario_id' => auth()->id(),
                'area_id' => $this->area->id,
                'target_user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se pudo actualizar el responsable.'
            ]);
        }
    }

    public function exportExcel()
    {
        $this->authorize('area.exportar-todo');

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
            ->paginate($this->perPageAsignados, ['*'], 'pageAsignados');

        $idsAgregados = $this->area->users()->pluck('users.id')->toArray();

        // Usuarios disponibles (no asignados)
        $usuariosDisponibles = User::whereNotIn('id', $idsAgregados)
            ->where('rol', 'admin')
            ->where('activo', true)
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchDisponibles . '%')
                    ->orWhere('email', 'like', '%' . $this->searchDisponibles . '%');
            })
            ->orderBy('name')
            ->paginate($this->perPageDisponibles, ['*'], 'pageDisponibles');

        return view('livewire.erp.negocio.area.area-user', [
            'usuariosAgregados' => $usuariosAgregados,
            'usuariosDisponibles' => $usuariosDisponibles,
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }
}
