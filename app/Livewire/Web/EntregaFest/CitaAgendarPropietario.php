<?php

namespace App\Livewire\Web\EntregaFest;

use App\Events\EntregaFest\EntregaFestCitaConfirmacion;
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
    public string $direccion_sede = '';

    // Campo del formulario
    public string $fecha_firma = '';

    public $enviado = false;
    public $mensaje_exito = '';

    public function mount($slug, $propietarioId)
    {
        $this->prospecto = ProspectoEntregaFest::with(['entregaFest', 'proyecto.unidadNegocio'])
            ->findOrFail($propietarioId);

        $this->evento = $this->prospecto->entregaFest;
        $this->direccion_sede = $this->prospecto->proyecto?->unidadNegocio?->direccion ?? '';

        // Validar que el slug corresponda al evento
        if ($this->evento->slug !== $slug) {
            abort(404, 'Evento no encontrado o link inválido.');
        }

        // Solo prospectos con contrato preliminar aprobado en backoffice
        if ($this->prospecto->estado_contrato_preeliminar_emitido !== 'CONFORME') {
            abort(403, 'Tu contrato aún no ha sido aprobado para agendar firma.');
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
            'fecha_firma' => 'required|date|after:today',
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
            // Actualizar la fecha de firma
            $this->prospecto->update([
                'fecha_firma' => $this->fecha_firma,
            ]);

            // Disparar evento para notificaciones vía n8n (Email y WhatsApp)
            EntregaFestCitaConfirmacion::dispatch($this->prospecto->refresh());

            $this->enviado = true;
            $this->mensaje_exito = '¡Listo! Tu cita de firma ha sido agendada con éxito. ' .
                'Te hemos enviado los detalles de confirmación a tu correo y WhatsApp.';

            Log::info('[CITA AGENDAR PUBLICA] Fecha agendada', [
                'prospecto_id' => $this->prospecto->id,
                'fecha_firma' => $this->fecha_firma,
            ]);
        } catch (\Exception $e) {
            Log::error('[CITA AGENDAR PUBLICA] Error: ' . $e->getMessage());
            session()->flash('error', 'Ocurrió un error al guardar tu cita. Por favor intenta más tarde.');
        }
    }

    public function render()
    {
        return view('livewire.web.entrega-fest.firma-publica');
    }
}
