<?php
/**
 * Item: Desarrollo de componentes Livewire reutilizables para estandarización de funcionalidades dinámicas.
 * Modulo: Sistema / Inicio
 */

namespace App\Livewire\Erp\Inicio;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Cita;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

#[Layout('layouts.erp.layout-erp')]
#[Title('Panel de Control - ERP Aybar')]
class InicioLivewire extends Component
{
    use WithFileUploads;

    // Propiedades de Perfil
    public $usuario;
    public $name;
    public $email;
    public $photo;

    // Propiedades de Seguridad
    public $password_actual;
    public $password_nuevo;
    public $password_confirmacion;

    // Estadísticas y Datos
    public $metricas = [];
    public $areasUsuario = [];
    public $rolesConPermisos = [];
    public $saludo;

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->name = $this->usuario->name;
        $this->email = $this->usuario->email;

        $this->setSaludo();
        $this->cargarMetricas();
        $this->cargarAreasYRoles();
    }

    private function setSaludo()
    {
        $hora = date('H');
        if ($hora < 12) {
            $this->saludo = 'Buenos días';
        } elseif ($hora < 19) {
            $this->saludo = 'Buenas tardes';
        } else {
            $this->saludo = 'Buenas noches';
        }
    }

    private function cargarMetricas()
    {
        $this->metricas = [
            'tickets_asignados' => Ticket::where('gestor_id', $this->usuario->id)->whereIn('estado_ticket_id', [1, 2])->count(),
            'proximas_citas' => Cita::where('gestor_id', $this->usuario->id)->where('fecha_inicio', '>=', now())->count(),
            'proyectos_activos' => 0, // Placeholder
            'participaciones' => $this->usuario->usuariosParticipantes ?? 0 // Placeholder
        ];
    }

    private function cargarAreasYRoles()
    {
        $this->areasUsuario = $this->usuario->areas()
            ->with(['tiposSolicitud'])
            ->get();

        $this->rolesConPermisos = $this->usuario->roles->map(function ($rol) {
            return [
                'nombre' => $rol->name,
                'permisos' => $rol->permissions->pluck('name'),
            ];
        });
    }

    public function actualizarPerfil()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $this->usuario->name = $this->name;

        if ($this->photo) {
            $path = $this->photo->store('profile-photos', 'public');
            $this->usuario->profile_photo_path = $path;
        }

        $this->usuario->save();

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Perfil Actualizado',
            'text' => 'Sus datos básicos han sido guardados correctamente.'
        ]);

        $this->photo = null; // Limpiar preview
    }

    public function actualizarPassword()
    {
        $this->validate([
            'password_actual' => 'required',
            'password_nuevo' => 'required|min:8|different:password_actual',
            'password_confirmacion' => 'required|same:password_nuevo',
        ]);

        if (!Hash::check($this->password_actual, $this->usuario->password)) {
            throw ValidationException::withMessages([
                'password_actual' => 'La contraseña actual no es correcta.',
            ]);
        }

        $this->usuario->password = Hash::make($this->password_nuevo);
        $this->usuario->password_changed_at = now();
        $this->usuario->must_change_password = false;
        $this->usuario->save();

        $this->reset(['password_actual', 'password_nuevo', 'password_confirmacion']);

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => 'Seguridad Actualizada',
            'text' => 'Su contraseña ha sido cambiada exitosamente.'
        ]);
    }

    public function render()
    {
        return view('livewire.erp.inicio.inicio-livewire');
    }
}
