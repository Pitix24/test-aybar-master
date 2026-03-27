<?php

namespace App\Livewire\Web\EntregaFest;

use App\Models\CopropietarioEntregaFest;
use App\Models\InvitadoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;


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
        if ($this->copropietario->prospecto->estado_backoffice !== 'CONFORME') {
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
            'cantidad_acompanantes' => 'required_if:asistira,si|integer|min:0|max:1',
            'transporte' => 'required_if:asistira,si|in:bus,propio',
            'observaciones' => 'nullable|string|max:500',
        ];
    }

    public function save()
    {
        $this->validate();

        // Doble check: ya respondió
        if ($this->copropietario->invitado) {
            return;
        }

        try {
            DB::beginTransaction();

            $confirmado = ($this->asistira === 'si');

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
                'transporte' => $confirmado ? ($this->transporte === 'bus' ? InvitadoEntregaFest::TRANSPORTE_BUS : InvitadoEntregaFest::TRANSPORTE_PROPIO) : InvitadoEntregaFest::TRANSPORTE_PROPIO,
                'observaciones_asistencia' => $this->observaciones,
            ]);

            DB::commit();

            // Despachar evento para notificaciones (Email/WhatsApp)
            \App\Events\EntregaFestAsistenciaConfirmada::dispatch($invitado);

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
