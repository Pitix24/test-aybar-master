<?php

namespace App\Livewire\Web\EntregaFest;

use App\Models\CopropietarioEntregaFest;
use App\Models\InvitadoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Events\EntregaFest\EntregaFestAsistenciaConfirmacion;
use App\Support\RedirigeSiEventoConcluido;

#[Layout('layouts.web.layout-web')]
#[Title('Formulario de Asistencia - Entrega Fest')]
class AsistenciaInvitacionCopropietario extends Component
{
    public $slug;
    public $copropietario;
    public $evento;

    // Form fields
    public $asistira = 'si';
    public $cantidad_acompanantes = 0;
    public $transporte = 'bus';
    public $observaciones = '';

    // Datos del acompañante
    public $acompanante_dni = '';
    public $acompanante_nombres = '';
    public $acompanante_email = '';
    public $acompanante_celular = '';

    public $enviado = false;
    public $mensaje_exito = '';
    public $codigo_invitado = '';

    use RedirigeSiEventoConcluido;

    public function mount($slug, $copropietarioId)
    {
        $this->copropietario = CopropietarioEntregaFest::with([
            'prospecto.entregaFest',
            'prospecto.proyecto',
            'invitado',
        ])->findOrFail($copropietarioId);

        $this->evento = $this->copropietario->prospecto->entregaFest;

        // 🛑 Si el evento ya se realizó → redirigir
        if ($redir = $this->redirigirSiConcluido($this->evento)) return $redir;

        // Validar slug
        if ($this->evento->slug !== $slug) {
            abort(404, 'Evento no encontrado o link inválido.');
        }

        // El prospecto titular debe estar aprobado en backoffice
        /*if ($this->copropietario->prospecto->estado_backoffice !== 'CONFORME') {
            abort(403, 'Tu evaluación aún no ha sido aprobada.');
        }*/

        // Si ya respondió (confirmó o rechazó), no permitir volver a llenar
        if (!is_null($this->copropietario->invitacion_confirmada)) {
            $this->enviado = true;
            $this->mensaje_exito = 'Ya hemos registrado tu respuesta anteriormente. ¡Muchas gracias!';
            $this->codigo_invitado = $this->copropietario->invitado?->codigo_invitado;
        }
    }

    protected function rules()
    {
        return [
            'asistira' => 'required|in:si,no',
            'cantidad_acompanantes' => 'required_if:asistira,si|integer|min:0|max:1',
            'transporte' => 'required_if:asistira,si|in:bus,propio',
            'observaciones' => 'nullable|string|max:500',

            // Validación para el acompañante
            'acompanante_dni' => 'required_if:cantidad_acompanantes,1|nullable|string|max:15',
            'acompanante_nombres' => 'required_if:cantidad_acompanantes,1|nullable|string|max:255',
            'acompanante_email' => 'nullable|email|max:255',
            'acompanante_celular' => 'nullable|string|max:20',
        ];
    }

    public function save()
    {
        $this->validate();

        // Doble check: ya respondió
        if (!is_null($this->copropietario->invitacion_confirmada)) {
            return;
        }

        try {
            DB::beginTransaction();

            $confirmado = ($this->asistira === 'si');

            // Actualizamos el copropietario con su respuesta
            $this->copropietario->update([
                'invitacion_confirmada' => $confirmado
            ]);

            if ($confirmado) {
                $codigo = 'INV-' . str_pad($this->evento->id, 3, '0', STR_PAD_LEFT) . '-' . strtoupper(bin2hex(random_bytes(3)));

                $invitado = InvitadoEntregaFest::create([
                    'entrega_fest_id' => $this->evento->id,
                    'prospecto_entrega_fest_id' => null,                          // no es titular
                    'copropietario_entrega_fest_id' => $this->copropietario->id,     // es copropietario
                    'codigo_invitado' => $codigo,
                    'cantidad_acompanantes_permitidos' => $this->cantidad_acompanantes,
                    'confirmado' => true,
                    'transporte' => $this->transporte === 'bus' ? InvitadoEntregaFest::TRANSPORTE_BUS : InvitadoEntregaFest::TRANSPORTE_PROPIO,
                    'observaciones_asistencia' => $this->observaciones,
                ]);

                // Si tiene acompañante, lo registramos
                if ($this->cantidad_acompanantes == 1) {
                    \App\Models\AcompananteEntregaFest::create([
                        'dni' => $this->acompanante_dni,
                        'nombres' => $this->acompanante_nombres,
                        'email' => $this->acompanante_email,
                        'celular' => $this->acompanante_celular,
                        'prospecto_entrega_fest_id' => $this->copropietario->prospecto_entrega_fest_id,
                        'invitado_entrega_fest_id' => $invitado->id,
                    ]);
                }

                DB::commit();

                // Despachar evento para notificaciones (Email/WhatsApp)
                EntregaFestAsistenciaConfirmacion::dispatch($invitado);

                $this->codigo_invitado = $codigo;
            } else {
                DB::commit();
                $this->codigo_invitado = null;
            }

            $this->enviado = true;
            $this->mensaje_exito = $confirmado
                ? '¡Excelente! Tu asistencia ha sido confirmada. Nos vemos en el evento.'
                : 'Gracias por informarnos. Lamentamos que no puedas asistir esta vez.';

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[ASIST. COPROP.] Error: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al procesar tu solicitud. Por favor intenta más tarde.');
        }
    }

    public function render()
    {
        return view('livewire.web.entrega-fest.asistencia-publica-copropietario');
    }
}
