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
use Illuminate\Support\Facades\Cache;
use OpenAI;

#[Layout('layouts.erp.layout-erp')]
#[Title('Panel de Control - ERP Aybar')]
class InicioLivewire extends Component
{
    use WithFileUploads;

    public $usuario;
    public $name;
    public $email;
    public $photo;

    public $password_actual;
    public $password_nuevo;
    public $password_confirmacion;

    public $metricas = [];
    public $areasUsuario = [];
    public $rolesConPermisos = [];
    public $saludo;
    public $mensajeBienvenida;

    public function mount()
    {
        $this->usuario = auth()->user();
        $this->name = $this->usuario->name;
        $this->email = $this->usuario->email;

        $this->setSaludo();
        $this->generarMensajeBienvenida();
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

    private function generarMensajeBienvenida()
    {
        $cacheKey = 'bienvenida_ai_motivacional_v3_' . $this->usuario->id . '_' . date('Ymd_') . (floor(date('H') / 8));

        $this->mensajeBienvenida = Cache::remember($cacheKey, now()->addHours(8), function () {
            try {
                $client = OpenAI::client(config('services.openai.key'));

                $roles = $this->usuario->roles->pluck('name')->implode(', ');

                $prompt = "Actúa como la empresa 'Aybar ERP' felicitando a un colaborador estrella.
                           Usuario: {$this->usuario->name}.
                           Roles: {$roles}.
                           Momento del día: {$this->saludo}.
                           
                           Instrucciones:
                           1. NO incluyas un saludo inicial ni el nombre del usuario.
                           2. Empieza DIRECTAMENTE con un mensaje de reconocimiento y apoyo total.
                           3. Frase clave: 'Lo estás haciendo muy bien, estamos orgullosos de tu esfuerzo. Sigue adelante porque sabemos que alcanzarás todos tus sueños y metas.'
                           4. Personaliza el mensaje motivacional de forma humana basándote en su rol ({$roles}).
                           5. Hazle sentir que su trabajo impacta positivamente y que la empresa está con él.
                           6. Máximo 180 caracteres. Tono inspirador, cálido y heroico.";

                $response = $client->chat()->create([
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Eres un mentor de productividad experto en gestión de equipos de soporte y validación.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 120,
                    'temperature' => 0.8, // Un poco más de creatividad para la motivación
                ]);

                return $response->choices[0]->message->content;
            } catch (\Exception $e) {
                return "¡Hola, {$this->usuario->name}! Es un excelente momento para avanzar con tus gestiones. Revisa tus indicadores y hagamos que hoy sea un día productivo.";
            }
        });
    }

    private function cargarMetricas()
    {
        $this->metricas = [
            'tickets_asignados' => Ticket::where('gestor_id', $this->usuario->id)->whereIn('estado_ticket_id', [1, 2])->count(),
            'proximas_citas' => Cita::where('gestor_id', $this->usuario->id)->where('fecha_inicio', '>=', now())->count(),
            'proyectos_activos' => 0,
            'participaciones' => $this->usuario->usuariosParticipantes ?? 0
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
            'photo' => 'nullable|image|max:2048',
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

        $this->photo = null;
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
