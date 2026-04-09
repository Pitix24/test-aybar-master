<?php

namespace App\Livewire\Erp\Atc\TipoSolicitud;

use App\Models\TipoSolicitud;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Attributes\Title;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Vincular Usuarios')]
class TipoSolicitudUser extends Component
{
    use WithPagination;

    public TipoSolicitud $tipoSolicitud;

    #[Url(as: 'ua')]
    public $searchAgregados = '';

    #[Url(as: 'ud')]
    public $searchDisponibles = '';

    public $perPageAsignados  = 15;
    public $perPageDisponibles = 15;

    public function mount($id)
    {
        $this->tipoSolicitud = TipoSolicitud::findOrFail($id);
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
        $this->authorize('tipo-solicitud.agregar-usuarios');

        try {
            DB::beginTransaction();

            $this->tipoSolicitud->users()->syncWithoutDetaching([$userId => ['is_principal' => false]]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type'  => 'success',
                'title' => 'Agregado',
                'text'  => 'Usuario asignado al tipo de solicitud correctamente.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[TIPO-SOLICITUD USER] Error al agregar usuario: " . $e->getMessage(), [
                'usuario_id'     => auth()->id(),
                'tipo_solicitud_id' => $this->tipoSolicitud->id,
                'target_user_id' => $userId,
            ]);
            $this->dispatch('alertaLivewire', [
                'type'  => 'error',
                'title' => 'Error',
                'text'  => 'No se pudo asignar el usuario.',
            ]);
        }
    }

    public function quitarUsuario($userId)
    {
        $this->authorize('tipo-solicitud.eliminar-usuarios');

        try {
            DB::beginTransaction();

            $this->tipoSolicitud->users()->detach($userId);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type'  => 'success',
                'title' => 'Quitado',
                'text'  => 'Usuario retirado del tipo de solicitud.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[TIPO-SOLICITUD USER] Error al quitar usuario: " . $e->getMessage(), [
                'usuario_id'        => auth()->id(),
                'tipo_solicitud_id' => $this->tipoSolicitud->id,
                'target_user_id'    => $userId,
            ]);
            $this->dispatch('alertaLivewire', [
                'type'  => 'error',
                'title' => 'Error',
                'text'  => 'No se pudo retirar al usuario.',
            ]);
        }
    }

    public function marcarPrincipal($userId)
    {
        $this->authorize('tipo-solicitud.marcar-principal-usuario');

        try {
            DB::beginTransaction();

            // Quitar principal actual
            $this->tipoSolicitud->users()->updateExistingPivot(
                $this->tipoSolicitud->users()->wherePivot('is_principal', true)->pluck('users.id')->toArray(),
                ['is_principal' => false]
            );

            // Asignar nuevo principal
            $this->tipoSolicitud->users()->updateExistingPivot($userId, ['is_principal' => true]);

            DB::commit();

            $this->dispatch('alertaLivewire', [
                'type'  => 'success',
                'title' => 'Actualizado',
                'text'  => 'Se ha asignado el nuevo responsable del tipo de solicitud.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[TIPO-SOLICITUD USER] Error al marcar principal: " . $e->getMessage(), [
                'usuario_id'        => auth()->id(),
                'tipo_solicitud_id' => $this->tipoSolicitud->id,
                'target_user_id'    => $userId,
            ]);
            $this->dispatch('alertaLivewire', [
                'type'  => 'error',
                'title' => 'Error',
                'text'  => 'No se pudo actualizar el responsable.',
            ]);
        }
    }

    public function render()
    {
        // Usuarios ya asignados
        $usuariosAgregados = $this->tipoSolicitud->users()
            ->where('rol', 'admin')
            ->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchAgregados . '%')
                    ->orWhere('email', 'like', '%' . $this->searchAgregados . '%');
            })
            ->orderBy('name')
            ->paginate($this->perPageAsignados, ['*'], 'pageAsignados');

        $idsAgregados = $this->tipoSolicitud->users()->pluck('users.id')->toArray();

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

        return view('livewire.erp.atc.tipo-solicitud.tipo-solicitud-user', [
            'usuariosAgregados'   => $usuariosAgregados,
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
