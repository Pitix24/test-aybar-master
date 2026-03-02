<?php

namespace App\Livewire\Public\EntregaFest;

use App\Models\CopropietarioEntregaFest;
use App\Models\InvitadoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

use App\Mail\EntregaFest\InstruccionesEventoMail;
use App\Services\WhatsappService;

#[Layout('layouts.web.layout-web')]
#[Title('Formulario de Asistencia - Entrega Fest')]
class AsistenciaPublicaCopropietario extends Component
{
    public $slug;
    public $copropietario;
    public $evento;

    // Form fields
    public $asistira = 'si';
    public $cantidad_acompanantes = 0;
    public $transporte = 'bus';
    public $observaciones = '';

    public $enviado = false;
    public $mensaje_exito = '';
    public $codigo_invitado = '';

    public function mount($slug, $copropietarioId)
    {
        $this->copropietario = CopropietarioEntregaFest::with([
            'prospecto.entregaFest',
            'prospecto.proyecto',
            'invitado',
        ])->findOrFail($copropietarioId);

        $this->evento = $this->copropietario->prospecto->entregaFest;

        // Validar que el slug corresponda al evento del lote
        if ($this->evento->slug !== $slug) {
            abort(404, 'Evento no encontrado o link inválido.');
        }

        // El prospecto titular debe estar aprobado en backoffice
        if ($this->copropietario->prospecto->estado_backoffice !== 'aprobado') {
            abort(403, 'Tu evaluación aún no ha sido aprobada.');
        }

        // Si el copropietario ya tiene su propia invitación, no llenar de nuevo
        if ($this->copropietario->invitado) {
            $this->enviado = true;
            $this->mensaje_exito = 'Ya hemos registrado tu respuesta anteriormente. ¡Muchas gracias!';
            $this->codigo_invitado = $this->copropietario->invitado->codigo_invitado;
        }
    }

    protected function rules()
    {
        return [
            'asistira' => 'required|in:si,no',
            'cantidad_acompanantes' => 'required_if:asistira,si|integer|min:0|max:3',
            'transporte' => 'required_if:asistira,si|in:bus,propio',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    public function save(WhatsappService $whatsapp)
    {
        $this->validate();

        // Doble check: ya respondió
        if ($this->copropietario->invitado) {
            return;
        }

        try {
            DB::beginTransaction();

            $confirmado = ($this->asistira === 'si');
            $estado_confirmacion = $confirmado ? 'confirmado' : 'no_asiste';

            $codigo = $confirmado
                ? 'INV-' . str_pad($this->evento->id, 3, '0', STR_PAD_LEFT) . '-' . strtoupper(bin2hex(random_bytes(3)))
                : 'NA-' . uniqid();

            $invitado = InvitadoEntregaFest::create([
                'entrega_fest_id' => $this->evento->id,
                'prospecto_entrega_fest_id' => null,                          // no es titular
                'copropietario_entrega_fest_id' => $this->copropietario->id,     // es copropietario
                'codigo_invitado' => $codigo,
                'cantidad_acompanantes_permitidos' => $confirmado ? $this->cantidad_acompanantes : 0,
                'confirmado' => $confirmado,
                'estado_confirmacion' => $estado_confirmacion,
                'transporte' => $confirmado ? $this->transporte : 'na',
                'observaciones_asistencia' => $this->observaciones,
            ]);

            DB::commit();

            // Enviar notificaciones si confirma
            if ($confirmado) {
                // 1. Ticket por correo
                if ($this->copropietario->email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($this->copropietario->email)
                            ->send(new \App\Mail\EntregaFest\TicketAsistenciaMail($invitado));
                    } catch (\Exception $e) {
                        Log::error('[ASIST. COPROP.] Error enviando ticket: ' . $e->getMessage());
                    }

                    // 2. Instrucciones por correo
                    try {
                        \Illuminate\Support\Facades\Mail::to($this->copropietario->email)
                            ->send(new InstruccionesEventoMail($invitado));
                    } catch (\Exception $e) {
                        Log::error('[ASIST. COPROP.] Error enviando instrucciones mail: ' . $e->getMessage());
                    }
                }

                // 3. WhatsApp con instrucciones e imagen
                if ($this->copropietario->celular) {
                    try {
                        $celRaw = preg_replace('/\D/', '', $this->copropietario->celular);
                        $celular = strlen($celRaw) === 9 ? '51' . $celRaw : $celRaw;
                        $imagenUrl = 'https://plataforma-digital.aybarcorp.com/assets/imagen/construccion-aybar-corp.jpg';
                        $caption = "Hola *{$invitado->nombre_completo}*, aquí te compartimos las instrucciones para el evento *{$this->evento->nombre}*. ¡Te esperamos!";

                        $whatsapp->sendImage($celular, $imagenUrl, $caption);
                    } catch (\Exception $e) {
                        Log::error('[ASIST. COPROP.] Error enviando instrucciones WhatsApp: ' . $e->getMessage());
                    }
                }
            }

            $this->enviado = true;
            $this->codigo_invitado = $confirmado ? $codigo : null;
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
        return view('livewire.public.entrega-fest.asistencia-publica-copropietario');
    }
}
