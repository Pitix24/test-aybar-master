<?php

namespace App\Livewire\Web\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Agenda tu Cita de Firma')]
class CitaAgendarPropietario extends Component
{
    public $slug;
    public $prospecto;
    public $evento;

    // Campo del formulario
    public string $fecha_firma = '';

    public $enviado = false;
    public $mensaje_exito = '';

    public function mount($slug, $propietarioId)
    {
        $this->prospecto = ProspectoEntregaFest::with(['entregaFest', 'proyecto'])
            ->findOrFail($propietarioId);

        $this->evento = $this->prospecto->entregaFest;

        // Validar que el slug corresponda al evento
        if ($this->evento->slug !== $slug) {
            abort(404, 'Evento no encontrado o link inválido.');
        }

        // Solo prospectos con contrato preliminar aprobado
        if ($this->prospecto->estado_contrato_preeliminar_emitido !== 'CONFORME') {
            abort(403, 'Tu contrato aún no ha sido aprobado.');
        }

        // Si ya tiene fecha de firma agendada, mostrar confirmación
        if ($this->prospecto->fecha_firma) {
            $this->enviado = true;
            $this->fecha_firma = $this->prospecto->fecha_firma;
            $this->mensaje_exito = 'Ya tienes una cita agendada. Si necesitas cambiarla, comunícate con nosotros.';
        }
    }

    protected function rules(): array
    {
        return [
            'fecha_firma' => [
                'required',
                'date',
                'after:today', // debe ser una fecha futura
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'fecha_firma.required' => 'Debes seleccionar una fecha para la firma.',
            'fecha_firma.date' => 'La fecha no es válida.',
            'fecha_firma.after' => 'La fecha debe ser posterior al día de hoy.',
        ];
    }

    public function save()
    {
        $this->validate();

        try {
            $this->prospecto->update([
                'fecha_firma' => $this->fecha_firma,
            ]);

            // Recargar el modelo para que el mail tenga datos frescos
            $this->prospecto->refresh()->load(['entregaFest', 'proyecto']);

            // Enviar correo de confirmación si tiene email registrado
            if ($this->prospecto->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($this->prospecto->email)
                        ->send(new \App\Mail\EntregaFest\FirmaConfirmacionMail($this->prospecto));
                } catch (\Exception $mailEx) {
                    Log::error('[FIRMA PUBLICA] Error enviando confirmación: ' . $mailEx->getMessage());
                }
            }

            $this->enviado = true;
            $this->mensaje_exito = '¡Listo! Tu cita de firma ha sido agendada para el ' .
                \Carbon\Carbon::parse($this->fecha_firma)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY [a las] HH:mm') .
                '. Te hemos enviado un correo de confirmación.';

            Log::info('[FIRMA PUBLICA] Fecha agendada', [
                'prospecto_id' => $this->prospecto->id,
                'fecha_firma' => $this->fecha_firma,
            ]);

        } catch (\Exception $e) {
            Log::error('[FIRMA PUBLICA] Error: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al guardar tu fecha. Por favor intenta más tarde.');
        }
    }

    public function render()
    {
        return view('livewire.web.entrega-fest.firma-publica');
    }
}
