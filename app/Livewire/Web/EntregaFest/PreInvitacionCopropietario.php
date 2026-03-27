<?php

namespace App\Livewire\Web\EntregaFest;

use App\Models\CopropietarioEntregaFest;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.web.layout-web')]
#[Title('Confirmar Interés (Copropietario) - Entrega Fest')]
class PreInvitacionCopropietario extends Component
{
    public $slug;
    public $copropietarioId;
    public $copropietario;
    public $evento;

    public $interes = 'si';
    public $enviado = false;
    public $mensaje_exito = '';

    public function mount($slug, $copropietarioId)
    {
        $this->slug = $slug;
        $this->copropietarioId = $copropietarioId;

        $this->copropietario = CopropietarioEntregaFest::with([
            'prospecto.entregaFest',
            'prospecto.proyecto',
        ])->findOrFail($copropietarioId);

        $this->evento = $this->copropietario->prospecto->entregaFest;

        // Validar slug
        if ($this->evento->slug !== $slug) {
            abort(404);
        }

        // Si ya confirmó la pre-invitación anteriormente (ya sea si o no)
        if ($this->copropietario->preinvitacion_confirmada !== null) {
            $this->enviado = true;
            $this->mensaje_exito = ($this->copropietario->preinvitacion_confirmada)
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

        $this->copropietario->update([
            'preinvitacion_confirmada' => ($respuesta === 'si')
        ]);

        $this->enviado = true;
        $this->mensaje_exito = ($respuesta === 'si')
            ? '¡Excelente! Como copropietario, hemos registrado tu interés. Te contactaremos pronto.'
            : 'Gracias por informarnos. Entendemos que no puedas participar en esta ocasión.';
    }

    public function render()
    {
        return view('livewire.public.entrega-fest.pre-invitacion-copropietario');
    }
}
