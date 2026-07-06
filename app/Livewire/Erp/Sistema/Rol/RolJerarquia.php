<?php

namespace App\Livewire\Erp\Sistema\Rol;

use App\Models\Area;
use App\Models\Level;
use App\Models\Rol;
use App\Services\JerarquiaService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
#[Title('Jerarquía de Roles')]
class RolJerarquia extends Component
{
    public $areas;

    // Filtros
    public $buscar = '';
    public $selectedAreaId = '';
    public $selectedLevelId = '';

    // Asignación
    public $rolSeleccionadoId = null;
    public $upper_id = '';

    public function mount()
    {
        $this->areas = Area::where('activo', true)->orderBy('nombre')->get();
    }

    public function resetFiltros(): void
    {
        $this->buscar = '';
        $this->selectedAreaId = '';
        $this->selectedLevelId = '';
    }

    public function seleccionarRol(int $rolId): void
    {
        $this->authorize('rol.jerarquia');
        $rol = Rol::findOrFail($rolId);
        $this->rolSeleccionadoId = $rol->id;
        $this->upper_id = $rol->upper_id ? (string) $rol->upper_id : '';
        $this->dispatch('abrirModalJerarquia');
    }

    public function guardarSuperior(): void
    {
        $this->authorize('rol.jerarquia');

        if (!$this->rolSeleccionadoId) {
            return;
        }

        $rol = Rol::findOrFail($this->rolSeleccionadoId);
        $newUpperId = $this->upper_id !== '' ? (int) $this->upper_id : null;

        $jerarquiaService = app(JerarquiaService::class);

        if (!$jerarquiaService->validarSinCiclos($rol->id, $newUpperId)) {
            $this->dispatch('alertaLivewire', [
                'type' => 'error',
                'title' => 'Error de Jerarquía',
                'text' => 'No puedes seleccionar este rol como superior ya que crearía un bucle/ciclo jerárquico.',
            ]);
            return;
        }

        $rol->update(['upper_id' => $newUpperId]);

        $this->rolSeleccionadoId = null;
        $this->upper_id = '';

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Guardado',
            'text' => 'La jerarquía se actualizó correctamente.',
        ]);

        $this->dispatch('cerrarModalJerarquia');
    }

    public function quitarSuperior(int $rolId): void
    {
        $this->authorize('rol.jerarquia');
        $rol = Rol::findOrFail($rolId);
        $rol->update(['upper_id' => null]);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Actualizado',
            'text' => 'El rol ahora es raíz (no tiene superior).',
        ]);
    }

    public function render()
    {
        $jerarquiaService = app(JerarquiaService::class);

        // Cargar árbol jerárquico
        $areaFiltroArbol = $this->selectedAreaId !== '' ? (int) $this->selectedAreaId : null;
        $arbolJerarquia = $jerarquiaService->obtenerArbol($areaFiltroArbol);

        // ====================== CONSULTA PRINCIPAL ======================
        $rolesQuery = Rol::query()
            ->with(['area', 'users', 'superior'])           // Quitamos 'level'
            ->whereNotNull('area_id')
            ->when($this->buscar !== '', fn($q) => $q->where('name', 'like', '%' . $this->buscar . '%'))
            ->when($this->selectedAreaId !== '', fn($q) => $q->where('area_id', (int) $this->selectedAreaId));

        $rolesList = $rolesQuery->orderBy('name')->get();

        // ====================== ROLES DISPONIBLES PARA SELECT ======================
        $rolesDisponibles = collect();
        if ($this->rolSeleccionadoId) {
            $rolSeleccionado = Rol::find($this->rolSeleccionadoId);
            if ($rolSeleccionado) {
                $rolesDisponibles = Rol::query()
                    ->where('id', '<>', $rolSeleccionado->id)
                    ->when($rolSeleccionado->area_id, fn($q) => $q->where('area_id', $rolSeleccionado->area_id))
                    ->orderBy('name')
                    ->get()
                    ->filter(function ($r) use ($rolSeleccionado) {
                        return !$rolSeleccionado->esAntepasadoDe($r);
                    });
            }
        }

        // ====================== RESUMEN ======================
        $vinculosActivos = Rol::whereNotNull('upper_id')->count();
        $rolesTotales = Rol::whereNotNull('area_id')->count();   // Ya no usamos level_id

        return view('livewire.erp.sistema.rol.rol-jerarquia', [
            'arbolJerarquia' => $arbolJerarquia,
            'rolesList' => $rolesList,
            'rolesDisponibles' => $rolesDisponibles,
            'resumen' => [
                'areas' => $this->areas->count(),
                'roles' => $rolesTotales,
                'vinculos' => $vinculosActivos,
            ]
        ]);
    }
}
