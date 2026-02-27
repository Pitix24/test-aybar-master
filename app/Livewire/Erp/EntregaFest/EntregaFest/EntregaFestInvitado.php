<?php

namespace App\Livewire\Erp\EntregaFest\EntregaFest;

use App\Models\EntregaFest;
use App\Models\InvitadoEntregaFest;
use App\Models\WhatsappContacto;
use App\Models\WhatsappConversacion;
use App\Models\WhatsappMensaje;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EntregaFest\InstruccionesEventoMail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Lazy]
#[Layout('layouts.erp.layout-erp', ['anchoPantalla' => '100%'])]
#[Title('Invitados del Evento')]
class EntregaFestInvitado extends Component
{
    use WithPagination;

    public EntregaFest $evento;

    #[Url(as: 'q')]
    public $buscar = '';

    #[Url(keep: true)]
    public $estado_confirmacion = '';

    #[Url(keep: true)]
    public $transporte = '';

    #[Url(keep: true)]
    public $perPage = 20;

    public function mount($id)
    {
        $this->evento = EntregaFest::findOrFail($id);
    }

    public function updated($property)
    {
        if (in_array($property, ['buscar', 'estado_confirmacion', 'transporte', 'perPage'])) {
            $this->resetPage();
        }
    }

    public function resetFiltros()
    {
        $this->reset(['buscar', 'estado_confirmacion', 'transporte']);
        $this->resetPage();
    }

    public function exportExcelFiltro()
    {
        $this->authorize('entrega-fest.invitados');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EntregaFest\EntregaFestInvitadoExport(
                $this->evento->id,
                $this->buscar,
                $this->estado_confirmacion,
                $this->transporte,
                false,
                $this->perPage,
                $this->getPage()
            ),
            'invitados_filtrados.xlsx'
        );
    }

    public function exportExcelTodo()
    {
        $this->authorize('entrega-fest.invitados');

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EntregaFest\EntregaFestInvitadoExport(
                $this->evento->id,
                '',
                '',
                '',
                true
            ),
            'invitados_todo_' . $this->evento->codigo . '.xlsx'
        );
    }

    // ──────────────────────────────────────────────────────────────
    // INSTRUCCIONES DEL EVENTO
    // ──────────────────────────────────────────────────────────────

    public function enviarCorreoInstrucciones()
    {
        $invitados = InvitadoEntregaFest::with(['prospecto.proyecto', 'copropietario.prospecto.proyecto', 'entregaFest'])
            ->where('entrega_fest_id', $this->evento->id)
            ->get()
            ->filter(fn($inv) => !empty($inv->email));

        if ($invitados->isEmpty()) {
            $this->dispatch('alertaLivewire', [
                'type' => 'info',
                'title' => 'Sin destinatarios',
                'text' => 'No hay invitados con email registrado.',
            ]);
            return;
        }

        $enviados = 0;
        foreach ($invitados as $invitado) {
            try {
                Mail::to($invitado->email)->send(new InstruccionesEventoMail($invitado));
                $enviados++;
            } catch (\Exception $e) {
                Log::error('[INSTRUCCIONES MAIL] Error a ' . $invitado->email . ': ' . $e->getMessage());
            }
        }

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Completado!',
            'text' => "Se enviaron $enviados correos de instrucciones.",
        ]);
    }

    public function enviarWhatsappInstrucciones(WhatsappService $whatsapp)
    {
        $imagenUrl = 'https://plataforma-digital.aybarcorp.com/assets/imagen/construccion-aybar-corp.jpg';

        $invitados = InvitadoEntregaFest::with(['prospecto', 'copropietario', 'entregaFest'])
            ->where('entrega_fest_id', $this->evento->id)
            ->get()
            ->filter(fn($inv) => !empty($inv->celular));

        if ($invitados->isEmpty()) {
            $this->dispatch('alertaLivewire', [
                'type' => 'info',
                'title' => 'Sin destinatarios',
                'text' => 'No hay invitados con celular registrado.',
            ]);
            return;
        }

        $formatearCelular = function (string $raw): string {
            $cel = preg_replace('/\D/', '', $raw);
            return strlen($cel) === 9 ? '51' . $cel : $cel;
        };

        $enviados = 0;
        foreach ($invitados as $invitado) {
            $celular = $formatearCelular($invitado->celular);
            $caption = "Hola *{$invitado->nombre_completo}*, aqui te compartimos las instrucciones para el evento *{$this->evento->nombre}*. ¡Te esperamos!";

            $response = $whatsapp->sendImage($celular, $imagenUrl, $caption);
            if ($response) {
                $enviados++;
                Log::info('[INSTRUCCIONES WSP] Enviado a: ' . $invitado->nombre_completo . ' | ' . $celular);
            } else {
                Log::warning('[INSTRUCCIONES WSP] Fallido para: ' . $invitado->nombre_completo . ' | ' . $celular);
            }
        }

        $this->dispatch('alertaLivewire', [
            'type' => 'success',
            'title' => '¡Completado!',
            'text' => "Se enviaron $enviados WhatsApp con instrucciones.",
        ]);
    }

    public function placeholder()
    {
        return <<<'HTML'
        <x-placeholder />
        HTML;
    }

    public function render()
    {
        $items = InvitadoEntregaFest::query()
            ->with([
                'prospecto.proyecto',
                'copropietario.prospecto.proyecto',
            ])
            ->where('entrega_fest_id', $this->evento->id)
            ->when($this->buscar, function ($query) {
                $query->where(function ($q) {
                    // Buscar en titular
                    $q->whereHas('prospecto', function ($sub) {
                        $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })
                        // Buscar en copropietario
                        ->orWhereHas('copropietario', function ($sub) {
                        $sub->where('nombres', 'like', '%' . $this->buscar . '%')
                            ->orWhere('dni', 'like', '%' . $this->buscar . '%');
                    })
                        ->orWhere('codigo_invitado', 'like', '%' . $this->buscar . '%');
                });
            })
            ->when($this->estado_confirmacion, fn($q) => $q->where('estado_confirmacion', $this->estado_confirmacion))
            ->when($this->transporte, fn($q) => $q->where('transporte', $this->transporte))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.erp.entrega-fest.entrega-fest.entrega-fest-invitado', [
            'items' => $items
        ]);
    }
}
