<?php

namespace App\Livewire\Web\EntregaFest;

use App\Events\EntregaFest\EntregaFestAsistenciaConfirmacion;
use App\Models\InvitadoEntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Formulario de Asistencia - Entrega Fest')]
class AsistenciaInvitacionPropietario extends Component
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

    public function mount($slug, $propietarioId)
    {
        $this->slug = $slug;
        $this->id = $propietarioId;

        $this->prospecto = ProspectoEntregaFest::with(['entregaFest', 'proyecto', 'invitado'])
            ->findOrFail($propietarioId);

        $this->evento = $this->prospecto->entregaFest;

        // Validar que el slug coincida con el evento del prospecto
        if ($this->evento->slug !== $slug) {
            abort(404, 'Evento no encontrado o link inválido.');
        }

        // Si ya respondió (confirmó o rechazó), no permitir volver a llenar
        if (!is_null($this->prospecto->invitacion_confirmada)) {
            $this->enviado = true;
            $this->mensaje_exito = 'Ya hemos registrado tu respuesta anteriormente. ¡Muchas gracias!';
            $this->codigo_invitado = $this->prospecto->invitado?->codigo_invitado;
        }

        // Si no está aprobado en backoffice, no debería estar aquí (opcional)
        if ($this->prospecto->estado_backoffice !== 'CONFORME') {
            abort(403, 'Tu evaluación aún no ha sido aprobada.');
        }
    }

    protected function rules()
    {
        return [
            'asistira' => 'required|in:si,no',
            'cantidad_acompanantes' => 'required_if:asistira,si|integer|min:0|max:1',
            'transporte' => 'required_if:asistira,si|in:bus,propio',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    public function save()
    {
        $this->validate();

        // Doble check
        if (!is_null($this->prospecto->invitacion_confirmada)) {
            return;
        }

        try {
            DB::beginTransaction();

            $confirmado = ($this->asistira === 'si');

            // Actualizamos el prospecto con su respuesta
            $this->prospecto->update([
                'invitacion_confirmada' => $confirmado
            ]);

            if ($confirmado) {
                // Generar código único solo si asiste
                $codigo = 'INV-' . str_pad($this->evento->id, 3, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);

                $invitado = InvitadoEntregaFest::create([
                    'entrega_fest_id' => $this->evento->id,
                    'prospecto_entrega_fest_id' => $this->prospecto->id,
                    'codigo_invitado' => $codigo,
                    'cantidad_acompanantes_permitidos' => $this->cantidad_acompanantes,
                    'confirmado' => true,
                    'transporte' => $this->transporte === 'bus' ? InvitadoEntregaFest::TRANSPORTE_BUS : InvitadoEntregaFest::TRANSPORTE_PROPIO,
                    'observaciones_asistencia' => $this->observaciones,
                ]);

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
            Log::error("[ASISTENCIA PUBLICA] Error: " . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al procesar tu solicitud. Por favor intenta más tarde.');
        }
    }

    public function render()
    {
        return view('livewire.web.entrega-fest.asistencia-publica');
    }
}
