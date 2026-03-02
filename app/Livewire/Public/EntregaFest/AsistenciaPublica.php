<?php

namespace App\Livewire\Public\EntregaFest;

use App\Models\InvitadoEntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

use App\Mail\EntregaFest\InstruccionesEventoMail;
use App\Services\WhatsappService;

#[Layout('layouts.web.layout-web')]
#[Title('Formulario de Asistencia - Entrega Fest')]
class AsistenciaPublica extends Component
{
    public $slug;
    public $id;
    public $prospecto;
    public $evento;

    // Form fields
    public $asistira = 'si';
    public $cantidad_acompanantes = 0;
    public $transporte = 'bus';
    public $observaciones = '';

    public $enviado = false;
    public $mensaje_exito = '';
    public $codigo_invitado = '';

    public function mount($slug, $id)
    {
        $this->slug = $slug;
        $this->id = $id;

        $this->prospecto = ProspectoEntregaFest::with(['entregaFest', 'proyecto'])
            ->findOrFail($id);

        $this->evento = $this->prospecto->entregaFest;

        // Validar que el slug coincida con el evento del prospecto
        if ($this->evento->slug !== $slug) {
            abort(404, 'Evento no encontrado o link inválido.');
        }

        // Si ya es invitado, no permitir volver a llenar
        if ($this->prospecto->invitado) {
            $this->enviado = true;
            $this->mensaje_exito = 'Ya hemos registrado tu respuesta anteriormente. ¡Muchas gracias!';
            $this->codigo_invitado = $this->prospecto->invitado->codigo_invitado;
        }

        // Si no está aprobado en backoffice, no debería estar aquí (opcional)
        if ($this->prospecto->estado_backoffice !== 'aprobado') {
            abort(403, 'Tu evaluación aún no ha sido aprobada.');
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

        if ($this->prospecto->invitado) {
            return;
        }

        try {
            DB::beginTransaction();

            $confirmado = ($this->asistira === 'si');
            $estado_confirmacion = $confirmado ? 'confirmado' : 'no_asiste';

            // Generar código único solo si asiste
            $codigo = null;
            if ($confirmado) {
                $codigo = 'INV-' . str_pad($this->evento->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            }

            $invitado = InvitadoEntregaFest::create([
                'entrega_fest_id' => $this->evento->id,
                'prospecto_entrega_fest_id' => $this->prospecto->id,
                'codigo_invitado' => $codigo ?? ('NA-' . uniqid()),
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
                if ($this->prospecto->email) {
                    try {
                        \Illuminate\Support\Facades\Mail::to($this->prospecto->email)
                            ->send(new \App\Mail\EntregaFest\TicketAsistenciaMail($invitado));
                    } catch (\Exception $e) {
                        Log::error("[ASISTENCIA PUBLICA] Error enviando ticket: " . $e->getMessage());
                    }

                    // 2. Instrucciones por correo
                    try {
                        \Illuminate\Support\Facades\Mail::to($this->prospecto->email)
                            ->send(new InstruccionesEventoMail($invitado));
                    } catch (\Exception $e) {
                        Log::error("[ASISTENCIA PUBLICA] Error enviando instrucciones mail: " . $e->getMessage());
                    }
                }

                // 3. WhatsApp con instrucciones e imagen
                if ($this->prospecto->celular) {
                    try {
                        $celRaw = preg_replace('/\D/', '', $this->prospecto->celular);
                        $celular = strlen($celRaw) === 9 ? '51' . $celRaw : $celRaw;
                        $imagenUrl = 'https://plataforma-digital.aybarcorp.com/assets/imagen/construccion-aybar-corp.jpg';
                        $caption = "Hola *{$invitado->nombre_completo}*, aquí te compartimos las instrucciones para el evento *{$this->evento->nombre}*. ¡Te esperamos!";

                        $whatsapp->sendImage($celular, $imagenUrl, $caption);
                    } catch (\Exception $e) {
                        Log::error("[ASISTENCIA PUBLICA] Error enviando instrucciones WhatsApp: " . $e->getMessage());
                    }
                }
            }

            $this->enviado = true;
            $this->codigo_invitado = $codigo;
            $this->mensaje_exito = $confirmado
                ? '¡Excelente! Tu asistencia ha sido confirmada. Nos vemos en el evento.'
                : 'Gracias por informarnos. Lamentamos que no puedas asistir esta vez.';

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("[ASISTENCIA PUBLICA] Error: " . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al procesar tu solicitud. Por favor intenta más tarde.');
        }
    }

    public function render()
    {
        return view('livewire.public.entrega-fest.asistencia-publica');
    }
}
