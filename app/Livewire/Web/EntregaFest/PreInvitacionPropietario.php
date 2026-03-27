<?php

namespace App\Livewire\Web\EntregaFest;

use App\Models\ProspectoEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Confirmar Interés - Entrega Fest')]
class PreInvitacionPropietario extends Component
{
    public $slug;
    public $id;
    public $prospecto;
    public $evento;

    public $interes = 'si';
    public $enviado = false;
    public $mensaje_exito = '';

    public function mount($slug, $propietarioId)
    {
        $this->slug = $slug;
        $this->id = $propietarioId;

        $this->prospecto = ProspectoEntregaFest::with(['entregaFest', 'proyecto'])
            ->findOrFail($propietarioId);

        $this->evento = $this->prospecto->entregaFest;

        // Validar slug
        if ($this->evento->slug !== $slug) {
            abort(404);
        }

        // Si ya confirmó la pre-invitación anteriormente (ya sea si o no)
        if ($this->prospecto->preinvitacion_confirmada !== null) {
            $this->enviado = true;
            $this->mensaje_exito = ($this->prospecto->preinvitacion_confirmada)
                ? 'Ya hemos registrado tu interés anteriormente. ¡Muchas gracias!'
                : 'Ya has indicado anteriormente que no podrás participar. ¡Gracias por informarnos!';
        }

        // Pre-llenar interés desde URL si existiera (aunque ya no se envía en el correo)
        if (request()->has('interes')) {
            $this->interes = request('interes');
        }
    }

    public function guardarInteres($respuesta)
    {
        $this->interes = $respuesta;

        $this->prospecto->update([
            'preinvitacion_confirmada' => ($respuesta === 'si')
        ]);

        $this->enviado = true;
        $this->mensaje_exito = ($respuesta === 'si')
            ? '¡Excelente! Hemos registrado tu interés en participar. Te contactaremos pronto con más detalles.'
            : 'Gracias por informarnos. Entendemos que no puedas participar en esta ocasión.';
    }

    public function render()
    {
        return view('livewire.web.entrega-fest.pre-invitacion-propietario');
    }
}
