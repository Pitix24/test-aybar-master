<?php

namespace App\Livewire\Public\EntregaFest;

use App\Models\InvitadoEntregaFest;
use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

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

    public function save()
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

            InvitadoEntregaFest::create([
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
        return view('livewire.public.entrega-fest.asistencia-publica');
    }
}
