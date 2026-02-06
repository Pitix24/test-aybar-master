<?php

namespace App\Livewire\Erp\Area;

use App\Models\Area;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Layout('layouts.erp.layout-erp')]
class AreaUser extends Component
{
    public Area $area;
    public $buscar = '';
    public $selectedUsers = [];
    public $principalUserId = null;

    public function mount($id)
    {
        $this->area = Area::findOrFail($id);

        $assignedUsers = $this->area->users()->get();
        $this->selectedUsers = $assignedUsers->pluck('id')->toArray();

        $principal = $assignedUsers->where('pivot.is_principal', true)->first();
        $this->principalUserId = $principal ? $principal->id : null;
    }

    public function toggleUser($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers = array_diff($this->selectedUsers, [$userId]);
            if ($this->principalUserId == $userId) {
                $this->principalUserId = null;
            }
        } else {
            $this->selectedUsers[] = $userId;
        }
    }

    public function setPrincipal($userId)
    {
        if (in_array($userId, $this->selectedUsers)) {
            $this->principalUserId = $userId;
        }
    }

    public function syncUsers()
    {
        try {
            DB::beginTransaction();

            $syncData = [];
            foreach ($this->selectedUsers as $userId) {
                $syncData[$userId] = [
                    'is_principal' => ($userId == $this->principalUserId)
                ];
            }

            $this->area->users()->sync($syncData);

            DB::commit();
            $this->dispatch('alertaLivewire', ['title' => 'Sincronizado', 'text' => 'Usuarios asignados correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al sincronizar usuarios de área: ' . $e->getMessage());
            $this->dispatch('alertaLivewire', ['title' => 'Error', 'text' => 'No se pudo sincronizar.']);
        }
    }

    public function render()
    {
        $users = User::where('activo', true)
            ->where('name', 'like', "%{$this->buscar}%")
            ->orderBy('name')
            ->get();

        return view('livewire.erp.area.area-user', compact('users'));
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-erp.placeholder />
        HTML;
    }
}
